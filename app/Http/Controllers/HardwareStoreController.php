<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\HardwareOrder;
use App\Models\HardwareOrderItem;
use App\Models\HardwareProduct;
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
use Illuminate\Validation\Rule;

class HardwareStoreController extends Controller
{
    use NormalizesEgyptianPhone;

    /**
     * Standard hardware & appliances categories dictionary.
     */
    public static function getCategoriesList(): array
    {
        return [
            'pos_equipment'       => 'أجهزة كاشير ونقاط بيع',
            'storage_ram'         => 'هاردات SSD ورامات فائقة السرعة',
            'networking_security' => 'شبكات وبصمة وتدريع أمني',
            'workstations'        => 'أجهزة ولابتوبات أعمال هندسية',
            'cooling_thermal'     => 'أنظمة التبريد والبطاريات المتقدمة',
            'networking_servers'  => 'السيرفرات ومعدات الشبكات المركزية',
            'cyber_security'      => 'أجهزة التدريع والتحصين السيبراني',
        ];
    }

    /**
     * Display the hardware & cybersecurity appliances store catalog.
     */
    public function index(Request $request)
    {
        $products = HardwareProduct::query()->where('is_active', true)->get();

        $totalCount = $products->count();
        $categories = [
            [
                'id'      => 'all',
                'slug'    => 'all',
                'name'    => 'كافة الأجهزة والمعدات',
                'name_ar' => 'كافة الأجهزة والمعدات',
                'icon'    => '📦',
                'count'   => $totalCount,
            ],
            [
                'id'      => 'pos_equipment',
                'slug'    => 'pos_equipment',
                'name'    => 'أجهزة كاشير ونقاط بيع',
                'name_ar' => 'أجهزة كاشير ونقاط بيع',
                'icon'    => '💳',
                'count'   => $products->where('category', 'pos_equipment')->count(),
            ],
            [
                'id'      => 'storage_ram',
                'slug'    => 'storage_ram',
                'name'    => 'هاردات SSD ورامات فائقة السرعة',
                'name_ar' => 'هاردات SSD ورامات فائقة السرعة',
                'icon'    => '⚡',
                'count'   => $products->where('category', 'storage_ram')->count(),
            ],
            [
                'id'      => 'networking_security',
                'slug'    => 'networking_security',
                'name'    => 'شبكات وبصمة وتدريع أمني',
                'name_ar' => 'شبكات وبصمة وتدريع أمني',
                'icon'    => '🛡️',
                'count'   => $products->where('category', 'networking_security')->count(),
            ],
            [
                'id'      => 'workstations',
                'slug'    => 'workstations',
                'name'    => 'أجهزة ولابتوبات أعمال هندسية',
                'name_ar' => 'أجهزة ولابتوبات أعمال هندسية',
                'icon'    => '💻',
                'count'   => $products->where('category', 'workstations')->count(),
            ],
        ];

        $user = Auth::user();
        $userWalletBalance = 0.00;
        if ($user) {
            $wallet = Wallet::where('user_id', $user->id)->first();
            $userWalletBalance = $wallet ? (float) ($wallet->balance ?? $wallet->cash_balance ?? 0) : 0.00;
        }

        return view('pages.store.hardware', compact('categories', 'products', 'userWalletBalance'));
    }

