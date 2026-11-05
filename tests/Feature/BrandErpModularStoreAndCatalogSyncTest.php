<?php

namespace Tests\Feature;

use App\Models\AiConversation;
use App\Models\ErpStoreCatalog;
use App\Models\Service;
use App\Models\ServicePackage;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

class BrandErpModularStoreAndCatalogSyncTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test Homepage single-command-row layout and 0ms popup modals.
     */
    public function test_homepage_single_command_row_layout_and_zero_ms_modals(): void
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200);

        // Verify the 6 sovereign sectors are present
        $response->assertSee('brand-erp');
        $response->assertSee('ai-agents');
        $response->assertSee('cybersecurity');
        $response->assertSee('maintenance');
        $response->assertSee('business-consulting');
        $response->assertSee('academy');

        // Verify BrandERP 0ms popup modals and triggers are present on home
        $response->assertSee('branderp-store-open');
        $response->assertSee('branderp-roi-open');
        $response->assertSee('modal-branderp-store');
        $response->assertSee('modal-branderp-roi');
    }

    /**
     * Test BrandERP 4-Tier Store Catalog and rendering in services/show.
     */
    public function test_branderp_modular_store_catalog_and_rendering(): void
    {
        // 1. Database Tier Integrity
        $coreCount = ErpStoreCatalog::where('tier', 'core')->count();
        $moduleCount = ErpStoreCatalog::where('tier', 'module')->count();
        $featureCount = ErpStoreCatalog::where('tier', 'feature')->count();
        $bundleCount = ErpStoreCatalog::where('tier', 'bundle')->count();

        $this->assertEquals(1, $coreCount, 'BrandERP Core must have exactly 1 record');
        $this->assertGreaterThanOrEqual(12, $moduleCount, 'BrandERP must have at least 12 major modules');
        $this->assertGreaterThanOrEqual(60, $featureCount, 'BrandERP must have at least 60 micro-features');
        $this->assertGreaterThanOrEqual(4, $bundleCount, 'BrandERP must have at least 4 bundles');

        // Core price check: exactly 2,000 EGP
        $core = ErpStoreCatalog::where('tier', 'core')->first();
        $this->assertEquals(2000.00, (float) $core->price);

        // 2. Services Show View for brand-erp
        $response = $this->get(route('services.show', 'brand-erp'));
        $response->assertStatus(200);

        // Verify the 4-Tier Showcase rendering
        $response->assertSee('TIER 1');
        $response->assertSee('TIER 2');
        $response->assertSee('TIER 3');
        $response->assertSee('TIER 4');
        $response->assertSee('2,000');
        $response->assertSee('155,000');
        $response->assertSee('BrandERP Core');
        $response->assertSee('Smart Upsell');
        $response->assertSee('modal-branderp-store');
        $response->assertSee('modal-branderp-roi');
    }

    /**
     * Test Service Catalog pages reflect updated Maintenance and Cybersecurity pricing & rules.
     */
    public function test_service_pages_reflect_updated_maintenance_and_cyber_rules(): void
    {
        // 1. Services Hub (services.index)
        $hubResponse = $this->get(route('services.index'));
        $hubResponse->assertStatus(200);
        $hubResponse->assertSee('2,000');
        $hubResponse->assertSee('400');
        $hubResponse->assertSee('700');

        // 2. Maintenance Service Landing Page
        $maintResponse = $this->get(route('services.show', 'maintenance'));
        $maintResponse->assertStatus(200);
        $maintResponse->assertSee('400');
        $maintResponse->assertSee('3 ساعات');
        $maintResponse->assertSee('البورد');

        // 3. Maintenance Package Subpage (revive)
        $maintPkgResponse = $this->get(route('services.package', ['maintenance', 'revive']));
        $maintPkgResponse->assertStatus(200);
        $maintPkgResponse->assertSee('1,200');
        $maintPkgResponse->assertSee('البورد');

        // 4. Cybersecurity Service Landing Page
        $cyberResponse = $this->get(route('services.show', 'cybersecurity'));
        $cyberResponse->assertStatus(200);
        $cyberResponse->assertSee('700');
        $cyberResponse->assertSee('5,000');
        $cyberResponse->assertSee('18');
        $cyberResponse->assertSee('NDA');

        // 5. Cybersecurity Package Subpage (personal-shield)
        $cyberPkgResponse = $this->get(route('services.package', ['cybersecurity', 'personal-shield']));
        $cyberPkgResponse->assertStatus(200);
        $cyberPkgResponse->assertSee('700');
    }

    /**
     * Test Souly AI chat endpoint guarantees non-empty response & visitor memory persistence.
     */
    public function test_ai_chat_guarantees_non_empty_response_and_visitor_memory(): void
    {
        // Fake external APIs to test resilient local fallback synthesis
        Http::fake([
            '*generativelanguage.googleapis.com*' => Http::response(['error' => 'API Unavailable'], 503),
            '*pinecone.io*' => Http::response(['error' => 'Pinecone Unavailable'], 503),
        ]);

        $visitorUuid = (string) Str::uuid();

        // Step 1: Initial ERP inquiry
        $res1 = $this->postJson(route('api.ai.chat'), [
            'message' => 'كم تكلفة كاشير BrandERP والموديولات؟',
            'visitor_uuid' => $visitorUuid,
        ]);

        $res1->assertStatus(200);
        $this->assertNotEmpty($res1->json('reply'), 'AI response must never be empty');
        $this->assertStringContainsString('2,000', $res1->json('reply'));

        // Step 2: Visitor introduces name
        $sessionUuid = $res1->json('conversation_id');
        $res2 = $this->postJson(route('api.ai.chat'), [
            'message' => 'أنا م. عصام عبد الرحمن',
            'conversation_id' => $sessionUuid,
            'visitor_uuid' => $visitorUuid,
        ]);

        $res2->assertStatus(200);
        $this->assertNotEmpty($res2->json('reply'));
        $this->assertStringContainsString('عصام', $res2->json('reply'));
        $this->assertEquals('عصام عبد الرحمن', $res2->json('visitor_name'));

        // Step 3: Returning visit memory check
        $historyRes = $this->getJson(route('api.ai.history', ['visitor_uuid' => $visitorUuid]));
        $historyRes->assertStatus(200);
        $this->assertEquals('عصام عبد الرحمن', $historyRes->json('visitor_name'));
        $this->assertStringContainsString('عصام', $historyRes->json('welcome_message'));
    }
}
