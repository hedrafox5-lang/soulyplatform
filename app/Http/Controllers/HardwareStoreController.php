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

class HardwareStoreController extends Controller
{
    use NormalizesEgyptianPhone;

    /**
     * Standard hardware & appliances categories dictionary.
     */
    public static function getCategoriesList(): array
    {
        return [
            'pos_equipment'       => 'نقاط البيع وأنظمة الكاشير POS',
            'storage_ram'         => 'وسائط التخزين والذاكرة العشوائية RAM',
            'cooling_thermal'     => 'أنظمة التبريد والبطاريات المتقدمة',
            'networking_servers'  => 'السيرفرات ومعدات الشبكات المركزية',
            'networking_security' => 'أجهزة جدران الحماية وأمن الشبكات',
            'cyber_security'      => 'أجهزة التدريع والتحصين السيبراني',
        ];
    }

    /**
     * Display the hardware & cybersecurity appliances store catalog.
     */
    public function index(Request $request)
    {
        // 1. Generate standard categories collection (supports both object and array access)
        $categoriesList = self::getCategoriesList();
        $categories = collect($categoriesList)->map(function ($name, $slug) {
            return (object)[
                'id'          => $slug,
                'slug'        => $slug,
                'name'        => $name,
                'name_ar'     => $name,
                'name_en'     => $slug,
                'description' => $name,
                'is_active'   => true,
            ];
        })->values();

        // 2. Query active products
        $query = HardwareProduct::query()->where('is_active', true);

        // Filter by category slug if provided
        if ($request->filled('category') && $request->query('category') !== 'all') {
            $selectedCategory = trim($request->query('category'));
            $query->where('category', $selectedCategory);
        }

        // Search in product names, description, or model
        if ($request->filled('search')) {
            $term = trim($request->query('search'));
            $query->where(function ($q) use ($term) {
                $q->where('name_ar', 'like', "%{$term}%")
                  ->orWhere('name_en', 'like', "%{$term}%")
                  ->orWhere('description_ar', 'like', "%{$term}%")
                  ->orWhere('model_number', 'like', "%{$term}%");
            });
        }

        // Sorting & pagination
        $products = $query->latest('id')->paginate(12);

        return view('pages.hardware-store', compact('categories', 'products'));
    }