    /**
     * Process hardware checkout with server-side pricing, inventory decrement inside atomic transaction,
     * promo code caps, and CRM lead capture.
     */
    public function checkout(Request $request)
    {
        $user = Auth::user();
        $customerName = $request->input('customer_name') ?? $request->input('name') ?? ($user?->name);
        $customerPhone = $request->input('customer_phone') ?? $request->input('phone') ?? ($user?->phone);
        $request->merge([
            'customer_name'  => $customerName,
            'customer_phone' => $customerPhone,
            'name'           => $customerName,
            'phone'          => $customerPhone,
        ]);

        $isAuth = Auth::check();
        $validated = $request->validate([
            'items'            => ['required', 'array', 'min:1'],
            'items.*.id'       => ['required', 'integer', 'exists:hardware_products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:50'],
            'customer_name'    => [Rule::requiredIf(!$isAuth), 'nullable', 'string', 'max:150'],
            'customer_phone'   => [Rule::requiredIf(!$isAuth), 'nullable', 'string'],
            'name'             => ['nullable', 'string', 'max:150'],
            'phone'            => ['nullable', 'string'],
            'email'            => ['nullable', 'email', 'max:190'],
            'company'          => ['nullable', 'string', 'max:150'],
            'shipping_address' => ['required', 'string', 'max:255'],
            'city'             => ['nullable', 'string', 'max:100'],
            'promo_code'       => ['nullable', 'string', 'max:50'],
            'payment_method'   => ['required', 'in:cash_on_delivery,wallet,card,bank_transfer,transfer,cod_deposit'],
            'notes'            => ['nullable', 'string', 'max:500'],
        ]);

        $normalizedPhone = null;

        if (!$user) {
            $normalizedPhone = $this->normalizeEgyptianPhone($validated['customer_phone'] ?? $validated['phone'] ?? null);
            if (!$this->isValidEgyptianPhone($normalizedPhone)) {
                return response()->json(['success' => false, 'message' => 'رقم الهاتف المصري غير صحيح.'], 422);
            }
        } else {
            $normalizedPhone = $this->normalizeEgyptianPhone($validated['customer_phone'] ?? $validated['phone'] ?? $user->phone ?? null);
            if (!$normalizedPhone) {
                $normalizedPhone = $user->phone ?? '01000000000';
            }
        }

        try {
            $order = DB::transaction(function () use ($validated, $user, $normalizedPhone) {
                // 1. Resolve or provision client safely
                if (!$user) {
                    $user = User::where('phone', $normalizedPhone)->first();
                    if (!$user) {
                        $user = User::create([
                            'name'      => strip_tags(trim($validated['customer_name'] ?? $validated['name'] ?? 'عميل سولي')),
                            'phone'     => $normalizedPhone,
                            'email'     => !empty($validated['email']) ? strtolower(trim($validated['email'])) : ('client_' . $normalizedPhone . '@soulyeg.online'),
                            'password'  => Hash::make(Str::random(16)),
                            'role'      => 'client',
                            'is_active' => true,
                        ]);

                        Wallet::create([
                            'user_id'       => $user->id,
                            'balance'       => 0.00,
                            'cash_balance'  => 0.00,
                            'bonus_balance' => 0.00,
                            'currency'      => 'EGP',
                            'is_locked'     => false,
                        ]);
                    } elseif ($validated['payment_method'] === 'wallet') {
                        throw new \DomainException('هذا الهاتف مسجل مسبقاً، يرجى تسجيل الدخول أولاً للدفع من رصيد المحفظة.');
                    }
                }

                // 2. Fetch and lock products for stock validation and server-side pricing
                $productIds = collect($validated['items'])->pluck('id')->all();
                $products = HardwareProduct::whereIn('id', $productIds)
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                $subtotal = 0.00;
                $lineItemsToCreate = [];

                foreach ($validated['items'] as $itemData) {
                    $product = $products->get($itemData['id']);
                    $qty = (int)$itemData['quantity'];

                    if (!$product || !$product->is_active) {
                        throw new \DomainException('أحد المنتجات المختارة غير متاح حالياً.');
                    }

                    // Check stock column flexibly (supports stock_qty or stock_quantity)
                    $currentStock = $product->stock_qty ?? $product->stock_quantity ?? 999;
                    if ($currentStock < $qty) {
                        throw new \DomainException("الكمية المطلوبة من ({$product->name_ar}) غير متوفرة في المخزن (المتاح: {$currentStock}).");
                    }

                    $unitPrice = (float)$product->price;
                    $itemTotal = round($unitPrice * $qty, 2);
                    $subtotal += $itemTotal;

                    // Decrement stock safely on whichever column is present
                    if (isset($product->stock_qty)) {
                        $product->decrement('stock_qty', $qty);
                    } elseif (isset($product->stock_quantity)) {
                        $product->decrement('stock_quantity', $qty);
                    }

                    $lineItemsToCreate[] = [
                        'product_id'   => $product->id,
                        'product_sku'  => $product->sku ?? ('SKU-' . $product->id),
                        'product_name' => $product->name_ar,
                        'name_ar'      => $product->name_ar,
                        'item_name'    => $product->name_ar,
                        'unit_price'   => $unitPrice,
                        'quantity'     => $qty,
                        'subtotal'     => $itemTotal,
                        'total_price'  => $itemTotal,
                    ];
                }

                // 3. Process promotional discount
                $discount = 0.00;
                if (!empty($validated['promo_code'])) {
                    $promo = PromoCode::where('code', strtoupper(trim($validated['promo_code'])))
                        ->where('is_active', true)
                        ->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>=', now()))
                        ->first();

                    if ($promo) {
                        if (!$promo->min_order_amount || $subtotal >= (float)$promo->min_order_amount) {
                            if ($promo->type === 'percentage') {
                                $discount = round($subtotal * ((float)$promo->value / 100), 2);
                                if ($promo->max_discount_cap) {
                                    $discount = min($discount, (float)$promo->max_discount_cap);
                                }
                            } else {
                                $discount = min($subtotal, (float)$promo->value);
                            }
                        }
                    }
                }

                $shippingCost = 0.00;
                $finalPayable = max(0.00, round($subtotal - $discount + $shippingCost, 2));

                $orderNumber = 'HW-' . date('ymd') . '-' . strtoupper(Str::random(5));
                $isWallet = ($validated['payment_method'] === 'wallet');
                $orderStatus = $isWallet ? 'confirmed_preparing' : 'pending_review';
                $paymentStatus = $isWallet ? 'paid' : 'pending';

                // 4. Handle wallet deduction if selected
                if ($isWallet) {
                    $wallet = Wallet::where('user_id', $user->id)
                        ->lockForUpdate()
                        ->first();

                    $walletBalance = (float)($wallet->balance ?? $wallet->cash_balance ?? 0);
                    if (!$wallet || $wallet->is_locked || $walletBalance < $finalPayable) {
                        throw new \DomainException('رصيدك في المحفظة غير كافٍ لسداد قيمة طلب المعدات.');
                    }

                    $wallet->decrement('balance', $finalPayable);
                    if (isset($wallet->cash_balance)) {
                        $wallet->decrement('cash_balance', $finalPayable);
                    }

                    WalletTransaction::create([
                        'wallet_id'       => $wallet->id,
                        'user_id'         => $user->id,
                        'type'            => 'purchase',
                        'amount'          => $finalPayable,
                        'reference_code'  => 'TXN-' . strtoupper(Str::random(12)),
                        'payment_method'  => 'wallet_balance',
                        'status'          => 'approved',
                        'admin_notes'     => "سداد قيمة طلب أجهزة وهاردوير ({$orderNumber})",
                    ]);
                }

                // 5. Create Hardware Order
                $customerName = strip_tags(trim($validated['customer_name'] ?? $validated['name'] ?? ($user->name ?? 'عميل سولي')));
                $shippingAddress = strip_tags(trim($validated['shipping_address'] ?? ''));

                $hardwareOrder = HardwareOrder::create([
                    'order_number'     => $orderNumber,
                    'user_id'          => $user->id,
                    'customer_name'    => $customerName,
                    'customer_phone'   => $normalizedPhone,
                    'shipping_address' => $shippingAddress,
                    'items_json'       => $lineItemsToCreate,
                    'total_amount'     => $finalPayable,
                    'payment_method'   => $validated['payment_method'],
                    'payment_status'   => $paymentStatus,
                    'status'           => $orderStatus,
                    'notes'            => !empty($validated['notes']) ? strip_tags(trim($validated['notes'])) : null,
                ]);

                // Create Order Line Items
                foreach ($lineItemsToCreate as $lineItem) {
                    HardwareOrderItem::create([
                        'order_id'     => $hardwareOrder->id,
                        'product_id'   => $lineItem['product_id'],
                        'product_sku'  => $lineItem['product_sku'],
                        'product_name' => $lineItem['product_name'],
                        'unit_price'   => $lineItem['unit_price'],
                        'quantity'     => $lineItem['quantity'],
                        'subtotal'     => $lineItem['subtotal'],
                    ]);
                }

                // 6. Sync CRM Lead
                try {
                    Lead::updateOrCreate(
                        ['phone' => $normalizedPhone],
                        [
                            'name'            => $customerName,
                            'email'           => $user->email,
                            'company'         => !empty($validated['company']) ? strip_tags(trim($validated['company'])) : null,
                            'status'          => $paymentStatus === 'paid' ? 'won' : 'contacted',
                            'user_id'         => $user->id,
                            'source'          => 'hardware_store',
                            'last_contact_at' => now(),
                        ]
                    );
                } catch (\Throwable $leadEx) {}

                return $hardwareOrder;
            });

            return response()->json([
                'success'      => true,
                'message'      => 'تم تسجيل طلب الشراء بنجاح وجارٍ تجهيز الشحنة.',
                'order_number' => $order->order_number,
                'status'       => $order->status,
                'redirect'     => route('portal.index'),
                'redirect_url' => route('portal.index'),
            ]);

        } catch (\DomainException $de) {
            return response()->json(['success' => false, 'message' => $de->getMessage()], 422);
        } catch (\Throwable $e) {
            Log::error('Hardware Checkout Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'حدث خطأ أثناء معالجة الطلب، يرجى إعادة المحاولة.'], 500);
        }
    }
}
