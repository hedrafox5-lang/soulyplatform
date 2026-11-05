<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\ClientDevice;
use App\Models\ErpOrder;
use App\Models\HardwareOrder;
use App\Models\Package;
use App\Models\PromoCode;
use App\Models\PromoCodeUsage;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Services\SoulyNotificationService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ClientPortalController extends Controller
{
    /**
     * Client Portal main dashboard summary.
     */
    public function index(): View
    {
        $user = Auth::user();

        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            [
                'cash_balance'  => 0.00,
                'bonus_balance' => 0.00,
                'currency'      => 'EGP',
                'is_locked'     => false,
            ]
        );

        $activeBookings = Booking::where('user_id', $user->id)
            ->with(['service', 'package'])
            ->latest()
            ->take(5)
            ->get();

        $recentTransactions = WalletTransaction::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        $recentTickets = Ticket::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        $devicesCount = ClientDevice::where('user_id', $user->id)->count();
        $erpOrdersCount = ErpOrder::where('user_id', $user->id)->where('status', 'active')->count();

        return view('portal.index', compact(
            'user',
            'wallet',
            'activeBookings',
            'recentTransactions',
            'recentTickets',
            'devicesCount',
            'erpOrdersCount'
        ));
    }

    /**
     * Digital wallet balance, dual ledger, and payment gateway info.
     */
    public function wallet(): View
    {
        $user = Auth::user();
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['cash_balance' => 0.00, 'bonus_balance' => 0.00, 'currency' => 'EGP', 'is_locked' => false]
        );

        $transactions = WalletTransaction::where('user_id', $user->id)
            ->latest()
            ->paginate(15);

        // Fetch live gateway account numbers from dynamic settings
        $gateways = [
            'vodafone_cash' => Setting::get('payment_vodafone_cash', '01090623326'),
            'instapay'      => Setting::get('payment_instapay', 'souly@instapay'),
            'bank_account'  => Setting::get('payment_bank_account', 'CIB Bank - 100054896231'),
        ];

        return view('portal.wallet', compact('user', 'wallet', 'transactions', 'gateways'));
    }

    /**
     * Submit wallet top-up request with payment proof or initiate online payment.
     */
    public function deposit(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'amount'                   => ['required', 'numeric', 'min:50', 'max:100000'],
            'gateway'                  => ['required', 'string', 'in:vodafone_cash,instapay,bank_transfer,card'],
            'sender_number_or_account' => ['required', 'string', 'max:100'],
            'receipt'                  => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ], [
            'amount.min'                   => 'الحد الأدنى لشحن المحفظة هو 50 ج.م.',
            'sender_number_or_account.required' => 'يرجى إدخال رقم المحفظة أو الحساب الذي قمت بالتحويل منه.',
        ]);

        $user = Auth::user();
        $receiptPath = null;

        if ($request->hasFile('receipt')) {
            $receiptPath = $request->file('receipt')->store('wallet_receipts/' . $user->id, 'public');
        }

        try {
            DB::transaction(function () use ($user, $validated, $receiptPath) {
                $wallet = Wallet::firstOrCreate(['user_id' => $user->id]);

                WalletTransaction::create([
                    'wallet_id'                => $wallet->id,
                    'user_id'                  => $user->id,
                    'reference'                => 'DEP-' . date('ymd') . '-' . strtoupper(Str::random(6)),
                    'type'                     => 'deposit',
                    'amount'                   => (float)$validated['amount'],
                    'currency'                 => 'EGP',
                    'payment_method'           => $validated['gateway'],
                    'sender_number_or_account' => strip_tags(trim($validated['sender_number_or_account'])),
                    'receipt_image'            => $receiptPath,
                    'status'                   => 'pending',
                    'description'              => "طلب شحن رصيد نقدي عبر {$validated['gateway']}",
                ]);
            });

            SoulyNotificationService::sendToAdmins(
                'wallet',
                'طلب شحن محفظة جديد 💳',
                "طلب شحن بقيمة {$validated['amount']} ج.م من العميل {$user->name}",
                route('admin.wallets.index'),
                'credit-card'
            );

            return redirect()->route('portal.wallet')
                             ->with('citadel_toast', 'تم إرسال طلب الشحن بنجاح، جاري التحقق من التحويل وإضافة الرصيد.');

        } catch (\Throwable $e) {
            Log::error('Wallet Deposit Error: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء معالجة طلب الإيداع، يرجى المحاولة لاحقاً.')->withInput();
        }
    }

    /**
     * Apply conditional promotional code or bonus wallet voucher.
     */
    public function applyPromo(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code'      => ['required', 'string', 'max:50'],
            'sector'    => ['required', 'string', 'in:maintenance,cyber_shield,hardware,branderp,wallet_bonus'],
            'subtotal'  => ['nullable', 'numeric', 'min:0'],
        ]);

        $code = strtoupper(trim($validated['code']));
        $user = Auth::user();
        $subtotal = (float)($validated['subtotal'] ?? 0);

        $promo = PromoCode::where('code', $code)
            ->where('is_active', true)
            ->first();

        if (!$promo) {
            return response()->json(['success' => false, 'message' => 'كود الخصم غير صحيح أو غير مفعل.'], 404);
        }

        // Check expiration
        if ($promo->expires_at && $promo->expires_at->isPast()) {
            return response()->json(['success' => false, 'message' => 'عفواً، انتهت صلاحية هذا الكود.'], 422);
        }

        // Check usage limits per user
        $userUsageCount = PromoCodeUsage::where('promo_code_id', $promo->id)
            ->where('user_id', $user->id)
            ->count();

        $maxPerUser = $promo->max_uses_per_user ?? 1;
        if ($userUsageCount >= $maxPerUser) {
            return response()->json(['success' => false, 'message' => 'لقد استنفدت الحد المسموح لاستخدام هذا الكود.'], 422);
        }

        // Check sector eligibility
        if ($promo->target_sector !== 'all' && $promo->target_sector !== $validated['sector']) {
            return response()->json(['success' => false, 'message' => 'هذا الكود غير مخصص لهذا القطاع أو الخدمة.'], 422);
        }

        // Check minimum subtotal requirement
        if ($promo->min_order_amount && $subtotal < (float)$promo->min_order_amount) {
            return response()->json([
                'success' => false,
                'message' => "يشترط هذا الكود حداً أدنى للطلب قدره " . number_format($promo->min_order_amount, 2) . " ج.م."
            ], 422);
        }

        // Calculate discount amount
        $discountAmount = 0.00;
        if ($promo->type === 'percentage') {
            $discountAmount = ($subtotal * ((float)$promo->value / 100));
            if ($promo->max_discount_amount) {
                $discountAmount = min($discountAmount, (float)$promo->max_discount_amount);
            }
        } elseif ($promo->type === 'fixed') {
            $discountAmount = min((float)$promo->value, $subtotal);
        } elseif ($promo->type === 'wallet_bonus') {
            // Instant bonus balance injection with usage rules
            DB::transaction(function () use ($user, $promo) {
                $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->firstOrFail();
                $wallet->increment('bonus_balance', (float)$promo->value);

                PromoCodeUsage::create([
                    'promo_code_id' => $promo->id,
                    'user_id'       => $user->id,
                    'amount_used'   => (float)$promo->value,
                ]);

                WalletTransaction::create([
                    'wallet_id'   => $wallet->id,
                    'user_id'     => $user->id,
                    'reference'   => 'BONUS-' . strtoupper(Str::random(6)),
                    'type'        => 'bonus_credit',
                    'amount'      => (float)$promo->value,
                    'currency'    => 'EGP',
                    'status'      => 'completed',
                    'description' => "شحن رصيد ترويجي مجاني عبر الكود ({$promo->code})",
                ]);
            });

            return response()->json([
                'success' => true,
                'type'    => 'wallet_bonus',
                'message' => "تم شحن {$promo->value} ج.م رصيد ترويجي مجاني في محفظتك بنجاح!",
            ]);
        }

        return response()->json([
            'success'         => true,
            'code'            => $promo->code,
            'discount_amount' => round($discountAmount, 2),
            'final_total'     => max(0, round($subtotal - $discountAmount, 2)),
            'message'         => 'تم تطبيق كود الخصم بنجاح.',
        ]);
    }

    /**
     * List user bookings with status filtering.
     */
    public function bookings(Request $request): View
    {
        $user = Auth::user();
        $query = Booking::where('user_id', $user->id)->with(['service', 'package']);

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        $bookings = $query->latest()->paginate(10);
        $totalCount = Booking::where('user_id', $user->id)->count();

        return view('portal.bookings', compact('user', 'bookings', 'totalCount'));
    }

    /**
     * Store a service booking from client portal with server-side pricing and atomic wallet checkout.
     */
    public function storeBooking(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'service_id'     => ['required', 'exists:services,id'],
            'package_id'     => ['nullable', 'exists:packages,id'],
            'scheduled_at'   => ['nullable', 'date', 'after_or_equal:today'],
            'preferred_time' => ['nullable', 'string', 'max:50'],
            'address'        => ['nullable', 'string', 'max:500'],
            'payment_method' => ['required', 'in:cash,wallet,bank_transfer'],
            'notes'          => ['nullable', 'string', 'max:1000'],
            'promo_code'     => ['nullable', 'string', 'max:50'],
        ]);

        $user = Auth::user();
        $service = Service::findOrFail($validated['service_id']);
        $package = !empty($validated['package_id']) ? Package::findOrFail($validated['package_id']) : null;

        $isFieldVisit = in_array($service->sector, ['engineering_maintenance', 'cyber_shield'], true);
        if (!$isFieldVisit && ($request->filled('scheduled_at') || $request->filled('preferred_time'))) {
            return back()->with('error', 'المواعيد والزيارات الميدانية مقتصرة فقط على قطاعي الصيانة والأمن السيبراني.')->withInput();
        }

        $subtotal = $package ? (float)$package->price : (float)($service->base_price ?? 0.00);
        $discount = 0.00;

        // Process promo code if provided
        if (!empty($validated['promo_code'])) {
            $promo = PromoCode::where('code', strtoupper(trim($validated['promo_code'])))
                ->where('is_active', true)
                ->first();

            if ($promo && (!$promo->expires_at || $promo->expires_at->isFuture())) {
                $discount = $promo->type === 'percentage' 
                    ? ($subtotal * ((float)$promo->value / 100))
                    : min((float)$promo->value, $subtotal);
            }
        }

        $finalTotal = max(0, round($subtotal - $discount, 2));

        try {
            $booking = DB::transaction(function () use ($user, $service, $package, $validated, $isFieldVisit, $finalTotal) {
                $bookingNumber = 'BK-' . date('ymd') . '-' . strtoupper(Str::random(5));
                $paymentStatus = 'unpaid';

                if ($validated['payment_method'] === 'wallet') {
                    $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->firstOrFail();

                    if ((float)$wallet->cash_balance < $finalTotal) {
                        throw new \DomainException('رصيدك النقدي في المحفظة غير كافٍ لسداد قيمة الحجز.');
                    }

                    $wallet->decrement('cash_balance', $finalTotal);

                    WalletTransaction::create([
                        'wallet_id'   => $wallet->id,
                        'user_id'     => $user->id,
                        'reference'   => $bookingNumber,
                        'type'        => 'payment',
                        'amount'      => $finalTotal,
                        'currency'    => 'EGP',
                        'status'      => 'completed',
                        'description' => "سداد قيمة حجز الخدمة ({$service->name_ar})",
                    ]);

                    $paymentStatus = 'paid';
                }

                return Booking::create([
                    'booking_number' => $bookingNumber,
                    'user_id'        => $user->id,
                    'service_id'     => $service->id,
                    'package_id'     => $package?->id,
                    'is_field_visit' => $isFieldVisit,
                    'preferred_date' => $isFieldVisit ? ($validated['scheduled_at'] ?? null) : null,
                    'preferred_time' => $isFieldVisit ? ($validated['preferred_time'] ?? null) : null,
                    'address'        => $validated['address'] ?? null,
                    'total_amount'   => $finalTotal,
                    'status'         => 'new',
                    'payment_status' => $paymentStatus,
                    'payment_method' => $validated['payment_method'],
                    'notes'          => $validated['notes'] ?? null,
                ]);
            });

            SoulyNotificationService::sendToAdmins(
                'booking',
                'حجز خدمة جديد 📅',
                "طلب حجز جديد برقم {$booking->booking_number} للخدمة {$service->name_ar}",
                route('admin.bookings.index'),
                'calendar'
            );

            return redirect()->route('portal.bookings')
                             ->with('citadel_toast', "تم تسجيل حجزك بنجاح برقم ({$booking->booking_number}).");

        } catch (\DomainException $de) {
            return back()->with('error', $de->getMessage())->withInput();
        } catch (\Throwable $e) {
            Log::error('Store Booking Error: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء حفظ الحجز، يرجى إعادة المحاولة.')->withInput();
        }
    }

    /**
     * View official certified invoice for a completed booking.
     */
    public function invoice(Booking $booking): View
    {
        abort_if((int)$booking->user_id !== (int)Auth::id(), 403, 'غير مصرح بعرض هذه الفاتورة.');

        $booking->load(['service', 'package', 'user']);
        return view('portal.invoice', compact('booking'));
    }

    /**
     * View digital warranty certificate for completed maintenance or cybersecurity services.
     */
    public function certificate(Booking $booking): View
    {
        abort_if((int)$booking->user_id !== (int)Auth::id(), 403, 'غير مصرح.');
        abort_if($booking->status !== 'completed', 404, 'الشهادة تصدر حصراً بعد اكتمال الخدمة واعتمادها.');

        $booking->load(['service', 'package', 'user']);
        return view('portal.certificate', compact('booking'));
    }

    /**
     * Support tickets list.
     */
    public function tickets(Request $request): View
    {
        $user = Auth::user();
        $query = Ticket::where('user_id', $user->id);

        if ($request->filled('scope')) {
            $query->where('department_scope', $request->query('scope'));
        }

        $tickets = $query->latest()->paginate(10);
        return view('portal.tickets', compact('user', 'tickets'));
    }

    /**
     * Store new support ticket.
     */
    public function storeTicket(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'subject'          => ['required', 'string', 'max:200'],
            'priority'         => ['required', 'in:low,medium,high,critical'],
            'department_scope' => ['required', 'string', 'in:general,maintenance,cyber_shield,branderp,hardware,billing'],
            'booking_id'       => ['nullable', 'exists:bookings,id'],
            'message'          => ['required', 'string', 'max:5000'],
            'attachments'      => ['nullable', 'array', 'max:3'],
            'attachments.*'    => ['file', 'mimes:jpeg,png,jpg,pdf,webp', 'max:5120'],
        ]);

        $user = Auth::user();

        $ticket = DB::transaction(function () use ($user, $validated, $request) {
            $newTicket = Ticket::create([
                'ticket_number'    => 'TCK-' . date('ymd') . '-' . strtoupper(Str::random(5)),
                'user_id'          => $user->id,
                'booking_id'       => $validated['booking_id'] ?? null,
                'subject'          => strip_tags(trim($validated['subject'])),
                'priority'         => $validated['priority'],
                'department_scope' => $validated['department_scope'],
                'status'           => 'open',
            ]);

            $attachmentPaths = [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $attachmentPaths[] = $file->store('ticket_attachments/' . $newTicket->id, 'public');
                }
            }

            TicketMessage::create([
                'ticket_id'        => $newTicket->id,
                'user_id'          => $user->id,
                'message'          => strip_tags(trim($validated['message'])),
                'attachments_json' => $attachmentPaths,
                'is_internal_note' => false,
            ]);

            return $newTicket;
        });

        SoulyNotificationService::sendToAdmins(
            'ticket',
            'تذكرة دعم فني جديدة 🎫',
            "تذكرة جديدة #{$ticket->ticket_number} من العميل {$user->name}",
            route('admin.tickets.index'),
            'chat-bubble-left-right'
        );

        return redirect()->route('portal.tickets.show', $ticket->id)
                         ->with('citadel_toast', 'تم فتح تذكرة الدعم الفني بنجاح.');
    }

    /**
     * Show ticket conversation strictly hiding internal admin notes from clients.
     */
    public function showTicket(Ticket $ticket): View
    {
        abort_if((int)$ticket->user_id !== (int)Auth::id(), 403, 'غير مصرح لك بعرض هذه التذكرة.');

        $ticket->load([
            'user',
            'messages' => function ($q) {
                // Strictly filter out internal admin notes to prevent data leakage
                $q->where('is_internal_note', false)->with('user')->orderBy('created_at', 'asc');
            }
        ]);

        return view('portal.ticket-detail', compact('ticket'));
    }

    /**
     * Post a client reply to an existing ticket.
     */
    public function replyTicket(Request $request, Ticket $ticket): RedirectResponse
    {
        abort_if((int)$ticket->user_id !== (int)Auth::id(), 403, 'غير مصرح.');

        $validated = $request->validate([
            'message'       => ['required', 'string', 'max:5000'],
            'attachments'   => ['nullable', 'array', 'max:3'],
            'attachments.*' => ['file', 'mimes:jpeg,png,jpg,pdf,webp', 'max:5120'],
        ]);

        $attachmentPaths = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $attachmentPaths[] = $file->store('ticket_attachments/' . $ticket->id, 'public');
            }
        }

        TicketMessage::create([
            'ticket_id'        => $ticket->id,
            'user_id'          => Auth::id(),
            'message'          => strip_tags(trim($validated['message'])),
            'attachments_json' => $attachmentPaths,
            'is_internal_note' => false,
        ]);

        // Reopen ticket if closed or staff-replied
        $ticket->update(['status' => 'client_replied']);

        SoulyNotificationService::sendToAdmins(
            'ticket',
            'رد جديد على تذكرة دعم 💬',
            "رد جديد على التذكرة #{$ticket->ticket_number} من العميل " . Auth::user()->name,
            route('admin.tickets.index'),
            'chat-bubble-oval-left-ellipsis'
        );

        return back()->with('citadel_toast', 'تم إرسال ردك بنجاح.');
    }

    /**
     * BrandERP licenses and power meter hub.
     */
    public function erpStore(): View
    {
        $user = Auth::user();
        $orders = ErpOrder::where('user_id', $user->id)->with('items')->latest()->get();
        return view('portal.erp-store', compact('user', 'orders'));
    }

    /**
     * Hardware purchases, delivery tracking, and warranty hub.
     */
    public function hardware(): View
    {
        $user = Auth::user();
        $orders = HardwareOrder::where('user_id', $user->id)->with('items')->latest()->get();
        return view('portal.hardware', compact('user', 'orders'));
    }

    /**
     * Redirect to dedicated ClientDeviceController.
     */
    public function devices(): RedirectResponse
    {
        return redirect()->action([ClientDeviceController::class, 'index']);
    }
}
