<?php

namespace Tests\Feature;

use App\Models\Service;
use Tests\TestCase;

class Phase10SimulatorsAndHomepageMultiRowTest extends TestCase
{
    /**
     * Test homepage renders all 6 interactive simulators in multi-row architecture.
     */
    public function test_homepage_renders_all_six_interactive_simulators_in_multi_row_architecture(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);

        // 1. BrandERP Simulator Row
        $response->assertSee('BrandERP ROI Recommender');
        $response->assertSee('ساعات عمل توفرها شهرياً');
        $response->assertSee('هدر مالي وجردي تحميه سنوياً');

        // 2. AI Agents Simulator Row
        $response->assertSee('AI Employee ROI Simulator');
        $response->assertSee('محاكاة حية للرد باللهجة المصرية');
        $response->assertSee('صافي التوفير المالي لشركتك شهرياً');

        // 3. Cybersecurity Scanner Row
        $response->assertSee('Cyber Shield Scanner');
        $response->assertSee('مؤشر التعرض للمخاطر (Risk Score)');
        $response->assertSee('ضمان هندسي سيبراني معتمد رسمياً');

        // 4. Maintenance Benchmark Row
        $response->assertSee('محاكي الهبوط الحراري');
        $response->assertSee('خصم 50% للزيارة الأولى بمقرك');
        $response->assertSee('فاتورة الصيانة بعد الخصم المعتمد');

        // 5. Business Consulting Diagnostic Row
        $response->assertSee('Diagnostic Matrix');
        $response->assertSee('تقرير النضج المؤسسي');
        $response->assertSee('خارطة التحول والتطوير المقترحة');

        // 6. Academy Curriculum & Certificate Preview Row
        $response->assertSee('المعاينة الفورية لشهادة سولي المعتمدة');
        $response->assertSee('معسكر وكلاء الـ AI والأتمتة');
        $response->assertSee('شهادة التخرج المعتمدة رسمياً');
    }

    /**
     * Test all 6 dedicated service landing pages render their dedicated simulators.
     */
    public function test_all_six_service_landing_pages_render_their_dedicated_simulators(): void
    {
        // 1. BrandERP Page
        $erpRes = $this->get('/services/brand-erp');
        $erpRes->assertStatus(200);
        $erpRes->assertSee('BrandERP ROI Recommender');
        $erpRes->assertSee('مستشار الباقة المثالية');

        // 2. AI Agents Page
        $aiRes = $this->get('/services/ai-agents');
        $aiRes->assertStatus(200);
        $aiRes->assertSee('AI Employee ROI Simulator');
        $aiRes->assertSee('محاكاة حية للرد باللهجة المصرية');

        // 3. Cybersecurity Page
        $cyberRes = $this->get('/services/cybersecurity');
        $cyberRes->assertStatus(200);
        $cyberRes->assertSee('Cyber Shield Scanner');
        $cyberRes->assertSee('مؤشر التعرض للمخاطر (Risk Score)');

        // 4. Maintenance Page
        $maintRes = $this->get('/services/maintenance');
        $maintRes->assertStatus(200);
        $maintRes->assertSee('محاكي الهبوط الحراري');
        $maintRes->assertSee('خصم 50% للزيارة الأولى');

        // 5. Business Consulting Page
        $consultRes = $this->get('/services/business-consulting');
        $consultRes->assertStatus(200);
        $consultRes->assertSee('Diagnostic Matrix');
        $consultRes->assertSee('خارطة التحول والتطوير المقترحة');

        // 6. Academy Page
        $acadRes = $this->get('/services/academy');
        $acadRes->assertStatus(200);
        $acadRes->assertSee('شهادة التخرج المعتمدة رسمياً');
        $acadRes->assertSee('خريطة التدريب العملي: من الصفر حتى سوق العمل');
    }

    /**
     * Test strict brand naming and complete absence of UltimatePOS and Citadel.
     */
    public function test_brand_name_remains_strictly_souly_without_citadel_or_ultimatepos(): void
    {
        $urls = [
            '/',
            '/services',
            '/services/brand-erp',
            '/services/ai-agents',
            '/services/cybersecurity',
            '/services/maintenance',
            '/services/business-consulting',
            '/services/academy',
        ];

        foreach ($urls as $url) {
            $response = $this->get($url);
            $response->assertStatus(200);

            $content = $response->getContent();
            $this->assertStringNotContainsStringIgnoringCase('UltimatePOS', $content);
            $this->assertStringNotContainsString('ديجيتال سيتادل', $content);
            $this->assertStringNotContainsString('ديدجيتال سيتادل', $content);
            $this->assertStringNotContainsString('سولي سيتادل', $content);
        }
    }

    /**
     * Test simulators contain valid booking and portal links.
     */
    public function test_simulators_contain_valid_booking_urls(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);

        $response->assertSee('/portal/bookings?service=brand-erp');
        $response->assertSee('/portal/bookings?service=ai-agents');
        $response->assertSee('/portal/bookings?service=cybersecurity');
        $response->assertSee('/portal/bookings?service=maintenance');
        $response->assertSee('/portal/bookings?service=business-consulting');
        $response->assertSee('/portal/bookings?service=academy');
    }
}
