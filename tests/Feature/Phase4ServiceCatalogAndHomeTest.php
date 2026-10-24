<?php

namespace Tests\Feature;

use App\Models\Service;
use Tests\TestCase;

class Phase4ServiceCatalogAndHomeTest extends TestCase
{
    public function test_service_catalog_seeder_counts_and_integrity(): void
    {
        $this->assertEquals(6, Service::count());

        // 1. BrandERP
        $erp = Service::where('slug', 'brand-erp')->first();
        $this->assertNotNull($erp);
        $this->assertEquals(3, $erp->packages()->count());
        $this->assertEquals(24, $erp->icons()->where('group_type', 'industry')->count());
        $this->assertEquals(20, $erp->icons()->where('group_type', 'feature')->count());
        $this->assertEquals(44, $erp->icons()->count());
        $this->assertNotNull($erp->rows()->where('row_type', 'before_after')->first());

        // 2. AI Agents
        $ai = Service::where('slug', 'ai-agents')->first();
        $this->assertNotNull($ai);
        $this->assertEquals(4, $ai->packages()->count());
        $this->assertEquals(16, $ai->icons()->where('group_type', 'industry')->count());
        $this->assertEquals(16, $ai->icons()->where('group_type', 'feature')->count());
        $this->assertEquals(32, $ai->icons()->count());

        // 3. Cybersecurity
        $cyber = Service::where('slug', 'cybersecurity')->first();
        $this->assertNotNull($cyber);
        $this->assertEquals(7, $cyber->packages()->count());
        $this->assertGreaterThanOrEqual(14, $cyber->icons()->count());
        // Verify pricing range (350 to 2500)
        $this->assertEquals('350.00', $cyber->packages()->where('slug', 'personal-shield')->first()->price);
        $this->assertEquals('2500.00', $cyber->packages()->where('slug', 'enterprise-cyber-fortress')->first()->price);

        // 4. Maintenance
        $maint = Service::where('slug', 'maintenance')->first();
        $this->assertNotNull($maint);
        $this->assertEquals(3, $maint->packages()->count());
        $this->assertGreaterThanOrEqual(14, $maint->icons()->count());
        $revivePkg = $maint->packages()->where('slug', 'revive')->first();
        $this->assertEquals('400.00', $revivePkg->price);
        $this->assertEquals('800.00', $revivePkg->original_price);
        $this->assertEquals(3, $revivePkg->min_units);

        // 5. Business Consulting
        $consulting = Service::where('slug', 'business-consulting')->first();
        $this->assertNotNull($consulting);
        $this->assertEquals(3, $consulting->packages()->count());
        $this->assertGreaterThanOrEqual(12, $consulting->icons()->count());

        // 6. Academy
        $academy = Service::where('slug', 'academy')->first();
        $this->assertNotNull($academy);
        $this->assertEquals(4, $academy->packages()->count());
        $this->assertGreaterThanOrEqual(14, $academy->icons()->count());
    }

    public function test_homepage_multi_row_rendering(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('نظام BrandERP المتكامل', false);
        $response->assertSee('موظفو الذكاء الاصطناعي AI Agents', false);
        $response->assertSee('الأمن السيبراني والتدريع الرقمي', false);
        $response->assertSee('خدمات الصيانة الهندسية الميدانية الفائقة', false);
        $response->assertSee('استشارات البيزنس والتحول الرقمي', false);
        $response->assertSee('أكاديمية سولي لكورسات الـ AI والهاكينج الأخلاقي', false);

        // Check for Before/After rows
        $response->assertSee('الفارق التشغيلي الحاسم', false);
        $response->assertSee('قبل تطبيق سولي', false);
        $response->assertSee('بعد تدريع سولي', false);

        // Check for interactive icon trigger attributes
        $response->assertSee('data-icon-popup', false);
        $response->assertSee('احجز هذه الباقة الآن', false);
    }
}
