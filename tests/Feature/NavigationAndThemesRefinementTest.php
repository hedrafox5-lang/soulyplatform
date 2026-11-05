<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavigationAndThemesRefinementTest extends TestCase
{
    /**
     * 1. Test floating quick dock is strictly rendered ONLY on Homepage.
     */
    public function test_floating_quick_dock_rendered_only_on_homepage_with_visible_labels(): void
    {
        $homeResponse = $this->get('/');
        $homeResponse->assertStatus(200);

        // Assert dock presence & Alpine controller
        $homeResponse->assertSee('soulyFloatingQuickDockController()', false);
        $homeResponse->assertSee('قطاعات سولي (توسيع)', false);

        // Assert visible Arabic service labels in dock
        $homeResponse->assertSee('BrandERP');
        $homeResponse->assertSee('موظفو AI');
        $homeResponse->assertSee('الأمن السيبراني');
        $homeResponse->assertSee('الصيانة الهندسية');
        $homeResponse->assertSee('الاستشارات');
        $homeResponse->assertSee('الأكاديمية');

        // 2. Assert dock is strictly absent from service pages
        $serviceResponse = $this->get('/services/brand-erp');
        $serviceResponse->assertStatus(200);
        $serviceResponse->assertDontSee('soulyFloatingQuickDockController()', false);

        // 3. Assert dock is absent from login
        $loginResponse = $this->get('/login');
        $loginResponse->assertStatus(200);
        $loginResponse->assertDontSee('soulyFloatingQuickDockController()', false);
    }

    /**
     * 2. Test Client Portal theme and top welcoming banner.
     */
    public function test_client_portal_distinct_theme_and_welcome_banner(): void
    {
        $client = User::where('role', 'client')->first() ?? User::factory()->create([
            'role' => 'client',
            'email' => 'client_theme_test@souly.local',
        ]);

        $response = $this->actingAs($client)->get(route('portal.index'));
        $response->assertStatus(200);

        // Assert client theme class
        $response->assertSee('theme-client-portal', false);

        // Assert welcoming banner
        $response->assertSee('بوابة العميل الخاصة — مرحباً بك يا', false);
        $response->assertSee($client->name, false);
        $response->assertSee('ACCOUNT ACTIVE', false);

        // Assert floating dock is absent
        $response->assertDontSee('soulyFloatingQuickDockController()', false);
    }

    /**
     * 3. Test Admin Command Center theme and top tactical status bar.
     */
    public function test_admin_command_distinct_theme_and_tactical_status_bar(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create([
            'role' => 'admin',
            'email' => 'admin_theme_test@souly.local',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.index'));
        $response->assertStatus(200);

        // Assert admin theme class
        $response->assertSee('theme-admin-command', false);

        // Assert tactical status bar
        $response->assertSee('مركز القيادة العليا وإدارة العمليات (وضع المدير العام)', false);
        $response->assertSee('ROOT COMMAND ACTIVE', false);

        // Assert floating dock is absent
        $response->assertDontSee('soulyFloatingQuickDockController()', false);
    }

    /**
     * 4. Test Login page cleanliness and removal of fake demo balance strings.
     */
    public function test_login_page_cleanliness_and_developer_quick_login(): void
    {
        $response = $this->get(route('login'));
        $response->assertStatus(200);

        // Assert fake/messy balance text is completely removed
        $response->assertDontSee('عميل (3,500 EGP)', false);
        $response->assertDontSee('(3,500 EGP)', false);

        // Assert clean authoritative login card
        $response->assertSee('تسجيل الدخول إلى', false);
        $response->assertSee('البريد الإلكتروني', false);
        $response->assertSee('كلمة المرور', false);

        // In local environment, assert discreet developer quick-access bar
        if (app()->isLocal()) {
            $response->assertSee('دخول سريع لبيئة التطوير', false);
            $response->assertSee('[حساب المدير العام]', false);
            $response->assertSee('[حساب عميل تجريبي]', false);
        }
    }
}
