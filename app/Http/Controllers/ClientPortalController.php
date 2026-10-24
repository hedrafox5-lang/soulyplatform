<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Lead;
use App\Models\Service;
use App\Models\ServicePackage;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ClientPortalController extends Controller
{
    /**
     * Client Portal Main Overview Dashboard.
     */
    public function index(): View
    {
        $user = Auth::user();
        $wallet = $user->walletOrCreate();

        $activeBookingsCount = Booking::where('user_id', $user->id)
            ->whereIn('status', ['new', 'confirmed', 'in_progress'])
            ->count();

        $completedBookingsCount = Booking::where('user_id', $user->id)
            ->where('status', 'completed')
            ->count();

        $openTicketsCount = Ticket::where('user_id', $user->id)
            ->whereIn('status', ['open', 'staff_replied', 'client_replied'])
            ->count();

        $recentBookings = Booking::with(['service', 'package'])
            ->where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        $recentTickets = Ticket::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        $recentTransactions = WalletTransaction::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        return view('portal.index', compact(
            'user',
            'wallet',
            'activeBookingsCount',
            'completedBookingsCount',
            'openTicketsCount',
            'recentBookings',
            'recentTickets',
            'recentTransactions'
        ));
    }

    /**
     * Client Wallet & Transactions Ledger.
     */
    public function wallet(): View
    {
        $user = Auth::user();
        $wallet = $user->walletOrCreate();

        $transactions = WalletTransaction::where('user_id', $user->id)
            ->latest()
            ->paginate(15);

        return view('portal.wallet', compact('user', 'wallet', 'transactions'));
    }

    /**
     * Submit Top-up / Deposit request to Souly Wallet.
     */
    public function deposit(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $wallet = $user->walletOrCreate();

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:50', 'max:1000000'],
            'payment_method' => ['required', 'string', 'in:vodafone_cash,instapay,bank_transfer'],
            'sender_number_or_account' => ['required', 'string', 'max:100'],
            'receipt' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ], [
            'amount.required' => 'يرجى إدخال مبلغ الشحن المطلوب.',
            'amount.min' => 'الحد الأدنى لشحن المحفظة هو 50 ج.م.',
            'payment_method.required' => 'يرجى اختيار وسيلة التحويل.',
            'sender_number_or_account.required' => 'يرجى إدخال رقم المحفظة أو الحساب المحول منه.',
            'receipt.mimes' => 'صيغة الإيصال يجب أن تكون صورة (JPG, PNG) أو ملف PDF.',
            'receipt.max' => 'حجم ملف الإيصال يجب ألا يتجاوز 5 ميجابايت.',
        ]);

        $receiptPath = null;
        if ($request->hasFile('receipt')) {
            $receiptPath = $request->file('receipt')->store('receipts', 'public');
        }

        $referenceCode = 'DEP-' . strtoupper(Str::random(8));

        WalletTransaction::create([
            'wallet_id' => $wallet->id,
            'user_id' => $user->id,
            'reference_code' => $referenceCode,
            'type' => 'deposit',
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'sender_number_or_account' => $validated['sender_number_or_account'],
            'receipt_path' => $receiptPath,
            'status' => 'pending',
            'admin_notes' => 'طلب شحن محفظة بانتظار مراجعة الإدارة وتأكيد التحويل.',
        ]);

        return redirect()->route('portal.wallet')->with('citadel_toast', [
            'type' => 'success',
            'message' => "تم تسجيل طلب شحن المحفظة بنجاح برقم مرجعي ({$referenceCode}). سيتم التأكيد وإضافة الرصيد فور المراجعة."
        ]);
    }

    /**
     * Client Bookings List & Booking Engine.
     */
    public function bookings(Request $request): View
    {
        $user = Auth::user();
        $query = Booking::with(['service', 'package'])->where('user_id', $user->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $bookings = $query->latest()->paginate(10);

        // Pre-load active services and packages for the booking creator modal
        $services = Service::with(['packages' => fn ($q) => $q->where('is_active', true)->orderBy('display_order')])
            ->where('is_active', true)
            ->orderBy('display_order')
            ->get();

        $wallet = $user->walletOrCreate();

        return view('portal.bookings', compact('user', 'wallet', 'bookings', 'services'));
    }

    /**
     * Process Instant Service Booking.
     */
    public function storeBooking(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $wallet = $user->walletOrCreate();

        $validated = $request->validate([
            'service_id' => ['required', 'exists:services,id'],
            'service_package_id' => ['nullable', 'exists:service_packages,id'],
            'quantity_or_hours' => ['required', 'integer', 'min:1', 'max:1000'],
            'payment_method' => ['required', 'string', 'in:wallet,direct_transfer,on_site'],
            'scheduled_at' => ['nullable', 'date'],
            'client_requirements' => ['nullable', 'string', 'max:3000'],
        ], [
            'service_id.required' => 'يرجى اختيار الخدمة المطلوبة.',
            'quantity_or_hours.required' => 'يرجى تحديد عدد الساعات أو الوحدات.',
            'payment_method.required' => 'يرجى تحديد طريقة الدفع.',
        ]);

        $service = Service::findOrFail($validated['service_id']);
        $package = null;
        if (!empty($validated['service_package_id'])) {
            $package = ServicePackage::where('service_id', $service->id)
                ->where('id', $validated['service_package_id'])
                ->firstOrFail();
        }

        // Calculate total price
        $units = (int) $validated['quantity_or_hours'];
        if ($package) {
            if ($package->billing_type === 'hourly' || $package->min_units > 1) {
                $totalAmount = (float) $package->price * $units;
            } else {
                $totalAmount = (float) $package->price;
            }
        } else {
            // Default hourly fallback or baseline
            $totalAmount = 500.00 * $units;
        }

        $bookingNumber = 'BKG-' . strtoupper(Str::random(8));

        // Payment Handling
        if ($validated['payment_method'] === 'wallet') {
            // Atomic DB Transaction with row lock
            try {
                DB::transaction(function () use ($user, $service, $package, $units, $totalAmount, $bookingNumber, $validated) {
                    $lockedWallet = Wallet::where('user_id', $user->id)->lockForUpdate()->firstOrFail();

                    if ((float) $lockedWallet->balance < (float) $totalAmount) {
                        throw new \Exception("insufficient_balance");
                    }

                    // 1. Deduct balance
                    $lockedWallet->decrement('balance', $totalAmount);

                    // 2. Create Wallet Purchase Transaction
                    WalletTransaction::create([
                        'wallet_id' => $lockedWallet->id,
                        'user_id' => $user->id,
                        'reference_code' => 'PUR-' . strtoupper(Str::random(8)),
                        'type' => 'purchase',
                        'amount' => $totalAmount,
                        'payment_method' => 'wallet_balance',
                        'sender_number_or_account' => "محفظة سولي: {$user->email}",
                        'status' => 'approved',
                        'admin_notes' => "خصم تلقائي فوري لحجز الخدمة: {$bookingNumber} ({$service->name})",
                    ]);

                    // 3. Create Booking with confirmed & paid status
                    Booking::create([
                        'booking_number' => $bookingNumber,
                        'user_id' => $user->id,
                        'service_id' => $service->id,
                        'service_package_id' => $package?->id,
                        'quantity_or_hours' => $units,
                        'total_amount' => $totalAmount,
                        'payment_method' => 'wallet',
                        'payment_status' => 'paid',
                        'status' => 'confirmed',
                        'scheduled_at' => $validated['scheduled_at'] ?? now()->addDay(),
                        'client_requirements' => $validated['client_requirements'] ?? null,
                    ]);
                });

                return redirect()->route('portal.bookings')->with('citadel_toast', [
                    'type' => 'success',
                    'message' => "تم تأكيد الحجز الفوري بنجاح برقم ({$bookingNumber}) وخُصم المبلغ ({$totalAmount} ج.م) من محفظتك!"
                ]);

            } catch (\Exception $e) {
                if ($e->getMessage() === 'insufficient_balance') {
                    $currentBalance = number_format($wallet->balance, 2);
                    $needed = number_format($totalAmount - $wallet->balance, 2);
                    return back()->withInput()->withErrors([
                        'payment_method' => "رصيد محفظتك غير كافٍ لإتمام الحجز الفوري. الرصيد الحالي: {$currentBalance} ج.م، والمبلغ المطلوب: {$totalAmount} ج.م (المتبقي للشحن: {$needed} ج.م).",
                    ]);
                }
                throw $e;
            }
        }

        // Direct Transfer or On-Site Payment
        Booking::create([
            'booking_number' => $bookingNumber,
            'user_id' => $user->id,
            'service_id' => $service->id,
            'service_package_id' => $package?->id,
            'quantity_or_hours' => $units,
            'total_amount' => $totalAmount,
            'payment_method' => $validated['payment_method'],
            'payment_status' => 'pending_verification',
            'status' => 'new',
            'scheduled_at' => $validated['scheduled_at'] ?? now()->addDay(),
            'client_requirements' => $validated['client_requirements'] ?? null,
        ]);

        // Capture/Update Lead for staff follow-up
        Lead::create([
            'name' => $user->name,
            'phone' => $user->phone ?? '01000000000',
            'email' => $user->email,
            'company_name' => $user->company_name,
            'interested_service_id' => $service->id,
            'interested_package_id' => $package?->id,
            'source' => 'website_booking',
            'status' => 'new',
            'notes' => "طلب حجز جديد برقم {$bookingNumber} بطريقة دفع: {$validated['payment_method']}. بانتظار التواصل والتأكيد.",
        ]);

        return redirect()->route('portal.bookings')->with('citadel_toast', [
            'type' => 'success',
            'message' => "تم تسجيل طلب الحجز بنجاح برقم ({$bookingNumber}). سيتواصل معك مهندس الحسابات فوراً لتأكيد الموعد وبيانات التنفيذ."
        ]);
    }

    /**
     * Client Support Tickets List.
     */
    public function tickets(): View
    {
        $user = Auth::user();
        $tickets = Ticket::with(['booking.service'])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(12);

        $userBookings = Booking::with('service')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return view('portal.tickets', compact('user', 'tickets', 'userBookings'));
    }

    /**
     * Open New Support Ticket.
     */
    public function storeTicket(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'department' => ['required', 'string', 'in:erp,ai_agents,cybersecurity,maintenance,consulting,academy,billing'],
            'priority' => ['required', 'string', 'in:low,medium,high,urgent'],
            'booking_id' => ['nullable', 'exists:bookings,id'],
            'message' => ['required', 'string', 'max:5000'],
        ], [
            'subject.required' => 'يرجى كتابة عنوان التذكرة.',
            'department.required' => 'يرجى تحديد القسم المختص.',
            'priority.required' => 'يرجى تحديد درجة الأولوية.',
            'message.required' => 'يرجى كتابة تفاصيل الاستفسار أو المشكلة.',
        ]);

        // Security check for booking ownership
        if (!empty($validated['booking_id'])) {
            $booking = Booking::where('id', $validated['booking_id'])->where('user_id', $user->id)->first();
            if (!$booking) {
                return back()->withErrors(['booking_id' => 'رقم الحجز غير صالح أو لا يتبع حسابك.']);
            }
        }

        $ticketNumber = 'TCK-' . strtoupper(Str::random(8));

        $ticket = Ticket::create([
            'ticket_number' => $ticketNumber,
            'user_id' => $user->id,
            'booking_id' => $validated['booking_id'] ?? null,
            'subject' => $validated['subject'],
            'department' => $validated['department'],
            'priority' => $validated['priority'],
            'status' => 'open',
        ]);

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'message' => $validated['message'],
            'is_internal_note' => false,
        ]);

        return redirect()->route('portal.tickets.show', $ticket)->with('citadel_toast', [
            'type' => 'success',
            'message' => "تم فتح التذكرة بنجاح برقم ({$ticketNumber}). سيتولى مهندس الدعم الرد عليك في أقرب وقت."
        ]);
    }

    /**
     * View Single Ticket & Live Message Thread.
     */
    public function showTicket(Ticket $ticket): View
    {
        $user = Auth::user();

        // Authorization check
        if ($ticket->user_id !== $user->id && !in_array($user->role, ['admin', 'staff'])) {
            abort(403, 'غير مصرح لك بمشاهدة هذه التذكرة.');
        }

        $ticket->load([
            'messages.user',
            'booking.service',
            'booking.package',
            'assignedStaff',
        ]);

        return view('portal.ticket-detail', compact('user', 'ticket'));
    }

    /**
     * Post a Reply in Ticket Thread.
     */
    public function replyTicket(Request $request, Ticket $ticket): RedirectResponse
    {
        $user = Auth::user();

        if ($ticket->user_id !== $user->id && !in_array($user->role, ['admin', 'staff'])) {
            abort(403, 'غير مصرح لك بالرد على هذه التذكرة.');
        }

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:5000'],
        ], [
            'message.required' => 'لا يمكنك إرسال رد فارغ.',
        ]);

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'message' => $validated['message'],
            'is_internal_note' => false,
        ]);

        // Update ticket status
        if ($user->role === 'client') {
            $ticket->status = 'client_replied';
        } else {
            $ticket->status = 'staff_replied';
        }
        $ticket->save();

        return back()->with('citadel_toast', [
            'type' => 'success',
            'message' => 'تم إرسال ردك بنجاح.'
        ]);
    }
}
