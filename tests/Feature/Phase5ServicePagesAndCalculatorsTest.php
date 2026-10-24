<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\ServicePackage;
use Tests\TestCase;

class Phase5ServicePagesAndCalculatorsTest extends TestCase
{
    /**
     * Test Services Hub index page lists all 6 active services.
     */
    public function test_services_index_displays_all_six_sectors(): void
    {
        $response = $this->get('/services');

        $response->assertStatus(200);
        $response->assertSee('منظومة الخدمات');
        $response->assertSee('السيادية الست');

        $services = Service::where('is_active', true)->get();
        $this->assertCount(6, $services);

        foreach ($services as $service) {
            $response->assertSee($service->name);
        }
    }

    /**
     * Test all 6 dedicated service landing pages load successfully with status 200.
     */
    public function test_all_six_service_landing_pages_render_successfully(): void
    {
        $services = Service::where('is_active', true)->get();
        $this->assertCount(6, $services);

        foreach ($services as $service) {
            $response = $this->get("/services/{$service->slug}");
            $response->assertStatus(200);
            $response->assertSee($service->name);
            $response->assertSee('استعراض الباقات والأسعار');
            $response->assertSee('مصفوفة المزايا والقدرات التشغيلية');
        }
    }

    /**
     * Test specific interactive widgets appear on their respective service pages.
     */
    public function test_service_specific_interactive_widgets_present(): void
    {
        // 1. Maintenance should have the Hourly & Visit Calculator with 50% discount
        $maintResponse = $this->get('/services/maintenance');
        $maintResponse->assertStatus(200);
        $maintResponse->assertSee('حاسبة التكلفة والضمان الفوري');
        $maintResponse->assertSee('احسب تكلفة زيارتك الهندسية والضمان بدقة');
        $maintResponse->assertSee('خصم 50% للزيارة الأولى');

        // 2. Cybersecurity should have the Security Assessment & Hourly Calculator
        $cyberResponse = $this->get('/services/cybersecurity');
        $cyberResponse->assertStatus(200);
        $cyberResponse->assertSee('حاسبة التكلفة والضمان الفوري');
        $cyberResponse->assertSee('احسب تكلفة زيارتك الهندسية والضمان بدقة');

        // 3. BrandERP should have the Package Recommender Engine
        $erpResponse = $this->get('/services/brand-erp');
        $erpResponse->assertStatus(200);
        $erpResponse->assertSee('مستشار الباقة المثالية');
        $erpResponse->assertSee('اكتشف باقة BrandERP الأنسب لنشاطك وحجم مبيعاتك');

        // 4. Academy should have the Course Curriculum Accordion
        $academyResponse = $this->get('/services/academy');
        $academyResponse->assertStatus(200);
        $academyResponse->assertSee('الخطة الدراسية والمعامل الحية');
        $academyResponse->assertSee('خريطة التدريب العملي: من الصفر حتى سوق العمل');
    }

    /**
     * Test dedicated package sub-pages load for sample packages across sectors.
     */
    public function test_package_subpages_load_with_pricing_and_architecture(): void
    {
        $sampleSlugs = [
            ['brand-erp', 'starter'],
            ['brand-erp', 'growth'],
            ['brand-erp', 'enterprise'],
            ['maintenance', 'promax'],
            ['cybersecurity', 'digital-forensics'],
            ['ai-agents', 'whatsapp-sales'],
            ['business-consulting', 'digital-blueprint'],
            ['academy', 'ai-automation-bootcamp'],
        ];

        foreach ($sampleSlugs as [$serviceSlug, $packageSlug]) {
            $response = $this->get("/services/{$serviceSlug}/{$packageSlug}");
            $response->assertStatus(200);

            $package = ServicePackage::whereHas('service', fn ($q) => $q->where('slug', $serviceSlug))
                ->where('slug', $packageSlug)
                ->firstOrFail();

            $response->assertSee($package->name);
            $response->assertSee('التسعير الهندسي المعتمد');
            $response->assertSee('خريطة التنفيذ الهندسي والجدول الزمني للتشغيل');
            $response->assertSee('وثيقة الالتزام الهندسي والضمان السيادي');
            $response->assertSee('الأسئلة الشائعة حول هذه الباقة');
            $response->assertSee('احجز هذه الباقة فوراً');
        }
    }

    /**
     * Test 404 response for invalid service or package slugs.
     */
    public function test_invalid_service_and_package_slugs_return_404(): void
    {
        $response1 = $this->get('/services/non-existent-service-slug-xyz');
        $response1->assertStatus(404);

        $response2 = $this->get('/services/brand-erp/non-existent-package-slug-xyz');
        $response2->assertStatus(404);
    }
}
