<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\ErpOrder;
use App\Models\ErpStoreCatalog;
use App\Models\HardwareOrder;
use App\Models\HardwareProduct;
use App\Models\Lead;
use App\Models\Service;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Tests\TestCase;

class MobileSuperAppExperienceTest extends TestCase
{
    /**
     * 1. Test fluid CSS styling, 0px horizontal blowout, and iOS auto-zoom prevention (16px font-size).
     */
    public function test_mobile_app_shell_renders_fluid_css_and_ios_zoom_prevention(): void
    {
        $response = $this->get('/?view=mobile');
        $response->assertStatus(200);

        $content = $response->getContent();

        // Zero horizontal overflow rules
        $this->assertStringContainsString('overflow-x: clip;', $content);
        $this->assertStringContainsString('max-width: 100vw;', $content);

        // iOS auto-zoom prevention: 16px minimum on inputs/selects/textareas for mobile screens
        $this->assertStringContainsString('@media (max-width: 767.98px)', $content);
        $this->assertStringContainsString('input, select, textarea', $content);
        $this->assertStringContainsString('font-size: 16px !important;', $content);

        // Safe area container padding for mobile fixed dock
        $this->assertStringContainsString('mobile-safe-container', $content);
        $this->assertStringContainsString('env(safe-area-inset-bottom', $content);
    }

    /**
     * 2. Test the 3 distinct native bottom app dock and header states (Guest, Client, Admin).
     */
    public function test_mobile_layout_three_distinct_nav_states(): void
    {
        // STATE A: GUEST
        $guestRes = $this->get('/?view=mobile');
        $guestRes->assertStatus(200);
        $guestRes->assertSee('mobile-souly-ai-orb', false);
        $guestRes->assertSee('mobile-dock-tab', false);
        $guestRes->assertSee('الرئيسية', false);
        $guestRes->assertSee('المتاجر', false);
        $guestRes->assertSee('سولي AI', false);
        $guestRes->assertSee('محفظتي', false);
        $guestRes->assertSee('خدماتنا', false);
        $guestRes->assertSee('$store.citadel.installApp()', false); // Header PWA Install Button

        // STATE B: CLIENT (Authenticated Non-Admin)
        $client = User::where('role', 'client')->first();
        if (!$client) {
            $client = User::create([
                'name' => 'عميل تجريبي ممتاز',
                'email' => 'client_test_superapp@souly.local',
                'password' => bcrypt('password'),
                'role' => 'client',
            ]);
            Wallet::create(['user_id' => $client->id, 'balance' => 4500.00, 'currency' => 'EGP']);
        }

        $clientRes = $this->actingAs($client)->get('/?view=mobile');
        $clientRes->assertStatus(200);
        $clientRes->assertSee('mobile-souly-ai-orb', false);
        $clientRes->assertSee('mobile-dock-tab', false);
        $clientRes->assertSee('متاجري', false);
        $clientRes->assertSee('محفظتي', false);
        $clientRes->assertSee('بوابتي', false);

        // STATE C: ADMIN COMMAND CENTER
        $admin = User::where('role', 'admin')->first();
        $adminRes = $this->actingAs($admin)->get('/?view=mobile');
        $adminRes->assertStatus(200);
        $adminRes->assertSee('وضع المدير', false);
        $adminRes->assertSee('mobile-souly-ai-orb', false);
        $adminRes->assertSee('القيادة', false);
        $adminRes->assertSee('الحجوزات', false);
        $adminRes->assertSee('القائمة ⚙️', false);
        $adminRes->assertSee('قائمة عمليات المدير التكتيكية', false);
    }

    /**
     * 3. Test homepage mobile Sector Quick-Story Bar and Single-Card Snap Carousels.
     */
    public function test_mobile_homepage_renders_quick_story_bar_and_single_card_carousels(): void
    {
        $response = $this->get('/?view=mobile');
        $response->assertStatus(200);

        // Sector Quick-Story Bar
        $response->assertSee('القطاعات السيادية الست', false);
        $response->assertSee('اسحب للتنقل السريع', false);

        // Single-card auto/swipe carousel integration
        $response->assertSee('soulyMobileCarousel', false);
        $response->assertSee('snap-center', false);
        $response->assertSee('w-[88%]', false);

        // Mobile tactical action buttons grid
        $response->assertSee('المحاكي وحاسبة العائد', false);
        $response->assertSee('التقليدي vs سولي', false);
    }

    /**
     * 4. Test service landing page sticky mobile sub-bar and single-card carousel.
     */
    public function test_service_landing_mobile_sticky_subbar_and_carousel(): void
    {
        $response = $this->get('/services/brand-erp?view=mobile');
        $response->assertStatus(200);

        // Sticky mobile sub-bar
        $response->assertSee('sticky top-14', false);
        $response->assertSee('open-express-booking', false);

        // Single-card carousel with 6 capability cards
        $response->assertSee('soulyMobileCarousel({ total: 6 })', false);
        $response->assertSee('data-cards-container="capabilities"', false);
    }

