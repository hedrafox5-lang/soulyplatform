<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ErpCatalogItem;
use App\Models\ErpCustomRequest;
use App\Models\ErpOrder;
use App\Models\ErpOrderItem;
use App\Models\ErpStoreCatalog;
use App\Models\ErpWishlist;
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
        $coreItem = ErpStoreCatalog::where('tier', 'core')->where('is_active', true)->first();
        $modules  = ErpStoreCatalog::where('tier', 'module')->where('is_active', true)->orderBy('display_order', 'asc')->get();
        $features = ErpStoreCatalog::where('tier', 'feature')->where('is_active', true)->orderBy('display_order', 'asc')->get();
        $bundles  = ErpStoreCatalog::where('tier', 'bundle')->where('is_active', true)->orderBy('display_order', 'asc')->get();

        $flashDeal = ErpStoreCatalog::where('tier', 'feature')->where('is_flash_deal', true)->where('is_active', true)->first()
            ?? $features->first();

        $departments = ErpStoreCatalog::where('tier', 'feature')
            ->where('is_active', true)
            ->pluck('department_ar')
            ->unique()
            ->filter()
            ->values();

        $industries = [
            ['id' => 'all', 'name' => 'جميع الأنشطة', 'icon' => '🌐'],
            ['id' => 'supermarket', 'name' => 'سوبرماركت وهايبر ماركت', 'icon' => '🛒'],
            ['id' => 'pharmacy', 'name' => 'صيدليات ومستودعات أدوية', 'icon' => '💊'],
            ['id' => 'restaurants', 'name' => 'مطاعم وكافيهات وكافتيريا', 'icon' => '🍽️'],
            ['id' => 'manufacturing', 'name' => 'مصانع وورش تصنيع', 'icon' => '🏭'],
            ['id' => 'retail_clothing', 'name' => 'محلات ملابس وأحذية', 'icon' => '👔'],
            ['id' => 'services_clinics', 'name' => 'عيادات ومراكز طبية وخدمات', 'icon' => '🩺'],
        ];

        $userWishlistIds = [];
        $userActiveTrialItemIds = [];

        if (Auth::check()) {
            $userId = Auth::id();
            $userWishlistIds = ErpWishlist::where('user_id', $userId)->pluck('catalog_item_id')->toArray();
            $userActiveTrialItemIds = DB::table('erp_order_items')
                ->join('erp_orders', 'erp_order_items.order_id', '=', 'erp_orders.id')
                ->where('erp_orders.user_id', $userId)
                ->where('erp_orders.is_trial', true)
                ->where('erp_orders.status', 'active')
                ->pluck('erp_order_items.catalog_item_id')
                ->toArray();
        }

        return view('pages.store.branderp', compact(
            'coreItem',
            'modules',
            'features',
            'bundles',
            'flashDeal',
            'departments',
            'industries',
            'userWishlistIds',
            'userActiveTrialItemIds'
        ));
    }

    /**
     * Start a safe free trial strictly once per user/account.
     */
    public function startTrial(Request $request)
    {
        if ($request->filled('catalog_item_id') && !$request->has('modules')) {
            $request->merge(['modules' => [(int) $request->input('catalog_item_id')]]);
        }

        $request->validate([
            'catalog_item_id' => ['nullable', 'integer', 'exists:erp_store_catalog,id'],
            'modules'         => ['required', 'array', 'min:1'],
            'modules.*'       => ['integer', 'exists:erp_store_catalog,id'],
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

                $selectedItems = ErpStoreCatalog::whereIn('id', $request->modules)->get();

                $erpOrder = ErpOrder::create([
                    'order_number'     => $orderNumber,
                    'user_id'          => $user->id,
                    'customer_name'    => $user->name,
                    'customer_phone'   => $user->phone,
                    'customer_email'   => $user->email,
                    'items_json'       => $selectedItems->pluck('slug')->toArray(),
                    'license_key'      => $licenseKey,
                    'status'           => 'active',
                    'payment_method'   => 'free_trial',
                    'total_amount'     => 0.00,
                    'is_trial'         => true,
                    'trial_expires_at' => now()->addDays(2),
                ]);
                foreach ($selectedItems as $item) {
                    ErpOrderItem::create([
                        'order_id'        => $erpOrder->id,
                        'catalog_item_id' => $item->id,
                        'item_code'       => $item->code ?? $item->slug,
                        'item_name'       => $item->name_ar ?? $item->name,
                        'tier'            => $item->tier,
                        'price'           => 0.00,
                    ]);
                    $item->increment('activations_count');
                }

                return $erpOrder;
            });

            return response()->json([
                'success'     => true,
                'message'     => 'تم تفعيل نسختك التجريبية بنجاح لمدة 48 ساعة.',
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
        if ($request->has('items') && !$request->has('modules')) {
            $itemsInput = $request->input('items');
            $slugs = [];
            if (is_array($itemsInput)) {
                foreach ($itemsInput as $it) {
                    if (is_array($it) && isset($it['slug'])) {
                        $slugs[] = $it['slug'];
                    } elseif (is_string($it)) {
                        $slugs[] = $it;
                    }
                }
            }
            if (!empty($slugs)) {
                $moduleIds = ErpStoreCatalog::whereIn('slug', $slugs)->pluck('id')->toArray();
                $request->merge(['modules' => $moduleIds]);
            }
        }

        if ($request->has('customer_name') && !$request->has('name')) {
            $request->merge(['name' => $request->input('customer_name')]);
        }
        if ($request->has('customer_phone') && !$request->has('phone')) {
            $request->merge(['phone' => $request->input('customer_phone')]);
        }

        $validated = $request->validate([
            'modules'        => ['required', 'array', 'min:1'],
            'modules.*'      => ['integer', 'exists:erp_store_catalog,id'],
            'name'           => ['required_without:user', 'nullable', 'string', 'max:150'],
            'customer_name'  => ['nullable', 'string', 'max:150'],
            'phone'          => ['required_without:user', 'nullable', 'string'],
            'customer_phone' => ['nullable', 'string'],
            'email'          => ['nullable', 'email', 'max:190'],
            'company'        => ['nullable', 'string', 'max:150'],
            'promo_code'     => ['nullable', 'string', 'max:50'],
            'notes'          => ['nullable', 'string', 'max:1000'],
            'payment_method' => ['required', 'in:wallet,card,bank_transfer,direct_transfer'],
        ]);

        $user = Auth::user();
        $normalizedPhone = null;

        if (!$user) {
            $phoneInput = $validated['customer_phone'] ?? $validated['phone'] ?? null;
            $normalizedPhone = $this->normalizeEgyptianPhone($phoneInput);
            if (!$this->isValidEgyptianPhone($normalizedPhone)) {
                return response()->json(['success' => false, 'message' => 'رقم هاتف مصري غير صحيح.'], 422);
            }
        } else {
            $normalizedPhone = $user->phone ?? ($validated['customer_phone'] ?? $validated['phone'] ?? null);
        }

        try {
            $erpOrder = DB::transaction(function () use ($validated, $user, $normalizedPhone) {
                // 1. Resolve or provision client account safely
                if (!$user) {
                    $user = User::where('phone', $normalizedPhone)->first();
                    if (!$user) {
                        $clientName = !empty($validated['customer_name'])
                            ? strip_tags(trim($validated['customer_name']))
                            : (!empty($validated['name']) ? strip_tags(trim($validated['name'])) : 'عميل BrandERP');

                        $user = User::create([
                            'name'      => $clientName,
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
                $catalogItems = ErpStoreCatalog::whereIn('id', $validated['modules'])->get();
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

                $customerName = !empty($validated['customer_name'])
                    ? strip_tags(trim($validated['customer_name']))
                    : (!empty($validated['name']) ? strip_tags(trim($validated['name'])) : $user->name);

                $orderStatus = ($paymentStatus === 'paid' || in_array($validated['payment_method'], ['wallet', 'card']))
                    ? ($paymentStatus === 'paid' ? 'active' : 'pending')
                    : 'pending';

                // 6. Create ERP order & line items
                $order = ErpOrder::create([
                    'order_number'   => $orderNumber,
                    'user_id'        => $user->id,
                    'customer_name'  => $customerName,
                    'customer_phone' => $normalizedPhone,
                    'customer_email' => $validated['email'] ?? $user->email,
                    'license_key'    => $licenseKey,
                    'status'         => $orderStatus,
                    'payment_method' => $validated['payment_method'],
                    'total_amount'   => $finalPayable,
                    'is_trial'       => false,
                    'items_json'     => $catalogItems->pluck('slug')->toArray(),
                    'notes'          => $validated['notes'] ?? null,
                ]);

                foreach ($catalogItems as $item) {
                    ErpOrderItem::create([
                        'order_id'        => $order->id,
                        'catalog_item_id' => $item->id,
                        'item_code'       => $item->code ?? $item->slug,
                        'item_name'       => $item->name_ar ?? $item->name,
                        'tier'            => $item->tier,
                        'price'           => $item->price,
                    ]);
                    $item->increment('activations_count');
                }

                // 7. Track in CRM Leads
                if ($normalizedPhone) {
                    Lead::updateOrCreate(
                        ['phone' => $normalizedPhone],
                        [
                            'name'            => $customerName,
                            'email'           => $user->email,
                            'company'         => !empty($validated['company']) ? strip_tags(trim($validated['company'])) : null,
                            'status'          => $orderStatus === 'active' ? 'won' : 'qualified',
                            'user_id'         => $user->id,
                            'source'          => 'erp_store',
                            'last_contact_at' => now(),
                        ]
                    );
                }

                return $order;
            });

            return response()->json([
                'success'      => true,
                'status'       => $erpOrder->status,
                'message'      => 'تم تسجيل طلب تراخيص BrandERP بنجاح.',
                'order_number' => $erpOrder->order_number,
                'license_key'  => $erpOrder->status === 'active' ? $erpOrder->license_key : null,
                'redirect'     => \Illuminate\Support\Facades\Route::has('portal.dashboard')
                    ? route('portal.dashboard')
                    : (\Illuminate\Support\Facades\Route::has('portal.erp_store') ? route('portal.erp_store') : url('/portal')),
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
        $customerName  = $request->input('customer_name') ?? $request->input('name');
        $customerPhone = $request->input('customer_phone') ?? $request->input('phone');
        $featureTitle  = $request->input('feature_title') ?? $request->input('scope_title');
        $featureDesc   = $request->input('feature_description') ?? $request->input('details');
        $industry      = $request->input('industry') ?? 'general';
        $budget        = $request->input('budget_range') ?? $request->input('budget');

        $request->merge([
            'customer_name'       => $customerName,
            'customer_phone'      => $customerPhone,
            'feature_title'       => $featureTitle,
            'feature_description' => $featureDesc,
        ]);

        $validated = $request->validate([
            'customer_name'       => ['required', 'string', 'max:150'],
            'customer_phone'      => ['required', 'string'],
            'email'               => ['nullable', 'email', 'max:190'],
            'company'             => ['nullable', 'string', 'max:150'],
            'industry'            => ['nullable', 'string', 'max:100'],
            'feature_title'       => ['required', 'string', 'max:200'],
            'feature_description' => ['required', 'string', 'max:2000'],
            'budget_range'        => ['nullable', 'string', 'max:100'],
        ]);

        $normalizedPhone = $this->normalizeEgyptianPhone($validated['customer_phone']);
        if (!$this->isValidEgyptianPhone($normalizedPhone)) {
            return response()->json(['success' => false, 'message' => 'رقم الهاتف المصري غير صحيح.'], 422);
        }

        ErpCustomRequest::create([
            'user_id'             => Auth::id(),
            'customer_name'       => strip_tags(trim($validated['customer_name'])),
            'customer_phone'      => $normalizedPhone,
            'industry'            => $validated['industry'] ?? $industry,
            'feature_title'       => strip_tags(trim($validated['feature_title'])),
            'feature_description' => strip_tags(trim($validated['feature_description'])),
            'budget_range'        => $validated['budget_range'] ?? ($budget ? (string)$budget : null),
            'status'              => 'pending',
        ]);

        Lead::create([
            'name'            => strip_tags(trim($validated['customer_name'])),
            'phone'           => $normalizedPhone,
            'email'           => !empty($validated['email']) ? strtolower(trim($validated['email'])) : null,
            'company'         => !empty($validated['company']) ? strip_tags(trim($validated['company'])) : null,
            'source'          => 'erp_custom_request',
            'status'          => 'new',
            'notes'           => "طلب برمجة خاصة: {$validated['feature_title']}\nالتفاصيل: {$validated['feature_description']}\nالميزانية المقترحة: " . ($validated['budget_range'] ?? ($budget ?? 'غير محدد')),
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
            ->where(function ($q) {
                $q->where('status', 'active')->orWhere('is_trial', true);
            })
            ->firstOrFail();

        $orderItem = ErpOrderItem::where('order_id', $order->id)
            ->where('id', $itemId)
            ->firstOrFail();

        return response()->json([
            'success'     => true,
            'order_id'    => $order->id,
            'item_name'   => $orderItem->item_name ?? $orderItem->name_ar,
            'license_key' => $order->license_key,
            'download_at' => now()->toDateTimeString(),
        ]);
    }

    /**
     * Toggle item in client ERP wishlist.
     */
    public function toggleWishlist(Request $request)
    {
        $validated = $request->validate([
            'catalog_item_id' => ['required', 'integer'],
        ]);

        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'يرجى تسجيل الدخول أولاً.'], 401);
        }

        $wishlist = ErpWishlist::where('user_id', $user->id)
            ->where('catalog_item_id', $validated['catalog_item_id'])
            ->first();

        if ($wishlist) {
            $wishlist->delete();
            return response()->json([
                'success' => true,
                'status'  => 'removed',
                'action'  => 'removed',
                'message' => 'تمت الإزالة من قائمة الرغبات.'
            ]);
        } else {
            ErpWishlist::create([
                'user_id'         => $user->id,
                'catalog_item_id' => $validated['catalog_item_id'],
            ]);
            return response()->json([
                'success' => true,
                'status'  => 'added',
                'action'  => 'added',
                'message' => 'تمت الإضافة إلى قائمة الرغبات بنجاح.'
            ]);
        }
    }

    /**
     * Generate quick quotation for selected modules.
     */
    public function quickQuotation(Request $request)
    {
        $rawItems = $request->query('items') ?? $request->input('item_ids');
        if (is_string($rawItems)) {
            $slugsOrIds = array_filter(array_map('trim', explode(',', $rawItems)));
            $items = ErpStoreCatalog::whereIn('slug', $slugsOrIds)
                ->orWhereIn('id', $slugsOrIds)
                ->get();
        } elseif (is_array($rawItems)) {
            $items = ErpStoreCatalog::whereIn('id', $rawItems)
                ->orWhereIn('slug', $rawItems)
                ->get();
        } else {
            $items = collect();
        }

        $subtotal = (float) $items->sum('price');
        $discount = $items->count() >= 3 ? round($subtotal * 0.15, 2) : 0.00;
        $total    = max(0.00, round($subtotal - $discount, 2));

        if ($request->wantsJson() && !$request->has('company')) {
            return response()->json([
                'success'  => true,
                'count'    => $items->count(),
                'subtotal' => $subtotal,
                'discount' => $discount,
                'total'    => $total,
            ]);
        }

        $clientName  = Auth::check() ? Auth::user()->name : ($request->query('client') ?? 'العميل الكريم');
        $companyName = $request->query('company') ?? 'مؤسسة أعمال معتمدة';

        return view('pages.store.quotation', compact('clientName', 'companyName', 'items', 'subtotal', 'discount', 'total'));
    }
}
