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
use App\Models\ServiceIcon;
use App\Models\ServicePackage;
use App\Models\ServiceRow;
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
        // 1. CORE USER ACCOUNTS & WALLETS
        // ---------------------------------------------------------------------
        $admin = User::create([
            'name' => 'Hedra Adel',
            'email' => 'admin@soulyeg.online',
            'password' => Hash::make('Password123!'),
            'role' => 'admin',
            'phone' => '+201000000001',
            'company_name' => 'سولي للتكنولوجيا والأنظمة',
            'avatar' => null,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $adminWallet = Wallet::create([
            'user_id' => $admin->id,
            'balance' => 50000.00,
            'currency' => 'EGP',
            'is_locked' => false,
        ]);

        WalletTransaction::create([
            'wallet_id' => $adminWallet->id,
            'user_id' => $admin->id,
            'reference_code' => 'TXN-ADM-' . strtoupper(Str::random(8)),
            'type' => 'deposit',
            'amount' => 50000.00,
            'payment_method' => 'system',
            'sender_number_or_account' => 'TREASURY-01',
            'status' => 'approved',
            'reviewed_by' => $admin->id,
            'admin_notes' => 'تخصيص رصيد الخزينة السيادية لمدير المنظومة',
        ]);

        $staff = User::create([
            'name' => 'Souly Operations',
            'email' => 'staff@soulyeg.online',
            'password' => Hash::make('Password123!'),
            'role' => 'staff',
            'phone' => '+201000000002',
            'company_name' => 'فريق عمليات سولي سيتادل',
            'avatar' => null,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        Wallet::create([
            'user_id' => $staff->id,
            'balance' => 0.00,
            'currency' => 'EGP',
            'is_locked' => false,
        ]);

        $client = User::create([
            'name' => 'عميل تجريبي',
            'email' => 'client@soulyeg.online',
            'password' => Hash::make('Password123!'),
            'role' => 'client',
            'phone' => '+201000000003',
            'company_name' => 'مؤسسة النور والرواد للتجارة',
            'avatar' => null,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $clientWallet = Wallet::create([
            'user_id' => $client->id,
            'balance' => 3500.00,
            'currency' => 'EGP',
            'is_locked' => false,
        ]);

        WalletTransaction::create([
            'wallet_id' => $clientWallet->id,
            'user_id' => $client->id,
            'reference_code' => 'TXN-CLI-' . strtoupper(Str::random(8)),
            'type' => 'deposit',
            'amount' => 3500.00,
            'payment_method' => 'instapay',
            'sender_number_or_account' => '01000000003@instapay',
            'status' => 'approved',
            'reviewed_by' => $admin->id,
            'admin_notes' => 'إيداع تجريبي عبر إنستاباي لتفعيل خدمات المنصة',
        ]);

        // ---------------------------------------------------------------------
        // 2. THE 6 CORE SERVICES
        // ---------------------------------------------------------------------

        // Service 1: BrandERP
        $erp = Service::create([
            'slug' => 'brand-erp',
            'name' => 'نظام BrandERP المتكامل',
            'tagline' => 'إدارة متكاملة ومؤتمتة للمخزون، الفواتير، ونقاط البيع السحابية',
            'short_description' => 'منظومة تخطيط موارد المؤسسات المتطورة المصممة للشركات والمصانع والمتاجر المتنامية مع ربط فوري بالفاتورة والإيصال الإلكتروني المصري.',
            'hero_badge' => 'الأكثر طلباً للشركات',
            'icon_key' => 'building-office-2',
            'accent_color' => '#00F5D4',
            'secondary_color' => '#7B2CBF',
            'hero_slides_json' => [
                ['title' => 'ربط لحظي بالفاتورة الإلكترونية', 'desc' => 'تكامل معتمد 100% مع مصلحة الضرائب المصرية'],
                ['title' => 'إدارة دقيقة لتعدد الفروع والمخازن', 'desc' => 'مزامنة لحظية لحركة الأصناف والتحويلات'],
            ],
            'stats_json' => [
                ['label' => 'دقة العمليات', 'value' => '99.9%'],
                ['label' => 'توفير التكاليف', 'value' => '45%'],
                ['label' => 'زمن الفوترة', 'value' => '< 1s'],
            ],
            'display_order' => 1,
            'is_active' => true,
        ]);

        ServiceRow::create([
            'service_id' => $erp->id,
            'row_key' => 'erp_before_after',
            'row_type' => 'before_after',
            'title' => 'الفارق التشغيلي قبل وبعد تطبيق BrandERP',
            'subtitle' => 'شاهد كيف يتحول العمل الإداري من العشوائية الورقية إلى الأتمتة السحابية الفورية',
            'payload_json' => [
                'before' => ['فواتير ورقية معرضة للتلف', 'فروقات جردية غير مفسرة', 'تعطل البيع عند انقطاع الإنترنت'],
                'after' => ['إيصال وفاتورة إلكترونية معتمدة', 'جرد رقمي وتتبع بالباركود والـ QR', 'عمل أوفلاين مع مزامنة سحابية تلقائية'],
            ],
            'display_order' => 1,
        ]);

        ServiceIcon::create([
            'service_id' => $erp->id,
            'group_type' => 'industry',
            'title' => 'تجار التجزئة والجملة',
            'icon_name' => 'shopping-cart',
            'badge_text' => 'POS فائق',
            'popup_title' => 'BrandERP لقطاع التجزئة والجملة',
            'popup_summary' => 'تحكم كامل بنقاط البيع، عروض الأسعار، مديونيات العملاء والموردين بدقة متناهية.',
            'popup_bullets_json' => ['نقاط بيع سحابية POS', 'متابعة حد الائتمان', 'طباعة فورية لكافة مقاسات الباركود'],
            'popup_cta_text' => 'احجز استشارة BrandERP الآن',
            'display_order' => 1,
        ]);

        ServiceIcon::create([
            'service_id' => $erp->id,
            'group_type' => 'feature',
            'title' => 'الربط الضريبي المباشر',
            'icon_name' => 'document-check',
            'badge_text' => 'معتمد رسمي',
            'popup_title' => 'الربط المباشر مع مصلحة الضرائب المصرية (ETA)',
            'popup_summary' => 'إرسال واستلام الفواتير والإيصالات لحظياً عبر الـ API مع شهادات التوقيع والختم الإلكتروني.',
            'popup_bullets_json' => ['توافق تام مع معايير ETA v1.0', 'تنبيهات فورية بحالة الاعتماد والرفض', 'أرشفة ضريبية لـ 5 سنوات'],
            'popup_cta_text' => 'تفعيل الربط الضريبي لمشروعك',
            'display_order' => 2,
        ]);

        $erpPackage1 = ServicePackage::create([
            'service_id' => $erp->id,
            'slug' => 'brand-erp-starter',
            'name' => 'باقة الانطلاق السحابي',
            'subtitle' => 'مثالية للمتاجر والمؤسسات الناشئة ذات الفرع الواحد',
            'badge_text' => 'بداية سريعة',
            'billing_type' => 'monthly',
            'original_price' => 1200.00,
            'price' => 850.00,
            'min_units' => 1,
            'unit_label' => 'شهرياً',
            'warranty_days' => 30,
            'features_json' => [
                'نقطة بيع POS سحابية واحدة',
                'مستخدمين غير محدودين للصلاحيات',
                'ربط الفاتورة والإيصال الإلكتروني',
                'دعم فني عبر الواتساب',
            ],
            'subpage_sections_json' => ['hero_specs', 'roi_calculator', 'faq'],
            'is_featured' => false,
            'display_order' => 1,
            'is_active' => true,
        ]);

        ServicePackage::create([
            'service_id' => $erp->id,
            'slug' => 'brand-erp-pro',
            'name' => 'باقة النمو الاحترافي (Pro Multi-Branch)',
            'subtitle' => 'للشركات المتوسعة، المصانع، وسلاسل الفروع المتعددة',
            'badge_text' => 'الأكثر شعبية',
            'billing_type' => 'monthly',
            'original_price' => 2500.00,
            'price' => 1800.00,
            'min_units' => 1,
            'unit_label' => 'شهرياً',
            'warranty_days' => 90,
            'features_json' => [
                'حتى 5 فروع ومخازن مركزية',
                'أوامر تصنيع وتكاليف إنتاج ذكية',
                'تكامل كامل مع مصلحة الضرائب والبنوك',
                'تطبيق موبايل لمتابعة الإيرادات لحظياً',
                'دعم فني مخصص ومهندس حسابات مباشر',
            ],
            'is_featured' => true,
            'display_order' => 2,
            'is_active' => true,
        ]);

        // Service 2: AI Agents
        $ai = Service::create([
            'slug' => 'ai-agents',
            'name' => 'موظفو الذكاء الاصطناعي AI Agents',
            'tagline' => 'كوادر ذكية مؤتمتة تعمل 24/7 لخدمة العملاء، إدارة المبيعات، والدعم الفني',
            'short_description' => 'تطوير ونشر وكلاء ذكاء اصطناعي سياديين متصلين بقواعد بياناتك، WhatsApp، Telegram، وموقعك مع فهم لغوي عربي ومصري فائق.',
            'hero_badge' => 'تقنية سيادية 2026',
            'icon_key' => 'cpu-chip',
            'accent_color' => '#7B2CBF',
            'secondary_color' => '#00F5D4',
            'hero_slides_json' => [
                ['title' => 'موظف مبيعات لا ينام أبداً', 'desc' => 'إغلاق الصفقات والرد على استفسارات الأسعار في 3 ثوانٍ'],
                ['title' => 'تكامل واتساب وتيليجرام وموقعك', 'desc' => 'قناة موحدة تدير آلاف المحادثات في نفس اللحظة'],
            ],
            'stats_json' => [
                ['label' => 'معدل الرد الفوري', 'value' => '100%'],
                ['label' => 'زيادة المبيعات', 'value' => '+62%'],
                ['label' => 'ساعات العمل', 'value' => '24/7/365'],
            ],
            'display_order' => 2,
            'is_active' => true,
        ]);

        ServiceIcon::create([
            'service_id' => $ai->id,
            'group_type' => 'feature',
            'title' => 'فهم اللهجة المصرية والعربية',
            'icon_name' => 'chat-bubble-left-right',
            'badge_text' => 'فهم لغوي فائق',
            'popup_title' => 'معالجة اللغة الطبيعية المخصصة للأسواق العربية',
            'popup_summary' => 'الوكيل يفهم المصطلحات التجارية والدارجة ويفاوض العملاء بلباقة بشرية تامة.',
            'popup_bullets_json' => ['فهم الصوت والنصوص', 'تحليل نبرة العميل والاهتمام', 'إرسال روابط المنتجات والدفع مباشرة'],
            'popup_cta_text' => 'اختبر الوكيل الحي الآن',
            'display_order' => 1,
        ]);

        ServicePackage::create([
            'service_id' => $ai->id,
            'slug' => 'ai-agent-sales',
            'name' => 'وكيل المبيعات والرد الآلي (Sales AI Agent)',
            'subtitle' => 'متخصص بإغلاق مبيعات الواتساب والإنستجرام وتأكيد الطلبات',
            'badge_text' => 'عائد فوري',
            'billing_type' => 'monthly',
            'original_price' => 3500.00,
            'price' => 2500.00,
            'min_units' => 1,
            'unit_label' => 'شهرياً',
            'warranty_days' => 30,
            'features_json' => [
                'ربط واتساب بزنس الرسمي Cloud API',
                'حتى 10,000 محادثة شهرياً',
                'ربط بكتالوج المنتجات ونظام المخزون',
                'لوحة تحكم وتحليلات ذكية للمحادثات',
            ],
            'is_featured' => true,
            'display_order' => 1,
            'is_active' => true,
        ]);

        // Service 3: Cybersecurity
        $cyber = Service::create([
            'slug' => 'cybersecurity',
            'name' => 'الأمن السيبراني والتدريع الرقمي',
            'tagline' => 'حصانة سيبرانية، اختبار اختراق، واكتشاف فوري للثغرات البرمجية والشبكية',
            'short_description' => 'خدمات فحص الثغرات واختبارات الاختراق الأخلاقي وتدريع البنية السحابية لحماية أسرار أعمالك وبيانات عملائك من أي اختراق.',
            'hero_badge' => 'حماية عسكرية مشددة',
            'icon_key' => 'shield-check',
            'accent_color' => '#FFB800',
            'secondary_color' => '#FF2A55',
            'hero_slides_json' => [
                ['title' => 'كشف الثغرات قبل استغلالها', 'desc' => 'محاكاة واقعية لهجمات القراصنة المتقدمة'],
                ['title' => 'جدار ناري وتدريع سحابي WAF', 'desc' => 'صد هجمات DDoS وحقن قواعد البيانات'],
            ],
            'stats_json' => [
                ['label' => 'نسبة الحماية', 'value' => '99.99%'],
                ['label' => 'زمن اكتشاف التهديد', 'value' => '< 5min'],
                ['label' => 'معايير الامتثال', 'value' => 'ISO/OWASP'],
            ],
            'display_order' => 3,
            'is_active' => true,
        ]);

        ServiceIcon::create([
            'service_id' => $cyber->id,
            'group_type' => 'threat',
            'title' => 'هجمات الفدية وتشفير البيانات',
            'icon_name' => 'lock-closed',
            'badge_text' => 'تهديد حرج',
            'popup_title' => 'التصدي لهجمات الفدية Ransomware',
            'popup_summary' => 'بروتوكولات عزل فوري ونسخ احتياطي مشفر ومحمي ضد التعديل أو التشفير الخبيث.',
            'popup_bullets_json' => ['نسخ احتياطي خارج الشبكة Air-gapped', 'كشف السلوكيات الشاذة', 'خطة استعادة نشاط فورية'],
            'popup_cta_text' => 'اطلب درع الحماية الآن',
            'display_order' => 1,
        ]);

        ServicePackage::create([
            'service_id' => $cyber->id,
            'slug' => 'cyber-audit-quick',
            'name' => 'فحص وتقييم الثغرات السريع (Vulnerability Assessment)',
            'subtitle' => 'فحص شامل لكافة منافذ وسيرفرات وتطبيقات مشروعك',
            'badge_text' => 'تقرير تفصيلي',
            'billing_type' => 'one_time',
            'original_price' => 9000.00,
            'price' => 6500.00,
            'min_units' => 1,
            'unit_label' => 'للفحص الشامل',
            'warranty_days' => 60,
            'features_json' => [
                'مسح شامل لمنافذ السيرفر ومواقع الويب',
                'فحص ثغرات OWASP Top 10',
                'تقرير أمني معتمد مصنف حسب الخطورة',
                'جلسة مع مهندس أمن سيبراني لشرح خطوات الإغلاق',
            ],
            'is_featured' => true,
            'display_order' => 1,
            'is_active' => true,
        ]);

        // Service 4: Maintenance
        $maint = Service::create([
            'slug' => 'maintenance',
            'name' => 'خدمات الصيانة الهندسية الفائقة',
            'tagline' => 'صيانة برمجية وسيرفرات وحلول هندسية عاجلة مع ضمان معتمد',
            'short_description' => 'دعم فني هندسي رفيع المستوى لصيانة الخوادم، إصلاح الأعطال الطارئة، وتحسين أداء التطبيقات وقواعد البيانات.',
            'hero_badge' => 'استجابة طارئة < 30 دقيقة',
            'icon_key' => 'wrench-screwdriver',
            'accent_color' => '#10B981',
            'secondary_color' => '#00F5D4',
            'stats_json' => [
                ['label' => 'زمن الاستجابة', 'value' => '< 30min'],
                ['label' => 'حل المشكلات', 'value' => '98.7%'],
                ['label' => 'فترة الضمان', 'value' => '30 يوم'],
            ],
            'display_order' => 4,
            'is_active' => true,
        ]);

        ServicePackage::create([
            'service_id' => $maint->id,
            'slug' => 'maintenance-hourly',
            'name' => 'الدعم الهندسي المباشر بالساعة',
            'subtitle' => 'تدخل سريع لحل الأعطال الطارئة وصيانة السيرفرات',
            'badge_text' => 'مرونة كاملة',
            'billing_type' => 'hourly',
            'original_price' => 600.00,
            'price' => 450.00,
            'min_units' => 3,
            'unit_label' => 'ساعة (الحد الأدنى 3 ساعات)',
            'warranty_days' => 14,
            'features_json' => [
                'تدخل مباشر خلال أقل من ساعة',
                'فحص سجلات الأخطاء وإصلاح انهيار السيرفر',
                'تحسين أداء وسرعة قواعد البيانات',
                'تقرير فني بما تم إنجازه وتوصيات الحماية',
            ],
            'is_featured' => true,
            'display_order' => 1,
            'is_active' => true,
        ]);

        // Service 5: Business Consulting
        $consulting = Service::create([
            'slug' => 'business-consulting',
            'name' => 'استشارات البيزنس والتحول الرقمي',
            'tagline' => 'هندسة نماذج الأعمال، أتمتة سلاسل الإمداد، وتوجيه استراتيجي للنمو',
            'short_description' => 'استشارات متخصصة من خبراء تنفيذيين لقيادة التحول الرقمي لمشروعك، ورفع الكفاءة التشغيلية، ومضاعفة الهوامش الربحية.',
            'hero_badge' => 'رؤية استراتيجية معتمدة',
            'icon_key' => 'chart-bar-square',
            'accent_color' => '#FFB800',
            'secondary_color' => '#7B2CBF',
            'stats_json' => [
                ['label' => 'معدل نمو الأرباح', 'value' => '+38%'],
                ['label' => 'تخفيض الهدر', 'value' => '50%'],
                ['label' => 'مشاريع ناجحة', 'value' => '+120'],
            ],
            'display_order' => 5,
            'is_active' => true,
        ]);

        ServicePackage::create([
            'service_id' => $consulting->id,
            'slug' => 'consulting-audit-session',
            'name' => 'جلسة استشارية تشخيصية مركزة',
            'subtitle' => 'تحليل عميق للتحديات التشغيلية والمالية مع خارطة حلول واضحة',
            'badge_text' => '1-on-1 مباشر',
            'billing_type' => 'hourly',
            'original_price' => 2200.00,
            'price' => 1500.00,
            'min_units' => 1,
            'unit_label' => 'جلسة (ساعة ونصف)',
            'warranty_days' => null,
            'features_json' => [
                'جلسة خاصة مع استشاري تحول رقمي معتمد',
                'دراسة نموذج العمل الحالي ونقاط الاختناق',
                'خطة عمل تنفيذية مكتوبة للتطبيق الفوري',
                'تسجيل كامل للجلسة مع ملف التوصيات',
            ],
            'is_featured' => true,
            'display_order' => 1,
            'is_active' => true,
        ]);

        // Service 6: Academy
        $academy = Service::create([
            'slug' => 'academy',
            'name' => 'أكاديمية سولي لكورسات الـ AI والهاكينج الأخلاقي',
            'tagline' => 'برامج تدريبية وتطبيق عملي لبناء نخبة مهندسي المستقبل السيبراني والذكاء الاصطناعي',
            'short_description' => 'دورات مكثفة ومشاريع حقيقية في بناء وكلاء الذكاء الاصطناعي، اختبار الاختراق السيبراني، وهندسة البرمجيات مع شهادات معتمدة.',
            'hero_badge' => 'تطبيق حي ومشاريع واقعية',
            'icon_key' => 'academic-cap',
            'accent_color' => '#00F5D4',
            'secondary_color' => '#FFB800',
            'stats_json' => [
                ['label' => 'ساعات التطبيق', 'value' => '+180h'],
                ['label' => 'نسبة التوظيف', 'value' => '92%'],
                ['label' => 'مشاريع التخرج', 'value' => '100% عملي'],
            ],
            'display_order' => 6,
            'is_active' => true,
        ]);

        ServicePackage::create([
            'service_id' => $academy->id,
            'slug' => 'academy-ai-agent-engineering',
            'name' => 'دبلومة هندسة وكلاء الذكاء الاصطناعي (AI Agent Mastery)',
            'subtitle' => 'تعلم بناء ونشر وكلاء ذكاء اصطناعي للشركات من الصفر حتى الإنتاج',
            'badge_text' => 'الكورس الأكثر طلباً',
            'billing_type' => 'course',
            'original_price' => 6500.00,
            'price' => 4500.00,
            'min_units' => 1,
            'unit_label' => 'للكورس بالكامل',
            'warranty_days' => 14,
            'features_json' => [
                '12 أسبوعاً من المحاضرات التفاعلية والمختبرات الحية',
                'بناء 4 وكلاء ذكاء اصطناعي فعليين للشركات',
                'ربط نماذج OpenAI و Claude و DeepSeek مع قواعد بيانات Vector',
                'شهادة معتمدة من سولي ديدجيتال سيتادل',
                'عضوية دائمة في مجتمع مهندسي سولي الخاص',
            ],
            'is_featured' => true,
            'display_order' => 1,
            'is_active' => true,
        ]);

        // ---------------------------------------------------------------------
        // 3. CANNED RESPONSE CATEGORIES & SMART RESPONSES
        // ---------------------------------------------------------------------
        $catGeneral = CannedResponseCategory::create([
            'name' => 'استفسارات عامة وترحيب',
            'slug' => 'general-welcome',
            'icon' => 'chat-bubble-oval-left-ellipsis',
            'accent_color' => '#00F5D4',
            'display_order' => 1,
        ]);

        $catErp = CannedResponseCategory::create([
            'name' => 'نظام BrandERP',
            'slug' => 'brand-erp-responses',
            'icon' => 'building-office-2',
            'accent_color' => '#00F5D4',
            'display_order' => 2,
        ]);

        $catAi = CannedResponseCategory::create([
            'name' => 'موظفو الذكاء الاصطناعي AI Agents',
            'slug' => 'ai-agents-responses',
            'icon' => 'cpu-chip',
            'accent_color' => '#7B2CBF',
            'display_order' => 3,
        ]);

        $catCyber = CannedResponseCategory::create([
            'name' => 'الأمن السيبراني والتدريع',
            'slug' => 'cybersecurity-responses',
            'icon' => 'shield-check',
            'accent_color' => '#FFB800',
            'display_order' => 4,
        ]);

        $catWallet = CannedResponseCategory::create([
            'name' => 'المحفظة والمدفوعات الإلكترونية',
            'slug' => 'wallet-billing-responses',
            'icon' => 'wallet',
            'accent_color' => '#10B981',
            'display_order' => 5,
        ]);

        // Canned Responses
        CannedResponse::create([
            'category_id' => $catGeneral->id,
            'title' => 'ترحيب رسمي وتعريف بمنظومة سولي',
            'shortcut_code' => '/welcome',
            'content' => "أهلاً بك في سولي ديدجيتال سيتادل (soulyeg.online) 🛡️\nيسعدنا مساعدتك في تسريع نمو وتدريع أعمالك رقمياً. كيف يمكن لفريقنا ووكلاؤنا الأذكياء خدمتك اليوم؟",
            'tags_json' => ['ترحيب', 'بداية', 'خدمة_عملاء'],
            'usage_count' => 142,
            'created_by' => $admin->id,
            'is_active' => true,
        ]);

        CannedResponse::create([
            'category_id' => $catErp->id,
            'title' => 'شرح الفاتورة الإلكترونية لنظام BrandERP',
            'shortcut_code' => '/erp-tax',
            'content' => "نظام BrandERP معتمد ومتوافق كلياً مع منظومة الفاتورة والإيصال الإلكتروني لمصلحة الضرائب المصرية (ETA). يتم الإرسال والاعتماد لحظياً بضغطة زر دون أي تعقيد محاسبي.",
            'tags_json' => ['ضرائب', 'فاتورة_الكترونية', 'erp'],
            'usage_count' => 89,
            'created_by' => $admin->id,
            'is_active' => true,
        ]);

        CannedResponse::create([
            'category_id' => $catAi->id,
            'title' => 'مزايا موظف المبيعات الذكي AI Agent',
            'shortcut_code' => '/ai-features',
            'content' => "موظف الذكاء الاصطناعي من سولي يتحدث اللهجة المصرية الدارجة والفصحى، متصل بكتالوج منتجاتك ونظام إدارة المخزون، ويرد في أقل من 3 ثوانٍ على الواتساب ووسائل التواصل لإتمام الطلبات 24/7.",
            'tags_json' => ['ai', 'مبيعات', 'واتساب'],
            'usage_count' => 67,
            'created_by' => $admin->id,
            'is_active' => true,
        ]);

        CannedResponse::create([
            'category_id' => $catWallet->id,
            'title' => 'بيانات الشحن عبر إنستاباي وفودافون كاش',
            'shortcut_code' => '/pay-info',
            'content' => "يمكنك شحن رصيد محفظتك الرقمية فورياً عبر:\n1. إنستاباي (InstaPay): souly@instapay\n2. فودافون كاش: 010xxxxxxxx\nيرجى رفع إيصال التحويل عبر بوابة المحفظة ليتم الاعتماد وإضافة الرصيد في دقائق.",
            'tags_json' => ['دفع', 'انستاباي', 'شحن_محفظة'],
            'usage_count' => 115,
            'created_by' => $admin->id,
            'is_active' => true,
        ]);

        // ---------------------------------------------------------------------
        // 4. SAMPLE CLIENT BOOKING, TICKET & MESSAGES
        // ---------------------------------------------------------------------
        $sampleBooking = Booking::create([
            'booking_number' => 'BKG-' . strtoupper(Str::random(8)),
            'user_id' => $client->id,
            'service_id' => $erp->id,
            'service_package_id' => $erpPackage1->id,
            'assigned_staff_id' => $staff->id,
            'quantity_or_hours' => 1,
            'total_amount' => 850.00,
            'payment_method' => 'wallet',
            'payment_status' => 'paid',
            'status' => 'confirmed',
            'scheduled_at' => now()->addDays(2),
            'client_requirements' => 'نرغب في ربط نظام نقاط البيع لفرعنا الرئيسي في القاهرة وربطه بالختم الإلكتروني للضرائب.',
            'staff_report' => 'تم استلام بيانات التفعيل وتجهيز قاعدة البيانات الخاصة بالعميل على السيرفر السحابي.',
        ]);

        $ticket = Ticket::create([
            'ticket_number' => 'TCK-' . strtoupper(Str::random(8)),
            'user_id' => $client->id,
            'booking_id' => $sampleBooking->id,
            'assigned_to' => $staff->id,
            'subject' => 'استفسار بخصوص ربط الختم الإلكتروني الفوري',
            'department' => 'erp',
            'priority' => 'high',
            'status' => 'staff_replied',
        ]);

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

        // ---------------------------------------------------------------------
        // 5. SAMPLE CRM LEADS & AI AGENT CONVERSATION
        // ---------------------------------------------------------------------
        $lead = Lead::create([
            'name' => 'أحمد فتحي القاضي',
            'phone' => '+201099887766',
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
        ]);

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

        $aiConv = AiConversation::create([
            'session_uuid' => (string) Str::uuid(),
            'user_id' => null,
            'lead_id' => $lead->id,
            'device_type' => 'mobile',
            'detected_intent' => 'erp_inquiry',
            'pinecone_namespace' => 'souly-knowledge-v1',
            'is_escalated' => false,
        ]);

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
