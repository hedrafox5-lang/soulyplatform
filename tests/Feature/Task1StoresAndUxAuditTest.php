<?php

namespace Tests\Feature;

use App\Models\ErpStoreCatalog;
use App\Models\HardwareOrder;
use App\Models\HardwareProduct;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class Task1StoresAndUxAuditTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * 1. Test floating-quick-dock renders strictly on Homepage ('/') and NOT on restricted pages (/login, /portal, /admin).
     */
    public function test_floating_quick_dock_rendered_only_on_home_and_not_on_restricted_pages(): void
    {
        // 1.1 Homepage renders the dock with Arabic service labels
        $homeResponse = $this->get('/');
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('soulyFloatingQuickDockController()', false);
        $homeResponse->assertSee('BrandERP');
        $homeResponse->assertSee('موظفو AI');
        $homeResponse->assertSee('الأمن السيبراني');
        $homeResponse->assertSee('الصيانة الهندسية');
        $homeResponse->assertSee('الاستشارات');
        $homeResponse->assertSee('الأكاديمية');

        // 1.2 Login page must NOT render floating dock
        $loginResponse = $this->get('/login');
        $loginResponse->assertStatus(200);
        $loginResponse->assertDontSee('soulyFloatingQuickDockController()', false);

        // 1.3 Client Portal must NOT render floating dock
        $client = User::factory()->create(['role' => 'client']);
        $portalResponse = $this->actingAs($client)->get('/portal');
        $portalResponse->assertStatus(200);
        $portalResponse->assertDontSee('soulyFloatingQuickDockController()', false);

        // 1.4 Admin Command Center must NOT render floating dock
        $admin = User::factory()->create(['role' => 'admin']);
        $adminResponse = $this->actingAs($admin)->get('/admin');
        $adminResponse->assertStatus(200);
        $adminResponse->assertDontSee('soulyFloatingQuickDockController()', false);
    }

    /**
     * 2. Test distinct visual theme class isolation (.theme-client-portal on /portal and .theme-admin-command on /admin).
     */
    public function test_theme_class_isolation_between_client_portal_and_admin_command(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        $admin = User::factory()->create(['role' => 'admin']);

        // Client Portal
        $portalResponse = $this->actingAs($client)->get(route('portal.index'));
        $portalResponse->assertStatus(200);
        $portalResponse->assertSee('theme-client-portal', false);
        $this->assertStringContainsString('theme-client-portal', $portalResponse->getContent());
        $this->assertStringNotContainsString('theme-admin-command', substr($portalResponse->getContent(), strpos($portalResponse->getContent(), '<body'), 200));

        // Admin Command Center
        $adminResponse = $this->actingAs($admin)->get(route('admin.index'));
        $adminResponse->assertStatus(200);
        $adminResponse->assertSee('theme-admin-command', false);
        $this->assertStringContainsString('theme-admin-command', $adminResponse->getContent());
        $this->assertStringNotContainsString('theme-client-portal', substr($adminResponse->getContent(), strpos($adminResponse->getContent(), '<body'), 200));
    }

    /**
     * 3. Test Navigation & Header Integration for Guest, Client and Admin states.
     */
    public function test_header_and_navigation_integration_for_all_states(): void
    {
        // 3.1 Guest State in Header: Sees "متجر BrandERP" and "متجر الهاردوير"
        $guestResponse = $this->get(route('home'));
        $guestResponse->assertStatus(200);
        $guestResponse->assertSee('متجر BrandERP');
        $guestResponse->assertSee('متجر الهاردوير');
        $guestResponse->assertSee('/store/branderp');
        $guestResponse->assertSee('/store/hardware');

        // 3.2 Client State: In header and portal tabs
        $client = User::factory()->create(['role' => 'client']);
        $portalResponse = $this->actingAs($client)->get(route('portal.index'));
        $portalResponse->assertStatus(200);

        // Portal navigation tabs in specified sequence
        $portalResponse->assertSee('لوحة حسابي');
        $portalResponse->assertSee('منظومتي في BrandERP');
        $portalResponse->assertSee('أوردرات الهاردوير');
        $portalResponse->assertSee('حجوزاتي');
        $portalResponse->assertSee('محفظتي');
        $portalResponse->assertSee('تذاكر الدعم');

        // 3.3 Admin Command Header & Dropdown
        $admin = User::factory()->create(['role' => 'admin']);
        $adminResponse = $this->actingAs($admin)->get(route('admin.index'));
        $adminResponse->assertStatus(200);
        $adminResponse->assertSee('إدارة متجر BrandERP (500 ميزة)');
        $adminResponse->assertSee('إدارة متجر الهاردوير والأوردرات');
        $adminResponse->assertSee('/admin/erp-store');
        $adminResponse->assertSee('/admin/hardware');
    }

    /**
     * 4. Test erp_store_catalog contains at least 500 micro-features, 16 modules, and 2,000 EGP Core.
     */
    public function test_erp_store_catalog_has_500_micro_features_16_modules_and_2000_egp_core(): void
    {
        $featuresCount = ErpStoreCatalog::where('tier', 'feature')->count();
        $this->assertGreaterThanOrEqual(500, $featuresCount, 'BrandERP must contain at least 500 micro-features');

        $modulesCount = ErpStoreCatalog::where('tier', 'module')->count();
        $this->assertGreaterThanOrEqual(16, $modulesCount, 'BrandERP must contain at least 16 major modules');

        $coreItem = ErpStoreCatalog::where('tier', 'core')->first();
        $this->assertNotNull($coreItem, 'BrandERP Core item must exist');
        $this->assertEquals(2000.00, (float) $coreItem->price, 'BrandERP Core must be priced at 2,000 EGP');
    }

    /**
     * 5. Test /store/branderp and /store/hardware load with 200 OK and functional checkout endpoints.
     */
    public function test_standalone_stores_load_and_support_filtering_and_checkout_endpoints(): void
    {
        // 5.1 Standalone BrandERP Store
        $erpStoreResponse = $this->get('/store/branderp');
        $erpStoreResponse->assertStatus(200);
        $erpStoreResponse->assertSee('2,000');
        $erpStoreResponse->assertSee('سوبرماركت وهايبر ماركت');

        // BrandERP Checkout validation
        $erpBadCheckout = $this->postJson('/store/branderp/checkout', []);
        $erpBadCheckout->assertStatus(422);

        // 5.2 Standalone Hardware Store
        $hwStoreResponse = $this->get('/store/hardware');
        $hwStoreResponse->assertStatus(200);
        $hwStoreResponse->assertSee('كافة الأجهزة والمعدات');
        $hwStoreResponse->assertSee('متوافق 100% مع منظومة BrandERP');

        // Hardware Checkout validation
        $hwBadCheckout = $this->postJson('/store/hardware/checkout', []);
        $hwBadCheckout->assertStatus(422);

        // Hardware Checkout success
        $sampleProduct = HardwareProduct::active()->firstOrFail();
        $hwGoodCheckout = $this->postJson('/store/hardware/checkout', [
            'customer_name'    => 'مؤسسة الاختبار الشامل',
            'customer_phone'   => '01011223344',
            'shipping_address' => 'القاهرة، مدينة نصر',
            'payment_method'   => 'transfer',
            'items'            => [
                ['id' => $sampleProduct->id, 'quantity' => 1]
            ],
        ]);
        $hwGoodCheckout->assertStatus(200);
        $hwGoodCheckout->assertJson(['success' => true]);
        $this->assertNotEmpty($hwGoodCheckout->json('order_number'));
    }

    /**
     * 6. Test Admin CRUD operations on /admin/hardware and /admin/erp-store.
     */
    public function test_admin_crud_operations_on_hardware_and_erp_store(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // 6.1 Admin ERP Store: update item and toggle
        $catalogItem = ErpStoreCatalog::where('tier', 'feature')->firstOrFail();
        $updateErpResponse = $this->actingAs($admin)->post("/admin/erp-store/catalog/{$catalogItem->id}/update", [
            'price' => 420.00,
            'roi_time_saved_ar' => 'توفير 18 ساعة شهرياً',
        ]);
        $updateErpResponse->assertRedirect();
        $this->assertEquals(420.00, (float) $catalogItem->fresh()->price);

        $initialActive = (bool) $catalogItem->is_active;
        $toggleErpResponse = $this->actingAs($admin)->postJson("/admin/erp-store/catalog/{$catalogItem->id}/toggle", [
            'field' => 'is_active',
        ]);
        $toggleErpResponse->assertStatus(200);
        $this->assertEquals(!$initialActive, (bool) $catalogItem->fresh()->is_active);

        // 6.2 Admin Hardware: product CRUD
        $hwCreateResponse = $this->actingAs($admin)->post('/admin/hardware/products', [
            'sku'             => 'AUDIT-HW-TEST01',
            'name_ar'         => 'وحدة تحكم باركود لاسلكية للاختبار',
            'category'        => 'pos_equipment',
            'brand'           => 'Souly Audit Lab',
            'price'           => 1850.00,
            'stock_qty'       => 15,
            'warranty_months' => 24,
            'specs_raw'       => "التردد: 2.4GHz Wireless\nالمدى: 50 متر",
        ]);
        $hwCreateResponse->assertRedirect();

        $createdProduct = HardwareProduct::where('sku', 'AUDIT-HW-TEST01')->firstOrFail();
        $this->assertEquals(1850.00, (float) $createdProduct->price);

        // Update hardware product
        $hwUpdateResponse = $this->actingAs($admin)->post("/admin/hardware/products/{$createdProduct->id}", [
            'sku'             => 'AUDIT-HW-TEST01',
            'name_ar'         => 'وحدة تحكم باركود لاسلكية للاختبار المحدثة',
            'category'        => 'pos_equipment',
            'brand'           => 'Souly Audit Lab',
            'price'           => 1950.00,
            'stock_qty'       => 20,
            'warranty_months' => 36,
        ]);
        $hwUpdateResponse->assertRedirect();
        $this->assertEquals(1950.00, (float) $createdProduct->fresh()->price);
        $this->assertEquals(36, $createdProduct->fresh()->warranty_months);

        // Hardware Order status update
        $order = HardwareOrder::create([
            'order_number'        => 'HW-AUDIT-ORDER-001',
            'user_id'             => $admin->id,
            'customer_name'       => 'عميل الاختبار',
            'customer_phone'      => '01000000000',
            'shipping_address'    => 'القاهرة',
            'items_json'          => [['sku' => 'AUDIT-HW-TEST01']],
            'total_amount'        => 1950.00,
            'status'              => 'pending_review',
        ]);

        $statusUpdate = $this->actingAs($admin)->post("/admin/hardware/orders/{$order->id}/status", [
            'status'               => 'out_for_delivery',
            'serial_numbers_notes' => 'SN-AUDIT-998877',
        ]);
        $statusUpdate->assertRedirect();
        $this->assertEquals('out_for_delivery', $order->fresh()->status);
        $this->assertEquals('SN-AUDIT-998877', $order->fresh()->serial_numbers_notes);

        // Delete hardware product
        $deleteHw = $this->actingAs($admin)->delete("/admin/hardware/products/{$createdProduct->id}");
        $deleteHw->assertRedirect();
        $this->assertNull(HardwareProduct::find($createdProduct->id));
    }

    /**
     * 7. Test Support Ticket creation properly persists selected department_scope.
     */
    public function test_support_ticket_creation_properly_persists_department_scope(): void
    {
        $client = User::factory()->create(['role' => 'client']);

        // 7.1 Hardware Store scope ticket
        $hwTicketPayload = [
            'subject'          => 'طلب استبدال سيريال ضمان الهاردوير',
            'department_scope' => 'hardware_store',
            'reference_code'   => 'HW-ORDER-8822',
            'priority'         => 'urgent',
            'message'          => 'يرجى مراجعة تفعيل الضمان للجهاز المذكور بالسيريال.',
        ];

        $hwResponse = $this->actingAs($client)->post(route('portal.tickets.store'), $hwTicketPayload);
        $hwResponse->assertRedirect();

        $hwTicket = Ticket::where('reference_code', 'HW-ORDER-8822')->firstOrFail();
        $this->assertEquals('hardware_store', $hwTicket->department_scope);
        $this->assertEquals('متجر قطع الهاردوير والأجهزة', $hwTicket->scope_label_ar);

        // 7.2 BrandERP Store scope ticket
        $erpTicketPayload = [
            'subject'          => 'استفسار ترخيص موديول الصيدليات',
            'department_scope' => 'branderp_store',
            'reference_code'   => 'LIC-ERP-PHARM-01',
            'priority'         => 'medium',
            'message'          => 'كيفية ربط مفتاح الترخيص مع الفاتورة الإلكترونية.',
        ];

        $erpResponse = $this->actingAs($client)->post(route('portal.tickets.store'), $erpTicketPayload);
        $erpResponse->assertRedirect();

        $erpTicket = Ticket::where('reference_code', 'LIC-ERP-PHARM-01')->firstOrFail();
        $this->assertEquals('branderp_store', $erpTicket->department_scope);
        $this->assertEquals('منظومة ومتجر BrandERP', $erpTicket->scope_label_ar);
    }
}
