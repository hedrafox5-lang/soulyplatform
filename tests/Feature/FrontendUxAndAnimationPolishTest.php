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
     * 3. Test that desktop sidebar contains collapse controls and state management.
     */
    public function test_desktop_layout_contains_collapsible_hud_dock(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);

        $response->assertSee('$store.citadel.sidebarCollapsed', false);
        $response->assertSee('$store.citadel.toggleSidebar()', false);
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
}