    /**
     * 5. Test modal templates transformed into native swipeable bottom sheets on mobile.
     */
    public function test_modals_render_as_native_swipeable_bottom_sheets(): void
    {
        $response = $this->get('/services/brand-erp?view=mobile');
        $response->assertStatus(200);

        $content = $response->getContent();

        // Bottom sheet class tokens
        $this->assertStringContainsString('mobile-bottom-sheet-modal', $content);
        $this->assertStringContainsString('mobile-bottom-sheet-panel', $content);
        $this->assertStringContainsString('max-md:rounded-t-3xl', $content);

        // Mobile drag/grip handle
        $this->assertStringContainsString('bg-slate-600 rounded-full mx-auto', $content);
    }

    /**
     * 6. Test standalone BrandERP and Hardware stores render floating mobile checkout bar at bottom-16.
     */
    public function test_standalone_stores_render_floating_mobile_checkout_bar(): void
    {
        // BrandERP Store
        $erpRes = $this->get('/store/branderp?view=mobile');
        $erpRes->assertStatus(200);
        $erpRes->assertSee('fixed bottom-16', false);
        $erpRes->assertSee('إجمالي المنظومة:', false);
        $erpRes->assertSee('تأكيد وحجز 🚀', false);

        // Hardware Store
        $hwRes = $this->get('/store/hardware?view=mobile');
        $hwRes->assertStatus(200);
        $hwRes->assertSee('fixed bottom-16', false);
        $hwRes->assertSee('إجمالي أجهزة الهاردوير:', false);
        $hwRes->assertSee('مراجعة السلة', false);
    }

    /**
     * 7. Test client portal hardware tracking stepper renders vertical mobile timeline.
     */
    public function test_client_portal_hardware_stepper_renders_vertical_mobile_timeline(): void
    {
        $client = User::where('role', 'client')->first();
        if (!$client) {
            $client = User::create([
                'name' => 'عميل هاردوير',
                'email' => 'client_hw_timeline@souly.local',
                'password' => bcrypt('password'),
                'role' => 'client',
            ]);
        }

        // Ensure at least one hardware order exists for test
        $order = HardwareOrder::first();
        if (!$order) {
            $order = HardwareOrder::create([
                'user_id' => $client->id,
                'order_number' => 'HWO-TEST-99',
                'customer_name' => $client->name,
                'customer_phone' => '01011112222',
                'shipping_address' => 'القاهرة',
                'items_json' => [['sku' => 'POS-01', 'name' => 'شاشة كاشير', 'price' => 5000, 'quantity' => 1]],
                'total_amount' => 5000.00,
                'payment_method' => 'wallet',
                'payment_status' => 'paid',
                'status' => 'pending_review',
            ]);
        }

        $response = $this->actingAs($client)->get('/portal/hardware?view=mobile');
        $response->assertStatus(200);

        // Verifies vertical mobile timeline markup
        $response->assertSee('sm:hidden space-y-5 pr-4 border-r-2 border-slate-800', false);
        $response->assertSee('قيد المراجعة', false);
        $response->assertSee('تأكيد توفر المخزون بالمستودع', false);
    }

    /**
     * 8. Test admin command center tables render tactical mobile action cards.
     */
    public function test_admin_command_tables_render_tactical_mobile_action_cards(): void
    {
        $admin = User::where('role', 'admin')->first();

        // Admin Bookings
        $resBookings = $this->actingAs($admin)->get('/admin/bookings?view=mobile');
        $resBookings->assertStatus(200);
        $resBookings->assertSee('md:hidden space-y-3', false);
        $resBookings->assertSee('تحديث وإسناد ⚙', false);

        // Admin Leads
        $resLeads = $this->actingAs($admin)->get('/admin/leads?view=mobile');
        $resLeads->assertStatus(200);
        $resLeads->assertSee('md:hidden space-y-3', false);
        $resLeads->assertSee('الملف والتواصل', false);

        // Admin Wallets
        $resWallets = $this->actingAs($admin)->get('/admin/wallets?view=mobile');
        $resWallets->assertStatus(200);
        $resWallets->assertSee('md:hidden space-y-3', false);

        // Admin Hardware Store
        $resHw = $this->actingAs($admin)->get('/admin/hardware?tab=products&view=mobile');
        $resHw->assertStatus(200);
        $resHw->assertSee('md:hidden space-y-3', false);
        $resHw->assertSee('تعديل المواصفات والسعر', false);

        // Admin ERP Store
        $resErp = $this->actingAs($admin)->get('/admin/erp-store?tab=catalog&view=mobile');
        $resErp->assertStatus(200);
        $resErp->assertSee('md:hidden space-y-3', false);
        $resErp->assertSee('⚡ فلاش', false);
    }
}
