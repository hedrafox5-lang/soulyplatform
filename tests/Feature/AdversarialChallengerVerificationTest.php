<?php

namespace Tests\Feature;

use App\Models\ClientDevice;
use App\Models\ErpCatalogItem;
use App\Models\ErpCustomRequest;
use App\Models\ErpOrder;
use App\Models\ErpOrderItem;
use App\Models\HardwareOrder;
use App\Models\HardwareOrderItem;
use App\Models\HardwareProduct;
use App\Models\Lead;
use App\Models\Service;
use App\Models\ServicePackage;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AdversarialChallengerVerificationTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
    }

    // =========================================================================
    // R1: Hardware Store Checkout Adversarial Verification
    // =========================================================================

    public function test_r1_hardware_checkout_empty_payload_fails_with_expected_validation_keys(): void
    {
        $response = $this->postJson('/store/hardware/checkout', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'customer_name',
            'customer_phone',
            'shipping_address',
            'payment_method',
            'items',
        ]);
    }

    public function test_r1_hardware_checkout_invalid_payloads_rejected(): void
    {
        $product = HardwareProduct::where('is_active', true)->firstOrFail();

        // 1. Invalid payment method
        $res1 = $this->postJson('/store/hardware/checkout', [
            'customer_name'    => 'عميل تجريبي',
            'customer_phone'   => '01011112222',
            'shipping_address' => 'شارع التحرير، القاهرة',
            'payment_method'   => 'unsupported_method',
            'items'            => [['id' => $product->id, 'quantity' => 1]],
        ]);
        $res1->assertStatus(422);
        $res1->assertJsonValidationErrors(['payment_method']);

        // 2. Non-existent product ID
        $res2 = $this->postJson('/store/hardware/checkout', [
            'customer_name'    => 'عميل تجريبي',
            'customer_phone'   => '01011112222',
            'shipping_address' => 'شارع التحرير، القاهرة',
            'payment_method'   => 'cash_on_delivery',
            'items'            => [['id' => 99999999, 'quantity' => 1]],
        ]);
        $res2->assertStatus(422);
        $res2->assertJsonValidationErrors(['items.0.id']);

        // 3. Zero or negative quantity
        $res3 = $this->postJson('/store/hardware/checkout', [
            'customer_name'    => 'عميل تجريبي',
            'customer_phone'   => '01011112222',
            'shipping_address' => 'شارع التحرير، القاهرة',
            'payment_method'   => 'cash_on_delivery',
            'items'            => [['id' => $product->id, 'quantity' => 0]],
        ]);
        $res3->assertStatus(422);
        $res3->assertJsonValidationErrors(['items.0.quantity']);

        // 4. Invalid Egyptian phone for guest
        $res4 = $this->postJson('/store/hardware/checkout', [
            'customer_name'    => 'عميل تجريبي',
            'customer_phone'   => '02345678', // not valid 11-digit mobile
            'shipping_address' => 'شارع التحرير، القاهرة',
            'payment_method'   => 'cash_on_delivery',
            'items'            => [['id' => $product->id, 'quantity' => 1]],
        ]);
        $res4->assertStatus(422);
        $res4->assertJson(['success' => false]);
    }

    public function test_r1_hardware_checkout_guest_succeeds_with_field_aliases(): void
    {
        $product = HardwareProduct::where('is_active', true)->where('stock_qty', '>=', 2)->firstOrFail();
        $initialStock = $product->stock_qty;

        $guestPhone = '011' . rand(10000000, 99999999);
        $payload = [
            'name'             => 'كريم ضيف سولي',
            'phone'            => $guestPhone,
            'shipping_address' => 'الإسكندرية، محرم بك، عمارة 12',
            'payment_method'   => 'cash_on_delivery',
            'items'            => [
                ['id' => $product->id, 'quantity' => 2],
            ],
            'notes'            => 'ملاحظات توصيل تجريبية',
        ];

        $response = $this->postJson('/store/hardware/checkout', $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'status'  => 'pending_review',
        ]);

        $orderNumber = $response->json('order_number');
        $this->assertNotEmpty($orderNumber);

        // Check HardwareOrder
        $order = HardwareOrder::where('order_number', $orderNumber)->firstOrFail();
        $this->assertEquals('pending_review', $order->status);
        $this->assertEquals('pending', $order->payment_status);
        $this->assertEquals('cash_on_delivery', $order->payment_method);
        $this->assertEquals('كريم ضيف سولي', $order->customer_name);

        // Check HardwareOrderItem in database
        $orderItems = HardwareOrderItem::where('order_id', $order->id)->get();
        $this->assertCount(1, $orderItems);
        $firstItem = $orderItems->first();
        $this->assertEquals($product->id, $firstItem->product_id);
        $this->assertEquals(2, $firstItem->quantity);
        $this->assertEquals((float) $product->price, (float) $firstItem->unit_price);
        $this->assertEquals((float) $product->price * 2, (float) $firstItem->subtotal);

        // Stock decrement check
        $this->assertEquals($initialStock - 2, $product->fresh()->stock_qty);
    }

    public function test_r1_hardware_checkout_auth_user_omits_name_and_phone(): void
    {
        $user = User::factory()->create([
            'name'  => 'د. طارق شاكر',
            'phone' => '012' . rand(10000000, 99999999),
            'role'  => 'client',
        ]);

        $product = HardwareProduct::where('is_active', true)->where('stock_qty', '>=', 1)->firstOrFail();

        $payload = [
            'shipping_address' => 'المنصورة، شارع المشاية السفلية',
            'payment_method'   => 'transfer',
            'items'            => [
                ['id' => $product->id, 'quantity' => 1],
            ],
        ];

        $response = $this->actingAs($user)->postJson('/store/hardware/checkout', $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'status'  => 'pending_review',
        ]);

        $orderNumber = $response->json('order_number');
        $order = HardwareOrder::where('order_number', $orderNumber)->firstOrFail();
        $this->assertEquals($user->id, $order->user_id);
        $this->assertEquals('د. طارق شاكر', $order->customer_name);
        $this->assertEquals($user->phone, $order->customer_phone);
    }

    public function test_r1_hardware_checkout_wallet_payment_lifecycle(): void
    {
        $user = User::factory()->create([
            'name'  => 'مهندس محمود المحفظة',
            'phone' => '015' . rand(10000000, 99999999),
            'role'  => 'client',
        ]);

        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0.00, 'cash_balance' => 0.00, 'currency' => 'EGP']
        );
        $wallet->update(['balance' => 0.00, 'cash_balance' => 0.00]);

        $product = HardwareProduct::where('is_active', true)->where('stock_qty', '>=', 1)->firstOrFail();
        $unitPrice = (float) $product->price;

        $payload = [
            'customer_name'    => $user->name,
            'customer_phone'   => $user->phone,
            'shipping_address' => 'القاهرة، مدينة نصر',
            'payment_method'   => 'wallet',
            'items'            => [
                ['id' => $product->id, 'quantity' => 1],
            ],
        ];

        // 1. Insufficient balance should return 422
        $resFail = $this->actingAs($user)->postJson('/store/hardware/checkout', $payload);
        $resFail->assertStatus(422);
        $resFail->assertJson([
            'success' => false,
            'message' => 'رصيدك في المحفظة غير كافٍ لسداد قيمة طلب المعدات.',
        ]);

        // 2. Fund the wallet and re-attempt
        $fundedAmount = $unitPrice + 5000.00;
        $wallet->update(['balance' => $fundedAmount, 'cash_balance' => $fundedAmount]);

        $resSuccess = $this->actingAs($user)->postJson('/store/hardware/checkout', $payload);
        $resSuccess->assertStatus(200);
        $resSuccess->assertJson([
            'success' => true,
            'status'  => 'confirmed_preparing',
        ]);

        $orderNumber = $resSuccess->json('order_number');
        $order = HardwareOrder::where('order_number', $orderNumber)->firstOrFail();
        $this->assertEquals('confirmed_preparing', $order->status);
        $this->assertEquals('paid', $order->payment_status);

        // Verify wallet balance decremented
        $wallet->refresh();
        $this->assertEqualsWithDelta($fundedAmount - $unitPrice, (float) $wallet->balance, 0.01);

        // Verify WalletTransaction created
        $txn = WalletTransaction::where('user_id', $user->id)->latest()->firstOrFail();
        $this->assertEquals('purchase', $txn->type);
        $this->assertEquals('approved', $txn->status);
        $this->assertEquals('wallet_balance', $txn->payment_method);
        $this->assertEqualsWithDelta($unitPrice, (float) $txn->amount, 0.01);
    }

    public function test_r1_hardware_store_catalog_view_renders_cleanly(): void
    {
        $response = $this->get('/store/hardware');
        $response->assertStatus(200);
        $response->assertSee('كافة الأجهزة والمعدات');
        $response->assertSee('أجهزة كاشير ونقاط بيع');
        $response->assertSee('هاردات SSD ورامات فائقة السرعة');
        $response->assertSee('شبكات وبصمة وتدريع أمني');
        $response->assertSee('أجهزة ولابتوبات أعمال هندسية');
    }

    // =========================================================================
    // R2: Alpine AI Widget Errors Adversarial Verification
    // =========================================================================

    public function test_r2_ai_widget_blade_definition_script_hoisting_and_event_listeners(): void
    {
        $renderedView = view('partials.souly-ai-widget')->render();

        // 1. Script defining function soulyAiWidgetController must be declared BEFORE <div x-data="soulyAiWidgetController()">
        $scriptPos = strpos($renderedView, 'function soulyAiWidgetController()');
        $xDataPos = strpos($renderedView, 'x-data="soulyAiWidgetController()"');

        $this->assertNotFalse($scriptPos, 'function soulyAiWidgetController() must exist in template');
        $this->assertNotFalse($xDataPos, 'x-data="soulyAiWidgetController()" must exist in template');
        $this->assertLessThan($xDataPos, $scriptPos, 'Controller script must execute BEFORE x-data element');

        // 2. Verify all reactive variables are initialized in constructor
        $this->assertStringContainsString('isOpen: false', $renderedView);
        $this->assertStringContainsString('isTyping: false', $renderedView);
        $this->assertStringContainsString("inputMessage: ''", $renderedView);
        $this->assertStringContainsString('visitorName:', $renderedView);
        $this->assertStringContainsString('welcomeMessage:', $renderedView);
        $this->assertStringContainsString('messages: []', $renderedView);

        // 3. Verify event listeners match specification
        $this->assertStringContainsString('@toggle-souly-ai.window="toggleWidget()"', $renderedView);
        $this->assertStringContainsString('@open-souly-ai.window="openWidget()"', $renderedView);
        $this->assertStringContainsString('@close-souly-ai.window="closeWidget()"', $renderedView);

        // 4. Verify no trailing redundant duplicate script block
        $firstScriptClose = strpos($renderedView, '</script>');
        $secondScriptOpen = strpos($renderedView, '<script', $firstScriptClose + 1);
        $this->assertFalse($secondScriptOpen, 'There should be no duplicate trailing script tag');
    }

    public function test_r2_souly_ui_js_defines_and_registers_soulyAiWidgetController(): void
    {
        $jsPath = public_path('js/souly-ui.js');
        $this->assertFileExists($jsPath);
        $content = file_get_contents($jsPath);

        // Check global definition
        $this->assertStringContainsString('soulyAiWidgetController', $content);
        $this->assertStringContainsString("Alpine.data('soulyAiWidgetController'", $content);

        // Check reactive property declarations in JS bundle
        $this->assertStringContainsString('isOpen:', $content);
        $this->assertStringContainsString('isTyping:', $content);
        $this->assertStringContainsString('inputMessage:', $content);
        $this->assertStringContainsString('visitorName:', $content);
        $this->assertStringContainsString('welcomeMessage:', $content);
        $this->assertStringContainsString('messages:', $content);
    }

    public function test_r2_master_layout_and_home_views_render_without_errors(): void
    {
        $welcomeView = view('welcome')->render();
        $this->assertNotEmpty($welcomeView);

        $services = Service::all();
        $homeView = view('pages.home', compact('services'))->render();
        $this->assertNotEmpty($homeView);
    }

    // =========================================================================
    // R3: Portal Bookings View, Devices View & Profile Update Adversarial Verification
    // =========================================================================

    public function test_r3_portal_bookings_view_receives_services_and_wallet_cleanly(): void
    {
        $user = User::factory()->create([
            'name' => 'عميل الحجوزات التجريبي',
            'role' => 'client',
        ]);
        $user->walletOrCreate();

        $response = $this->actingAs($user)->get('/portal/bookings');
        $response->assertStatus(200);

        // Verify view data
        $response->assertViewHas('services');
        $response->assertViewHas('wallet');
        $response->assertViewHas('bookings');
        $response->assertViewHas('totalCount');

        $services = $response->viewData('services');
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $services);

        // Verify services packages relation is preloaded
        foreach ($services as $service) {
            $this->assertTrue($service->relationLoaded('packages'), 'Service packages relation must be eager-loaded');
        }

        // Verify HTML does not output undefined variable error
        $response->assertDontSee('Undefined variable');
        $response->assertDontSee('ErrorException');
    }

    public function test_r3_portal_devices_view_renders_with_zero_500_errors(): void
    {
        $user = User::factory()->create([
            'name' => 'عميل الأجهزة التجريبي',
            'role' => 'client',
        ]);

        // Scenario 1: User with 0 devices
        $resEmpty = $this->actingAs($user)->get('/portal/devices');
        $resEmpty->assertStatus(200);
        $resEmpty->assertDontSee('Undefined variable');
        $resEmpty->assertDontSee('ErrorException');

        // Scenario 2: User with 2 devices
        ClientDevice::create([
            'user_id'          => $user->id,
            'model_number'     => 'DELL-9520-XPS',
            'details'          => 'Dell XPS 15 9520 Core i7 32GB RAM',
            'status'           => 'under_repair',
        ]);
        ClientDevice::create([
            'user_id'          => $user->id,
            'model_number'     => 'APPLE-M2-MAX',
            'details'          => 'MacBook Pro M2 Max 64GB Unified',
            'status'           => 'ready_for_pickup',
        ]);

        $resPopulated = $this->actingAs($user)->get('/portal/devices');
        $resPopulated->assertStatus(200);
        $resPopulated->assertSee('DELL-9520-XPS');
        $resPopulated->assertSee('APPLE-M2-MAX');
    }

    public function test_r3_portal_profile_update_route_exists_and_persists_changes(): void
    {
        // 1. Verify route registration
        $this->assertTrue(Route::has('portal.profile.update'), 'portal.profile.update route must be defined');

        $user = User::factory()->create([
            'name'         => 'الاسم القديم',
            'email'        => 'old.email.' . rand(1000, 9999) . '@test.com',
            'phone'        => '01055554444',
            'company_name' => 'الشركة القديمة',
            'password'     => Hash::make('OldPassword123'),
            'role'         => 'client',
        ]);

        $newEmail = 'new.email.' . rand(1000, 9999) . '@test.com';

        // 2. Test valid POST request to /portal/profile
        $response = $this->actingAs($user)->postJson('/portal/profile', [
            'name'                  => 'الاسم الجديد المحدث',
            'phone'                 => '01099998888',
            'email'                 => $newEmail,
            'company_name'          => 'شركة سولي المحدثة',
            'password'              => 'NewPassword2026!',
            'password_confirmation' => 'NewPassword2026!',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        // Verify in database
        $user->refresh();
        $this->assertEquals('الاسم الجديد المحدث', $user->name);
        $this->assertEquals('01099998888', $user->phone);
        $this->assertEquals($newEmail, $user->email);
        $this->assertEquals('شركة سولي المحدثة', $user->company_name);
        $this->assertTrue(Hash::check('NewPassword2026!', $user->password));

        // 3. Test invalid data (password confirmation mismatch)
        $failResponse = $this->actingAs($user)->postJson('/portal/profile', [
            'name'                  => 'اسم آخر',
            'phone'                 => '01099998888',
            'email'                 => $newEmail,
            'password'              => 'NewPassword2026!',
            'password_confirmation' => 'Mismatch123',
        ]);
        $failResponse->assertStatus(422);
        $failResponse->assertJsonValidationErrors(['password']);
    }

    // =========================================================================
    // R4: BrandERP Store Query Schema Alignment Adversarial Verification
    // =========================================================================

    public function test_r4_erp_store_page_loads_without_sql_1054_error(): void
    {
        $user = User::factory()->create(['role' => 'client']);

        // Check with authenticated user
        $response = $this->actingAs($user)->get('/store/branderp');
        $response->assertStatus(200);
        $response->assertSee('أنظمة BrandERP السحابية');

        // Check with guest user
        $guestResponse = $this->get('/store/branderp');
        $guestResponse->assertStatus(200);
    }

    public function test_r4_erp_store_trial_and_checkout_persistence_matches_schema(): void
    {
        $user = User::factory()->create([
            'name'  => 'عميل تجربة ERP',
            'phone' => '01077778888',
            'role'  => 'client',
        ]);

        $catalogItem = ErpCatalogItem::where('is_active', true)->firstOrFail();

        // 1. Test Start Trial
        $trialRes = $this->actingAs($user)->postJson('/store/branderp/trial', [
            'catalog_item_id' => $catalogItem->id,
        ]);

        $trialRes->assertStatus(200);
        $trialRes->assertJson([
            'success' => true,
        ]);

        $orderId = $trialRes->json('order_id');
        $this->assertNotEmpty($orderId);
        $order = ErpOrder::findOrFail($orderId);
        $this->assertTrue((bool) $order->is_trial);
        $this->assertEquals('active', $order->status);

        // Check ErpOrderItem
        $orderItems = ErpOrderItem::where('order_id', $order->id)->get();
        $this->assertCount(1, $orderItems);
        $item = $orderItems->first();
        $this->assertEquals($catalogItem->id, $item->catalog_item_id);
        $this->assertEquals($catalogItem->name_ar, $item->item_name);

        // 2. Test Checkout
        $checkoutRes = $this->actingAs($user)->postJson('/store/branderp/checkout', [
            'customer_name'    => $user->name,
            'customer_phone'   => $user->phone,
            'shipping_address' => 'القاهرة، المعادي',
            'payment_method'   => 'bank_transfer',
            'items'            => [$catalogItem->slug],
        ]);

        $checkoutRes->assertStatus(200);
        $checkoutRes->assertJson([
            'success' => true,
        ]);
    }

    public function test_r4_erp_wishlist_toggle_uses_correct_column(): void
    {
        $user = User::factory()->create(['role' => 'client']);
        $catalogItem = ErpCatalogItem::where('is_active', true)->firstOrFail();

        // 1. Add to wishlist
        $resAdd = $this->actingAs($user)->postJson('/store/branderp/wishlist', [
            'catalog_item_id' => $catalogItem->id,
        ]);
        $resAdd->assertStatus(200);
        $resAdd->assertJson(['success' => true, 'action' => 'added']);

        $inDb = DB::table('erp_wishlists')
            ->where('user_id', $user->id)
            ->where('catalog_item_id', $catalogItem->id)
            ->exists();
        $this->assertTrue($inDb, 'Wishlist item must be stored with catalog_item_id');

        // 2. Remove from wishlist
        $resRemove = $this->actingAs($user)->postJson('/store/branderp/wishlist', [
            'catalog_item_id' => $catalogItem->id,
        ]);
        $resRemove->assertStatus(200);
        $resRemove->assertJson(['success' => true, 'action' => 'removed']);

        $stillInDb = DB::table('erp_wishlists')
            ->where('user_id', $user->id)
            ->where('catalog_item_id', $catalogItem->id)
            ->exists();
        $this->assertFalse($stillInDb);
    }

    public function test_r4_erp_store_custom_request_and_quotation_export(): void
    {
        $user = User::factory()->create(['role' => 'client']);
        $catalogItem = ErpCatalogItem::where('is_active', true)->firstOrFail();

        // Custom Request
        $reqRes = $this->actingAs($user)->postJson('/store/branderp/custom-request', [
            'name'                 => 'شركة الصفا للتجارة',
            'phone'                => '01066667777',
            'email'                => 'alsafa@test.com',
            'organization_name'    => 'الصفا الدولية',
            'business_type'        => 'retail',
            'scope_title'          => 'نظام نقاط بيع مخصص',
            'details'              => 'نظام نقاط بيع مخصص مع ربط الفاتورة الإلكترونية المصرية',
        ]);
        $reqRes->assertStatus(200);
        $reqRes->assertJson(['success' => true]);

        // Quotation Export (JSON)
        $quoteRes = $this->actingAs($user)->getJson('/store/branderp/quotation?items=' . $catalogItem->slug);
        $quoteRes->assertStatus(200);
        $quoteRes->assertJson(['success' => true]);

        $quoteViewRes = $this->actingAs($user)->get('/store/branderp/quotation?company=Al-Safwa&items=' . $catalogItem->slug);
        $quoteViewRes->assertStatus(200);
        $quoteViewRes->assertSee('Al-Safwa');
    }
}
