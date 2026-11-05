@php
    $slug = $service->slug;

    $configs = [
        'brand-erp' => [
            'color'       => '#00F5D4',
            'rgb'         => '0, 245, 212',
            'border'      => 'rgba(0, 245, 212, 0.35)',
            'bg_glow'     => 'rgba(0, 245, 212, 0.08)',
            'badge'       => 'النواة التشغيلية والسيادة الرقمية · يبدأ من 2,000 ج.م',
            'icon'        => '🏪',
            'title'       => 'نظام BrandERP المتكامل',
            'hook'        => 'نظام ERP متكامل يبدأ من 2,000 ج.م فقط ككاشير ومبيعات فورية، ويتوسع معك بأكثر من 300 موديول وميزة دقيقة تلغي التكاليف البرمجية الباهظة. تحكم كامل في الفروع والمخازن والمالية وواتساب بدون قيود ترخيص وبملكية تامة لنشاطك.',
            'sim_label'   => '🎛️ مستشار الباقة ومحاكي العائد (ROI)',
            'store_label' => '💎 متجر الإضافات (2,000 ج.م + ميزات)',
            'cards' => [
                ['icon' => '⚡', 'title' => 'كاشير أوفلاين بلا انقطاع', 'desc' => 'يعمل بدون إنترنت 24/7 مع طباعة حرارية'],
                ['icon' => '📦', 'title' => 'المخازن وتعدد الفروع', 'desc' => 'مزامنة فورية للأرصدة بين كافة المستودعات'],
                ['icon' => '📱', 'title' => 'فواتير واتساب PDF تلقائية', 'desc' => 'إرسال آلي للفاتورة لحظة إتمام المعاملة'],
                ['icon' => '🏛️', 'title' => 'الفاتورة والربط الضريبي', 'desc' => 'معتمدة رسمياً لدى مصلحة الضرائب المصرية'],
                ['icon' => '📊', 'title' => 'تقارير أرباح وخسائر حية', 'desc' => 'حساب ربحية كل فاتورة وصنف لحظياً'],
                ['icon' => '🤖', 'title' => 'ذكاء اصطناعي تنبؤي للمبيعات', 'desc' => 'توقع نفاد المخزون وتحديد أفضل الأصناف'],
            ],
        ],
        'ai-agents' => [
            'color'       => '#9D4EDD',
            'rgb'         => '157, 78, 221',
            'border'      => 'rgba(157, 78, 221, 0.35)',
            'bg_glow'     => 'rgba(157, 78, 221, 0.08)',
            'badge'       => 'القوة العاملة الرقمية 24/7 · رد في 1.8 ثانية',
            'icon'        => '🤖',
            'title'       => 'موظفو الذكاء الاصطناعي AI Agents',
            'hook'        => 'وكلاء ذكاء اصطناعي مستقلون يعملون على مدار الساعة للرد اللحظي باللهجة المصرية الفصحى وإغلاق المبيعات وخدمة العملاء في 1.8 ثانية. بديل رقمي فائق يوفر ما يصل إلى 27,500 ج.م شهرياً من رواتب وتكاليف خدمة العملاء البشرية مع مزامنة لحظية.',
            'sim_label'   => '🎛️ محاكي الموظف وحاسبة التوفير',
            'store_label' => '💎 باقات موظفي الذكاء الاصطناعي',
            'cards' => [
                ['icon' => '⚡', 'title' => 'رد فوري في 1.8 ثانية', 'desc' => 'باللهجة المصرية الطبيعية المؤهلة بيعياً'],
                ['icon' => '💬', 'title' => 'إغلاق الصفقات على واتساب', 'desc' => 'تسجيل الأوردرات والعناوين وحساب الفاتورة'],
                ['icon' => '🧠', 'title' => 'ذاكرة سياقية دائمة للعميل', 'desc' => 'حفظ تفضيلات وسجل كل زائر وسوابق مشترياته'],
                ['icon' => '📞', 'title' => 'التقاط العملاء المحتملين CRM', 'desc' => 'تحويل الدردشة لفرص بيعية مؤكدة برقم الموبايل'],
                ['icon' => '🔄', 'title' => 'مزامنة لحظية مع المخزون', 'desc' => 'الرد بالأسعار والكميات المتاحة من ERP فوراً'],
                ['icon' => '🌐', 'title' => 'خدمة مستمرة 24/7 بلا إجازات', 'desc' => 'صفر تأخير وصفر هدر للعملاء طوال اليوم'],
            ],
        ],
        'cybersecurity' => [
            'color'       => '#FF2A55',
            'rgb'         => '255, 42, 85',
            'border'      => 'rgba(255, 42, 85, 0.35)',
            'bg_glow'     => 'rgba(255, 42, 85, 0.08)',
            'badge'       => 'درع الحماية والسيادة الرقمية · ضمان معتمد',
            'icon'        => '🛡️',
            'title'       => 'الأمن السيبراني والتدريع الرقمي',
            'hook'        => 'تدريع سيبراني شامل للأجهزة والشبكات والبيانات الحساسة ضد الاختراق وفيروسات الفدية مع ضمان هندسي رسمي معتمد. حماية أمنية متكاملة لشركتك ولعائلتك بمقرك وأمام عينيك مع عزل تام للثغرات واختبارات اختراق دورية.',
            'sim_label'   => '🎛️ الماسح التفاعلي وحاسبة التدريع',
            'store_label' => '💎 باقات التدريع والأمن السيبراني',
            'cards' => [
                ['icon' => '🛡️', 'title' => 'عزل فيروسات الفدية والتجسس', 'desc' => 'حظر الهجمات المتقدمة قبل وصولها لملفاتك'],
                ['icon' => '🌐', 'title' => 'تدريع الراوتر والشبكات', 'desc' => 'إغلاق الثغرات والمنافذ المفتوحة وجدران نارية'],
                ['icon' => '👨‍👩‍👧', 'title' => 'حماية العائلة والمحتوى', 'desc' => 'تصفية المواقع والتطبيقات غير الآمنة للأطفال'],
                ['icon' => '🔐', 'title' => 'تشفير النسخ الاحتياطية', 'desc' => 'استعادة فورية للبيانات المعزولة عند الطوارئ'],
                ['icon' => '📜', 'title' => 'ضمان هندسي سيبراني رسمي', 'desc' => 'تقرير فحص أمني معتمد ومختوم من خبير سولي'],
                ['icon' => '👁️', 'title' => 'مراقبة التسلل اللحظية', 'desc' => 'إنذار مبكر ضد أي محاولة دخول مشبوهة'],
            ],
        ],
        'maintenance' => [
            'color'       => '#FFB800',
            'rgb'         => '255, 184, 0',
            'border'      => 'rgba(255, 184, 0, 0.35)',
            'bg_glow'     => 'rgba(255, 184, 0, 0.08)',
            'badge'       => 'هندسة معتمدة بمقرك · 400 ج.م/ساعة',
            'icon'        => '🔧',
            'title'       => 'خدمات الصيانة الهندسية الميدانية الفائقة',
            'hook'        => 'صيانة هندسية ميدانية فائقة لأجهزة الكمبيوتر واللابتوب ومحطات العمل وأجهزة Mac وسيرفرات الشركات داخل مقرك دون نقل أجهزتك. تسعير عادل ومعتمد 400 ج.م/ساعة (الحد الأدنى 3 ساعات للزيارة = 1,200 ج.م - بدون أي خصم على الساعات الإضافية - باستثناء صيانة الماذر بورد) مع معاجين ألمانية وضمان معتمد.',
            'sim_label'   => '🎛️ محاكي الهبوط الحراري وحاسبة التكلفة',
            'store_label' => '💎 باقات الصيانة الهندسية بمقرك',
            'cards' => [
                ['icon' => '⏱️', 'title' => '400 ج.م/ساعة (حد أدنى 3س)', 'desc' => 'زيارة 1,200 ج.م بدون أي رسوم خفية'],
                ['icon' => '❄️', 'title' => 'معاجين Arctic MX-6 الألمانية', 'desc' => 'تخفيض الحرارة حتى -25°C مع هدوء المراوح'],
                ['icon' => '🏠', 'title' => 'صيانة بمقرك وأمام عينيك', 'desc' => 'حماية تامة لبياناتك وخصوصيتك دون نقل الجهاز'],
                ['icon' => '🧪', 'title' => 'اختبارات ضغط شاقة Stress Test', 'desc' => 'تأكيد الاستقرار تحت أقصى أحمال الريندر'],
                ['icon' => '📜', 'title' => 'ضمان هندسي معتمد ومكتوب', 'desc' => 'متابعة دورية ودعم فني متخصص بعد الزيارة'],
                ['icon' => '⚠️', 'title' => 'لا تشمل صيانة الماذر بورد', 'desc' => 'تركيز فائق على الأداء والتبريد الآمن'],
            ],
        ],
        'business-consulting' => [
            'color'       => '#10B981',
            'rgb'         => '16, 185, 129',
            'border'      => 'rgba(16, 185, 129, 0.35)',
            'bg_glow'     => 'rgba(16, 185, 129, 0.08)',
            'badge'       => 'خارطة النمو ومضاعفة الأرباح · ربحية +45%',
            'icon'        => '📈',
            'title'       => 'استشارات البيزنس والتحول الرقمي',
            'hook'        => 'تشخيص استراتيجي معمق لعمليات شركتك وسلاسل الإمداد والتسعير لإيقاف الهدر المالي ومضاعفة الأرباح الصافية حتى +45%. خطط تحول رقمي مدروسة تعيد هندسة بيئة عملك وتضعك في صدارة قطاعك التنافسي مع توجيه تنفيذي مستمر.',
            'sim_label'   => '🎛️ مصفوفة تشخيص النضج وخارطة النمو',
            'store_label' => '💎 باقات الاستشارات والتمكين التنفيذي',
            'cards' => [
                ['icon' => '📊', 'title' => 'تحليل النضج المؤسسي 360°', 'desc' => 'كشف فجوات الأداء والربحية التشغيلية بدقة'],
                ['icon' => '💰', 'title' => 'هندسة الأسعار وهوامش الربح', 'desc' => 'زيادة العائد الصافي حتى +45% بدون خسارة عملاء'],
                ['icon' => '⚙️', 'title' => 'أتمتة دورات العمل اليدوية', 'desc' => 'إلغاء البيروقراطية وتكرار المهام اليدوية'],
                ['icon' => '🧭', 'title' => 'خارطة طريق 90 يوماً', 'desc' => 'أهداف تنفيذية دقيقة ومحددة زمنياً بالأرقام'],
                ['icon' => '👥', 'title' => 'إعادة هيكلة فرق العمل وKPI', 'desc' => 'ربط الإنتاجية بالحوافز والمؤشرات الملموسة'],
                ['icon' => '🎯', 'title' => 'خطط التوسع وافتتاح الفروع', 'desc' => 'نماذج تشغيلية مدروسة قابلة للتكرار بنجاح'],
            ],
        ],
        'academy' => [
            'color'       => '#3B82F6',
            'rgb'         => '59, 130, 246',
            'border'      => 'rgba(59, 130, 246, 0.35)',
            'bg_glow'     => 'rgba(59, 130, 246, 0.08)',
            'badge'       => 'مختبرات احترافية 100% عملي · شهادة QR معتمدة',
            'icon'        => '🎓',
            'title'       => 'أكاديمية سولي لكورسات الـ AI والهاكينج الأخلاقي',
            'hook'        => 'معسكرات تدريبية تطبيقية مكثفة تؤهلك مباشرة لسوق العمل الحر والشركات العالمية في مجالات وكلاء الذكاء الاصطناعي والأمن السيبراني. تدريب عملي 100% داخل معامل حية مع مشاريع واقعية وشهادة تخرج معتمدة برمز QR رسمي قابل للتحقق الدولي.',
            'sim_label'   => '🎛️ الخطة الدراسية ومعاينة الشهادة الفورية',
            'store_label' => '💎 باقات التسجيل بالمعسكرات',
            'cards' => [
                ['icon' => '🤖', 'title' => 'معسكر وكلاء الذكاء الاصطناعي', 'desc' => 'برمجة ونشر AI Agents في السوق وتطبيقها'],
                ['icon' => '🛡️', 'title' => 'معسكر الهاكينج الأخلاقي', 'desc' => 'اختبار الاختراق والتدريع الميداني العملي'],
                ['icon' => '💻', 'title' => 'معامل وسيرفرات تطبيق حية', 'desc' => '100% تدريب عملي ومشاريع حقيقية من اليوم الأول'],
                ['icon' => '📜', 'title' => 'شهادة تخرج معتمدة برمز QR', 'desc' => 'معتمدة رسمياً وقابلة للتحقق الدولي فور التخرج'],
                ['icon' => '💼', 'title' => 'تأهيل لسوق العمل والحر', 'desc' => 'بناء بورتفوليو احترافي يُقنع أصحاب الأعمال'],
                ['icon' => '🤝', 'title' => 'إشراف ومتابعة هندسية فردية', 'desc' => 'توجيه خطوة بخطوة وتدريب فردي حتى التوظيف'],
            ],
        ],
    ];

    $cfg = $configs[$slug] ?? [
        'color'       => $service->accent_color ?? '#00F5D4',
        'rgb'         => '0, 245, 212',
        'border'      => 'rgba(0, 245, 212, 0.35)',
        'bg_glow'     => 'rgba(0, 245, 212, 0.08)',
        'badge'       => $service->hero_badge ?? 'منظومة سولي السيادية',
        'icon'        => '⚡',
        'title'       => $service->name,
        'hook'        => $service->tagline ?? $service->short_description,
        'sim_label'   => '🎛️ محاكي الخدمة وحاسبة التوفير',
        'store_label' => '💎 الباقات ومتجر الإضافات',
        'cards'       => [],
    ];
