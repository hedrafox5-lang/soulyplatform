<?php

namespace Tests\Feature;

use App\Models\Service;
use Tests\TestCase;

class ServicesShowMaxCardsAndThemesTest extends TestCase
{
    /**
     * Test all 6 service landing pages have distinct cyber color identities.
     */
    public function test_all_six_service_pages_have_distinct_cyber_color_identities(): void
    {
        $expectedThemes = [
            'brand-erp' => '#00F5D4',           // Cyber Cyan
            'ai-agents' => '#9D4EDD',           // Neon Purple
            'cybersecurity' => '#FF2A55',       // Crimson Shield
            'maintenance' => '#FFB800',         // Cyber Gold
            'business-consulting' => '#10B981', // Emerald Matrix
            'academy' => '#3B82F6',             // Electric Sapphire
        ];

        foreach ($expectedThemes as $slug => $color) {
            $response = $this->get("/services/{$slug}");
            $response->assertStatus(200);

            // Assert that the distinct cyber color appears in the page markup
            $content = $response->getContent();
            $this->assertStringContainsString(
                $color,
                $content,
                "Service [{$slug}] should display its distinct cyber color [{$color}]."
            );
        }
    }

    /**
     * Test BrandERP service page showcases the 4-tier modular pricing architecture and store links.
     */
    public function test_branderp_page_showcases_revolutionary_modular_pricing_and_store_links(): void
    {
        $response = $this->get('/services/brand-erp');
        $response->assertStatus(200);

        // 1. Tier 1: Core Base
        $response->assertSee('2,000');
        $response->assertSee('النواة الأساسية');
        $response->assertSee('TIER 1');

        // 2. Tier 2: 16 Major Modules
        $response->assertSee('16 موديول');
        $response->assertSee('TIER 2');

        // 3. Tier 3: 500 Micro-Features Store
        $response->assertSee('500 ميزة');
        $response->assertSee('TIER 3');

        // 4. Tier 4: Enterprise Class-A Suite
        $response->assertSee('155,000');
        $response->assertSee('Class-A');
        $response->assertSee('TIER 4');

        // 5. Links to standalone store and modal triggers
        $response->assertSee(route('store.branderp'));
        $response->assertSee('branderp-store-open');
        $response->assertSee('branderp-roi-open');
    }

    /**
     * Test standalone BrandERP store route returns 200 and renders full interactive workspace.
     */
    public function test_standalone_branderp_store_page_loads_and_has_interactive_builder(): void
    {
        $response = $this->get('/store/branderp');
        $response->assertStatus(200);

        $response->assertSee('BrandERP');
        $response->assertSee('الموديلار الشامل');
        $response->assertSee('CORE BASE · TIER 1');
        $response->assertSee('2,000');
        $response->assertSee('سلة المنظومة المخصصة');
        $response->assertSee('موديولاً الرئيسية');
        $response->assertSee('ميزة دقيقة');
        $response->assertSee('باقات المؤسسات');
    }

    /**
     * Test strict "Max 6 Cards" rule on service page flow and 0ms Capabilities Explorer Modal.
     */
    public function test_max_six_cards_rule_strictly_enforced_on_service_page_flow(): void
    {
        $services = Service::where('is_active', true)->with('icons')->get();

        foreach ($services as $service) {
            $response = $this->get("/services/{$service->slug}");
            $response->assertStatus(200);

            $content = $response->getContent();

            // Count the number of on-page capability preview cards rendered in the main flow
            $previewCardMatches = preg_match_all('/data-icon-card-preview/i', $content);

            // Must NOT exceed 6 cards on page flow!
            $this->assertLessThanOrEqual(
                6,
                $previewCardMatches,
                "Service [{$service->slug}] must display at most 6 cards on the main page flow, but found {$previewCardMatches}."
            );

            // If the service has more than 6 icons, it must render exactly 6 on page flow and show the modal trigger
            if ($service->icons->count() > 6) {
                $this->assertEquals(
                    6,
                    $previewCardMatches,
                    "Service [{$service->slug}] has {$service->icons->count()} icons; it must display exactly 6 preview cards on page flow."
                );

                // Check trigger banner and button
                $response->assertSee('استكشف كافة القدرات والمميزات');
                $response->assertSee('explorerModalOpen = true');
            }

            // Fixed-height Scrollable Capabilities Explorer Modal must be present in DOM
            $this->assertStringContainsString('id="modal-capabilities-explorer"', $content);
            $this->assertStringContainsString('max-h-[80vh]', $content);
            $this->assertStringContainsString('overflow-y-auto flex-1 custom-scrollbar', $content);
            $this->assertStringContainsString('sticky top-0', $content);
            $this->assertStringContainsString('sticky bottom-0', $content);
        }
    }

    /**
     * Test services index page references the 2,000 EGP Core Base and links to /store/branderp.
     */
    public function test_services_index_displays_branderp_2000_egp_and_store_link(): void
    {
        $response = $this->get('/services');
        $response->assertStatus(200);

        $response->assertSee('2,000');
        $response->assertSee(route('store.branderp'));
        $response->assertSee('متجر BrandERP الموديلار');
    }
}