    /**
     * Process hardware checkout with server-side pricing, inventory decrement inside atomic transaction,
     * promo code caps, and CRM lead capture.
     */
    public function checkout(Request $request)
    {
        $validated = $request->validate([
            'items'            => ['required', 'array', 'min:1'],
            'items.*.id'       => ['required', 'integer', 'exists:hardware_products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:50'],
            'name'             => ['required_without:user', 'nullable', 'string', 'max:150'],
            'phone'            => ['required_without:user', 'nullable', 'string'],
            'email'            => ['nullable', 'email', 'max:190'],
            'company'          => ['nullable', 'string', 'max:150'],
            'shipping_address' => ['required', 'string', 'max:255'],
            'city'             => ['nullable', 'string', 'max:100'],
            'promo_code'       => ['nullable', 'string', 'max:50'],
            'payment_method'   => ['required', 'in:cash_on_delivery,wallet,card,bank_transfer'],
            'notes'            => ['nullable', 'string', 'max:500'],
        ]);

        $user = Auth::user();
        $normalizedPhone = null;

        if (!$user) {
            $normalizedPhone = $this->normalizeEgyptianPhone($validated['phone'] ?? null);
            if (!$this->isValidEgyptianPhone($normalizedPhone)) {
                return response()->json(['success' => false, 'message' => 'رقم الهاتف المصري غير صحيح.'], 422);
            }
        } else {
            $normalizedPhone = $user->phone;
        }

        try {
            $order = DB::transaction(function () use ($validated, $user, $normalizedPhone) {
                // 1. Resolve or provision client safely
                if (!$user) {
                    $user = User::where('phone', $normalizedPhone)->first();
                    if (!$user) {
                        $user = User::create([
                            'name'      => strip_tags(trim($validated['name'])),
                            'phone'     => $normalizedPhone,
                            'email'     => !empty($validated['email']) ? strtolower(trim($validated['email'])) : ('client_' . $normalizedPhone . '@soulyeg.online'),
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
                        'hardware_product_id' => $product->id,
                        'product_id'          => $product->id,
                        'name_ar'             => $product->name_ar,
                        'item_name'           => $product->name_ar,
                        'unit_price'          => $unitPrice,
                        'quantity'            => $qty,
                        'total_price'         => $itemTotal,
                        'subtotal'            => $itemTotal,
                        'warranty_months'     => $product->warranty_months ?? 12,
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

                // Shipping cost
                $shippingCost = 50.00;
                $finalPayable = max(0.00, round($subtotal - $discount + $shippingCost, 2));

                $orderNumber = 'HW-' . date('ymd') . '-' . strtoupper(Str::random(5));
                $paymentStatus = 'unpaid';

                // 4. Handle wallet deduction if selected
                if ($validated['payment_method'] === 'wallet') {
                    $wallet = Wallet::where('user_id', $user->id)
                        ->lockForUpdate()
                        ->first();

                    if (!$wallet || $wallet->is_locked || (float)($wallet->cash_balance ?? $wallet->balance) < $finalPayable) {
                        throw new \DomainException('رصيدك في المحفظة غير كافٍ لسداد قيمة طلب المعدات والشحن.');
                    }

                    if (isset($wallet->cash_balance)) {
                        $wallet->decrement('cash_balance', $finalPayable);
                    } else {
                        $wallet->decrement('balance', $finalPayable);
                    }

                    WalletTransaction::create([
                        'wallet_id'   => $wallet->id,
                        'user_id'     => $user->id,
                        'type'        => 'payment',
                        'amount'      => $finalPayable,
                        'currency'    => 'EGP',
                        'status'      => 'completed',
                        'reference'   => $orderNumber,
                        'description' => "سداد قيمة طلب أجهزة وهاردوير ({$orderNumber})",
                    ]);

                    $paymentStatus = 'paid';
                }

                // 5. Create Hardware Order with robust field naming
                $customerName = strip_tags(trim($validated['name'] ?? ($user->name ?? 'عميل سولي')));
                $shippingAddress = strip_tags(trim($validated['shipping_address'] ?? ''));

                $hardwareOrder = HardwareOrder::create([
                    'order_number'     => $orderNumber,
                    'user_id'          => $user->id,
                    'customer_name'    => $customerName,
                    'customer_phone'   => $normalizedPhone,
                    'status'           => 'pending',
                    'payment_status'   => $paymentStatus,
                    'payment_method'   => $validated['payment_method'],
                    'subtotal_amount'  => $subtotal,
                    'discount_amount'  => $discount,
                    'shipping_cost'    => $shippingCost,
                    'total_amount'     => $finalPayable,
                    'shipping_address' => $shippingAddress,
                    'city'             => strip_tags(trim($validated['city'] ?? '')),
                    'items_json'       => $lineItemsToCreate,
                    'notes'            => !empty($validated['notes']) ? strip_tags(trim($validated['notes'])) : null,
                ]);

                // Create Order Line Items if model exists
                foreach ($lineItemsToCreate as $lineItem) {
                    $lineItem['hardware_order_id'] = $hardwareOrder->id;
                    try {
                        HardwareOrderItem::create($lineItem);
                    } catch (\Throwable $itemEx) {
                        // Suppressed if table structure only relies on items_json
                    }
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
                'redirect'     => route('portal.index'),
            ]);

        } catch (\DomainException $de) {
            return response()->json(['success' => false, 'message' => $de->getMessage()], 422);
        } catch (\Throwable $e) {
            Log::error('Hardware Checkout Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'حدث خطأ أثناء معالجة الطلب، يرجى إعادة المحاولة.'], 500);
        }
    }
}
