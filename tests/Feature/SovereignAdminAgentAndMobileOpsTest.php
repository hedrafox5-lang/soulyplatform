<?php

namespace Tests\Feature;

use App\Models\AiActionSnapshot;
use App\Models\AiApiKey;
use App\Models\Booking;
use App\Models\HardwareProduct;
use App\Models\Lead;
use App\Models\Service;
use App\Models\Setting;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Services\AdminAiToolExecutor;
use App\Services\AdminSovereignAgentService;
use App\Services\SoulyAiKeyPoolService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SovereignAdminAgentAndMobileOpsTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;
    protected User $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'hedra.adel.test@souly.eg'],
            [
                'name' => 'المهندس هدرا عادل',
                'phone' => '01090623326',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        $this->client = User::firstOrCreate(
            ['email' => 'client.test.sovereign@souly.eg'],
            [
                'name' => 'عميل اختبار تجريبي',
                'phone' => '01011122233',
                'password' => bcrypt('password'),
                'role' => 'client',
                'is_active' => true,
            ]
        );
    }

    /**
     * Test 1: AI API Keys Vault and Automated Failover Rotation.
     */
    public function test_ai_key_pool_failover_and_dynamic_model_resolution(): void
    {
        AiApiKey::query()->delete();

        $key1 = 'TEST_KEY_FAILOVER_1_429';
        $key2 = 'TEST_KEY_FAILOVER_2_SUCCESS';

        AiApiKey::create([
            'label' => 'Primary Test Key',
            'provider' => 'gemini',
            'api_key' => $key1,
            'scope' => 'all',
            'priority' => 1,
            'status' => 'active',
        ]);

        AiApiKey::create([
            'label' => 'Backup Test Key',
            'provider' => 'gemini',
            'api_key' => $key2,
            'scope' => 'all',
            'priority' => 2,
            'status' => 'active',
        ]);

        Http::fake([
            "https://generativelanguage.googleapis.com/*key={$key1}*" => Http::response(['error' => ['code' => 429, 'message' => 'Quota reached']], 429),
            "https://generativelanguage.googleapis.com/*key={$key2}*" => Http::response([
                'candidates' => [
                    ['content' => ['parts' => [['text' => 'تم استلام الأمر بنجاح عبر المفتاح البديل.']]]]
                ]
            ], 200),
            "https://generativelanguage.googleapis.com/*" => Http::response([
                'candidates' => [
                    ['content' => ['parts' => [['text' => 'استجابة عامة.']]]]
                ]
            ], 200),
        ]);

        $keyPool = app(SoulyAiKeyPoolService::class);
        $result = $keyPool->callGemini('gemini-3.8-flash', 'generateContent', [
            'contents' => [['parts' => [['text' => 'مرحبا']]]]
        ]);

        $this->assertTrue($result['success']);
        $this->assertEquals('gemini-3.8-flash', $result['model']);

        // Check key1 was put into cooldown
        $k1Record = AiApiKey::where('api_key', $key1)->first();
        $this->assertEquals('cooldown', $k1Record->status);
    }

    /**
     * Test 2: Admin Sovereign Agent Database Read/Write & 1-Tap Undo.
     */
    public function test_sovereign_admin_agent_read_write_and_1_tap_undo(): void
    {
        $toolExecutor = app(AdminAiToolExecutor::class);

        // 1. Create a dummy hardware product to modify
        $product = HardwareProduct::create([
            'name' => 'Barcode Scanner X-500',
            'name_ar' => 'قارئ باركود لاسلكي X-500',
            'sku' => 'TEST-SKU-' . uniqid(),
            'slug' => 'test-sku-' . uniqid(),
            'category' => 'pos_equipment',
            'brand' => 'Sunmi',
            'price' => 1500.00,
            'stock_qty' => 5,
            'warranty_months' => 12,
            'is_active' => true,
        ]);

        // 2. Execute update via tool executor
        $updateResult = $toolExecutor->updateHardwareProduct($product->id, [
            'price' => 2200.00,
            'stock_qty' => 12,
        ], $this->admin->id);

        $this->assertTrue($updateResult['success']);
        $snapshotId = $updateResult['snapshot_id'];
        $this->assertNotNull($snapshotId);

        // Product should have new price & stock
        $this->assertEquals(2200.00, $product->fresh()->price);
        $this->assertEquals(12, $product->fresh()->stock_qty);

        // 3. Execute 1-Tap Undo via controller/tool
        $undoResult = $toolExecutor->revertSnapshot($snapshotId);
        $this->assertTrue($undoResult['success']);

        // Product should revert to original price (1500) and stock (5)
        $this->assertEquals(1500.00, $product->fresh()->price);
        $this->assertEquals(5, $product->fresh()->stock_qty);
    }

    /**
     * Test 3: AI Chat-to-Dossier Parser extracts client data and creates booking.
     */
    public function test_ai_chat_parser_creates_dossier_and_booking(): void
    {
        $rawChat = "العميل: مساء الخير يا باشمهندس.. أنا مايكل من فرع مصر الجديدة.. عندي كاشير تاتش الشاشة بتطفي لوحدها.. محتاج مهندس ضروري.. رقمي 01234567890";

        Http::fake([
            'https://generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => json_encode([
                                        'name' => 'مايكل - فرع مصر الجديدة',
                                        'phone' => '01234567890',
                                        'district' => 'مصر الجديدة، القاهرة',
                                        'device_model' => 'كاشير تاتش',
                                        'issue_description' => 'الشاشة بتطفي لوحدها وبحاجة لزيارة مهندس',
                                        'required_hardware' => 'باور سبلاي أو كابل شاشة',
                                        'recommended_stage' => 'awaiting_call'
                                    ])
                                ]
                            ]
                        ]
                    ]
                ]
            ], 200)
        ]);

        $agentService = app(AdminSovereignAgentService::class);
        $result = $agentService->parseChatAndCreateDossier($rawChat, $this->admin->id);

        $this->assertTrue($result['success']);
        $this->assertDatabaseHas('users', ['phone' => '01234567890']);
        $this->assertDatabaseHas('bookings', [
            'pipeline_stage' => 'awaiting_call',
            'call_confirmed' => false,
        ]);
        $this->assertDatabaseHas('leads', [
            'phone' => '01234567890',
        ]);
    }

    /**
     * Test 4: 1-Tap Call Confirmation Toggle on Bookings and Leads.
     */
    public function test_1_tap_call_confirmation_toggle(): void
    {
        $service = Service::first();

        $booking = Booking::create([
            'booking_number' => 'SOULY-TEST-' . uniqid(),
            'user_id' => $this->client->id,
            'service_id' => $service ? $service->id : 1,
            'quantity_or_hours' => 1,
            'total_amount' => 1200.00,
            'payment_method' => 'wallet',
            'payment_status' => 'unpaid',
            'status' => 'new',
            'pipeline_stage' => 'awaiting_call',
            'call_confirmed' => false,
        ]);

        // Toggle call confirmation via endpoint
        $response = $this->actingAs($this->admin)->postJson(route('admin.bookings.toggle_call', $booking->id));
        $response->assertStatus(200);
        $response->assertJson(['success' => true, 'call_confirmed' => true]);

        $this->assertTrue((bool) $booking->fresh()->call_confirmed);
        $this->assertEquals('confirmed_scheduled', $booking->fresh()->pipeline_stage);

        // Toggle back
        $response2 = $this->actingAs($this->admin)->postJson(route('admin.bookings.toggle_call', $booking->id));
        $response2->assertStatus(200);
        $response2->assertJson(['success' => true, 'call_confirmed' => false]);
        $this->assertFalse((bool) $booking->fresh()->call_confirmed);
    }

    /**
     * Test 5: Field Engineer Visit Stopwatch Mode (1,200 EGP for 3h + 400 EGP extra).
     */
    public function test_field_engineer_stopwatch_hours_and_pricing(): void
    {
        $service = Service::first();

        $booking = Booking::create([
            'booking_number' => 'SOULY-VISIT-' . uniqid(),
            'user_id' => $this->client->id,
            'service_id' => $service ? $service->id : 1,
            'quantity_or_hours' => 1,
            'total_amount' => 1200.00,
            'payment_method' => 'on_site',
            'payment_status' => 'unpaid',
            'status' => 'in_progress',
            'pipeline_stage' => 'visit_in_progress',
        ]);

        // Complete 5 hours visit (3 base + 2 extra * 400 = 2,000 EGP)
        $response = $this->actingAs($this->admin)->postJson(
            route('admin.bookings.stopwatch', $booking->id),
            ['actual_hours' => 5]
        );

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'total_amount' => 2000.00,
            'actual_hours' => 5,
        ]);

        $this->assertEquals(2000.00, (float) $booking->fresh()->total_amount);
        $this->assertEquals('completed_warranty', $booking->fresh()->pipeline_stage);
    }

    /**
     * Test 6: Mobile Inline AJAX Update for Hardware Products.
     */
    public function test_hardware_mobile_inline_price_and_stock_update(): void
    {
        $product = HardwareProduct::create([
            'name' => 'Touch POS Terminal Core',
            'name_ar' => 'شاشة كاشير تاتش',
            'sku' => 'POS-INLINE-' . uniqid(),
            'slug' => 'pos-inline-' . uniqid(),
            'category' => 'pos_equipment',
            'brand' => 'Souly Core',
            'price' => 8500.00,
            'stock_qty' => 3,
            'warranty_months' => 24,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->postJson(
            route('admin.hardware.inline_product', $product->id),
            [
                'price' => 9200.00,
                'stock_qty' => 7,
            ]
        );

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertEquals(9200.00, (float) $product->fresh()->price);
        $this->assertEquals(7, $product->fresh()->stock_qty);
    }

    /**
     * Test 7: Payment Gateways Live Configuration and Wallet Deposit Approval Flow.
     */
    public function test_payment_gateways_config_and_wallet_deposit_flow(): void
    {
        // Update live gateways
        $gwResponse = $this->actingAs($this->admin)->postJson(
            route('admin.wallets.gateways'),
            [
                'payment_vodafone_cash' => '01026042784',
                'payment_instapay' => 'hedra@instapay',
                'payment_bank_account' => 'CIB Account 999888',
            ]
        );
        $gwResponse->assertStatus(200);
        $this->assertEquals('01026042784', Setting::get('payment_vodafone_cash'));

        // Client creates deposit
        $wallet = $this->client->getOrCreateWallet();
        $initialBalance = $wallet->balance;

        $tx = WalletTransaction::create([
            'wallet_id' => $wallet->id,
            'user_id' => $this->client->id,
            'type' => 'deposit',
            'amount' => 500.00,
            'payment_method' => 'instapay',
            'reference_code' => 'TX-TEST-' . uniqid(),
            'status' => 'pending',
        ]);

        // Admin approves deposit via AJAX
        $approveResponse = $this->actingAs($this->admin)->postJson(route('admin.wallets.approve', $tx->id));
        $approveResponse->assertStatus(200);
        $approveResponse->assertJson(['success' => true, 'status' => 'approved']);

        $this->assertEquals($initialBalance + 500.00, $wallet->fresh()->balance);
    }

    /**
     * Test 8: 1-Tap JSON Database Backup Snapshot and Admin AI Copilot View.
     */
    public function test_json_database_backup_and_admin_ai_copilot_view(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.system.backup_snapshot'));
        $response->assertStatus(200);
        $this->assertStringContainsString('attachment;', $response->headers->get('content-disposition'));

        // Admin AI Copilot View
        $viewResponse = $this->actingAs($this->admin)->get(route('admin.ai_copilot.index'));
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee('منظومة القائد المنفرد', false);
        $viewResponse->assertSee('خزينة مفاتيح الـ API المتعددة', false);
    }
}
