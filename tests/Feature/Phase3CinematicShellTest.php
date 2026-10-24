<?php

namespace Tests\Feature;

use Tests\TestCase;

class Phase3CinematicShellTest extends TestCase
{
    public function test_homepage_renders_with_cinematic_loader_and_icon_popup(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('souly-cinematic-loader', false);
        $response->assertSee('loader-progress-bar', false);
        $response->assertSee('souly-icon-sheet-container', false);
        $response->assertSee('souly-precache-data', false);
        $response->assertSee('souly-loader.js', false);
        $response->assertSee('souly-ui.js', false);
    }

    public function test_mobile_view_renders_bottom_dock_and_souly_ai_orb(): void
    {
        $response = $this->get('/?view=mobile');

        $response->assertStatus(200);
        $response->assertSee('mobile-souly-ai-orb', false);
        $response->assertSee('mobile-dock-tab', false);
        $response->assertSee('سولي AI', false);
        $response->assertSee('محفظتي', false);
    }

    public function test_services_data_is_shared_and_precache_json_is_valid(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewHas('citadelServices');
        $response->assertViewHas('citadelPrecacheJson');
        $response->assertViewHas('citadelWalletBalance');

        $services = $response->viewData('citadelServices');
        $this->assertCount(6, $services);

        $json = json_decode($response->viewData('citadelPrecacheJson'), true);
        $this->assertIsArray($json);
        $this->assertArrayHasKey('icons', $json);
        $this->assertNotEmpty($json['icons']);
    }
}
