<?php

namespace Tests\Feature;

use App\Models\CannedResponse;
use App\Models\CannedResponseCategory;
use App\Models\Lead;
use App\Models\Service;
use App\Models\ServicePackage;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

class Phase12SettingsCannedDrawerAndAiAgentTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test /admin/settings authorization:
     * Admin (ROOT) has access (200), Staff is forbidden (403), Client is forbidden (403).
     */
    public function test_admin_settings_authorization(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin_' . Str::random(6) . '@soulyeg.online',
            'phone' => '010' . rand(10000000, 99999999),
        ]);

        $staff = User::factory()->create([
            'role' => 'staff',
            'email' => 'staff_' . Str::random(6) . '@soulyeg.online',
            'phone' => '011' . rand(10000000, 99999999),
        ]);

        $client = User::factory()->create([
            'role' => 'client',
            'email' => 'client_' . Str::random(6) . '@soulyeg.online',
            'phone' => '012' . rand(10000000, 99999999),
        ]);

        // 1. Admin can access
        $resAdmin = $this->actingAs($admin)->get(route('admin.settings.index'));
        $resAdmin->assertStatus(200);
        $resAdmin->assertSee('إدارة منظومة سولي ومستودع المعرفة الفكتوري');
        $resAdmin->assertSee('Pinecone API Key');
        $resAdmin->assertSee('مزامنة قاعدة معرفة سولي مع Pinecone الآن 🔄');

        // 2. Staff cannot access sensitive settings (403)
        $resStaff = $this->actingAs($staff)->get(route('admin.settings.index'));
        $resStaff->assertStatus(403);

        // 3. Client cannot access (403)
        $resClient = $this->actingAs($client)->get(route('admin.settings.index'));
        $resClient->assertStatus(403);
    }

    /**
     * Test admin can update system settings dynamically in DB & cache.
     */
    public function test_admin_can_update_system_settings_in_database_and_cache(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin_' . Str::random(6) . '@soulyeg.online',
            'phone' => '010' . rand(10000000, 99999999),
        ]);

        $newVodafoneNumber = '01011223344';
        $newWelcomeMsg = 'مرحباً بك في سولي! كيف نخدمك اليوم؟';

        $response = $this->actingAs($admin)->post(route('admin.settings.update'), [
            'settings' => [
                'vodafone_cash_number' => $newVodafoneNumber,
                'ai_welcome_message' => $newWelcomeMsg,
                'loader_duration_seconds' => '7',
            ],
            '__submitted_cinematic' => '1',
        ]);

        $response->assertSessionHas('citadel_toast');
        $response->assertRedirect();

        // Verify Setting::get pulls the updated value from cache / DB
        $this->assertEquals($newVodafoneNumber, Setting::get('vodafone_cash_number'));
        $this->assertEquals($newWelcomeMsg, Setting::get('ai_welcome_message'));
        $this->assertEquals(7, Setting::get('loader_duration_seconds'));
    }

    /**
     * Test 1-click Pinecone Knowledge Sync endpoint:
     * Ingests services, packages, and icons, storing them in vector store and updating timestamp.
     */
    public function test_admin_can_sync_knowledge_base_with_pinecone(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin_' . Str::random(6) . '@soulyeg.online',
            'phone' => '010' . rand(10000000, 99999999),
        ]);

        $response = $this->actingAs($admin)->postJson(route('admin.settings.sync_pinecone'));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $data = $response->json();
        $this->assertGreaterThanOrEqual(20, $data['synced_count']);
        $this->assertNotNull($data['timestamp']);

        // Verify last synced timestamp is set in Setting
        $lastSynced = Setting::get('pinecone_last_synced_at');
        $this->assertNotNull($lastSynced);
    }

    /**
     * Test canned responses API and quick creation for the staff drawer.
     */
    public function test_canned_responses_data_api_and_quick_creation_for_staff_drawer(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
            'email' => 'staff_' . Str::random(6) . '@soulyeg.online',
            'phone' => '011' . rand(10000000, 99999999),
        ]);

        // 1. Fetch Drawer Data
        $dataResponse = $this->actingAs($staff)->getJson(route('admin.canned-responses.data'));
        $dataResponse->assertStatus(200);
        $dataResponse->assertJsonStructure([
            'status',
            'categories',
            'responses',
        ]);

        // 2. Quick create new canned response from inside the drawer
        $cat = CannedResponseCategory::firstOrFail();
        $uniqueCode = '/test-' . Str::lower(Str::random(6));

        $createResponse = $this->actingAs($staff)->postJson(route('admin.canned-responses.store'), [
            'category_id' => $cat->id,
            'title' => 'رد اختبار مهندسي الدعم الفني',
            'shortcut_code' => $uniqueCode,
            'content' => 'أهلاً بك، تم فحص الخادم وتأكيد جاهزية النظام بنجاح.',
            'tags' => 'سيرفر, فحص, دعم',
        ]);

        $createResponse->assertStatus(200);
        $createResponse->assertJson([
            'status' => 'success',
        ]);

        // Verify DB
        $canned = CannedResponse::where('shortcut_code', $uniqueCode)->first();
        $this->assertNotNull($canned);
        $this->assertEquals('رد اختبار مهندسي الدعم الفني', $canned->title);
    }

    /**
     * Test Souly AI Agent returns rich package suggestions with slugs and captures Egyptian Arabic leads.
     */
    public function test_souly_ai_agent_returns_rich_package_suggestions_and_detects_egyptian_leads(): void
    {
        $egyptianPhone = '010' . rand(10000000, 99999999);
        $query = "عايز اعرف اسعار سيستم الكاشير والمخازن erp بكام عندكم ورقمي {$egyptianPhone}";

        $response = $this->postJson(route('api.ai.chat'), [
            'message' => $query,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'reply',
            'conversation_id',
            'package_suggestions',
        ]);

        $data = $response->json();
        $this->assertTrue($data['lead_captured']);
        $this->assertNotEmpty($data['package_suggestions']);

        // Verify package suggestion contains rich fields for 1-tap modal
        $firstSuggestion = $data['package_suggestions'][0];
        $this->assertArrayHasKey('service_slug', $firstSuggestion);
        $this->assertArrayHasKey('package_slug', $firstSuggestion);
        $this->assertArrayHasKey('price_raw', $firstSuggestion);
        $this->assertEquals('brand-erp', $firstSuggestion['service_slug']);

        // Verify lead in CRM has Egyptian summary badge
        $lead = Lead::where('phone', $egyptianPhone)->first();
        $this->assertNotNull($lead);
        $this->assertEquals('ai_agent', $lead->source);
        $this->assertStringContainsString('مصرية عامية', $lead->ai_summary);
        $this->assertStringContainsString('BrandERP', $lead->ai_summary);
    }
}