@endphp

<!-- ===================================================================== -->
<!-- SOVEREIGN COMMAND ROW: {{ strtoupper($service->slug) }}               -->
<!-- ===================================================================== -->
<section id="service-{{ $service->slug }}"
         class="relative rounded-3xl glass-obsidian cursor-radial-glow border p-5 sm:p-7 lg:p-8 space-y-6 transition-all duration-300 group overflow-hidden scroll-mt-28 shadow-xl"
         style="border-color: {{ $cfg['border'] }}; box-shadow: 0 10px 40px {{ $cfg['bg_glow'] }};">

    <!-- Ambient Mesh Glow -->
    <div class="absolute -top-24 -right-24 w-80 h-80 rounded-full blur-[90px] pointer-events-none opacity-20 transition-opacity group-hover:opacity-30"
         style="background-color: {{ $cfg['color'] }};"></div>
    <div class="absolute -bottom-24 -left-24 w-72 h-72 rounded-full blur-[80px] pointer-events-none opacity-10"
         style="background-color: {{ $cfg['color'] }};"></div>
    <div class="absolute inset-0 bg-[radial-gradient(#1c1f36_1px,transparent_1px)] [background-size:24px_24px] opacity-25 pointer-events-none"></div>

    <!-- ═══════════════ 1. HEADER & PUNCHY HOOK ═══════════════ -->
    <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-4 sm:gap-5 pb-5 border-b border-obsidian-border/70">
        <div class="space-y-2.5 max-w-4xl">
            <!-- Badges Bar -->
            <div class="flex items-center gap-2 flex-wrap">
                <span class="font-mono text-[10px] px-2.5 py-0.5 rounded-md font-black text-obsidian-base tracking-wider uppercase shadow-sm"
                      style="background-color: {{ $cfg['color'] }};">
                    0{{ $index + 1 }} · {{ strtoupper($service->slug) }}
                </span>
                <span class="text-xs font-cairo font-bold px-3 py-0.5 rounded-full border shadow-sm"
                      style="background-color: {{ $cfg['color'] }}15; border-color: {{ $cfg['color'] }}40; color: {{ $cfg['color'] }};">
                    {{ $cfg['badge'] }}
                </span>
                @if($service->slug === 'maintenance')
                    <span class="text-xs font-cairo font-bold px-3 py-0.5 rounded-full bg-cyber-gold/15 text-cyber-gold border border-cyber-gold/40">
                        ⚡ 400 ج.م/ساعة · الحد الأدنى 3 ساعات للزيارة
                    </span>
                @endif
            </div>

            <!-- Title & Main Icon -->
            <div class="flex items-center gap-3">
                <span class="text-2xl sm:text-3xl p-2 rounded-2xl bg-obsidian-surface border border-obsidian-border shrink-0 shadow-sm">
                    {{ $cfg['icon'] }}
                </span>
                <h2 class="text-xl sm:text-2xl lg:text-3xl font-extrabold text-white font-cairo leading-tight">
                    {{ $cfg['title'] }}
                </h2>
            </div>

            <!-- Hook Text -->
            <p class="text-xs sm:text-sm text-slate-300 font-cairo leading-relaxed max-w-3xl">
                {{ $cfg['hook'] }}
            </p>
        </div>

        <!-- Meta Counters Pill -->
        <div class="shrink-0 flex items-center gap-2 self-start lg:self-center">
            <span class="px-3 py-1.5 rounded-xl text-xs font-cairo font-bold bg-obsidian-surface border border-obsidian-border text-slate-300 flex items-center gap-1.5 shadow-sm">
                <span class="font-mono font-bold" style="color: {{ $cfg['color'] }};">{{ $service->packages->count() }}</span>
                <span>باقات معتمدة</span>
            </span>
            <span class="px-3 py-1.5 rounded-xl text-xs font-cairo font-bold bg-obsidian-surface border border-obsidian-border text-slate-300 flex items-center gap-1.5 shadow-sm">
                <span class="font-mono font-bold text-cyber-gold">{{ $service->icons->count() }}</span>
                <span>ميزة ونشاط</span>
            </span>
        </div>
    </div>

    <!-- ═══════════════ 2. MICRO-FEATURE CARDS (ZERO VERTICAL JUMP CAROUSEL ON MOBILE) ═══════════════ -->
    <div class="relative z-10 space-y-2">
        <div class="flex items-center justify-between text-xs font-cairo text-slate-400">
            <span class="font-bold flex items-center gap-1.5 text-white">
                <span class="w-2 h-2 rounded-full" style="background-color: {{ $cfg['color'] }};"></span>
                <span>أبرز المميزات والقدرات الفائقة (اضغط للتفاصيل 0ms):</span>
            </span>
            <span class="text-[11px] font-mono text-slate-400 hidden sm:inline-block">6 قدرات حاسمة</span>
        </div>

        <!-- Desktop Grid View (>= 768px) -->
        <div class="hidden md:grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-2.5">
            @foreach($cfg['cards'] as $cIdx => $card)
                @php
                    $matchedIcon = $service->icons->first(fn($i) => str_contains($i->title, mb_substr($card['title'], 0, 8)));
                    $iconId = $matchedIcon ? $matchedIcon->id : ('c_' . $service->id . '_' . $cIdx);
                    $iconSummary = $matchedIcon ? $matchedIcon->popup_summary : $card['desc'];
                    $iconBullets = $matchedIcon && $matchedIcon->popup_bullets_json ? $matchedIcon->popup_bullets_json : [
                        $card['title'] . ': ' . $card['desc'],
                        'منظومة سولي السيادية — أداء هندسي معتمد 100%',
                        'جاهزية فورية للتكامل والتشغيل بمقرك أو سحابياً'
                    ];
                @endphp
                <button type="button"
                        @click="$dispatch('open-icon-popup', {
                            id: '{{ $iconId }}',
                            title: '{{ addslashes($card['title']) }}',
                            badge_text: '{{ addslashes($cfg['badge']) }}',
                            popup_title: '{{ addslashes($card['title']) }}',
                            popup_summary: '{{ addslashes($iconSummary) }}',
                            popup_bullets: {{ json_encode($iconBullets) }},
                            service_name: '{{ addslashes($service->name) }}',
                            popup_cta_text: 'احجز استشارة فورية الآن'
                        })"
                        class="p-3 rounded-2xl bg-obsidian-surface/90 hover:bg-obsidian-card border border-obsidian-border text-right flex flex-col justify-between gap-2 transition-all duration-200 group/card active:scale-95 shadow-sm hover:shadow-md cursor-pointer"
                        onmouseenter="this.style.borderColor='{{ $cfg['color'] }}'"
                        onmouseleave="this.style.borderColor=''">
                    <div class="flex items-center justify-between w-full">
                        <span class="text-xl group-hover/card:scale-110 transition-transform">{{ $card['icon'] }}</span>
                        <span class="text-[9px] font-mono text-slate-400 group-hover/card:text-white transition-colors">0{{ $cIdx + 1 }}</span>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-white font-cairo leading-snug group-hover/card:text-cyber-cyan transition-colors line-clamp-1">
                            {{ $card['title'] }}
                        </h4>
                        <p class="text-[10px] text-slate-400 font-cairo line-clamp-2 mt-0.5 leading-relaxed">
                            {{ $card['desc'] }}
                        </p>
                    </div>
                </button>
            @endforeach
        </div>

        <!-- Mobile Horizontal-Only Swiper (< 768px) -->
        <div class="md:hidden" x-data="soulyMobileCarousel({ total: {{ count($cfg['cards']) }} })">
            <div x-ref="track"
                 @scroll.debounce.50ms="onScroll()"
                 class="flex gap-3 overflow-x-auto snap-x snap-mandatory scrollbar-none py-2 px-1">
                @foreach($cfg['cards'] as $cIdx => $card)
                    @php
                        $matchedIcon = $service->icons->first(fn($i) => str_contains($i->title, mb_substr($card['title'], 0, 8)));
                        $iconId = $matchedIcon ? $matchedIcon->id : ('c_m_' . $service->id . '_' . $cIdx);
                        $iconSummary = $matchedIcon ? $matchedIcon->popup_summary : $card['desc'];
                        $iconBullets = $matchedIcon && $matchedIcon->popup_bullets_json ? $matchedIcon->popup_bullets_json : [
                            $card['title'] . ': ' . $card['desc'],
                            'منظومة سولي السيادية — أداء هندسي معتمد 100%',
                            'جاهزية فورية للتكامل والتشغيل بمقرك أو سحابياً'
                        ];
                    @endphp
                    <div class="w-[88%] shrink-0 snap-center">
                        <button type="button"
                                @click="$dispatch('open-icon-popup', {
                                    id: '{{ $iconId }}',
                                    title: '{{ addslashes($card['title']) }}',
                                    badge_text: '{{ addslashes($cfg['badge']) }}',
                                    popup_title: '{{ addslashes($card['title']) }}',
                                    popup_summary: '{{ addslashes($iconSummary) }}',
                                    popup_bullets: {{ json_encode($iconBullets) }},
                                    service_name: '{{ addslashes($service->name) }}',
                                    popup_cta_text: 'احجز استشارة فورية الآن'
                                })"
                                class="w-full h-full p-4 rounded-2xl bg-obsidian-surface/95 border text-right flex flex-col justify-between gap-3 shadow-lg active:scale-95 transition-all cursor-pointer"
                                style="border-color: {{ $cfg['border'] }};">
                            <div class="flex items-center justify-between w-full">
                                <span class="text-2xl p-2 rounded-xl bg-obsidian-card border border-obsidian-border">{{ $card['icon'] }}</span>
                                <span class="text-xs font-mono px-2 py-0.5 rounded-full bg-obsidian-card font-bold" style="color: {{ $cfg['color'] }};">{{ $cIdx + 1 }} / {{ count($cfg['cards']) }}</span>
                            </div>
                            <div class="space-y-1">
                                <h4 class="text-sm font-black text-white font-cairo leading-snug">
                                    {{ $card['title'] }}
                                </h4>
                                <p class="text-xs text-slate-300 font-cairo leading-relaxed">
                                    {{ $card['desc'] }}
                                </p>
                            </div>
                            <div class="pt-2 border-t border-obsidian-border/60 flex items-center justify-between text-[11px] font-cairo font-bold">
                                <span class="text-slate-400">اضغط لعرض المواصفات</span>
                                <span style="color: {{ $cfg['color'] }};">تفاصيل 0ms ⇥</span>
                            </div>
                        </button>
                    </div>
                @endforeach
            </div>

            <!-- Dots Indicator and Controls -->
            <div class="flex items-center justify-between pt-2 px-1">
                <div class="flex items-center gap-1.5">
                    @foreach($cfg['cards'] as $dotIdx => $c)
                        <button type="button" 
                                @click="goTo({{ $dotIdx }})"
                                class="h-1.5 rounded-full transition-all duration-300 cursor-pointer"
                                :class="active === {{ $dotIdx }} ? 'w-6' : 'w-2 bg-slate-600'"
                                :style="active === {{ $dotIdx }} ? 'background-color: {{ $cfg['color'] }};' : ''"
                                aria-label="الشريحة {{ $dotIdx + 1 }}"></button>
                    @endforeach
                </div>
                <div class="flex items-center gap-1">
                    <button type="button" @click="prev()" class="p-1 px-2.5 rounded-lg bg-obsidian-surface border border-obsidian-border text-slate-300 hover:text-white text-xs cursor-pointer">
                        ‹
                    </button>
                    <span class="text-[11px] font-mono text-slate-400" x-text="`${active + 1} / {{ count($cfg['cards']) }}`"></span>
                    <button type="button" @click="next()" class="p-1 px-2.5 rounded-lg bg-obsidian-surface border border-obsidian-border text-slate-300 hover:text-white text-xs cursor-pointer">
                        ›
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══════════════ 3. THE 0ms ACTION POPUPS DOCK & NAVIGATION CTAS ═══════════════ -->
    <div class="relative z-10 pt-2 border-t border-obsidian-border/70">
        
        <!-- Desktop Dock (>= 768px) -->
        <div class="hidden md:flex flex-col xl:flex-row items-stretch xl:items-center justify-between gap-3">
            <div class="flex items-center gap-1.5 overflow-x-auto scrollbar-none pb-1 xl:pb-0">
                <!-- Button 1: Simulator / ROI -->
                <button type="button"
                        @click="openSectorModal('{{ $service->slug }}', 'simulator')"
                        class="px-3.5 py-2 rounded-xl text-xs font-cairo font-black text-obsidian-base transition-all duration-200 flex items-center gap-1.5 shrink-0 shadow-md active:scale-95 whitespace-nowrap cursor-pointer"
                        style="background-color: {{ $cfg['color'] }};">
                    <span>{{ $cfg['sim_label'] }}</span>
                    <span class="w-1.5 h-1.5 rounded-full bg-obsidian-base animate-ping"></span>
                </button>

                <!-- Button 2: Traditional vs. Souly -->
                <button type="button"
                        @click="openSectorModal('{{ $service->slug }}', 'comparison')"
                        class="px-3 py-2 rounded-xl text-xs font-cairo font-bold bg-obsidian-surface hover:bg-obsidian-card border border-obsidian-border hover:border-slate-500 text-slate-200 hover:text-white transition-all flex items-center gap-1.5 shrink-0 active:scale-95 whitespace-nowrap cursor-pointer">
                    <span>⚔️</span>
                    <span>التقليدي مقابل سولي</span>
                </button>

                <!-- Button 3: Compatible Industries -->
                <button type="button"
                        @click="openSectorModal('{{ $service->slug }}', 'industries')"
                        class="px-3 py-2 rounded-xl text-xs font-cairo font-bold bg-obsidian-surface hover:bg-obsidian-card border border-obsidian-border hover:border-slate-500 text-slate-200 hover:text-white transition-all flex items-center gap-1.5 shrink-0 active:scale-95 whitespace-nowrap cursor-pointer">
                    <span>🏢</span>
                    <span>القطاعات المتوافقة</span>
                    <span class="text-[10px] font-mono px-1.5 py-0.2 rounded bg-obsidian-card text-cyber-cyan font-bold">{{ $service->icons->where('group_type', 'industry')->count() ?: '8+' }}</span>
                </button>

                <!-- Button 4: Advanced Capabilities -->
                <button type="button"
                        @click="openSectorModal('{{ $service->slug }}', 'features')"
                        class="px-3 py-2 rounded-xl text-xs font-cairo font-bold bg-obsidian-surface hover:bg-obsidian-card border border-obsidian-border hover:border-slate-500 text-slate-200 hover:text-white transition-all flex items-center gap-1.5 shrink-0 active:scale-95 whitespace-nowrap cursor-pointer">
                    <span>⚡</span>
                    <span>المزايا والقدرات المتقدمة</span>
                    <span class="text-[10px] font-mono px-1.5 py-0.2 rounded bg-obsidian-card text-cyber-gold font-bold">{{ $service->icons->where('group_type', '!=', 'industry')->count() ?: '12+' }}</span>
                </button>

                <!-- Button 5: Packages & Modular Store -->
                <button type="button"
                        @if($service->slug === 'brand-erp')
                            @click="$dispatch('branderp-store-open')"
                        @else
                            @click="openSectorModal('{{ $service->slug }}', 'packages')"
                        @endif
                        class="px-3 py-2 rounded-xl text-xs font-cairo font-bold bg-obsidian-surface hover:bg-obsidian-card border border-cyber-gold/40 text-cyber-gold hover:text-amber-300 transition-all flex items-center gap-1.5 shrink-0 active:scale-95 whitespace-nowrap shadow-sm cursor-pointer">
                    <span>{{ $cfg['store_label'] }}</span>
                </button>
            </div>

            <div class="flex items-center gap-2 justify-end shrink-0 pt-1 xl:pt-0">
                <a href="{{ route('services.show', $service->slug) }}"
                   class="px-4 py-2 rounded-xl text-xs font-cairo font-bold text-slate-300 hover:text-white bg-obsidian-surface hover:bg-obsidian-card border border-obsidian-border hover:border-cyber-cyan/40 transition-all flex items-center gap-1.5 active:scale-95 whitespace-nowrap">
                    <span>استكشف المنظومة بالكامل</span>
                    <span class="text-cyber-cyan font-mono">←</span>
                </a>

                @php $featuredPkg = $service->packages->firstWhere('is_featured', true) ?? $service->packages->first(); @endphp
                <button type="button"
                        @click="$dispatch('open-express-booking', {
                            serviceId: {{ $service->id }},
                            serviceSlug: '{{ $service->slug }}',
                            serviceName: '{{ addslashes($service->name) }}',
                            packageId: {{ $featuredPkg ? $featuredPkg->id : 'null' }},
                            packageSlug: '{{ $featuredPkg ? $featuredPkg->slug : 'standard' }}',
                            packageName: '{{ $featuredPkg ? addslashes($featuredPkg->name) : 'حجز فوري' }}',
                            price: {{ $featuredPkg ? (float) $featuredPkg->price : 0 }}
                        })"
                        class="px-4 py-2 rounded-xl text-xs font-cairo font-bold text-obsidian-base bg-gradient-to-r from-cyber-cyan to-[#00BB9B] hover:shadow-[0_0_20px_rgba(0,245,212,0.4)] transition-all flex items-center gap-1.5 active:scale-95 whitespace-nowrap shadow-sm font-black cursor-pointer">
                    <span>احجز الآن</span>
                    <span>⚡</span>
                </button>
            </div>
        </div>

        <!-- Mobile Tactical Action Grid (< 768px) -->
        <div class="md:hidden space-y-2.5">
            <!-- 2x2 Clean Action Buttons Grid -->
            <div class="grid grid-cols-2 gap-2 text-xs font-cairo font-bold">
                <button type="button"
                        @click="openSectorModal('{{ $service->slug }}', 'simulator')"
                        class="p-2.5 rounded-xl text-obsidian-base font-black flex items-center justify-center gap-1 shadow-sm active:scale-95 text-center truncate cursor-pointer"
                        style="background-color: {{ $cfg['color'] }};">
                    <span>🎛️</span>
                    <span class="truncate">المحاكي وحاسبة العائد</span>
                </button>

                <button type="button"
                        @click="openSectorModal('{{ $service->slug }}', 'comparison')"
                        class="p-2.5 rounded-xl bg-obsidian-surface border border-obsidian-border text-slate-200 flex items-center justify-center gap-1 active:scale-95 text-center truncate cursor-pointer">
                    <span>⚔️</span>
                    <span class="truncate">التقليدي vs سولي</span>
                </button>

                <button type="button"
                        @click="openSectorModal('{{ $service->slug }}', 'industries')"
                        class="p-2.5 rounded-xl bg-obsidian-surface border border-obsidian-border text-slate-200 flex items-center justify-center gap-1 active:scale-95 text-center truncate cursor-pointer">
                    <span>🏢</span>
                    <span class="truncate">القطاعات ({{ $service->icons->where('group_type', 'industry')->count() ?: '8+' }})</span>
                </button>

                <button type="button"
                        @click="openSectorModal('{{ $service->slug }}', 'features')"
                        class="p-2.5 rounded-xl bg-obsidian-surface border border-obsidian-border text-slate-200 flex items-center justify-center gap-1 active:scale-95 text-center truncate cursor-pointer">
                    <span>⚡</span>
                    <span class="truncate">المزايا ({{ $service->icons->where('group_type', '!=', 'industry')->count() ?: '12+' }})</span>
                </button>
            </div>

            <!-- Full-Width Store / Packages Button -->
            <button type="button"
                    @if($service->slug === 'brand-erp')
                        @click="$dispatch('branderp-store-open')"
                    @else
                        @click="openSectorModal('{{ $service->slug }}', 'packages')"
                    @endif
                    class="w-full py-2.5 px-3 rounded-xl bg-obsidian-surface border border-cyber-gold/40 text-cyber-gold text-xs font-bold font-cairo flex items-center justify-center gap-2 active:scale-95 shadow-sm cursor-pointer">
                <span>{{ $cfg['store_label'] }}</span>
                <span>💎</span>
            </button>

            <!-- 2-Col Bottom CTAs: Explore + Express Book -->
            <div class="grid grid-cols-2 gap-2 pt-1 text-xs font-cairo font-bold">
                <a href="{{ route('services.show', $service->slug) }}"
                   class="py-2.5 px-2 rounded-xl bg-obsidian-surface border border-obsidian-border text-slate-300 text-center flex items-center justify-center gap-1 active:scale-95">
                    <span>تفاصيل المنظومة</span>
                    <span class="text-cyber-cyan font-mono">←</span>
                </a>

                @php $featuredPkg = $service->packages->firstWhere('is_featured', true) ?? $service->packages->first(); @endphp
                <button type="button"
                        @click="$dispatch('open-express-booking', {
                            serviceId: {{ $service->id }},
                            serviceSlug: '{{ $service->slug }}',
                            serviceName: '{{ addslashes($service->name) }}',
                            packageId: {{ $featuredPkg ? $featuredPkg->id : 'null' }},
                            packageSlug: '{{ $featuredPkg ? $featuredPkg->slug : 'standard' }}',
                            packageName: '{{ $featuredPkg ? addslashes($featuredPkg->name) : 'حجز فوري' }}',
                            price: {{ $featuredPkg ? (float) $featuredPkg->price : 0 }}
                        })"
                        class="py-2.5 px-2 rounded-xl text-obsidian-base bg-gradient-to-r from-cyber-cyan to-[#00BB9B] text-center flex items-center justify-center gap-1 shadow-neon-cyan font-black active:scale-95 cursor-pointer">
                    <span>احجز الآن</span>
                    <span>⚡</span>
                </button>
            </div>
        </div>

    </div>

</section>