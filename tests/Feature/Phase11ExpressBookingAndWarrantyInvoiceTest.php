<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Lead;
use App\Models\Service;
use App\Models\ServicePackage;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

class Phase11ExpressBookingAndWarrantyInvoiceTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test guest can perform 1-Tap Express Booking:
     * Auto-creates account, wallet, logs in, creates booking, and logs CRM lead.
     */
    public function test_guest_can_perform_one_tap_express_booking_and_receives_account_wallet_booking_and_lead(): void
    {
        $service = Service::where('slug', 'cybersecurity')->firstOrFail();
        $package = ServicePackage::where('service_id', $service->id)->where('slug', 'personal-shield')->firstOrFail();

        $guestPhone = '010' . rand(10000000, 99999999);
        $guestName = 'المهندس كريم الشريف ' . Str::random(4);

        $response = $this->postJson(route('express-book.store'), [
            'name' => $guestName,
            'phone' => $guestPhone,
            'service_slug' => $service->slug,
            'package_slug' => $package->slug,
            'payment_method' => 'instapay',
            'notes' => 'فحص هاتف آيفون ضد برامج التجسس',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        // 1. Verify User was created
        $user = User::where('phone', $guestPhone)->first();
        $this->assertNotNull($user);
        $this->assertEquals($guestName, $user->name);
        $this->assertEquals('client', $user->role);

        // 2. Verify Wallet was created with 0.00
        $this->assertNotNull($user->wallet);
        $this->assertEquals(0.00, (float) $user->wallet->balance);

        // 3. Verify user was logged in
        $this->assertAuthenticatedAs($user);

        // 4. Verify Booking was created
        $booking = Booking::where('user_id', $user->id)->first();
        $this->assertNotNull($booking);
        $this->assertEquals($service->id, $booking->service_id);
        $this->assertEquals($package->id, $booking->service_package_id);
        $this->assertEquals('instapay', $booking->payment_method);
        $this->assertEquals('pending_verification', $booking->payment_status);
        $this->assertEquals('new', $booking->status);
        $this->assertEquals($package->price, $booking->total_amount);

        // 5. Verify Lead was created in CRM with source website_booking
        $lead = Lead::where('phone', $guestPhone)->first();
        $this->assertNotNull($lead);
        $this->assertEquals('website_booking', $lead->source);
        $this->assertEquals('new', $lead->status);
        $this->assertStringContainsString($booking->booking_number, $lead->notes);
    }

    /**
     * Test authenticated client with sufficient wallet balance pays immediately:
     * Deducts balance atomically, confirms booking, logs purchase transaction.
     */
    public function test_authenticated_client_with_wallet_balance_confirms_booking_and_deducts_atomically(): void
    {
        $uniqueSuffix = Str::random(6);
        $client = User::factory()->create([
            'name' => 'د. سارة عادل',
            'email' => 'sara_' . $uniqueSuffix . '@soulyeg.online',
            'phone' => '011' . rand(10000000, 99999999),
            'role' => 'client',
        ]);

        $wallet = $client->walletOrCreate();
        $wallet->update(['balance' => 3000.00]);

        $service = Service::where('slug', 'brand-erp')->firstOrFail();
        $package = ServicePackage::where('service_id', $service->id)->where('slug', 'starter')->firstOrFail();

        $packagePrice = (float) $package->price; // 850.00 EGP

        $response = $this->actingAs($client)->post(route('express-book.store'), [
            'service_id' => $service->id,
            'package_id' => $package->id,
            'payment_method' => 'wallet',
            'notes' => 'تفعيل فوري لكاشير السوبرماركت',
        ]);

        // Expect redirect to the invoice / certificate page
        $booking = Booking::where('user_id', $client->id)->latest()->first();
        $this->assertNotNull($booking);

        $response->assertRedirect(route('portal.bookings.invoice', $booking->id));

        // 1. Booking Status & Payment Status
        $this->assertEquals('confirmed', $booking->status);
        $this->assertEquals('paid', $booking->payment_status);
        $this->assertEquals('wallet', $booking->payment_method);

        // 2. Wallet Deducted Atomically
        $wallet->refresh();
        $this->assertEquals(3000.00 - $packagePrice, (float) $wallet->balance);

        // 3. Wallet Transaction Recorded
        $tx = WalletTransaction::where('wallet_id', $wallet->id)
            ->where('type', 'purchase')
            ->first();
        $this->assertNotNull($tx);
        $this->assertEquals('approved', $tx->status);
        $this->assertEquals($packagePrice, (float) $tx->amount);

        // 4. Lead Captured
        $lead = Lead::where('phone', $client->phone)->first();
        $this->assertNotNull($lead);
        $this->assertEquals('website_booking', $lead->source);
    }

    /**
     * Test authenticated client with insufficient wallet balance gets error prompt.
     */
    public function test_authenticated_client_with_insufficient_wallet_returns_error(): void
    {
        $client = User::factory()->create([
            'role' => 'client',
            'phone' => '010' . rand(10000000, 99999999),
            'email' => 'client_' . Str::random(6) . '@soulyeg.online',
        ]);
        $wallet = $client->walletOrCreate();
        $wallet->update(['balance' => 100.00]);

        $service = Service::where('slug', 'ai-agents')->firstOrFail();
        $package = ServicePackage::where('service_id', $service->id)->firstOrFail();

        $response = $this->actingAs($client)->postJson(route('express-book.store'), [
            'service_id' => $service->id,
            'package_id' => $package->id,
            'payment_method' => 'wallet',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
        ]);
    }

    /**
     * Test client can view their official printable invoice and engineering warranty certificate.
     */
    public function test_client_can_view_official_invoice_and_engineering_warranty_certificate(): void
    {
        $client = User::factory()->create([
            'role' => 'client',
            'name' => 'م. طارق المهندس',
            'email' => 'tariq_' . Str::random(6) . '@soulyeg.online',
            'phone' => '012' . rand(10000000, 99999999),
        ]);
        $service = Service::where('slug', 'maintenance')->firstOrFail();
        $package = ServicePackage::where('service_id', $service->id)->where('slug', 'promax')->firstOrFail();

        $uniqueBkg = 'BKG-' . strtoupper(Str::random(8));

        $booking = Booking::create([
            'booking_number' => $uniqueBkg,
            'user_id' => $client->id,
            'service_id' => $service->id,
            'service_package_id' => $package->id,
            'quantity_or_hours' => 3,
            'total_amount' => 2250.00,
            'payment_method' => 'vodafone_cash',
            'payment_status' => 'paid',
            'status' => 'confirmed',
            'scheduled_at' => now()->addDay(),
        ]);

        $response = $this->actingAs($client)->get(route('portal.bookings.invoice', $booking->id));
        $response->assertStatus(200);

        // Asserts content
        $response->assertSee('فاتورة رسمية وشهادة ضمان هندسي');
        $response->assertSee($uniqueBkg);
        $response->assertSee('م. طارق المهندس');
        $response->assertSee('Souly ProMax');
        $response->assertSee('شهادة الضمان الهندسي المعتمدة');
        $response->assertSee('ضمان ساري لمدة 30 يوماً');
        $response->assertSee('رمز QR رسمي وموثق');
        $response->assertSee('سولي');

        // Brand rules: no citadel or ultimatepos
        $content = $response->getContent();
        $this->assertStringNotContainsStringIgnoringCase('UltimatePOS', $content);
        $this->assertStringNotContainsString('ديجيتال سيتادل', $content);
        $this->assertStringNotContainsString('ديدجيتال سيتادل', $content);

        // Also test certificate route alias
        $certResponse = $this->actingAs($client)->get(route('portal.bookings.certificate', $booking->id));
        $certResponse->assertStatus(200);
    }

    /**
     * Test unauthorized client cannot view another client's invoice (403 forbidden).
     */
    public function test_unauthorized_client_cannot_view_another_clients_invoice(): void
    {
        $clientA = User::factory()->create([
            'role' => 'client',
            'email' => 'client_a_' . Str::random(6) . '@soulyeg.online',
            'phone' => '015' . rand(10000000, 99999999),
        ]);
        $clientB = User::factory()->create([
            'role' => 'client',
            'email' => 'client_b_' . Str::random(6) . '@soulyeg.online',
            'phone' => '015' . rand(10000000, 99999999),
        ]);
        $service = Service::firstOrFail();

        $uniqueBkgSec = 'BKG-' . strtoupper(Str::random(8));

        $bookingA = Booking::create([
            'booking_number' => $uniqueBkgSec,
            'user_id' => $clientA->id,
            'service_id' => $service->id,
            'quantity_or_hours' => 1,
            'total_amount' => 1000.00,
            'payment_method' => 'on_site',
            'payment_status' => 'pending_verification',
            'status' => 'new',
        ]);

        // Client B tries to view Client A's invoice -> 403
        $response = $this->actingAs($clientB)->get(route('portal.bookings.invoice', $bookingA->id));
        $response->assertStatus(403);

        // Admin can view Client A's invoice -> 200
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create([
            'role' => 'admin',
            'email' => 'admin_' . Str::random(6) . '@soulyeg.online',
            'phone' => '010' . rand(10000000, 99999999),
        ]);
        $adminResponse = $this->actingAs($admin)->get(route('portal.bookings.invoice', $bookingA->id));
        $adminResponse->assertStatus(200);
    }

    /**
     * Test wallet view displays 1-tap quick amount chips and payment instructions.
     */
    public function test_wallet_view_displays_quick_amount_chips_and_payment_instructions(): void
    {
        $client = User::factory()->create([
            'role' => 'client',
            'email' => 'client_w_' . Str::random(6) . '@soulyeg.online',
            'phone' => '012' . rand(10000000, 99999999),
        ]);
        $response = $this->actingAs($client)->get(route('portal.wallet'));

        $response->assertStatus(200);
        $response->assertSee('فودافون كاش (Vodafone Cash)');
        $response->assertSee('إنستاباي اللحظي (InstaPay)');
        $response->assertSee('01090623326');
        $response->assertSee('souly@instapay');
        $response->assertSee('1-Tap Fast Amount');
        $response->assertSee('رمز الـ QR المباشر للتحويل');
    }
}
