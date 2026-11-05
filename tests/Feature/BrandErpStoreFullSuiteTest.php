<?php

namespace Tests\Feature;

use App\Models\ErpCustomRequest;
use App\Models\ErpOrder;
use App\Models\ErpOrderItem;
use App\Models\ErpSalesProposal;
use App\Models\ErpStoreCatalog;
use App\Models\ErpWishlist;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class BrandErpStoreFullSuiteTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * 1. Test database seeder integrity: 1 core, 16 modules, 500 features, 6 bundles.
     */
    public function test_branderp_database_seeder_counts_and_tiers(): void
    {
        $coreCount = ErpStoreCatalog::where('tier', 'core')->count();
        $moduleCount = ErpStoreCatalog::where('tier', 'module')->count();
        $featureCount = ErpStoreCatalog::where('tier', 'feature')->count();
        $bundleCount = ErpStoreCatalog::where('tier', 'bundle')->count();

        $this->assertEquals(1, $coreCount, 'BrandERP Core must have exactly 1 record');
        $this->assertEquals(16, $moduleCount, 'BrandERP must have exactly 16 major modules');
        $this->assertEquals(500, $featureCount, 'BrandERP must have exactly 500 granular micro-features');
        $this->assertEquals(6, $bundleCount, 'BrandERP must have exactly 6 curated bundles');

        // Verify Core pricing (2,000 EGP)
        $core = ErpStoreCatalog::where('tier', 'core')->first();
        $this->assertNotNull($core);
        $this->assertEquals(2000.00, (float) $core->price);

        // Verify Class-A Enterprise Suite pricing (155,000 EGP)
        $enterprise = ErpStoreCatalog::where('tier', 'bundle')->where('slug', 'erp-bundle-class-a')->first();
        $this->assertNotNull($enterprise);
        $this->assertEquals(155000.00, (float) $enterprise->price);

        // Verify 16 distinct departments among the 500 micro-features
        $departments = ErpStoreCatalog::where('tier', 'feature')->distinct()->pluck('department_ar')->filter();
        $this->assertGreaterThanOrEqual(16, $departments->count());

        // Verify price tiers are compliant (100, 200, 350, 500, 750)
        $prices = ErpStoreCatalog::where('tier', 'feature')->distinct()->pluck('price')->map(fn($p) => (int)$p)->unique()->values()->all();
        foreach ([100, 200, 350, 500, 750] as $expectedPrice) {
            $this->assertContains($expectedPrice, $prices);
        }
    }

    /**
     * 2. Test Standalone BrandERP Store frontend view and 20 Pro Features.
     */
    public function test_store_branderp_page_renders_with_20_creative_features(): void
    {
        $response = $this->get(route('store.branderp'));
        $response->assertStatus(200);

        // Feature 1: Industry Blueprints
        $response->assertSee('سوبرماركت وهايبر ماركت');
        $response->assertSee('صيدليات ومستودعات أدوية');
        $response->assertSee('مطاعم وكافيهات وكافتيريا');
        $response->assertSee('مصانع وورش تصنيع');
        $response->assertSee('محلات ملابس وأحذية');

        // Feature 2: Flash Feature Deal Banner & Timer
        $response->assertSee('عرض الفلاش اليومي 50% خصم');
        $response->assertSee('countdown.hours');

        // Feature 3: Sales Engineer Picks
        $response->assertSee('ترشيحات مهندس المبيعات');

        // Feature 4 & 5: Search & Slang Support
        $response->assertSee('بحث ذكي: شكك، آجل، باركود ميزان، درج، شفت، جرد...');

        // Feature 6: Price Tiers
        $response->assertSee('فئة السعر:');

        // Feature 7: 16 Departments Navigation
        $response->assertSee('المبيعات ونقاط البيع');
        $response->assertSee('المخازن والمستودعات');

        // Feature 8: Client-side virtualization pagination controls
        $response->assertSee('prevPage()');

        // Feature 9: Live UI Preview Modal
        $response->assertSee('previewModalOpen');

        // Feature 10 & 11: 48h Trial & Wishlist
        $response->assertSee('startTrial');
        $response->assertSee('toggleWishlist');

        // Feature 12 & 13: Floating Cart Dock & Quotation Export
        $response->assertSee('سلة المنظومة المخصصة');
        $response->assertSee('تصدير عرض سعر رسمي');

        // Feature 14 & 15: Checkout & Custom Feature Request Form
        $response->assertSee('openCheckoutModal');
        $response->assertSee('اطلب برمجة ميزة خاصة لنشاطك');

        // Feature 16: Multi-tier Smart Upsell
        $response->assertSee('خصم الباقة المجمعة (35%)');
    }

    /**
     * 3. Test Wishlist toggle API endpoint.
     */
    public function test_store_branderp_wishlist_toggle(): void
    {
        $user = User::factory()->create();
        $catalogItem = ErpStoreCatalog::where('tier', 'feature')->first();

        // 1. Add to wishlist
        $response = $this->actingAs($user)->postJson(route('store.branderp.wishlist'), [
            'catalog_item_id' => $catalogItem->id,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'status'  => 'added',
        ]);
        $this->assertDatabaseHas('erp_wishlists', [
            'user_id' => $user->id,
            'catalog_item_id' => $catalogItem->id,
        ]);

        // 2. Remove from wishlist
        $response2 = $this->actingAs($user)->postJson(route('store.branderp.wishlist'), [
            'catalog_item_id' => $catalogItem->id,
        ]);

        $response2->assertStatus(200);
        $response2->assertJson([
            'success' => true,
            'status'  => 'removed',
        ]);
        $this->assertDatabaseMissing('erp_wishlists', [
            'user_id' => $user->id,
            'catalog_item_id' => $catalogItem->id,
        ]);
    }

    /**
     * 4. Test 48-Hour Free Trial API endpoint.
     */
    public function test_store_branderp_start_trial(): void
    {
        $user = User::factory()->create();
        $catalogItem = ErpStoreCatalog::where('tier', 'feature')->first();

        $response = $this->actingAs($user)->postJson(route('store.branderp.trial'), [
            'catalog_item_id' => $catalogItem->id,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseHas('erp_orders', [
            'user_id' => $user->id,
            'is_trial' => true,
        ]);

        $this->assertDatabaseHas('erp_order_items', [
            'catalog_item_id' => $catalogItem->id,
            'price' => 0.00,
        ]);
    }

    /**
     * 5. Test Instant Checkout with License Generation.
     */
    public function test_store_branderp_checkout_order_creation(): void
    {
        $user = User::factory()->create();
        $core = ErpStoreCatalog::where('tier', 'core')->first();
        $feature = ErpStoreCatalog::where('tier', 'feature')->first();

        $payload = [
            'customer_name' => 'شركة النيل للحلول التجارية',
            'customer_phone' => '01012345678',
            'payment_method' => 'direct_transfer',
            'total_amount' => 2200,
            'items' => [
                ['slug' => $core->slug],
                ['slug' => $feature->slug],
            ],
            'notes' => 'تركيب سيرفر محلي مع أجهزة كاشير',
        ];

        $response = $this->actingAs($user)->postJson(route('store.branderp.checkout'), $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'status' => 'pending',
        ]);

        $responseData = $response->json();
        $this->assertNotEmpty($responseData['order_number']);

        $this->assertDatabaseHas('erp_orders', [
            'order_number' => $responseData['order_number'],
            'customer_name' => 'شركة النيل للحلول التجارية',
            'payment_method' => 'direct_transfer',
            'status' => 'pending',
        ]);
    }

    /**
     * 6. Test Custom Feature Request Submission.
     */
    public function test_store_branderp_custom_request_submission(): void
    {
        $payload = [
            'customer_name' => 'محمود العربي',
            'customer_phone' => '01234567890',
            'industry' => 'factory',
            'feature_title' => 'ربط مع ماكينة الليزر لقص المعادن وسحب كمية الصاج آلياً',
            'feature_description' => 'نحتاج إضافة واجهة تسحب قراءات إحداثيات السُمك وتطرحها من رصيد خام الصاج في المخزن مباشرة.',
            'budget_range' => '5,000 - 10,000 ج.م',
        ];

        $response = $this->postJson(route('store.branderp.custom_request'), $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseHas('erp_custom_requests', [
            'customer_name' => 'محمود العربي',
            'feature_title' => 'ربط مع ماكينة الليزر لقص المعادن وسحب كمية الصاج آلياً',
        ]);
    }

    /**
     * 7. Test Quotation Export View.
     */
    public function test_store_branderp_quotation_export(): void
    {
        $core = ErpStoreCatalog::where('tier', 'core')->first();
        $feature = ErpStoreCatalog::where('tier', 'feature')->first();

        $response = $this->get(route('store.branderp.quotation', [
            'items' => $core->slug . ',' . $feature->slug,
            'company' => 'مجموعة الصفا والمروة التجارية',
        ]));

        $response->assertStatus(200);
        $response->assertSee('عرض سعر منظومة BrandERP الموديلار المعتمد');
        $response->assertSee('مجموعة الصفا والمروة التجارية');
        $response->assertSee($core->name_ar);
        $response->assertSee($feature->name_ar);
        $response->assertSee('APPROVED & VERIFIED SOULY CITADEL', false);
    }

    /**
     * 8. Test Client Portal ERP Store Management & Power Meter.
     */
    public function test_client_portal_erp_store_management(): void
    {
        $user = User::factory()->create(['role' => 'client']);

        // Create an ERP Order for this user
        $order = ErpOrder::create([
            'order_number' => 'SOULY-TEST-001',
            'user_id' => $user->id,
            'customer_name' => $user->name,
            'customer_email' => $user->email,
            'customer_phone' => '01000000000',
            'total_amount' => 4500,
            'status' => 'active',
            'payment_method' => 'wallet',
            'license_key' => 'SOULY-ERP-TEST-LIC-KEY-999',
            'is_trial' => false,
            'items_json' => ['erp-core', 'mod-accounting-pro'],
        ]);

        $core = ErpStoreCatalog::where('tier', 'core')->first();
        $module = ErpStoreCatalog::where('tier', 'module')->first();

        ErpOrderItem::create([
            'order_id' => $order->id,
            'catalog_item_id' => $core->id,
            'tier' => 'core',
            'item_code' => $core->code,
            'item_name' => $core->name_ar,
            'price' => $core->price,
        ]);

        ErpOrderItem::create([
            'order_id' => $order->id,
            'catalog_item_id' => $module->id,
            'tier' => 'module',
            'item_code' => $module->code,
            'item_name' => $module->name_ar,
            'price' => $module->price,
        ]);

        $response = $this->actingAs($user)->get(route('portal.erp_store'));

        $response->assertStatus(200);
        $response->assertSee('إدارة منظومة BrandERP والتراخيص');
        $response->assertSee('مؤشر قوة واكتمال BrandERP');
        $response->assertSee('SOULY-ERP-TEST-LIC-KEY-999');
        $response->assertSee('النواة الأساسية (BrandERP Core)');
        $response->assertSee('SOULY-TEST-001');
    }

    /**
     * 9. Test Admin ERP Store Hub and Management Controls.
     */
    public function test_admin_erp_store_hub_and_controls(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = User::factory()->create(['role' => 'client']);

        // 1. Visit Admin ERP Hub
        $response = $this->actingAs($admin)->get(route('admin.erp_store.index'));
        $response->assertStatus(200);
        $response->assertSee('إدارة متجر وأوردرات BrandERP');
        $response->assertSee('الكتالوج والمميزات');
        $response->assertSee('طلبات الشراء والتراخيص');
        $response->assertSee('طلبات البرمجة الخاصة');
        $response->assertSee('إرسال اقتراح مهندس المبيعات');

        // 2. Toggle Catalog Item Active Status
        $feature = ErpStoreCatalog::where('tier', 'feature')->first();
        $initialStatus = $feature->is_active;

        $toggleResponse = $this->actingAs($admin)->postJson(route('admin.erp_store.toggle_catalog', $feature->id), [
            'field' => 'is_active',
        ]);
        $toggleResponse->assertStatus(200);
        $toggleResponse->assertJson(['success' => true]);

        $this->assertEquals(!$initialStatus, (bool) $feature->fresh()->is_active);

        // 3. Update Catalog Item Price & Details
        $updateResponse = $this->actingAs($admin)->post(route('admin.erp_store.update_catalog', $feature->id), [
            'price' => 450,
            'roi_time_saved_ar' => 'توفير 15 ساعة شهرياً',
        ]);
        $updateResponse->assertRedirect();
        $this->assertEquals(450.00, (float) $feature->fresh()->price);

        // 4. Update Order Status & Approve
        $order = ErpOrder::create([
            'order_number' => 'SOULY-TEST-ORD-88',
            'user_id' => $client->id,
            'customer_name' => $client->name,
            'customer_email' => $client->email,
            'customer_phone' => '01112223334',
            'total_amount' => 3000,
            'status' => 'pending',
            'payment_method' => 'direct_transfer',
            'items_json' => ['erp-core'],
        ]);

        $statusResponse = $this->actingAs($admin)->post(route('admin.erp_store.order_status', $order->id), [
            'status' => 'active',
        ]);
        $statusResponse->assertRedirect();
        $this->assertEquals('active', $order->fresh()->status);
        $this->assertNotEmpty($order->fresh()->license_key);

        // 5. Reply to Custom Feature Request
        $customReq = ErpCustomRequest::create([
            'customer_name' => 'سارة حسن',
            'customer_phone' => '01099887766',
            'industry' => 'pharmacy',
            'feature_title' => 'ربط مع الهيئة المصرية للدواء',
            'feature_description' => 'تتبع باركود 2D للأدوية المخدرة',
            'budget_range' => '5,000 - 10,000 ج.م',
            'status' => 'pending',
        ]);

        $replyResponse = $this->actingAs($admin)->post(route('admin.erp_store.custom_reply', $customReq->id), [
            'status' => 'approved',
            'admin_response' => 'تمت الموافقة ودراسة المواصفات وجاهزون للتنفيذ.',
        ]);
        $replyResponse->assertRedirect();
        $this->assertEquals('approved', $customReq->fresh()->status);
        $this->assertEquals('تمت الموافقة ودراسة المواصفات وجاهزون للتنفيذ.', $customReq->fresh()->admin_response);

        // 6. Send Sales Engineer Proposal
        $modules = ErpStoreCatalog::where('tier', 'module')->take(2)->get();

        $proposalResponse = $this->actingAs($admin)->post(route('admin.erp_store.send_proposal'), [
            'user_id' => $client->id,
            'title' => 'عرض حصري: باقة التحول الرقمي الشامل للصيدليات',
            'notes' => 'بناء على حجم عملياتكم، نوصي بتفعيل موديول الوصفات الطبية مع ربط شبكة الفروع.',
            'proposed_items' => $modules->pluck('slug')->toArray(),
            'discount_percent' => 20,
        ]);
        $proposalResponse->assertRedirect();

        $this->assertDatabaseHas('erp_sales_proposals', [
            'user_id' => $client->id,
            'discount_percent' => 20,
        ]);
    }
}
