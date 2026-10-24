<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Lead;
use App\Models\Service;
use App\Models\ServicePackage;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use App\Models\WalletTransaction;
use Tests\TestCase;

class Phase6ClientPortalAndWalletTest extends TestCase
{
    /**
     * Test guest registration creates user, wallet with 0.00 EGP, CRM lead, and logs them in.
     */
    public function test_guest_can_register_and_receives_active_wallet(): void
    {
        $uniqueEmail = 'newclient_' . uniqid() . '@example.com';

        $response = $this->post('/register', [
            'name' => 'المهندس كريم التميمي',
            'email' => $uniqueEmail,
            'phone' => '01019283746',
            'company_name' => 'شركة التميمي للحلول',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
        ]);

        $response->assertRedirect(route('portal.index'));
        $this->assertAuthenticated();

        $user = User::where('email', $uniqueEmail)->first();
        $this->assertNotNull($user);
        $this->assertEquals('client', $user->role);

        // Check wallet
        $this->assertNotNull($user->wallet);
        $this->assertEquals(0.00, (float) $user->wallet->balance);
        $this->assertEquals('EGP', $user->wallet->currency);

        // Check lead capture
        $lead = Lead::where('email', $uniqueEmail)->first();
        $this->assertNotNull($lead);

        // Clean up
        $user->wallet->delete();
        $user->delete();
        $lead->delete();
    }

    /**
     * Test 1-click Demo Login for client, staff, and admin accounts.
     */
    public function test_one_click_demo_login_authenticates_roles(): void
    {
        // 1. Client Demo
        $clientResponse = $this->post('/login/demo/client');
        $clientResponse->assertRedirect(route('portal.index'));
        $this->assertAuthenticated();
        $this->assertEquals('client@soulyeg.online', auth()->user()->email);

        // 2. Staff Demo
        $staffResponse = $this->post('/login/demo/staff');
        $staffResponse->assertRedirect(route('portal.index'));
        $this->assertAuthenticated();
        $this->assertEquals('staff@soulyeg.online', auth()->user()->email);

        // 3. Admin Demo
        $adminResponse = $this->post('/login/demo/admin');
        $adminResponse->assertRedirect(route('portal.index'));
        $this->assertAuthenticated();
        $this->assertEquals('admin@soulyeg.online', auth()->user()->email);
    }

