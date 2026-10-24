<?php

namespace Tests\Feature;

use App\Models\AiConversation;
use App\Models\Booking;
use App\Models\CannedResponse;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\Service;
use App\Models\ServicePackage;
use App\Models\User;
use App\Models\WalletTransaction;
use Tests\TestCase;

class Phase7And8AdminCrmAndAiAgentTest extends TestCase
{
    /**
     * Test route security: guests redirected, clients forbidden (403), staff and admin allowed (200).
     */
    public function test_admin_route_authorization(): void
    {
        // 1. Guest
        $guestResponse = $this->get('/admin');
        $guestResponse->assertRedirect(route('login'));

        // 2. Client Forbidden
        $client = User::where('role', 'client')->firstOrFail();
        $this->actingAs($client);
        $clientResponse = $this->get('/admin');
        $clientResponse->assertStatus(403);

        // 3. Staff Allowed
        $staff = User::where('role', 'staff')->firstOrFail();
        $this->actingAs($staff);
        $staffResponse = $this->get('/admin');
        $staffResponse->assertStatus(200);

        // 4. Admin Allowed
        $admin = User::where('role', 'admin')->firstOrFail();
        $this->actingAs($admin);
        $adminResponse = $this->get('/admin');
        $adminResponse->assertStatus(200);
        $adminResponse->assertSee('مركز القيادة وإدارة العمليات');
    }

    /**
     * Test Admin 1-Click approving wallet deposit atomically increments client balance.
     */
    public function test_admin_approving_wallet_deposit_increments_client_balance(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();
        $client = User::where('role', 'client')->firstOrFail();
        $wallet = $client->walletOrCreate();

        $initialBalance = (float) $wallet->balance;

        $depositTx = WalletTransaction::create([
            'wallet_id' => $wallet->id,
            'user_id' => $client->id,
            'reference_code' => 'TEST-DEP-' . uniqid(),
            'type' => 'deposit',
            'amount' => 1250.00,
            'payment_method' => 'vodafone_cash',
            'sender_number_or_account' => '01011223344',
            'status' => 'pending',
        ]);

        $this->actingAs($admin);
        $response = $this->post("/admin/wallets/{$depositTx->id}/approve", [
            'admin_notes' => 'تم التحقق من استلام المبلغ في فودافون كاش.',
        ]);

        $response->assertSessionHasNoErrors();
        $depositTx->refresh();
        $this->assertEquals('approved', $depositTx->status);
        $this->assertEquals($admin->id, $depositTx->reviewed_by);

        $wallet->refresh();
        $this->assertEquals($initialBalance + 1250.00, (float) $wallet->balance);

        // Cleanup
        $wallet->balance = $initialBalance;
        $wallet->save();
        $depositTx->delete();
    }

    /**
     * Test updating booking status and assigning staff.
     */
    public function test_admin_updating_booking_status_and_assigning_staff(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();
        $staff = User::where('role', 'staff')->firstOrFail();
        $client = User::where('role', 'client')->firstOrFail();
        $service = Service::firstOrFail();

        $booking = Booking::create([
            'booking_number' => 'BKG-TEST-' . uniqid(),
            'user_id' => $client->id,
            'service_id' => $service->id,
            'quantity_or_hours' => 2,
            'total_amount' => 1000.00,
            'payment_method' => 'on_site',
            'payment_status' => 'unpaid',
            'status' => 'new',
        ]);

        $this->actingAs($admin);
        $response = $this->post("/admin/bookings/{$booking->id}/update", [
            'status' => 'in_progress',
            'payment_status' => 'paid',
            'assigned_staff_id' => $staff->id,
            'staff_report' => 'تم الانتقال لمقر العميل والبدء في الفحص الهندسي.',
        ]);

        $response->assertSessionHasNoErrors();
        $booking->refresh();
        $this->assertEquals('in_progress', $booking->status);
        $this->assertEquals('paid', $booking->payment_status);
        $this->assertEquals($staff->id, $booking->assigned_staff_id);

        // Cleanup
        $booking->delete();
    }

