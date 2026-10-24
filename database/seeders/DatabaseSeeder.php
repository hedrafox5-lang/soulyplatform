<?php

namespace Database\Seeders;

use App\Models\AiConversation;
use App\Models\AiMessage;
use App\Models\Booking;
use App\Models\CannedResponse;
use App\Models\CannedResponseCategory;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\Service;
use App\Models\ServicePackage;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ---------------------------------------------------------------------
        // 1. CORE USER ACCOUNTS & FINANCIAL WALLETS
        // ---------------------------------------------------------------------
        $admin = User::firstOrCreate(
            ['email' => 'admin@soulyeg.online'],
            [
                'name' => 'Hedra Adel',
                'password' => Hash::make('Password123!'),
                'role' => 'admin',
                'phone' => '+201000000001',
                'company_name' => 'سولي للتكنولوجيا والأنظمة',
                'avatar' => null,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $adminWallet = Wallet::firstOrCreate(
            ['user_id' => $admin->id],
            [
                'balance' => 50000.00,
                'currency' => 'EGP',
                'is_locked' => false,
            ]
        );

        WalletTransaction::firstOrCreate(
            ['reference_code' => 'TXN-ADM-TREASURY'],
            [
                'wallet_id' => $adminWallet->id,
                'user_id' => $admin->id,
                'type' => 'deposit',
                'amount' => 50000.00,
                'payment_method' => 'system',
                'sender_number_or_account' => 'TREASURY-01',
                'status' => 'approved',
                'reviewed_by' => $admin->id,
                'admin_notes' => 'تخصيص رصيد الخزينة السيادية لمدير المنظومة',
            ]
        );

        $staff = User::firstOrCreate(
            ['email' => 'staff@soulyeg.online'],
            [
                'name' => 'Souly Operations',
                'password' => Hash::make('Password123!'),
                'role' => 'staff',
                'phone' => '+201000000002',
                'company_name' => 'فريق عمليات سولي سيتادل',
                'avatar' => null,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        Wallet::firstOrCreate(
            ['user_id' => $staff->id],
            [
                'balance' => 0.00,
                'currency' => 'EGP',
                'is_locked' => false,
            ]
        );

        $client = User::firstOrCreate(
            ['email' => 'client@soulyeg.online'],
            [
                'name' => 'عميل تجريبي',
                'password' => Hash::make('Password123!'),
                'role' => 'client',
                'phone' => '+201000000003',
                'company_name' => 'مؤسسة النور والرواد للتجارة',
                'avatar' => null,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $clientWallet = Wallet::firstOrCreate(
            ['user_id' => $client->id],
            [
                'balance' => 3500.00,
                'currency' => 'EGP',
                'is_locked' => false,
            ]
        );

        WalletTransaction::firstOrCreate(
            ['reference_code' => 'TXN-CLI-OPENING'],
            [
                'wallet_id' => $clientWallet->id,
                'user_id' => $client->id,
                'type' => 'deposit',
                'amount' => 3500.00,
                'payment_method' => 'instapay',
                'sender_number_or_account' => '01000000003@instapay',
                'status' => 'approved',
                'reviewed_by' => $admin->id,
                'admin_notes' => 'إيداع تجريبي عبر إنستاباي لتفعيل خدمات المنصة',
            ]
        );

        // ---------------------------------------------------------------------
        // 2. THE 6 CORE SERVICES CATALOG (Comprehensive Seeding)
        // ---------------------------------------------------------------------
        $this->call(ServiceCatalogSeeder::class);

        $erp = Service::where('slug', 'brand-erp')->first();
        $erpStarterPackage = ServicePackage::where('service_id', $erp->id)->where('slug', 'starter')->first();

        // ---------------------------------------------------------------------
        // 3. CANNED RESPONSE CATEGORIES & SMART RESPONSES
        // ---------------------------------------------------------------------
        $catGeneral = CannedResponseCategory::firstOrCreate(
            ['slug' => 'general-welcome'],
            [
                'name' => 'استفسارات عامة وترحيب',
                'icon' => 'chat-bubble-oval-left-ellipsis',
                'accent_color' => '#00F5D4',
                'display_order' => 1,
            ]
        );

        $catErp = CannedResponseCategory::firstOrCreate(
            ['slug' => 'brand-erp-responses'],
            [
                'name' => 'نظام BrandERP',
                'icon' => 'building-office-2',
                'accent_color' => '#00F5D4',
                'display_order' => 2,
            ]
        );

        $catAi = CannedResponseCategory::firstOrCreate(
            ['slug' => 'ai-agents-responses'],
            [
                'name' => 'موظفو الذكاء الاصطناعي AI Agents',
                'icon' => 'cpu-chip',
                'accent_color' => '#7B2CBF',
                'display_order' => 3,
            ]
        );

        $catCyber = CannedResponseCategory::firstOrCreate(
            ['slug' => 'cybersecurity-responses'],
            [
                'name' => 'الأمن السيبراني والتدريع',
                'icon' => 'shield-check',
                'accent_color' => '#FFB800',
                'display_order' => 4,
            ]
        );

        $catWallet = CannedResponseCategory::firstOrCreate(
            ['slug' => 'wallet-billing-responses'],
            [
                'name' => 'المحفظة والمدفوعات الإلكترونية',
                'icon' => 'wallet',
                'accent_color' => '#10B981',
                'display_order' => 5,
            ]
        );

        CannedResponse::firstOrCreate(
            ['shortcut_code' => '/welcome'],
            [
                'category_id' => $catGeneral->id,
                'title' => 'ترحيب رسمي وتعريف بمنظومة سولي',
                'content' => "أهلاً بك في سولي ديدجيتال سيتادل (soulyeg.online) 🛡️\nيسعدنا مساعدتك في تسريع نمو وتدريع أعمالك رقمياً. كيف يمكن لفريقنا ووكلاؤنا الأذكياء خدمتك اليوم؟",
                'tags_json' => ['ترحيب', 'بداية', 'خدمة_عملاء'],
                'usage_count' => 142,
                'created_by' => $admin->id,
                'is_active' => true,
            ]
        );

        CannedResponse::firstOrCreate(
            ['shortcut_code' => '/erp-tax'],
            [
                'category_id' => $catErp->id,
                'title' => 'شرح الفاتورة الإلكترونية لنظام BrandERP',
                'content' => "نظام BrandERP معتمد ومتوافق كلياً مع منظومة الفاتورة والإيصال الإلكتروني لمصلحة الضرائب المصرية (ETA). يتم الإرسال والاعتماد لحظياً بضغطة زر دون أي تعقيد محاسبي.",
                'tags_json' => ['ضرائب', 'فاتورة_الكترونية', 'erp'],
                'usage_count' => 89,
                'created_by' => $admin->id,
                'is_active' => true,
            ]
        );

        CannedResponse::firstOrCreate(
            ['shortcut_code' => '/ai-features'],
            [
                'category_id' => $catAi->id,
                'title' => 'مزايا موظف المبيعات الذكي AI Agent',
                'content' => "موظف الذكاء الاصطناعي من سولي يتحدث اللهجة المصرية الدارجة والفصحى، متصل بكتالوج منتجاتك ونظام إدارة المخزون، ويرد في أقل من 3 ثوانٍ على الواتساب ووسائل التواصل لإتمام الطلبات 24/7.",
                'tags_json' => ['ai', 'مبيعات', 'واتساب'],
                'usage_count' => 67,
                'created_by' => $admin->id,
                'is_active' => true,
            ]
        );

        CannedResponse::firstOrCreate(
            ['shortcut_code' => '/pay-info'],
            [
                'category_id' => $catWallet->id,
                'title' => 'بيانات الشحن عبر إنستاباي وفودافون كاش',
                'content' => "يمكنك شحن رصيد محفظتك الرقمية فورياً عبر:\n1. إنستاباي (InstaPay): souly@instapay\n2. فودافون كاش: 010xxxxxxxx\nيرجى رفع إيصال التحويل عبر بوابة المحفظة ليتم الاعتماد وإضافة الرصيد في دقائق.",
                'tags_json' => ['دفع', 'انستاباي', 'شحن_محفظة'],
                'usage_count' => 115,
                'created_by' => $admin->id,
                'is_active' => true,
            ]
        );

        // ---------------------------------------------------------------------
        // 4. SAMPLE CLIENT BOOKING, TICKET & MESSAGES
        // ---------------------------------------------------------------------
        $sampleBooking = Booking::firstOrCreate(
            ['booking_number' => 'BKG-CITADEL-001'],
            [
                'user_id' => $client->id,
                'service_id' => $erp->id,
                'service_package_id' => $erpStarterPackage ? $erpStarterPackage->id : null,
                'assigned_staff_id' => $staff->id,
                'quantity_or_hours' => 1,
                'total_amount' => 850.00,
                'payment_method' => 'wallet',
                'payment_status' => 'paid',
                'status' => 'confirmed',
                'scheduled_at' => now()->addDays(2),
                'client_requirements' => 'نرغب في ربط نظام نقاط البيع لفرعنا الرئيسي في القاهرة وربطه بالختم الإلكتروني للضرائب.',
                'staff_report' => 'تم استلام بيانات التفعيل وتجهيز قاعدة البيانات الخاصة بالعميل على السيرفر السحابي.',
            ]
        );

        $ticket = Ticket::firstOrCreate(
            ['ticket_number' => 'TCK-CITADEL-001'],
            [
                'user_id' => $client->id,
                'booking_id' => $sampleBooking->id,
                'assigned_to' => $staff->id,
                'subject' => 'استفسار بخصوص ربط الختم الإلكتروني الفوري',
                'department' => 'erp',
                'priority' => 'high',
                'status' => 'staff_replied',
            ]
        );

        if ($ticket->messages()->count() === 0) {
            TicketMessage::create([
                'ticket_id' => $ticket->id,
                'user_id' => $client->id,
                'message' => 'السلام عليكم، قمنا باستخراج فلاشة الختم الإلكتروني من شركة مصر للمقاصة، كيف يمكننا ربطها بالسيرفر السحابي؟',
                'attachments_json' => null,
                'is_internal_note' => false,
            ]);

            TicketMessage::create([
                'ticket_id' => $ticket->id,
                'user_id' => $staff->id,
                'message' => 'وعليكم السلام ورحمة الله، تم تجهيز تطبيق Souly Signer لربط الختم المحلي بالسحابة آلياً. سيقوم المهندس بالتواصل معك خلال ساعة لإتمام الإعداد.',
                'attachments_json' => null,
                'is_internal_note' => false,
            ]);
        }

        // ---------------------------------------------------------------------
        // 5. SAMPLE CRM LEADS & AI AGENT CONVERSATION
        // ---------------------------------------------------------------------
        $lead = Lead::firstOrCreate(
            ['phone' => '+201099887766'],
            [
                'name' => 'أحمد فتحي القاضي',
                'email' => 'ahmed.fathy@al-fajr.com',
                'company_name' => 'مجموعة الفجر للصناعات الغذائية',
                'business_activity' => 'تصنيع وتوزيع منتجات الألبان والعصائر',
                'interested_service_id' => $erp->id,
                'interested_package_id' => null,
                'source' => 'ai_agent',
                'status' => 'qualified',
                'assigned_to' => $staff->id,
                'ai_summary' => 'العميل يمتلك مصنعاً و 3 مخازن توزيع، يبحث عن حل أتمتة لأوامر التصنيع وتتبع الصلاحيات والفاتورة الإلكترونية.',
                'notes' => 'تم التواصل الأولي ونال عرض الأسعار قبوله، بانتظار اجتماع أونلاين يوم الأحد.',
            ]
        );

        if ($lead->activities()->count() === 0) {
            LeadActivity::create([
                'lead_id' => $lead->id,
                'user_id' => null,
                'type' => 'ai_capture',
                'description' => 'تم التقاط بيانات العميل وتأهيل اهتمامه عبر وكيل المبيعات الذكي على الواتساب.',
            ]);

            LeadActivity::create([
                'lead_id' => $lead->id,
                'user_id' => $staff->id,
                'type' => 'call',
                'description' => 'مكالمة هاتفية مدتها 15 دقيقة لمناقشة المتطلبات الفنية للمصنع.',
            ]);
        }

        $aiConv = AiConversation::firstOrCreate(
            ['lead_id' => $lead->id],
            [
                'session_uuid' => (string) Str::uuid(),
                'user_id' => null,
                'device_type' => 'mobile',
                'detected_intent' => 'erp_inquiry',
                'pinecone_namespace' => 'souly-knowledge-v1',
                'is_escalated' => false,
            ]
        );

        if ($aiConv->messages()->count() === 0) {
            AiMessage::create([
                'ai_conversation_id' => $aiConv->id,
                'sender' => 'visitor',
                'content' => 'السلام عليكم، هل لديكم نظام ERP يدعم المصانع وتتبع تواريخ الصلاحية؟',
                'tokens_used' => 18,
            ]);

            AiMessage::create([
                'ai_conversation_id' => $aiConv->id,
                'sender' => 'souly_ai',
                'content' => 'وعليكم السلام يا فندم! بالتأكيد، نظام BrandERP يشتمل على موديول تصنيع متكامل (BOM) مع تتبع دقيق لأرقام التشغيلات (Batch Numbers) وتواريخ الصلاحية وتنبيهات الهدر. تحب أحد مهندسينا ينسق معاك ديمو حي للمصنع؟',
                'suggested_links_json' => ['https://soulyeg.online/#brand-erp'],
                'tokens_used' => 64,
            ]);
        }
    }
}