    /**
     * Test logout invalidates session and redirects to login.
     */
    public function test_logout_invalidates_session_and_redirects_to_login(): void
    {
        $client = User::where('email', 'client@soulyeg.online')->firstOrFail();
        $this->actingAs($client);

        $response = $this->post('/logout');
        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    /**
     * Test client submitting a wallet top-up request.
     */
    public function test_client_can_submit_wallet_deposit_request(): void
    {
        $client = User::where('email', 'client@soulyeg.online')->firstOrFail();
        $this->actingAs($client);

        $response = $this->post('/portal/wallet/deposit', [
            'amount' => 1500.00,
            'payment_method' => 'vodafone_cash',
            'sender_number_or_account' => '01099887766',
        ]);

        $response->assertRedirect(route('portal.wallet'));

        $tx = WalletTransaction::where('user_id', $client->id)
            ->where('type', 'deposit')
            ->where('amount', 1500.00)
            ->first();

        $this->assertNotNull($tx);
        $this->assertEquals('pending', $tx->status);
        $this->assertEquals('vodafone_cash', $tx->payment_method);
        $this->assertStringStartsWith('DEP-', $tx->reference_code);

        // Cleanup
        $tx->delete();
    }

    /**
     * Test booking with wallet payment: balance deducted atomically, status paid and confirmed.
     */
    public function test_booking_with_wallet_payment_deducts_balance_atomically_and_confirms_booking(): void
    {
        $client = User::where('email', 'client@soulyeg.online')->firstOrFail();
        $wallet = $client->walletOrCreate();

        // Ensure user has at least 3500.00 balance for test
        $wallet->balance = 3500.00;
        $wallet->save();

        $this->actingAs($client);

        $service = Service::where('slug', 'brand-erp')->firstOrFail();
        $package = ServicePackage::where('service_id', $service->id)->where('slug', 'starter')->firstOrFail();
        $packagePrice = (float) $package->price; // 850.00

        $response = $this->post('/portal/bookings', [
            'service_id' => $service->id,
            'service_package_id' => $package->id,
            'quantity_or_hours' => 1,
            'payment_method' => 'wallet',
            'scheduled_at' => now()->addDays(2)->format('Y-m-d'),
            'client_requirements' => 'الرجاء ربط أجهزة الباركود وتفعيل الفاتورة الإلكترونية',
        ]);

        $response->assertRedirect(route('portal.bookings'));

        // Check wallet balance deducted
        $wallet->refresh();
        $this->assertEquals(3500.00 - $packagePrice, (float) $wallet->balance);

        // Check wallet purchase transaction
        $purchaseTx = WalletTransaction::where('user_id', $client->id)
            ->where('type', 'purchase')
            ->where('amount', $packagePrice)
            ->latest()
            ->first();

        $this->assertNotNull($purchaseTx);
        $this->assertEquals('approved', $purchaseTx->status);

        // Check Booking
        $booking = Booking::where('user_id', $client->id)
            ->where('service_id', $service->id)
            ->latest()
            ->first();

        $this->assertNotNull($booking);
        $this->assertEquals('paid', $booking->payment_status);
        $this->assertEquals('confirmed', $booking->status);
        $this->assertEquals('wallet', $booking->payment_method);

        // Restore wallet balance
        $wallet->balance = 3500.00;
        $wallet->save();

        // Cleanup
        $purchaseTx->delete();
        $booking->delete();
    }

    /**
     * Test booking with insufficient wallet balance triggers validation error.
     */
    public function test_booking_with_insufficient_wallet_balance_returns_error(): void
    {
        $poorUser = User::create([
            'name' => 'عميل رصيده صفر',
            'email' => 'zerobalance_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'role' => 'client',
            'is_active' => true,
        ]);
        $poorWallet = $poorUser->walletOrCreate();
        $poorWallet->balance = 0.00;
        $poorWallet->save();

        $this->actingAs($poorUser);

        $service = Service::where('slug', 'brand-erp')->firstOrFail();
        $package = ServicePackage::where('service_id', $service->id)->firstOrFail();

        $response = $this->post('/portal/bookings', [
            'service_id' => $service->id,
            'service_package_id' => $package->id,
            'quantity_or_hours' => 1,
            'payment_method' => 'wallet',
        ]);

        $response->assertSessionHasErrors(['payment_method']);

        // Assert balance unchanged
        $poorWallet->refresh();
        $this->assertEquals(0.00, (float) $poorWallet->balance);

        // Clean up
        $poorWallet->delete();
        $poorUser->delete();
    }

    /**
     * Test booking with direct transfer creates pending verification booking and logs lead.
     */
    public function test_booking_with_direct_transfer_creates_pending_verification(): void
    {
        $client = User::where('email', 'client@soulyeg.online')->firstOrFail();
        $this->actingAs($client);

        $service = Service::where('slug', 'maintenance')->firstOrFail();

        $response = $this->post('/portal/bookings', [
            'service_id' => $service->id,
            'quantity_or_hours' => 3,
            'payment_method' => 'direct_transfer',
            'client_requirements' => 'صيانة شبكات وسيرفرات الشركة فرع المعادي',
        ]);

        $response->assertRedirect(route('portal.bookings'));

        $booking = Booking::where('user_id', $client->id)
            ->where('service_id', $service->id)
            ->latest()
            ->first();

        $this->assertNotNull($booking);
        $this->assertEquals('pending_verification', $booking->payment_status);
        $this->assertEquals('new', $booking->status);
        $this->assertEquals('direct_transfer', $booking->payment_method);

        // Clean up
        $booking->delete();
    }

    /**
     * Test client opening a support ticket and replying to it.
     */
    public function test_client_can_open_ticket_and_exchange_messages(): void
    {
        $client = User::where('email', 'client@soulyeg.online')->firstOrFail();
        $this->actingAs($client);

        // 1. Open Ticket
        $response = $this->post('/portal/tickets', [
            'subject' => 'استفسار حول دمج الكاشير مع طابعات الإيصالات الحرارية',
            'department' => 'erp',
            'priority' => 'high',
            'message' => 'مرحباً مهندس سولي.. نواجه صعوبة في تعريف طابعة إيصالات USB على جهاز الكاشير في الفرع الرئيسي.',
        ]);

        $ticket = Ticket::where('user_id', $client->id)->latest()->first();
        $this->assertNotNull($ticket);
        $response->assertRedirect(route('portal.tickets.show', $ticket));
        $this->assertEquals('open', $ticket->status);
        $this->assertStringStartsWith('TCK-', $ticket->ticket_number);

        // Check initial message
        $firstMsg = TicketMessage::where('ticket_id', $ticket->id)->first();
        $this->assertNotNull($firstMsg);
        $this->assertEquals($client->id, $firstMsg->user_id);

        // 2. Post Reply
        $replyResponse = $this->post("/portal/tickets/{$ticket->id}/reply", [
            'message' => 'تم تجربة المنفذ الثاني وعملت الطابعة بنجاح، شكراً لكم!',
        ]);

        $replyResponse->assertSessionHasNoErrors();
        $ticket->refresh();
        $this->assertEquals('client_replied', $ticket->status);

        $messagesCount = TicketMessage::where('ticket_id', $ticket->id)->count();
        $this->assertEquals(2, $messagesCount);

        // Clean up
        TicketMessage::where('ticket_id', $ticket->id)->delete();
        $ticket->delete();
    }
}
