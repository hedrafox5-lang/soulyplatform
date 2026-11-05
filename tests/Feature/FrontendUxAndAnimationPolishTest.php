<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FrontendUxAndAnimationPolishTest extends TestCase
{
    /**
     * 1. Test that public views include Context Menu, Command Palette, and Notification Center.
     */
    public function test_homepage_includes_all_interactive_cyber_hud_modules(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);

        // Loading bar & UI script
        $response->assertSee('souly-ui.js', false);

        // Context Menu
        $response->assertSee('souly-context-menu', false);
        $response->assertSee('$store.citadel.contextMenuOpen', false);

        // Omni-Search Command Palette
        $response->assertSee('souly-command-palette', false);
        $response->assertSee('⌘K', false);

        // Notification Center
        $response->assertSee('souly-notification-center', false);
        $response->assertSee('notificationCenterOpen', false);

        // Reactive Toast
        $response->assertSee('toastVisible', false);
    }

    /**
     * 2. Test that industry and activity badges use glowing cyber-cyan styles instead of dead black backgrounds.
     */
    public function test_activity_and_industry_badges_have_no_dead_black_backgrounds(): void
    {
        $homeResponse = $this->get('/');
        $homeResponse->assertStatus(200);

        // Verify the old harsh style is gone from the activity badges
        $homeResponse->assertDontSee('px-1.5 py-0.5 rounded bg-obsidian-base text-slate-400', false);
        // Verify the new cyber-cyan glowing badge is present
        $homeResponse->assertSee('bg-cyber-cyan/15 text-cyber-cyan border border-cyber-cyan/30', false);

        // Service Show page
        $serviceResponse = $this->get('/services/brand-erp');
        $serviceResponse->assertStatus(200);
        $serviceResponse->assertDontSee('px-1.5 py-0.5 rounded bg-obsidian-base text-slate-400', false);
        $serviceResponse->assertSee('bg-cyber-cyan/15 text-cyber-cyan border border-cyber-cyan/30', false);
    }

    /**
     * 3. Test that desktop layout renders a clean sticky cyber-glass header without sidebar clutter.
     */
    public function test_desktop_layout_contains_collapsible_hud_dock(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);

        // Assert sidebar is eliminated
        $response->assertDontSee('$store.citadel.sidebarCollapsed', false);
        $response->assertDontSee('$store.citadel.toggleSidebar()', false);

        // Assert clean sticky header & role-aware states
        $response->assertSee('fixed top-0 inset-x-0 z-50', false);
        $response->assertSee('backdrop-blur-xl', false);
        $response->assertSee('الصفحة الرئيسية', false);
        $response->assertSee('خدمات سولي', false);
    }

    /**
     * 4. Test that context menu displays appropriate actions according to user role.
     */
    public function test_context_menu_renders_differently_for_guest_and_authenticated_users(): void
    {
        // 1. Guest
        $guestResponse = $this->get('/');
        $guestResponse->assertSee('ضيف سيادي', false);
        $guestResponse->assertSee('نسخ رابط الصفحة الحالية', false);

        // 2. Client User
        $client = User::where('role', 'client')->first();
        $clientResponse = $this->actingAs($client)->get('/');
        $clientResponse->assertSee('بوابة العميل والمحفظة', false);
        $clientResponse->assertSee('حجز ومتابعة الخدمات', false);
        $clientResponse->assertSee('تذاكر الدعم الهندسي', false);

        // 3. Admin User
        $admin = User::where('role', 'admin')->first();
        $adminResponse = $this->actingAs($admin)->get('/');
        $adminResponse->assertSee('مركز القيادة Admin HQ', false);
        $adminResponse->assertSee('اعتماد شحن المحافظ', false);
        $adminResponse->assertSee('خزينة الردود الذكية', false);
    }

    /**
     * 5. Test that command palette contains all 6 core services and navigation items.
     */
    public function test_command_palette_contains_services_and_packages_data(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);

        $response->assertSee('نظام BrandERP المتكامل', false);
        $response->assertSee('موظفو ووكلاء الذكاء الاصطناعي', false);
        $response->assertSee('التدريع السيبراني والتحقيقات الجنائية', false);
        $response->assertSee('الصيانة الهندسية للأجهزة والشبكات', false);
        $response->assertSee('أكاديمية سولي للتعليم التقني', false);
        $response->assertSee('الاستشارات وتطوير الأعمال', false);
    }

    /**
     * 6. Test that UltimatePOS is strictly forbidden and nowhere in rendered views or database,
     * and that Arabic typography strictly enforces Cairo and Tahoma.
     */
    public function test_ultimatepos_is_forbidden_and_fonts_strictly_enforce_cairo_and_tahoma(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);

        // Assert UltimatePOS is nowhere in the page
        $this->assertStringNotContainsStringIgnoringCase('UltimatePOS', $response->getContent());
        $this->assertStringNotContainsStringIgnoringCase('Ultimate_POS', $response->getContent());

        // Assert typography uses Cairo and Tahoma, and Readex Pro is removed from font imports
        $response->assertSee('Cairo', false);
        $response->assertSee('Tahoma', false);
        $response->assertDontSee('Readex+Pro', false);

        // Check BrandERP landing page
        $erpResponse = $this->get('/services/brand-erp');
        $erpResponse->assertStatus(200);
        $this->assertStringNotContainsStringIgnoringCase('UltimatePOS', $erpResponse->getContent());

        // Check official emblem artwork is embedded
        $response->assertSee('souly-emblem-retina.png', false);
    }

    /**
     * 7. Test desktop sticky header and its 3 distinct states: Guest, Client, and Admin.
     */
    public function test_desktop_header_three_distinct_user_states_and_sticky_shell(): void
    {
        // 1. Guest State (State A)
        $guestResponse = $this->get('/');
        $guestResponse->assertStatus(200);
        $guestResponse->assertSee('fixed top-0 inset-x-0 z-50', false);
        $guestResponse->assertSee('backdrop-blur-xl bg-[#05050A]/85 border-b border-[#1C1F36]', false);
        $guestResponse->assertSee('الصفحة الرئيسية', false);
        $guestResponse->assertSee('خدمات سولي', false);
        $guestResponse->assertSee('تحدث مع سولي AI', false);
        $guestResponse->assertSee('تسجيل الدخول', false);
        $guestResponse->assertSee('احجز الآن / استشارة', false);
        $guestResponse->assertDontSee('المحفظة:', false);
        $guestResponse->assertDontSee('إدارة المنصة ⚙️', false);

        // 2. Client State (State B)
        $client = User::where('role', 'client')->first();
        $clientResponse = $this->actingAs($client)->get('/');
        $clientResponse->assertStatus(200);
        $clientResponse->assertSee('لوحة حسابي', false);
        $clientResponse->assertSee('حجوزاتي', false);
        $clientResponse->assertSee('الدعم الفني', false);
        $clientResponse->assertSee('المحفظة:', false);
        $clientResponse->assertSee('ج.م', false);
        $clientResponse->assertSee('تسجيل الخروج', false);
        $clientResponse->assertDontSee('إدارة المنصة ⚙️', false);

        // 3. Admin State (State C)
        $admin = User::where('role', 'admin')->first();
        $adminResponse = $this->actingAs($admin)->get('/');
        $adminResponse->assertStatus(200);
        $adminResponse->assertSee('غرفة العمليات', false);
        $adminResponse->assertSee('الحجوزات', false);
        $adminResponse->assertSee('العملاء CRM', false);
        $adminResponse->assertSee('المالية', false);
        $adminResponse->assertSee('إدارة المنصة ⚙️', false);
        $adminResponse->assertSee('إدارة الخدمات والباقات', false);
        $adminResponse->assertSee('خزينة الردود', false);
        $adminResponse->assertSee('إعدادات النظام', false);
    }

    /**
     * 8. Test that Unified Button Design System and Top Cyber Loading Indicator are rendered properly.
     */
    public function test_unified_button_design_system_and_top_cyber_loading_indicator(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);

        // Check CSS Button classes in master stylesheet
        $response->assertSee('.btn-souly-primary', false);
        $response->assertSee('.btn-souly-glass', false);
        $response->assertSee('.btn-souly-outline', false);
        $response->assertSee('.btn-souly-compact', false);
        $response->assertSee('soulyScannerPulse', false);

        // Check button sizing metrics
        $response->assertSee('min-width: 140px', false);
        $response->assertSee('min-width: 100px !important', false);

        // Check guest buttons use standard compact class
        $response->assertSee('btn-souly-outline btn-souly-compact', false);
        $response->assertSee('btn-souly-primary btn-souly-compact', false);

        // Ensure no experimental border-image hacks exist in rendered output
        $this->assertStringNotContainsString('border-image:', $response->getContent());
    }
}
