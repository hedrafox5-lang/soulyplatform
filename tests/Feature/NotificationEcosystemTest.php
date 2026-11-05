<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Notification;
use App\Models\Service;
use App\Models\ServicePackage;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Services\SoulyAiService;
use App\Services\SoulyNotificationService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

class NotificationEcosystemTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;
    protected User $client;
    protected Service $service;
    protected ServicePackage $package;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin_' . Str::random(6) . '@soulyeg.online',
            'phone' => '010' . rand(10000000, 99999999),
        ]);

        $this->client = User::factory()->create([
            'role' => 'client',
            'email' => 'client_' . Str::random(6) . '@soulyeg.online',
            'phone' => '012' . rand(10000000, 99999999),
        ]);

        $this->service = Service::first() ?: Service::create([
            'name' => 'نظام BrandERP',
            'slug' => 'brand-erp',
            'short_description' => 'نظام سحابي متكامل',
            'full_description' => 'نظام سحابي متكامل',
            'hero_badge' => 'سحابي',
            'is_active' => true,
        ]);

        $this->package = $this->service->packages()->first() ?: ServicePackage::create([
            'service_id' => $this->service->id,
            'name' => 'باقة الانطلاق',
            'slug' => 'starter',
            'price' => 850.00,
            'unit_label' => 'شهرياً',
            'warranty_days' => 30,
            'is_active' => true,
        ]);
    }

    /**
     * Test notification creation via SoulyNotificationService and User model relationships.
     */
    public function test_notification_service_and_user_relationship(): void
    {
        $notif = SoulyNotificationService::send(
            userId: $this->client->id,
            type: 'wallet',
            title: 'إشعار تجريبي',
            message: 'تم شحن المحفظة بنجاح بمبلغ 500 ج.م',
            actionUrl: '/portal/wallet'
        );

        $this->assertInstanceOf(Notification::class, $notif);
        $this->assertFalse($notif->is_read);
        $this->assertEquals('wallet', $notif->icon);

        // Check relation on User
        $this->assertTrue($this->client->notifications()->where('id', $notif->id)->exists());
        $this->assertEquals(1, $this->client->unreadNotifications()->where('id', $notif->id)->count());

        // Check Unread Count
        $unreadCount = SoulyNotificationService::getUnreadCount($this->client->id);
        $this->assertGreaterThanOrEqual(1, $unreadCount);
    }

    /**
     * Test automated notification triggers on Wallet deposit and admin approval/rejection.
     */
    public function test_wallet_deposit_and_approval_triggers_notifications(): void
    {
        // 1. Client submits deposit
        $depositResponse = $this->actingAs($this->client)->post(route('portal.wallet.deposit'), [
            'amount' => 750,
            'payment_method' => 'vodafone_cash',
            'sender_number_or_account' => '01012345678',
        ]);

        $depositResponse->assertRedirect(route('portal.wallet'));

        // Verify Admin received notification for pending deposit
        $adminNotif = Notification::where('user_id', $this->admin->id)
            ->where('type', 'wallet')
            ->where('title', 'like', '%إيداع جديد%')
            ->first();
        $this->assertNotNull($adminNotif);
        $this->assertStringContainsString('750', $adminNotif->message);

        // 2. Admin approves deposit
        $tx = WalletTransaction::where('user_id', $this->client->id)->where('status', 'pending')->first();
        $this->assertNotNull($tx);

        $approveResponse = $this->actingAs($this->admin)->post(route('admin.wallets.approve', $tx));
        $approveResponse->assertRedirect();

        // Verify Client received notification for approval
        $clientNotif = Notification::where('user_id', $this->client->id)
            ->where('type', 'wallet')
            ->where('title', 'like', '%اعتماد شحن%')
            ->first();
        $this->assertNotNull($clientNotif);
        $this->assertStringContainsString('750', $clientNotif->message);
    }

    /**
     * Test automated notification triggers on Bookings (creation & status change).
     */
    public function test_booking_creation_and_status_change_triggers_notifications(): void
    {
        $wallet = $this->client->walletOrCreate();
        $wallet->update(['balance' => 2000.00]);

        // 1. Client creates booking via wallet
        $bookingResponse = $this->actingAs($this->client)->post(route('portal.bookings.store'), [
            'service_id' => $this->service->id,
            'service_package_id' => $this->package->id,
            'quantity_or_hours' => 1,
            'payment_method' => 'wallet',
        ]);

        $bookingResponse->assertRedirect(route('portal.bookings'));

        // Verify Admin & Client notifications
        $adminBookingNotif = Notification::where('user_id', $this->admin->id)
            ->where('type', 'booking')
            ->first();
        $this->assertNotNull($adminBookingNotif);

        $clientBookingNotif = Notification::where('user_id', $this->client->id)
            ->where('type', 'booking')
            ->first();
        $this->assertNotNull($clientBookingNotif);

        // 2. Admin updates booking status to in_progress
        $booking = Booking::where('user_id', $this->client->id)->latest()->first();
        $this->assertNotNull($booking);

        $updateResponse = $this->actingAs($this->admin)->post(route('admin.bookings.update', $booking), [
            'status' => 'in_progress',
            'payment_status' => 'paid',
        ]);
        $updateResponse->assertRedirect();

        $clientStatusNotif = Notification::where('user_id', $this->client->id)
            ->where('type', 'booking')
            ->where('message', 'like', '%قيد التنفيذ الهندسي%')
            ->first();
        $this->assertNotNull($clientStatusNotif);
    }

    /**
     * Test automated notification triggers on Support Tickets creation and replies.
     */
    public function test_support_ticket_lifecycle_triggers_notifications(): void
    {
        // 1. Client creates a ticket
        $ticketRes = $this->actingAs($this->client)->post(route('portal.tickets.store'), [
            'subject' => 'استفسار عن ربط الفاتورة الإلكترونية',
            'department' => 'erp',
            'priority' => 'medium',
            'message' => 'نرجو توضيح خطوات ربط التوكن والشهادة الرقمية',
        ]);

        $ticketRes->assertRedirect();

        $ticket = Ticket::where('user_id', $this->client->id)->latest()->first();
        $this->assertNotNull($ticket);

        // Verify Admin received ticket alert
        $adminTicketNotif = Notification::where('user_id', $this->admin->id)
            ->where('type', 'ticket')
            ->where('title', 'like', '%تذكرة دعم فني جديدة%')
            ->first();
        $this->assertNotNull($adminTicketNotif);

        // 2. Staff/Admin replies to ticket
        $replyRes = $this->actingAs($this->admin)->post(route('portal.tickets.reply', $ticket), [
            'message' => 'أهلاً بك يا فندم، يمكنك تحميل ملف التعريف من الرابط التالي وسيتواصل معك مهندسنا.',
        ]);
        $replyRes->assertRedirect();

        // Verify Client received reply notification
        $clientReplyNotif = Notification::where('user_id', $this->client->id)
            ->where('type', 'ticket')
            ->where('title', 'like', '%رد جديد من فريق الدعم%')
            ->first();
        $this->assertNotNull($clientReplyNotif);
    }

    /**
     * Test Souly AI capturing Egyptian lead triggers notification to Admins.
     */
    public function test_ai_agent_egyptian_lead_capture_triggers_admin_notification(): void
    {
        $aiService = new SoulyAiService();
        $phone = '01098765432';

        $aiService->reply(
            message: "معاك أ. عادل فوزي وعاوز نظام ERP وهذا رقمي: {$phone}",
            visitorName: 'عادل فوزي',
            visitorPhone: $phone
        );

        $adminCrmNotif = Notification::where('user_id', $this->admin->id)
            ->where('type', 'crm')
            ->where('title', 'like', '%عميل محتمل جديد عبر سولي AI%')
            ->first();

        $this->assertNotNull($adminCrmNotif);
        $this->assertStringContainsString($phone, $adminCrmNotif->message);
    }

    /**
     * Test Notification drawer AJAX endpoints:
     * - GET /notifications
     * - POST /notifications/{id}/read
     * - POST /notifications/mark-all-read
     */
    public function test_notification_drawer_ajax_endpoints(): void
    {
        $notif1 = SoulyNotificationService::send($this->client->id, 'info', 'تنبيه 1', 'محتوى 1');
        $notif2 = SoulyNotificationService::send($this->client->id, 'wallet', 'تنبيه 2', 'محتوى 2');

        // 1. Fetch List
        $listRes = $this->actingAs($this->client)->getJson(route('notifications.list'));
        $listRes->assertStatus(200);
        $listRes->assertJsonStructure([
            'success',
            'unread_count',
            'notifications',
        ]);
        $this->assertGreaterThanOrEqual(2, $listRes->json('unread_count'));

        // 2. Mark Single Read
        $readRes = $this->actingAs($this->client)->postJson(route('notifications.read', $notif1));
        $readRes->assertStatus(200);
        $this->assertTrue($notif1->fresh()->is_read);

        // 3. Mark All Read
        $allReadRes = $this->actingAs($this->client)->postJson(route('notifications.mark_all_read'));
        $allReadRes->assertStatus(200);
        $this->assertEquals(0, $allReadRes->json('unread_count'));
        $this->assertTrue($notif2->fresh()->is_read);
    }

    /**
     * Test Admin manual notification broadcaster dispatch endpoint.
     */
    public function test_admin_can_send_manual_notification_broadcast(): void
    {
        // 1. Broadcast to All
        $resAll = $this->actingAs($this->admin)->postJson(route('admin.notifications.send_manual'), [
            'recipient' => 'all',
            'type' => 'system',
            'title' => 'تحديث مجدول للسيرفر',
            'message' => 'سيتم إجراء صيانة مجدولة لمدة 5 دقائق فجر الغد.',
            'action_url' => '/status',
        ]);

        $resAll->assertStatus(200);
        $resAll->assertJson(['success' => true]);

        $broadcast = Notification::whereNull('user_id')
            ->where('title', 'تحديث مجدول للسيرفر')
            ->first();
        $this->assertNotNull($broadcast);

        // 2. Send to Specific Client
        $resClient = $this->actingAs($this->admin)->postJson(route('admin.notifications.send_manual'), [
            'recipient' => (string) $this->client->id,
            'type' => 'wallet',
            'title' => 'مكافأة شحن خاصة لحسابك',
            'message' => 'تم منحك رصيد إضافي بقيمة 200 ج.م على عملية الشحن القادمة.',
            'action_url' => '/portal/wallet',
        ]);

        $resClient->assertStatus(200);
        $personalNotif = Notification::where('user_id', $this->client->id)
            ->where('title', 'مكافأة شحن خاصة لحسابك')
            ->first();
        $this->assertNotNull($personalNotif);
    }
}
