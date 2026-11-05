<?php

namespace Tests\Feature;

use App\Models\ClientDevice;
use App\Models\ErpOrder;
use App\Models\ErpOrderItem;
use App\Models\ErpStoreCatalog;
use App\Models\ErpWishlist;
use App\Models\HardwareOrder;
use App\Models\HardwareOrderItem;
use App\Models\HardwareProduct;
use App\Models\Service;
use App\Models\ServicePackage;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdversarialChallengerR1toR4Test extends TestCase
{
    use DatabaseTransactions;

    protected User $clientUser;
    protected HardwareProduct $activeProduct;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Set up test client user with wallet
        $this->clientUser = User::create([
            'name'      => 'فاحص التحدي سولي',
            'email'     => 'challenger_' . Str::random(8) . '@soulyeg.online',
            'phone'     => '0109' . rand(1000000, 9999999),
            'password'  => Hash::make('Secret1234!'),
            'role'      => 'client',
            'is_active' => true,
        ]);

        Wallet::create([
            'user_id'       => $this->clientUser->id,
            'balance'       => 5000.00,
            'cash_balance'  => 5000.00,
            'bonus_balance' => 0.00,
            'currency'      => 'EGP',
            'is_locked'     => false,
        ]);

        // 2. Set up test hardware product with adequate stock
        $existing = HardwareProduct::where('is_active', true)->where('stock_qty', '>=', 10)->first();
        if ($existing) {
            $this->activeProduct = $existing;
        } else {
            $this->activeProduct = HardwareProduct::create([
                'slug'           => 'hw-challenge-' . Str::random(6),
                'sku'            => 'HW-CHALLENGE-' . Str::random(6),
                'name_ar'        => 'جهاز فحص سيبراني متقدم',
                'brand'          => 'SoulyCyber',
                'category'       => 'networking_security',
                'price'          => 1200.00,
                'stock_qty'      => 50,
                'is_active'      => true,
            ]);
        }
    }

    // =========================================================================
    // REQUIREMENT R1: Hardware Store Checkout Tests
    // =========================================================================

    /**
     * R1 Test 1: Guest submission with no name must return 422 with validation error on customer_name.
     */
    public function test_r1_guest_submission_with_no_name_returns_422(): void
    {
        Auth::logout();

        $payload = [
            'items' => [
                ['id' => $this->activeProduct->id, 'quantity' => 1]
            ],
            'shipping_address' => 'شارع التحرير، الدقي، الجيزة',
            'payment_method'   => 'bank_transfer',
            // customer_name and customer_phone omitted
        ];

        $response = $this->postJson('/store/hardware/checkout', $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['customer_name', 'customer_phone']);
    }

    /**
     * R1 Test 2: Guest submission with name and valid data must return 200 with order_number.
     */
    public function test_r1_guest_submission_with_name_and_valid_data_returns_200_with_order_number(): void
    {
        Auth::logout();

        $guestPhone = '0112' . rand(1000000, 9999999);
        $payload = [
            'customer_name'    => 'أحمد محمود الزائر',
            'customer_phone'   => $guestPhone,
            'shipping_address' => 'مدينة نصر، الحي السابع، القاهرة',
            'payment_method'   => 'transfer',
            'items'            => [
                ['id' => $this->activeProduct->id, 'quantity' => 2]
            ],
        ];

        $response = $this->postJson('/store/hardware/checkout', $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'status'  => 'pending_review',
        ]);
        $this->assertNotEmpty($response->json('order_number'));
        $this->assertStringStartsWith('HW-', $response->json('order_number'));

        // Verify order in database
        $expectedTotal2Qty = round((float)$this->activeProduct->price * 2, 2);
        $order = HardwareOrder::where('order_number', $response->json('order_number'))->first();
        $this->assertNotNull($order);
        $this->assertEquals($expectedTotal2Qty, (float) $order->total_amount);
        $this->assertEquals('pending_review', $order->status);
        $this->assertEquals('pending', $order->payment_status);

        // Verify line items created in database
        $items = HardwareOrderItem::where('order_id', $order->id)->get();
        $this->assertCount(1, $items);
        $this->assertEquals($this->activeProduct->id, $items->first()->product_id);
        $this->assertEquals(2, $items->first()->quantity);
        $this->assertEquals($expectedTotal2Qty, (float) $items->first()->subtotal);
    }

    /**
     * R1 Test 3: Authenticated user submission with name omitted must return 200 and auto-fill user name.
     */
    public function test_r1_authenticated_user_submission_with_name_omitted_auto_fills_user_name(): void
    {
        $payload = [
            'shipping_address' => 'المعادي، كورنيش النيل، برج الصفا',
            'payment_method'   => 'bank_transfer',
            'items'            => [
                ['id' => $this->activeProduct->id, 'quantity' => 1]
            ],
            // name and customer_name omitted intentionally
        ];

        $response = $this->actingAs($this->clientUser)
            ->postJson('/store/hardware/checkout', $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'status'  => 'pending_review',
        ]);

        $orderNumber = $response->json('order_number');
        $this->assertNotEmpty($orderNumber);

        $expectedTotal1Qty = round((float)$this->activeProduct->price * 1, 2);
        $order = HardwareOrder::where('order_number', $orderNumber)->first();
        $this->assertNotNull($order);
        $this->assertEquals($this->clientUser->id, $order->user_id);
        $this->assertEquals($this->clientUser->name, $order->customer_name);
        $this->assertEquals($expectedTotal1Qty, (float) $order->total_amount);
    }

    /**
     * R1 Test 4: Test wallet payments with sufficient balance (auto-deducts, marks confirmed_preparing and paid).
     */
    public function test_r1_wallet_payment_with_sufficient_balance_deducts_and_confirms(): void
    {
        $walletBefore = Wallet::where('user_id', $this->clientUser->id)->first();
        $this->assertEquals(5000.00, (float) $walletBefore->balance);

        $expectedTotal = round((float)$this->activeProduct->price * 2, 2);

        $payload = [
            'shipping_address' => 'التجمع الخامس، القاهرة الجديدة',
            'payment_method'   => 'wallet',
            'items'            => [
                ['id' => $this->activeProduct->id, 'quantity' => 2]
            ],
        ];

        $response = $this->actingAs($this->clientUser)
            ->postJson('/store/hardware/checkout', $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'status'  => 'confirmed_preparing',
        ]);

        $walletAfter = Wallet::where('user_id', $this->clientUser->id)->first();
        $this->assertEquals(5000.00 - $expectedTotal, (float) $walletAfter->balance);

        // Verify wallet transaction
        $orderNumber = $response->json('order_number');
        $txn = WalletTransaction::where('user_id', $this->clientUser->id)
            ->where('type', 'purchase')
            ->where('admin_notes', 'like', "%{$orderNumber}%")
            ->first();
        $this->assertNotNull($txn);
        $this->assertEquals($expectedTotal, (float) $txn->amount);
        $this->assertEquals('approved', $txn->status);
    }

    /**
     * R1 Stress Test 5: Wallet payment with insufficient balance must return 422 and not decrement.
     */
    public function test_r1_adversarial_wallet_insufficient_balance_returns_422(): void
    {
        // Reduce wallet balance to 500 EGP
        $wallet = Wallet::where('user_id', $this->clientUser->id)->first();
        $wallet->update(['balance' => 500.00, 'cash_balance' => 500.00]);

        $payload = [
            'shipping_address' => 'الشيخ زايد، الجيزة',
            'payment_method'   => 'wallet',
            'items'            => [
                ['id' => $this->activeProduct->id, 'quantity' => 1] // 1200 EGP > 500 EGP
            ],
        ];

        $response = $this->actingAs($this->clientUser)
            ->postJson('/store/hardware/checkout', $payload);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'message' => 'رصيدك في المحفظة غير كافٍ لسداد قيمة طلب المعدات.',
        ]);

        // Verify wallet balance remained untouched
        $this->assertEquals(500.00, (float) $wallet->fresh()->balance);
    }

    /**
     * R1 Stress Test 6: Out-of-stock product must return 422.
     */
    public function test_r1_adversarial_out_of_stock_product_returns_422(): void
    {
        $outOfStock = HardwareProduct::create([
            'slug'            => 'hw-out-stock-01',
            'sku'             => 'HW-OUT-STOCK-01',
            'name_ar'         => 'جهاز نفد من المخزن',
            'brand'           => 'Generic',
            'category'        => 'pos_equipment',
            'price'           => 500.00,
            'stock_qty'       => 0,
            'is_active'       => true,
        ]);

        $payload = [
            'shipping_address' => 'الإسكندرية، سموحة',
            'payment_method'   => 'bank_transfer',
            'items'            => [
                ['id' => $outOfStock->id, 'quantity' => 1]
            ],
        ];

        $response = $this->actingAs($this->clientUser)
            ->postJson('/store/hardware/checkout', $payload);

        $response->assertStatus(422);
        $this->assertStringContainsString('غير متوفرة في المخزن', $response->json('message'));
    }

    // =========================================================================
    // REQUIREMENT R2: Alpine AI Widget Tests
    // =========================================================================

    /**
     * R2 Test 1: Render partials.souly-ai-widget and inspect that @toggle-souly-ai.window="toggleWidget()" is present.
     */
    public function test_r2_ai_widget_blade_contains_canonical_toggle_handler(): void
    {
        $viewContent = view('partials.souly-ai-widget')->render();

        $this->assertStringContainsString(
            '@toggle-souly-ai.window="toggleWidget()"',
            $viewContent,
            'The Alpine AI widget must contain @toggle-souly-ai.window="toggleWidget()"'
        );

        $this->assertStringContainsString(
            '@open-souly-ai.window="openWidget()"',
            $viewContent
        );

        $this->assertStringContainsString(
            '@close-souly-ai.window="closeWidget()"',
            $viewContent
        );

        $this->assertStringContainsString(
            'x-data="soulyAiWidgetController()"',
            $viewContent
        );
    }

    /**
     * R2 Test 2: Inspect souly-ui.js and master.blade.php for soulyAiWidgetController registration on global and Alpine.data.
     */
    public function test_r2_souly_ui_js_and_master_register_souly_ai_controller(): void
    {
        $uiJs = file_get_contents(public_path('js/souly-ui.js'));
        $masterBlade = file_get_contents(resource_path('views/layouts/master.blade.php'));

        // In souly-ui.js: global.soulyAiWidgetController defined
        $this->assertStringContainsString('global.soulyAiWidgetController = function soulyAiWidgetController()', $uiJs);

        // In souly-ui.js: auto-registered with Alpine.data
        $this->assertStringContainsString("Alpine.data('soulyAiWidgetController', global.soulyAiWidgetController)", $uiJs);

        // In master.blade.php: registered inside alpine:init
        $this->assertStringContainsString("Alpine.data('soulyAiWidgetController', window.soulyAiWidgetController)", $masterBlade);

        // Check the 6 reactive properties defined in souly-ui.js
        $requiredProperties = ['isOpen', 'visitorName', 'welcomeMessage', 'messages', 'isTyping', 'inputMessage'];
        foreach ($requiredProperties as $prop) {
            $this->assertMatchesRegularExpression(
                '/' . preg_quote($prop, '/') . '\s*:/',
                $uiJs,
                "souly-ui.js must define property '{$prop}' on soulyAiWidgetController"
            );
        }
    }

    /**
     * R2 Test 3: Verify Blade views that embed or use layout master render without PHP/Blade errors.
     */
    public function test_r2_views_render_cleanly_without_blade_errors(): void
    {
        $widgetHtml = view('partials.souly-ai-widget')->render();
        $this->assertNotEmpty($widgetHtml);

        $welcomeHtml = view('welcome')->render();
        $this->assertNotEmpty($welcomeHtml);

        // Hardware store page render via route
        $response = $this->get('/store/hardware');
        $response->assertStatus(200);
    }

    // =========================================================================
    // REQUIREMENT R3: Portal Bookings & Devices Views & Profile Route Tests
    // =========================================================================

    /**
     * R3 Test 1: Render portal/bookings.blade.php via authenticated client user with zero undefined variable warnings.
     */
    public function test_r3_portal_bookings_renders_cleanly_without_undefined_variable_warnings(): void
    {
        $this->actingAs($this->clientUser);

        // Call the controller action directly to test data passing and view rendering
        $controller = app(\App\Http\Controllers\ClientPortalController::class);
        $request = \Illuminate\Http\Request::create('/portal/bookings', 'GET');
        $response = $controller->bookings($request);

        $this->assertInstanceOf(\Illuminate\View\View::class, $response);

        // Verify required view data is passed
        $viewData = $response->getData();
        $this->assertArrayHasKey('services', $viewData);
        $this->assertArrayHasKey('wallet', $viewData);
        $this->assertArrayHasKey('bookings', $viewData);
        $this->assertArrayHasKey('user', $viewData);
        $this->assertArrayHasKey('totalCount', $viewData);

        // Render HTML and assert no errors
        $html = $response->render();
        $this->assertNotEmpty($html);
        $this->assertStringContainsString('الحجوزات والخدمات الهندسية المعتمدة', $html);
    }

    /**
     * R3 Test 2: Render portal/devices.blade.php with zero undefined $wallet warnings.
     */
    public function test_r3_portal_devices_renders_cleanly_without_undefined_wallet(): void
    {
        $this->actingAs($this->clientUser);

        // Create a test client device
        ClientDevice::create([
            'user_id'      => $this->clientUser->id,
            'model_number' => 'MacBook Pro M2 Max',
            'details'      => 'فحص صيانة معتمد وتغيير معجون حراري',
            'status'       => 'in_maintenance',
        ]);

        $controller = app(\App\Http\Controllers\ClientDeviceController::class);
        $request = \Illuminate\Http\Request::create('/portal/devices', 'GET');
        $response = $controller->index($request);

        $this->assertInstanceOf(\Illuminate\View\View::class, $response);

        // Render HTML and assert zero undefined variable exceptions
        $html = $response->render();
        $this->assertNotEmpty($html);
        $this->assertStringContainsString('خزينة أجهزتي وجلسات الصيانة المعتمدة', $html);
        $this->assertStringContainsString('MacBook Pro M2 Max', $html);
    }

    /**
     * R3 Test 3: Test POST /portal/profile route and controller. Verify returns success without 404.
     */
    public function test_r3_post_portal_profile_updates_successfully_without_404(): void
    {
        $newName = 'سولي المحترف للمعلومات';
        $newPhone = '01011112222';
        $newCompany = 'شركة سولي للتقنيات المتقدمة';

        $payload = [
            'name'         => $newName,
            'phone'        => $newPhone,
            'email'        => $this->clientUser->email,
            'company_name' => $newCompany,
        ];

        $response = $this->actingAs($this->clientUser)
            ->postJson('/portal/profile', $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'تم تحديث الملف الشخصي بنجاح',
        ]);

        // Verify database updated
        $this->clientUser->refresh();
        $this->assertEquals($newName, $this->clientUser->name);
        $this->assertEquals($newPhone, $this->clientUser->phone);
        $this->assertEquals($newCompany, $this->clientUser->company_name);
    }

    /**
     * R3 Stress Test 4: Profile update validation failures (invalid email, password mismatch, unauthenticated).
     */
    public function test_r3_adversarial_profile_validations(): void
    {
        // 1. Unauthenticated request to /portal/profile should be blocked (302 redirect to login)
        Auth::logout();
        $unauthResp = $this->post('/portal/profile', ['name' => 'اختراق']);
        $unauthResp->assertRedirect('/login');

        // 2. Authenticated with mismatched password confirmation
        $mismatchResp = $this->actingAs($this->clientUser)
            ->postJson('/portal/profile', [
                'name'                  => $this->clientUser->name,
                'phone'                 => $this->clientUser->phone,
                'password'              => 'NewPassword123!',
                'password_confirmation' => 'DifferentPassword!',
            ]);
        $mismatchResp->assertStatus(422);
        $mismatchResp->assertJsonValidationErrors(['password']);

        // 3. Duplicate email of another user
        $otherUser = User::create([
            'name'      => 'مستخدم آخر',
            'email'     => 'other_' . Str::random(6) . '@soulyeg.online',
            'phone'     => '0102' . rand(1000000, 9999999),
            'password'  => Hash::make('secret'),
            'role'      => 'client',
            'is_active' => true,
        ]);

        $duplicateEmailResp = $this->actingAs($this->clientUser)
            ->postJson('/portal/profile', [
                'name'  => $this->clientUser->name,
                'phone' => $this->clientUser->phone,
                'email' => $otherUser->email,
            ]);
        $duplicateEmailResp->assertStatus(422);
        $duplicateEmailResp->assertJsonValidationErrors(['email']);
    }

    // =========================================================================
    // REQUIREMENT R4: BrandERP Store Query & Operations Tests
    // =========================================================================

    /**
     * R4 Test 1: Execute active trial query from ErpStoreController.php:65-71 via DB builder.
     * Must execute with zero "1054 Column not found" SQL exceptions.
     */
    public function test_r4_active_trial_query_executes_without_sql_1054_column_not_found(): void
    {
        $userId = $this->clientUser->id;

        // Create an ErpOrder and ErpOrderItem to test the join against live DB
        $erpOrder = ErpOrder::create([
            'order_number'    => 'ERP-TRIAL-' . Str::random(5),
            'user_id'         => $userId,
            'customer_name'   => $this->clientUser->name,
            'customer_email'  => $this->clientUser->email,
            'customer_phone'  => $this->clientUser->phone,
            'total_amount'    => 0.00,
            'status'          => 'active',
            'is_trial'        => true,
            'trial_starts_at' => now(),
            'trial_ends_at'   => now()->addDays(14),
            'items_json'      => [],
        ]);

        $erpItem = ErpOrderItem::create([
            'order_id'        => $erpOrder->id,
            'catalog_item_id' => 1,
            'item_code'       => 'MOD-POS-TEST',
            'item_name'       => 'وحدة كاشير تجريبية',
            'tier'            => 'feature',
            'price'           => 0.00,
        ]);

        // Exact query from ErpStoreController.php lines 65-71
        $userActiveTrialItemIds = DB::table('erp_order_items')
            ->join('erp_orders', 'erp_order_items.order_id', '=', 'erp_orders.id')
            ->where('erp_orders.user_id', $userId)
            ->where('erp_orders.is_trial', true)
            ->where('erp_orders.status', 'active')
            ->pluck('erp_order_items.catalog_item_id')
            ->toArray();

        $this->assertIsArray($userActiveTrialItemIds);
        $this->assertContains(1, $userActiveTrialItemIds);

        // Also test the controller index() method directly
        $controller = app(\App\Http\Controllers\ErpStoreController::class);
        $request = \Illuminate\Http\Request::create('/store/branderp', 'GET');
        $this->actingAs($this->clientUser);
        $response = $controller->index($request);

        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
        $html = $response->render();
        $this->assertNotEmpty($html);
    }

    /**
     * R4 Test 2: Test wishlist toggle via Tinker or test.
     */
    public function test_r4_wishlist_toggle_adds_and_removes_cleanly(): void
    {
        $catalogItem = ErpStoreCatalog::first();
        if (!$catalogItem) {
            $catalogItem = ErpStoreCatalog::create([
                'slug'              => 'test-module-slug-' . Str::random(4),
                'code'              => 'MOD-TEST-' . Str::random(4),
                'name_ar'           => 'وحدة فحص تجريبية',
                'name_en'           => 'Test Module',
                'description_ar'    => 'وصف وحدة تجريبية',
                'description_en'    => 'Test Description',
                'tier'              => 'module',
                'module_type'       => 'accounting',
                'price'             => 350.00,
                'is_active'         => true,
            ]);
        }

        // Toggle 1: Add to wishlist
        $response1 = $this->actingAs($this->clientUser)
            ->postJson('/store/branderp/wishlist', [
                'catalog_item_id' => $catalogItem->id,
            ]);

        $response1->assertStatus(200);
        $response1->assertJson([
            'success' => true,
            'status'  => 'added',
            'action'  => 'added',
        ]);
        $this->assertTrue(ErpWishlist::where('user_id', $this->clientUser->id)->where('catalog_item_id', $catalogItem->id)->exists());

        // Toggle 2: Remove from wishlist
        $response2 = $this->actingAs($this->clientUser)
            ->postJson('/store/branderp/wishlist', [
                'catalog_item_id' => $catalogItem->id,
            ]);

        $response2->assertStatus(200);
        $response2->assertJson([
            'success' => true,
            'status'  => 'removed',
            'action'  => 'removed',
        ]);
        $this->assertFalse(ErpWishlist::where('user_id', $this->clientUser->id)->where('catalog_item_id', $catalogItem->id)->exists());
    }

    /**
     * R4 Test 3: Test BrandERP checkout creates ErpOrder with line items.
     */
    public function test_r4_branderp_checkout_creates_order_and_order_items(): void
    {
        $catalogItem = ErpStoreCatalog::where('is_active', true)->first();
        $this->assertNotNull($catalogItem, 'Requires at least one active catalog item in database');

        $payload = [
            'items'            => [$catalogItem->slug],
            'customer_name'    => 'عميل تجريبي للأنظمة',
            'customer_email'   => 'erp_customer@soulyeg.online',
            'customer_phone'   => '01012345678',
            'company_name'     => 'سولي براند للبرمجيات',
            'payment_method'   => 'bank_transfer',
            'billing_interval' => 'monthly',
        ];

        $response = $this->actingAs($this->clientUser)
            ->postJson('/store/branderp/checkout', $payload);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $orderNumber = $response->json('order_number');
        $this->assertNotEmpty($orderNumber);

        // Verify order in database
        $order = ErpOrder::where('order_number', $orderNumber)->first();
        $this->assertNotNull($order);
        $this->assertEquals($this->clientUser->id, $order->user_id);

        // Verify order item created
        $orderItem = ErpOrderItem::where('order_id', $order->id)->first();
        $this->assertNotNull($orderItem);
        $this->assertEquals($catalogItem->id, $orderItem->catalog_item_id);
    }
}
