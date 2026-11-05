<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class Phase9LogoAndPwaTest extends TestCase
{
    /**
     * 1. Test that the Cyber-Falcon logo component renders with all size tokens and contains all 6 core visual elements.
     */
    public function test_cyber_falcon_logo_component_renders_cleanly_with_all_size_tokens(): void
    {
        $sizes = ['xs', 'sm', 'md', 'lg', 'hero'];

        foreach ($sizes as $size) {
            $html = Blade::render("<x-souly-logo size='{$size}' :showText='true' />");

            $this->assertStringContainsString('viewBox="0 0 500 500"', $html);
            $this->assertStringContainsString('souly-hud-radar', $html);
            $this->assertStringContainsString('souly-hud-cards', $html);
            $this->assertStringContainsString('souly-falcon-head', $html);
            $this->assertStringContainsString('souly-cyber-eye', $html);
            $this->assertStringContainsString('souly-base-system', $html);
            $this->assertStringContainsString('SOULY', $html);
            $this->assertStringContainsString('CITADEL', $html);
            $this->assertStringContainsString('#00F5D4', $html);
        }

        // Test without text
        $noTextHtml = Blade::render("<x-souly-logo size='hero' :showText='false' />");
        $this->assertStringContainsString('viewBox="0 0 500 500"', $noTextHtml);
        $this->assertStringNotContainsString('SOULYEG.ONLINE // SOVEREIGN CYBER CITADEL', $noTextHtml);
    }

    /**
     * 2. Test that manifest.json is a valid, well-formed PWA manifest with Arabic RTL configuration.
     */
    public function test_manifest_json_is_valid_with_arabic_and_pwa_fields(): void
    {
        $manifestPath = public_path('manifest.json');
        $this->assertFileExists($manifestPath);

        $content = file_get_contents($manifestPath);
        $json = json_decode($content, true);

        $this->assertIsArray($json);
        $this->assertEquals('ar', $json['lang']);
        $this->assertEquals('rtl', $json['dir']);
        $this->assertStringContainsString('قلعة سولي الرقمية', $json['name']);
        $this->assertEquals('سولي', $json['short_name']);
        $this->assertEquals('standalone', $json['display']);
        $this->assertEquals('#00F5D4', $json['theme_color']);
        $this->assertEquals('#05050A', $json['background_color']);

        // Check shortcuts
        $this->assertArrayHasKey('shortcuts', $json);
        $this->assertCount(4, $json['shortcuts']);
        $urls = array_column($json['shortcuts'], 'url');
        $this->assertContains('/services', $urls);
        $this->assertContains('/portal/wallet', $urls);
        $this->assertContains('/?open_ai=1', $urls);
        $this->assertContains('/portal', $urls);

        // Check icons
        $this->assertArrayHasKey('icons', $json);
        $this->assertGreaterThanOrEqual(2, count($json['icons']));
    }

    /**
     * 3. Test that the Service Worker script is present and contains caching strategies.
     */
    public function test_service_worker_file_exists_and_contains_pwa_lifecycle(): void
    {
        $swPath = public_path('sw.js');
        $this->assertFileExists($swPath);

        $content = file_get_contents($swPath);
        $this->assertStringContainsString('souly-citadel', $content);
        $this->assertStringContainsString("addEventListener('install'", $content);
        $this->assertStringContainsString("addEventListener('activate'", $content);
        $this->assertStringContainsString("addEventListener('fetch'", $content);
        $this->assertStringContainsString('skipWaiting()', $content);
        $this->assertStringContainsString('clients.claim()', $content);
    }

    /**
     * 4. Test that public and auth layouts contain PWA meta tags and Cyber-Falcon logo.
     */
    public function test_layouts_contain_pwa_meta_tags_and_falcon_logo(): void
    {
        // 1. Homepage
        $homeResponse = $this->get('/');
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('manifest.json', false);
        $homeResponse->assertSee('apple-touch-icon', false);
        $homeResponse->assertSee('favicon.ico', false);
        $this->assertFileExists(public_path('favicon.ico'));
        $this->assertGreaterThan(1000, filesize(public_path('favicon.ico')));
        $homeResponse->assertSee('theme-color', false);
        $homeResponse->assertSee('#00F5D4', false);
        $homeResponse->assertSee('souly-hud-radar', false);

        // 2. Login Page
        $loginResponse = $this->get('/login');
        $loginResponse->assertStatus(200);
        $loginResponse->assertSee('souly-falcon-head', false);

        // 3. Register Page
        $registerResponse = $this->get('/register');
        $registerResponse->assertStatus(200);
        $registerResponse->assertSee('souly-falcon-head', false);
    }

    /**
     * 5. Test that mobile layout includes PWA install button and iOS guidance modal.
     */
    public function test_mobile_layout_contains_pwa_install_elements(): void
    {
        $response = $this->get('/?view=mobile');
        $response->assertStatus(200);

        // Install button in mobile header
        $response->assertSee('$store.citadel.installApp()', false);
        // iOS guide modal
        $response->assertSee('showIosInstallModal', false);
        $response->assertSee('تثبيت تطبيق قلعة سولي', false);
        $response->assertSee('Add to Home Screen', false);
    }
}
