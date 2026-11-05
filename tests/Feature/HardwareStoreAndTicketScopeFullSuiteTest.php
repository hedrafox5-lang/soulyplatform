<?php

namespace Tests\Feature;

use App\Models\HardwareOrder;
use App\Models\HardwareOrderItem;
use App\Models\HardwareProduct;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class HardwareStoreAndTicketScopeFullSuiteTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * 1. Test Seeded Hardware Products Integrity: 24 products across 4 categories (6 each).
     */
    public function test_hardware_database_seeder_counts_and_categories(): void
    {
        $totalCount = HardwareProduct::count();
        $this->assertGreaterThanOrEqual(24, $totalCount, 'Hardware products must have at least 24 items');

        $categories = [
            'pos_equipment'       => 'POS & Cashier Systems',
            'storage_ram'         => 'NVMe SSDs & High-Speed RAM',
            'networking_security' => 'Networking, Firewalls & Biometric Access',
            'workstations'        => 'Engineering Workstations & Laptops',
        ];

        foreach ($categories as $catKey => $catLabel) {
            $catCount = HardwareProduct::where('category', $catKey)->count();
            $this->assertGreaterThanOrEqual(6, $catCount, "Category {$catKey} ({$catLabel}) must have at least 6 products");
        }

        // Verify prices and warranty values are valid
        $samplePos = HardwareProduct::where('category', 'pos_equipment')->first();
        $this->assertNotNull($samplePos);
        $this->assertGreaterThan(0, (float) $samplePos->price);
        $this->assertGreaterThanOrEqual(12, $samplePos->warranty_months);
        $this->assertTrue((bool) $samplePos->compatible_with_branderp);
    }

    /**
     * 2. Test Standalone Hardware Store frontend view and categories.
     */
    public function test_store_hardware_page_renders_cleanly(): void
    {
        $response = $this->get('/store/hardware');
        $response->assertStatus(200);

        // Category filter titles in view
        $response->assertSee('كافة الأجهزة والمعدات');
        $response->assertSee('أجهزة كاشير ونقاط بيع');
        $response->assertSee('هاردات SSD ورامات فائقة السرعة');
        $response->assertSee('شبكات وبصمة وتدريع أمني');
        $response->assertSee('أجهزة ولابتوبات أعمال هندسية');

        // BrandERP compatibility badge
        $response->assertSee('متوافق 100% مع منظومة BrandERP');

        // Specs modal and cart drawer triggers
        $response->assertSee('سلة الأجهزة');
        $response->assertSee('متابعة عملية الشراء وتأكيد الطلب');
    }

    /**
     * 3. Test Hardware Checkout via Direct Transfer (Guest or Auth).
     */
    public function test_hardware_checkout_validation_and_direct_transfer(): void
    {
        // 3.1 Validation check
        $badResponse = $this->postJson('/store/hardware/checkout', []);
        $badResponse->assertStatus(422);
        $badResponse->assertJsonValidationErrors(['customer_name', 'customer_phone', 'shipping_address', 'payment_method', 'items']);

        // 3.2 Successful Checkout
        $product1 = HardwareProduct::where('category', 'pos_equipment')->active()->firstOrFail();
        $product2 = HardwareProduct::where('category', 'storage_ram')->active()->firstOrFail();

        $initialStock1 = $product1->stock_qty;
        $initialStock2 = $product2->stock_qty;

        $payload = [
            'customer_name'    => 'شركة الأهرام للحلول التقنية',
            'customer_phone'   => '01012345678',
            'shipping_address' => 'القاهرة، المعادي الجديدة، شارع النصر، مبنى 14',
            'payment_method'   => 'transfer',
            'items'            => [
                ['id' => $product1->id, 'quantity' => 2],
                ['id' => $product2->id, 'quantity' => 1],
            ],
            'notes'            => 'يرجى التوصيل خلال ساعات العمل الصباحية',
        ];

        $response = $this->postJson('/store/hardware/checkout', $payload);
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'status'  => 'pending_review',
        ]);

        $orderNumber = $response->json('order_number');
        $this->assertNotEmpty($orderNumber);
        $this->assertStringStartsWith('HW-', $orderNumber);

        // Verify in Database
        $order = HardwareOrder::where('order_number', $orderNumber)->firstOrFail();
        $this->assertEquals('pending_review', $order->status);
        $this->assertEquals('pending', $order->payment_status);
        $this->assertEquals('transfer', $order->payment_method);
        $this->assertEquals(
            ((float) $product1->price * 2) + ((float) $product2->price * 1),
            (float) $order->total_amount
        );

        // Verify order items
        $this->assertCount(2, $order->items);
        $this->assertEquals($initialStock1 - 2, $product1->fresh()->stock_qty);
        $this->assertEquals($initialStock2 - 1, $product2->fresh()->stock_qty);
    }

    /**
     * 4. Test Hardware Checkout via Digital Wallet Balance (Deduction & Instant Confirmation).
     */
    public function test_hardware_checkout_via_wallet_deducts_balance_and_activates_order(): void
    {
        $user = User::factory()->create([
            'name' => 'مهندس أحمد سولي',
            'email' => 'ahmed.wallet.test@souly.local',
            'role' => 'client',
        ]);

        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 60000.00, 'currency' => 'EGP']
        );
        $wallet->update(['balance' => 60000.00]);

        $product = HardwareProduct::where('category', 'pos_equipment')->active()->firstOrFail();
        $unitPrice = (float) $product->price;
        $orderCost = $unitPrice * 2;

        $payload = [
            'customer_name'    => $user->name,
            'customer_phone'   => '01222233344',
            'shipping_address' => 'الإسكندرية، سموحة، طريق 14 مايو',
            'payment_method'   => 'wallet',
            'items'            => [
                ['id' => $product->id, 'quantity' => 2],
            ],
        ];

        $response = $this->actingAs($user)->postJson('/store/hardware/checkout', $payload);
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'status'  => 'confirmed_preparing',
        ]);

        $orderNumber = $response->json('order_number');
        $order = HardwareOrder::where('order_number', $orderNumber)->firstOrFail();

        $this->assertEquals('confirmed_preparing', $order->status);
        $this->assertEquals('paid', $order->payment_status);
        $this->assertEquals($user->id, $order->user_id);

        // Wallet balance deducted
        $this->assertEquals(60000.00 - $orderCost, (float) $wallet->fresh()->balance);

        // Wallet purchase transaction created
        $transaction = WalletTransaction::where('wallet_id', $wallet->id)
            ->where('type', 'purchase')
            ->latest('id')
            ->firstOrFail();

        $this->assertEquals($orderCost, (float) $transaction->amount);
        $this->assertEquals('approved', $transaction->status);
    }

    /**
     * 5. Test Client Portal Hardware Orders Hub with 4-Step Stepper & Ticket Query Link.
     */
    public function test_portal_hardware_orders_hub_and_warranty_tracker(): void
    {
        $client = User::factory()->create([
            'name' => 'عميل تجريبي للبوابة',
            'email' => 'client.portal.hw@souly.local',
            'role' => 'client',
        ]);

        $order = HardwareOrder::create([
            'order_number'        => 'HW-PORTAL-TEST01',
            'user_id'             => $client->id,
            'customer_name'       => $client->name,
            'customer_phone'      => '01099887766',
            'shipping_address'    => 'الجيزة، المهندسين، شارع جامعة الدول',
            'items_json'          => [
                ['name' => 'طابعة إيصالات كاشير حرارية', 'sku' => 'POS-PRN-80', 'quantity' => 1, 'price' => 3800, 'subtotal' => 3800]
            ],
            'total_amount'        => 3800.00,
            'payment_method'      => 'transfer',
            'payment_status'      => 'paid',
            'status'              => 'confirmed_preparing',
            'serial_numbers_notes'=> 'SN-PRN-2026-X99281',
        ]);

        HardwareOrderItem::create([
            'order_id'     => $order->id,
            'product_sku'  => 'POS-PRN-80',
            'product_name' => 'طابعة إيصالات كاشير حرارية عالية السرعة',
            'unit_price'   => 3800.00,
            'quantity'     => 1,
            'subtotal'     => 3800.00,
        ]);

        $response = $this->actingAs($client)->get('/portal/hardware');
        $response->assertStatus(200);

        // Assert order number and serial
        $response->assertSee('HW-PORTAL-TEST01');
        $response->assertSee('SN-PRN-2026-X99281');
        $response->assertSee('طابعة إيصالات كاشير حرارية عالية السرعة');

        // Assert 4-step stepper label
        $response->assertSee('جاري التجهيز والفحص');
        $response->assertSee('المرحلة 2 من 4');

        // Assert ticket action link with query params
        $expectedTicketUrl = route('portal.tickets', ['scope' => 'hardware_store', 'ref' => 'HW-PORTAL-TEST01']);
        $response->assertSee($expectedTicketUrl);
        $response->assertSee('فتح تذكرة دعم أو ضمان لهذا الأوردر');
    }

    /**
     * 6. Test Admin Hardware Hub: Product CRUD, Stock Control & 4-Stage Order Progression.
     */
    public function test_admin_hardware_products_crud_and_order_status_pipeline(): void
    {
        $admin = User::factory()->create([
            'name' => 'المدير العام سولي',
            'email' => 'admin.hw.crud@souly.local',
            'role' => 'admin',
        ]);

        // 6.1 View admin hardware dashboard
        $response = $this->actingAs($admin)->get('/admin/hardware');
        $response->assertStatus(200);
        $response->assertSee('كتالوج الأجهزة والمخزون');

        // 6.2 Store a new hardware product
        $newProductData = [
            'sku'                      => 'HW-BARCODE-99',
            'name_ar'                  => 'ماسح باركود ليزري أوتوماتيكي سولي',
            'category'                 => 'pos_equipment',
            'brand'                    => 'Souly Citadel Tech',
            'price'                    => 2450.00,
            'old_price'                => 2900.00,
            'stock_qty'                => 20,
            'warranty_months'          => 24,
            'description_ar'           => 'ماسح باركود عالي الدقة وسرعة القراءة متوافق تماماً مع كاشير BrandERP.',
            'specs_raw'                => "الدقة: 2D QR & Barcode\nسرعة المسح: 300 مسحة/ثانية",
            'compatible_with_branderp' => '1',
        ];

        $storeResponse = $this->actingAs($admin)->post('/admin/hardware/products', $newProductData);
        $storeResponse->assertRedirect(route('admin.hardware.index', ['tab' => 'products']));

        $createdProduct = HardwareProduct::where('sku', 'HW-BARCODE-99')->firstOrFail();
        $this->assertEquals(2450.00, (float) $createdProduct->price);
        $this->assertEquals('Souly Citadel Tech', $createdProduct->brand);
        $this->assertTrue((bool) $createdProduct->compatible_with_branderp);

        // 6.3 Update product
        $updateResponse = $this->actingAs($admin)->post("/admin/hardware/products/{$createdProduct->id}", [
            'sku'                      => 'HW-BARCODE-99',
            'name_ar'                  => 'ماسح باركود ليزري أوتوماتيكي سولي المطور v2',
            'category'                 => 'pos_equipment',
            'brand'                    => 'Souly Citadel Tech',
            'price'                    => 2600.00,
            'stock_qty'                => 25,
            'warranty_months'          => 36,
            'compatible_with_branderp' => '1',
        ]);
        $updateResponse->assertRedirect(route('admin.hardware.index', ['tab' => 'products']));
        $this->assertEquals(2600.00, (float) $createdProduct->fresh()->price);
        $this->assertEquals(36, $createdProduct->fresh()->warranty_months);

        // 6.4 Toggle product active state
        $toggleResponse = $this->actingAs($admin)->postJson("/admin/hardware/products/{$createdProduct->id}/toggle");
        $toggleResponse->assertStatus(200);
        $this->assertFalse((bool) $createdProduct->fresh()->is_active);

        // 6.5 Order status progression through 4 stages
        $order = HardwareOrder::create([
            'order_number'        => 'HW-STATUS-PROGRESS-01',
            'user_id'             => $admin->id,
            'customer_name'       => 'مؤسسة النصر',
            'customer_phone'      => '01122334455',
            'shipping_address'    => 'القاهرة',
            'items_json'          => [['sku' => 'HW-BARCODE-99', 'name' => 'ماسح باركود']],
            'total_amount'        => 2600.00,
            'status'              => 'pending_review',
        ]);

        // Stage 2: confirmed_preparing
        $this->actingAs($admin)->post("/admin/hardware/orders/{$order->id}/status", [
            'status'               => 'confirmed_preparing',
            'serial_numbers_notes' => 'SN-HW-BARCODE-00129',
        ]);
        $this->assertEquals('confirmed_preparing', $order->fresh()->status);
        $this->assertEquals('SN-HW-BARCODE-00129', $order->fresh()->serial_numbers_notes);

        // Stage 3: out_for_delivery
        $this->actingAs($admin)->post("/admin/hardware/orders/{$order->id}/status", [
            'status' => 'out_for_delivery',
        ]);
        $this->assertEquals('out_for_delivery', $order->fresh()->status);

        // Stage 4: delivered_warranty_active
        $this->actingAs($admin)->post("/admin/hardware/orders/{$order->id}/status", [
            'status'         => 'delivered_warranty_active',
            'payment_status' => 'paid',
        ]);
        $this->assertEquals('delivered_warranty_active', $order->fresh()->status);
        $this->assertEquals('paid', $order->fresh()->payment_status);

        // 6.6 Delete product
        $deleteResponse = $this->actingAs($admin)->delete("/admin/hardware/products/{$createdProduct->id}");
        $deleteResponse->assertRedirect(route('admin.hardware.index', ['tab' => 'products']));
        $this->assertNull(HardwareProduct::find($createdProduct->id));
    }

    /**
     * 7. Test Multi-Store Categorized Support Tickets (Hardware, BrandERP, Services, Academy, Wallet).
     */
    public function test_multi_store_categorized_support_tickets_system(): void
    {
        $client = User::factory()->create([
            'name' => 'طارق الدعم الفني',
            'email' => 'tarek.support@souly.local',
            'role' => 'client',
        ]);

        $admin = User::factory()->create([
            'name' => 'مدير الدعم الفني',
            'email' => 'admin.tickets.audit@souly.local',
            'role' => 'admin',
        ]);

        // 7.1 Open ticket under hardware_store scope with order reference
        $ticketPayload = [
            'subject'          => 'استفسار حول تفعيل شهادة ضمان طابعة الكاشير',
            'department_scope' => 'hardware_store',
            'reference_code'   => 'HW-261027-ABCDE',
            'priority'         => 'high',
            'message'          => 'استلمنا الجهاز وبحاجة لربط السيريال رقم SN-9988 بضمان الـ 24 شهراً المعتمد.',
        ];

        $response = $this->actingAs($client)->post(route('portal.tickets.store'), $ticketPayload);
        $ticket = Ticket::where('reference_code', 'HW-261027-ABCDE')->firstOrFail();

        $this->assertEquals('hardware_store', $ticket->department_scope);
        $this->assertEquals('متجر قطع الهاردوير والأجهزة', $ticket->scope_label_ar);
        $this->assertEquals('open', $ticket->status);
        $this->assertEquals('high', $ticket->priority);

        // 7.2 View tickets list filtered by scope
        $listResponse = $this->actingAs($client)->get(route('portal.tickets', ['scope' => 'hardware_store']));
        $listResponse->assertStatus(200);
        $listResponse->assertSee('متجر قطع الهاردوير والأجهزة');
        $listResponse->assertSee('HW-261027-ABCDE');
        $listResponse->assertSee('استفسار حول تفعيل شهادة ضمان طابعة الكاشير');

        // 7.3 View single ticket thread
        $detailResponse = $this->actingAs($client)->get(route('portal.tickets.show', $ticket));
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee('متجر قطع الهاردوير والأجهزة');
        $detailResponse->assertSee('المرجع: HW-261027-ABCDE');
        $detailResponse->assertSee('استلمنا الجهاز وبحاجة لربط السيريال');

        // 7.4 Admin dashboard reflects the categorized ticket in recent tickets table
        $adminDash = $this->actingAs($admin)->get(route('admin.index'));
        $adminDash->assertStatus(200);
        $adminDash->assertSee('HW-261027-ABCDE');
        $adminDash->assertSee('متجر قطع الهاردوير والأجهزة');
    }
}
