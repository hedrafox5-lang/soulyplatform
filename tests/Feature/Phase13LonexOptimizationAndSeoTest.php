<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\ServicePackage;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Tests\TestCase;

class Phase13LonexOptimizationAndSeoTest extends TestCase
{
    /**
     * Test the 1-command deployment helper executes cleanly with code 0.
     */
    public function test_souly_deploy_command_executes_successfully(): void
    {
        $this->artisan('souly:deploy')
            ->expectsOutputToContain('SOULY PLATFORM DEPLOYMENT COMPLETE')
            ->expectsOutputToContain('Lonex 5% CPU Limit & ModSecurity Compliance Check')
            ->assertExitCode(0);
    }

    /**
     * Test root and public .htaccess exist with security hardening and caching rules.
     */
    public function test_root_and_public_htaccess_exist_with_hardening_and_caching_directives(): void
    {
        $rootHtaccess = base_path('.htaccess');
        $publicHtaccess = public_path('.htaccess');

        $this->assertFileExists($rootHtaccess);
        $this->assertFileExists($publicHtaccess);

        $rootContent = file_get_contents($rootHtaccess);
        $this->assertStringContainsString('.env', $rootContent);
        $this->assertStringContainsString('RewriteRule ^(.*)$ public/$1', $rootContent);

        $publicContent = file_get_contents($publicHtaccess);
        $this->assertStringContainsString('mod_deflate.c', $publicContent);
        $this->assertStringContainsString('mod_expires.c', $publicContent);
        $this->assertStringContainsString('ExpiresByType text/css', $publicContent);
        $this->assertStringContainsString('X-Content-Type-Options "nosniff"', $publicContent);
    }

    /**
     * Test master layout renders Arabic SEO meta, OpenGraph tags, and JSON-LD structured data.
     */
    public function test_master_layout_renders_arabic_seo_meta_opengraph_and_json_ld_schema(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);

        // SEO Meta
        $response->assertSee('<meta name="description"', false);
        $response->assertSee('سولي', false);

        // OpenGraph
        $response->assertSee('<meta property="og:site_name" content="سولي">', false);
        $response->assertSee('<meta property="og:type" content="website">', false);
        $response->assertSee('<meta property="og:image"', false);

        // JSON-LD Schema
        $response->assertSee('application/ld+json', false);
        $response->assertSee('"@type": "Organization"', false);
        $response->assertSee('"telephone": "+201090623326"', false);

        // Brand rules: no citadel or ultimatepos
        $content = $response->getContent();
        $this->assertStringNotContainsStringIgnoringCase('UltimatePOS', $content);
        $this->assertStringNotContainsString('ديجيتال سيتادل', $content);
    }

    /**
     * Test catalog caching in HomeController and automatic invalidation in AdminServiceController.
     */
    public function test_home_catalog_caching_and_automatic_invalidation(): void
    {
        Cache::forget('souly_catalog_v1_ids');

        // 1. Visit homepage -> should warm up cache
        $this->get('/');
        $this->assertTrue(Cache::has('souly_catalog_v1_ids'));

        // 2. Admin creates a new package -> should invalidate the cache automatically
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);
        $service = Service::firstOrFail();

        $this->actingAs($admin)->post(route('admin.services.packages.store', $service->id), [
            'name' => 'باقة تجريبية كاش اختبار',
            'slug' => 'test-pkg-' . Str::lower(Str::random(6)),
            'billing_type' => 'monthly',
            'price' => 999.00,
            'warranty_days' => 30,
            'features' => "ميزة 1\nميزة 2",
        ]);

        // Verify cache was cleared automatically
        $this->assertFalse(Cache::has('souly_catalog_v1_ids'));

        // Clean up created test package to preserve seeded catalog count
        ServicePackage::where('slug', 'like', 'test-pkg-%')->delete();
    }

    /**
     * Test ModSecurity safe AJAX endpoints accept clean JSON payloads without issues.
     */
    public function test_modsecurity_safe_ajax_endpoints_accept_clean_json(): void
    {
        $response = $this->postJson(route('api.ai.chat'), [
            'message' => 'مرحباً، ما هي باقات الصيانة الهندسية المتاحة؟',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'reply',
            'conversation_id',
        ]);
    }

    /**
     * Test /run-souly-deploy-now endpoint is secured and requires authorization.
     */
    public function test_run_souly_deploy_now_route_is_secured_against_unauthorized_access(): void
    {
        // 1. Guest in testing environment (non-local) should be forbidden (403)
        $guestResponse = $this->get('/run-souly-deploy-now');
        $guestResponse->assertStatus(403);

        // 2. Client role user should also be forbidden (403)
        $client = User::where('role', 'client')->first();
        $clientResponse = $this->actingAs($client)->get('/run-souly-deploy-now');
        $clientResponse->assertStatus(403);

        // 3. Admin user should be authorized (200)
        $admin = User::where('role', 'admin')->first();
        $adminResponse = $this->actingAs($admin)->get('/run-souly-deploy-now');
        $adminResponse->assertStatus(200);
        $adminResponse->assertSee('تم تنفيذ أمر النشر');
    }
}