    /**
     * Test CRM Lead creation, status pipeline advancement, and activity logging.
     */
    public function test_crm_leads_creation_status_update_and_activity_logging(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();
        $this->actingAs($admin);

        $testPhone = '010' . rand(10000000, 99999999);

        // 1. Create Lead
        $createResponse = $this->post('/admin/leads', [
            'name' => 'الأستاذ عماد الشاذلي',
            'phone' => $testPhone,
            'email' => 'emad@example.com',
            'company_name' => 'مجموعة الشاذلي للصناعات',
            'status' => 'new',
            'notes' => 'مهتم بنظام BrandERP لربط 4 فروع.',
        ]);

        $lead = Lead::where('phone', $testPhone)->firstOrFail();
        $createResponse->assertRedirect(route('admin.leads.show', $lead));
        $this->assertEquals('new', $lead->status);

        // 2. Advance Status to Contacted
        $statusResponse = $this->post("/admin/leads/{$lead->id}/status", [
            'status' => 'contacted',
        ]);
        $statusResponse->assertSessionHasNoErrors();
        $lead->refresh();
        $this->assertEquals('contacted', $lead->status);

        // 3. Log Activity Note
        $activityResponse = $this->post("/admin/leads/{$lead->id}/activities", [
            'type' => 'call',
            'description' => 'تم إجراء مكالمة هاتفية مدتها 15 دقيقة، وتم الاتفاق على إرسال عرض مالي لباقة النمو.',
        ]);
        $activityResponse->assertSessionHasNoErrors();

        $activity = LeadActivity::where('lead_id', $lead->id)->where('type', 'call')->first();
        $this->assertNotNull($activity);
        $this->assertEquals($admin->id, $activity->user_id);

        // Cleanup
        LeadActivity::where('lead_id', $lead->id)->delete();
        $lead->delete();
    }

    /**
     * Test Smart Canned Responses Vault has at least 25 seeded responses and usage increments.
     */
    public function test_canned_responses_vault_seeding_and_increment_usage_api(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();
        $this->actingAs($admin);

        // 1. Vault Page Loads
        $pageResponse = $this->get('/admin/canned-responses');
        $pageResponse->assertStatus(200);
        $pageResponse->assertSee('خزينة الردود الذكية المعتمدة');

        // 2. Count is at least 25
        $count = CannedResponse::where('is_active', true)->count();
        $this->assertGreaterThanOrEqual(25, $count);

        // 3. Increment Usage AJAX
        $sampleResponse = CannedResponse::firstOrFail();
        $initialUsage = $sampleResponse->usage_count;

        $ajaxResponse = $this->postJson("/admin/canned-responses/{$sampleResponse->id}/increment-usage");
        $ajaxResponse->assertStatus(200);
        $ajaxResponse->assertJson([
            'status' => 'success',
            'id' => $sampleResponse->id,
            'usage_count' => $initialUsage + 1,
        ]);

        $sampleResponse->refresh();
        $this->assertEquals($initialUsage + 1, $sampleResponse->usage_count);
    }

    /**
     * Test Souly AI Agent chat endpoint returns intelligent synthesized reply and package suggestions.
     */
    public function test_souly_ai_agent_chat_endpoint_and_knowledge_synthesis(): void
    {
        $response = $this->postJson('/api/ai/chat', [
            'message' => 'ما هي مميزات وأسعار نظام BrandERP السحابي؟',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'reply',
            'conversation_id',
            'lead_captured',
            'package_suggestions',
        ]);

        $data = $response->json();
        $this->assertNotEmpty($data['reply']);
        $this->assertStringContainsString('BrandERP', $data['reply']);
        $this->assertNotEmpty($data['conversation_id']);
    }

    /**
     * Test Souly AI Agent automatically captures visitor phone number and records lead in CRM.
     */
    public function test_souly_ai_agent_lead_detection_captures_phone_number_in_crm(): void
    {
        $testPhone = '010' . rand(10000000, 99999999);
        $message = "أنا مهتم بخدمات الصيانة وهذا رقمي للتواصل فوراً: {$testPhone}";

        $response = $this->postJson('/api/ai/chat', [
            'message' => $message,
            'visitor_name' => 'طارق الزيات',
        ]);

        $response->assertStatus(200);
        $data = $response->json();
        $this->assertTrue($data['lead_captured']);
        $this->assertStringContainsString($testPhone, $data['reply']);

        // Assert Lead in DB
        $lead = Lead::where('phone', $testPhone)->first();
        $this->assertNotNull($lead);
        $this->assertEquals('ai_agent', $lead->source);
        $this->assertEquals('new', $lead->status);

        // Assert Conversation & Message in DB
        $conv = AiConversation::where('session_uuid', $data['conversation_id'])->first();
        $this->assertNotNull($conv);
        $this->assertEquals($lead->id, $conv->lead_id);
        $this->assertGreaterThanOrEqual(2, $conv->messages()->count());

        // Cleanup
        $conv->messages()->delete();
        $conv->delete();
        $lead->delete();
    }
}
