<?php

namespace Tests\Feature;

use App\Models\AiConversation;
use App\Models\Booking;
use App\Models\CannedResponseCategory;
use App\Models\Lead;
use App\Models\Service;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Wallet;
use Tests\TestCase;

class Phase2DatabaseArchitectureTest extends TestCase
{
    public function test_core_users_exist_with_correct_roles(): void
    {
        $admin = User::where('email', 'admin@soulyeg.online')->first();
        $this->assertNotNull($admin);
        $this->assertTrue($admin->isAdmin());
        $this->assertEquals('Hedra Adel', $admin->name);
        $this->assertEquals('+201000000001', $admin->phone);

        $staff = User::where('email', 'staff@soulyeg.online')->first();
        $this->assertNotNull($staff);
        $this->assertTrue($staff->isStaff());
        $this->assertFalse($staff->isAdmin());

        $client = User::where('email', 'client@soulyeg.online')->first();
        $this->assertNotNull($client);
        $this->assertTrue($client->isClient());
    }

    public function test_wallets_and_wallet_transactions_relationships(): void
    {
        $admin = User::where('email', 'admin@soulyeg.online')->first();
        $this->assertNotNull($admin->wallet);
        $this->assertEquals('50000.00', $admin->wallet->balance);
        $this->assertTrue($admin->wallet->hasSufficientBalance(25000));
        $this->assertFalse($admin->wallet->hasSufficientBalance(60000));

        $client = User::where('email', 'client@soulyeg.online')->first();
        $this->assertNotNull($client->wallet);
        $this->assertEquals('3500.00', $client->wallet->balance);
        $this->assertCount(1, $client->walletTransactions);

        // Test User::walletOrCreate() helper
        $newUser = User::create([
            'name' => 'Auto Wallet Test',
            'email' => 'auto.wallet@example.com',
            'password' => bcrypt('password'),
            'role' => 'client',
        ]);
        $wallet = $newUser->walletOrCreate();
        $this->assertInstanceOf(Wallet::class, $wallet);
        $this->assertEquals('0.00', $wallet->balance);
        $this->assertEquals($wallet->id, $newUser->walletOrCreate()->id);
    }

    public function test_six_core_services_exist_with_proper_relations(): void
    {
        $expectedSlugs = [
            'brand-erp',
            'ai-agents',
            'cybersecurity',
            'maintenance',
            'business-consulting',
            'academy',
        ];

        foreach ($expectedSlugs as $slug) {
            $service = Service::where('slug', $slug)->first();
            $this->assertNotNull($service, "Service [{$slug}] should exist.");
            $this->assertTrue($service->is_active);
            $this->assertNotEmpty($service->accent_color);
        }

        // Test BrandERP relationships
        $erp = Service::where('slug', 'brand-erp')->first();
        $this->assertGreaterThanOrEqual(1, $erp->rows->count());
        $this->assertGreaterThanOrEqual(2, $erp->icons->count());
        $this->assertGreaterThanOrEqual(2, $erp->packages->count());

        $package = $erp->packages->first();
        $this->assertEquals($erp->id, $package->service->id);
        $this->assertIsArray($package->features_json);
    }

    public function test_bookings_tickets_and_messages_relationships(): void
    {
        $booking = Booking::with(['user', 'service', 'package', 'assignedStaff'])->first();
        $this->assertNotNull($booking);
        $this->assertEquals('client@soulyeg.online', $booking->user->email);
        $this->assertEquals('brand-erp', $booking->service->slug);
        $this->assertEquals('paid', $booking->payment_status);

        $ticket = Ticket::with(['user', 'booking', 'messages'])->first();
        $this->assertNotNull($ticket);
        $this->assertEquals($booking->id, $ticket->booking_id);
        $this->assertCount(2, $ticket->messages);
        $this->assertEquals('erp', $ticket->department);
    }

    public function test_crm_leads_activities_and_canned_responses(): void
    {
        $lead = Lead::with(['activities', 'conversations'])->first();
        $this->assertNotNull($lead);
        $this->assertEquals('أحمد فتحي القاضي', $lead->name);
        $this->assertGreaterThanOrEqual(2, $lead->activities->count());

        $category = CannedResponseCategory::with('responses')->where('slug', 'brand-erp-responses')->first();
        $this->assertNotNull($category);
        $this->assertGreaterThanOrEqual(1, $category->responses->count());

        $response = $category->responses->first();
        $this->assertEquals('/erp-tax', $response->shortcut_code);
        $this->assertIsArray($response->tags_json);
    }

    public function test_ai_agent_conversations_and_messages(): void
    {
        $conv = AiConversation::with('messages')->first();
        $this->assertNotNull($conv);
        $this->assertNotEmpty($conv->session_uuid);
        $this->assertCount(2, $conv->messages);

        $firstMsg = $conv->messages->first();
        $this->assertEquals('visitor', $firstMsg->sender);

        $replyMsg = $conv->messages->last();
        $this->assertEquals('souly_ai', $replyMsg->sender);
        $this->assertIsArray($replyMsg->suggested_links_json);
    }
}
