<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ErpCatalogItem;
use App\Models\ErpOrder;
use App\Models\ErpOrderItem;
use App\Models\Lead;
use App\Models\PromoCode;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Traits\NormalizesEgyptianPhone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ErpStoreController extends Controller
{
    use NormalizesEgyptianPhone;

    /**
     * Display the BrandERP interactive builder store catalog.
     */
    public function index(Request $request)
    {
        $catalogItems = ErpCatalogItem::where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->get();

        $departments = $catalogItems->pluck('department_ar')->unique()->filter()->values();

        return view('pages.erp-store', compact('catalogItems', 'departments'));
    }

    /**
     * Start a safe free trial strictly once per user/account.
     */
    public function startTrial(Request $request)
    {
        $request->validate([
            'modules' => ['required', 'array', 'min:1'],
            'modules.*' => ['integer', 'exists:erp_catalog_items,id'],
        ]);

        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'يرجى تسجيل الدخول أولاً لتفعيل التجربة المجانية.',
            ], 401);
        }

        // Prevent repeated trial abuse
        $hasExistingTrial = ErpOrder::where('user_id', $user->id)
            ->where('is_trial', true)
            ->exists();

        if ($hasExistingTrial) {
            return response()->json([
                'success' => false,
                'message' => 'لقد استفدت بالفعل من التجربة المجانية المتاحة لحسابك مسبقاً.',
            ], 422);
        }

        try {
            $order = DB::transaction(function () use ($user, $request) {
                $orderNumber = 'ERP-TRL-' . date('ymd') . '-' . strtoupper(Str::random(4));
                $licenseKey  = 'SOULY-TRL-' . strtoupper(Str::random(4)) . '-' . strtoupper(Str::random(4)) . '-' . strtoupper(Str::random(4));

                $erpOrder = ErpOrder::create([
                    'order_number'    => $orderNumber,
                    'user_id'         => $user->id,
                    'license_key'     => $licenseKey,
                    'status'          => 'active',
                    'payment_status'  => 'trial',
                    'payment_method'  => 'free_trial',
                    'subtotal_amount' => 0.00,
                    'discount_amount' => 0.00,
                    'total_amount'    => 0.00,
                    'is_trial'        => true,
                    'trial_ends_at'   => now()->addDays(14),
                ]);

                $selectedItems = ErpCatalogItem::whereIn('id', $request->modules)->get();
                foreach ($selectedItems as $item) {
                    ErpOrderItem::create([
                        'erp_order_id'        => $erpOrder->id,
                        'erp_catalog_item_id' => $item->id,
                        'name_ar'             => $item->name_ar,
                        'price'               => 0.00,
                    ]);
                    $item->increment('activations_count');
                }

                return $erpOrder;
            });

            return response()->json([
                'success'     => true,
                'message'     => 'تم تفعيل نسختك التجريبية بنجاح لمدة 14 يوماً.',
                'license_key' => $order->license_key,
                'order_id'    => $order->id,
            ]);

        } catch (\Throwable $e) {
            Log::error('ERP Trial Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'حدث خطأ أثناء تفعيل النسخة التجريبية.'], 500);
        }
    }

    /**
     * Secure checkout: recalculate prices authoritatively from database, apply conditional bundle discounts,
     * lock wallet row atomically, and issue instant perpetual license keys.
     */
    public function checkout(Request $request)
    {
        $validated = $request->validate([
            'modules'        => ['required', 'array', 'min:1'],
            'modules.*'      => ['integer', 'exists:erp_catalog_items,id'],
            'name'           => ['required_without:user', 'nullable', 'string', 'max:150'],
            'phone'          => ['required_without:user', 'nullable', 'string'],
            'email'          => ['nullable', 'email', 'max:190'],
            'company'        => ['nullable', 'string', 'max:150'],
            'promo_code'     => ['nullable', 'string', 'max:50'],
            'payment_method' => ['required', 'in:wallet,card,bank_transfer'],
        ]);

        $user = Auth::user();
        $normalizedPhone = null;

        if (!$user) {
            $normalizedPhone = $this->normalizeEgyptianPhone($validated['phone'] ?? null);
            if (!$this->isValidEgyptianPhone($normalizedPhone)) {
                return response()->json(['success' => false, 'message' => 'رقم هاتف مصري غير صحيح.'], 422);
            }
        } else {
            $normalizedPhone = $user->phone;
        }

        try {
            $erpOrder = DB::transaction(function () use ($validated, $user, $normalizedPhone) {
                // 1. Resolve or provision client account safely
                if (!$user) {
                    $user = User::where('phone', $normalizedPhone)->first();
                    if (!$user) {
                        $user = User::create([
                            'name'      => strip_tags(trim($validated['name'])),
                            'phone'     => $normalizedPhone,
                            'email'     => !empty($validated['email']) ? strtolower(trim($validated['email'])) : null,
                            'password'  => Hash::make(Str::random(16)),
                            'role'      => 'client',
                            'is_active' => true,
                        ]);

                        Wallet::create([
                            'user_id'       => $user->id,
                            'cash_balance'  => 0.00,
                            'bonus_balance' => 0.00,
                            'currency'      => 'EGP',
                            'is_locked'     => false,
                        ]);
                    } elseif ($validated['payment_method'] === 'wallet') {
                        throw new \DomainException('هذا الحساب مسجل مسبقاً، يرجى تسجيل الدخول أولاً للدفع من رصيد المحفظة.');
                    }
                }

                // 2. Authoritative database pricing calculation (Never trust client payload)
                $catalogItems = ErpCatalogItem::whereIn('id', $validated['modules'])->get();
                $subtotal = 0.00;
                foreach ($catalogItems as $item) {
                    $subtotal += (float)$item->price;
                }

                // 3. Apply multi-module bundle discount (15% for 4+ modules)
                $bundleDiscount = 0.00;
                if ($catalogItems->count() >= 4) {
                    $bundleDiscount = round($subtotal * 0.15, 2);
                }

                $netAmount = max(0.00, $subtotal - $bundleDiscount);

                // 4. Validate and apply promotional code if provided
                $promoDiscount = 0.00;
                if (!empty($validated['promo_code'])) {
                    $promo = PromoCode::where('code', strtoupper(trim($validated['promo_code'])))
                        ->where('is_active', true)
                        ->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>=', now()))
                        ->first();

                    if ($promo) {
                        if (!$promo->min_order_amount || $netAmount >= (float)$promo->min_order_amount) {
                            if ($promo->type === 'percentage') {
                                $promoDiscount = round($netAmount * ((float)$promo->value / 100), 2);
                                if ($promo->max_discount_cap) {
                                    $promoDiscount = min($promoDiscount, (float)$promo->max_discount_cap);
                                }
                            } else {
                                $promoDiscount = min($netAmount, (float)$promo->value);
                            }
                        }
                    }
                }

                $totalDiscount = $bundleDiscount + $promoDiscount;
                $finalPayable  = max(0.00, round($subtotal - $totalDiscount, 2));

                $paymentStatus = 'unpaid';
                $orderNumber   = 'ERP-' . date('ymd') . '-' . strtoupper(Str::random(5));
                $licenseKey    = 'SOULY-ERP-' . strtoupper(Str::random(4)) . '-' . strtoupper(Str::random(4)) . '-' . strtoupper(Str::random(4));

                // 5. Handle atomic wallet deduction
                if ($validated['payment_method'] === 'wallet') {
                    $wallet = Wallet::where('user_id', $user->id)
                        ->lockForUpdate()
                        ->first();

                    if (!$wallet || $wallet->is_locked || (float)$wallet->cash_balance < $finalPayable) {
                        throw new \DomainException('رصيدك النقدي في المحفظة غير كافٍ لإتمام عملية الشراء.');
                    }

                    $wallet->decrement('cash_balance', $finalPayable);

                    WalletTransaction::create([
                        'wallet_id'   => $wallet->id,
                        'user_id'     => $user->id,
                        'type'        => 'payment',
                        'amount'      => $finalPayable,
                        'currency'    => 'EGP',
                        'status'      => 'completed',
                        'reference'   => $orderNumber,
                        'description' => "شراء تراخيص موديولات BrandERP (" . $catalogItems->count() . " موديول)",
                    ]);

                    $paymentStatus = 'paid';
                }

                // 6. Create ERP order & line items
                $order = ErpOrder::create([
                    'order_number'    => $orderNumber,
                    'user_id'         => $user->id,
                    'license_key'     => $licenseKey,
                    'status'          => $paymentStatus === 'paid' ? 'active' : 'pending',
                    'payment_status'  => $paymentStatus,
                    'payment_method'  => $validated['payment_method'],
                    'subtotal_amount' => $subtotal,
                    'discount_amount' => $totalDiscount,
                    'total_amount'    => $finalPayable,
                    'company_name'    => !empty($validated['company']) ? strip_tags(trim($validated['company'])) : null,
                    'is_trial'        => false,
                ]);

                foreach ($catalogItems as $item) {
                    ErpOrderItem::create([
                        'erp_order_id'        => $order->id,
                        'erp_catalog_item_id' => $item->id,
                        'name_ar'             => $item->name_ar,
                        'price'               => $item->price,
                    ]);
                    $item->increment('activations_count');
                }

                // 7. Track in CRM Leads
                Lead::updateOrCreate(
                    ['phone' => $normalizedPhone],
                    [
                        'name'            => $user->name,
                        'email'           => $user->email,
                        'company'         => !empty($validated['company']) ? strip_tags(trim($validated['company'])) : null,
                        'status'          => $paymentStatus === 'paid' ? 'won' : 'qualified',
                        'user_id'         => $user->id,
                        'source'          => 'erp_store',
                        'last_contact_at' => now(),
                    ]
                );

                return $order;
            });

            return response()->json([
                'success'      => true,
                'message'      => 'تم تسجيل طلب تراخيص BrandERP بنجاح.',
                'order_number' => $erpOrder->order_number,
                'license_key'  => $erpOrder->payment_status === 'paid' ? $erpOrder->license_key : null,
                'redirect'     => route('portal.dashboard'),
            ]);

        } catch (\DomainException $de) {
            return response()->json(['success' => false, 'message' => $de->getMessage()], 422);
        } catch (\Throwable $e) {
            Log::error('ERP Checkout Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'حدث خطأ غير متوقع أثناء معالجة الطلب.'], 500);
        }
    }

    /**
     * Submit custom ERP development / customization request.
     */
    public function submitCustomRequest(Request $request)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:150'],
            'phone'       => ['required', 'string'],
            'email'       => ['nullable', 'email', 'max:190'],
            'company'     => ['nullable', 'string', 'max:150'],
            'scope_title' => ['required', 'string', 'max:200'],
            'details'     => ['required', 'string', 'max:2000'],
            'budget'      => ['nullable', 'numeric', 'min:0'],
        ]);

        $normalizedPhone = $this->normalizeEgyptianPhone($validated['phone']);
        if (!$this->isValidEgyptianPhone($normalizedPhone)) {
            return response()->json(['success' => false, 'message' => 'رقم الهاتف المصري غير صحيح.'], 422);
        }

        Lead::create([
            'name'            => strip_tags(trim($validated['name'])),
            'phone'           => $normalizedPhone,
            'email'           => !empty($validated['email']) ? strtolower(trim($validated['email'])) : null,
            'company'         => !empty($validated['company']) ? strip_tags(trim($validated['company'])) : null,
            'source'          => 'erp_custom_request',
            'status'          => 'new',
            'notes'           => "طلب برمجة خاصة: {$validated['scope_title']}\nالتفاصيل: {$validated['details']}\nالميزانية المقترحة: " . ($validated['budget'] ?? 'غير محدد'),
            'last_contact_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم استلام طلب البرمجة الخاصة بنجاح، سيتواصل معك مهندس النظم خلال 24 ساعة.',
        ]);
    }

    /**
     * Download authorized ERP module or license file (Strict IDOR Protected).
     */
    public function downloadItem(Request $request, int $orderId, int $itemId)
    {
        $user = Auth::user();
        if (!$user) {
            abort(401, 'Unauthenticated.');
        }

        // Strict ownership check preventing IDOR
        $order = ErpOrder::where('id', $orderId)
            ->where('user_id', $user->id)
            ->whereIn('payment_status', ['paid', 'trial'])
            ->firstOrFail();

        $orderItem = ErpOrderItem::where('erp_order_id', $order->id)
            ->where('id', $itemId)
            ->firstOrFail();

        return response()->json([
            'success'     => true,
            'order_id'    => $order->id,
            'item_name'   => $orderItem->name_ar,
            'license_key' => $order->license_key,
            'download_at' => now()->toDateTimeString(),
        ]);
    }
}
