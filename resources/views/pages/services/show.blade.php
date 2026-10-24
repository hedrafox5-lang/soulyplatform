@extends(request()->header('sec-ch-ua-mobile') === '?1' || request()->query('view') === 'mobile' ? 'layouts.mobile' : 'layouts.desktop')

@section('title', $service->name . ' | سولي ديدجيتال سيتادل')
@section('page-title', $service->name)

@section('content')
<div class="space-y-16" x-data="serviceLandingController()">

    <!-- ===================================================================== -->
    <!-- 1. SERVICE HERO BANNER WITH DYNAMIC ACCENT COLORS                     -->
    <!-- ===================================================================== -->
    <section class="relative overflow-hidden rounded-3xl glass-obsidian border border-obsidian-border p-6 sm:p-10 lg:p-12">
        <!-- Ambient Bioluminescent Glow -->
        <div class="absolute -top-32 -left-32 w-96 h-96 rounded-full blur-[140px] opacity-25 pointer-events-none" 
             style="background-color: {{ $service->accent_color }};"></div>
        <div class="absolute -bottom-32 -right-32 w-96 h-96 rounded-full blur-[140px] opacity-20 pointer-events-none" 
             style="background-color: {{ $service->secondary_color }};"></div>

        <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            
            <div class="lg:col-span-8 space-y-4">
                <!-- Breadcrumbs & Sector Badge -->
                <div class="flex flex-wrap items-center gap-2 text-xs">
                    <a href="{{ route('services.index') }}" class="text-slate-400 hover:text-cyber-cyan transition-colors">كافة الخدمات</a>
                    <span class="text-slate-600">/</span>
                    <span class="px-3 py-1 rounded-full font-mono font-bold"
                          style="background-color: {{ $service->accent_color }}15; border: 1px solid {{ $service->accent_color }}40; color: {{ $service->accent_color }};">
                        {{ $service->hero_badge ?? 'منظومة سيادية معتمدة' }}
                    </span>
                </div>

                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white font-cairo leading-tight">
                    {{ $service->name }}
                </h1>

                <p class="text-sm sm:text-base lg:text-lg text-slate-300 font-readex leading-relaxed">
                    {{ $service->tagline ?? $service->short_description }}
                </p>

                <!-- Navigation Jump Pills -->
                <div class="flex flex-wrap items-center gap-3 pt-3">
                    <a href="#packages-section" 
                       class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-cyber-cyan via-cyber-gold to-cyber-purple text-obsidian-base font-extrabold text-xs font-cairo shadow-neon-cyan hover:scale-[1.02] active:scale-95 transition-all flex items-center gap-2">
                        <span>استعراض الباقات والأسعار</span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </a>

                    <a href="#icons-matrix-section" 
                       class="px-5 py-2.5 rounded-xl bg-obsidian-surface hover:bg-obsidian-card border border-obsidian-border hover:border-cyber-cyan/40 text-slate-200 text-xs font-cairo font-bold transition-all">
                        مصفوفة المزايا ({{ $service->icons->count() }})
                    </a>

                    @if(in_array($service->slug, ['maintenance', 'cybersecurity', 'brand-erp', 'academy', 'ai-agents']))
                        <a href="#special-widget-section" 
                           class="px-5 py-2.5 rounded-xl bg-obsidian-surface hover:bg-obsidian-card border border-cyber-gold/40 text-cyber-gold text-xs font-cairo font-bold transition-all flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-cyber-gold animate-ping"></span>
                            <span>أداة الحساب الذكية</span>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Stats & Quick Telemetry HUD -->
            <div class="lg:col-span-4 flex flex-col gap-3">
                @foreach(($service->stats_json ?? []) as $stat)
                    <div class="p-4 rounded-2xl bg-obsidian-surface/90 border border-obsidian-border flex items-center justify-between gap-4">
                        <div>
                            <span class="text-xs text-slate-400 font-mono block">{{ $stat['label'] }}</span>
                            <span class="text-[11px] text-slate-500 font-cairo">مؤشر أداء معتمد</span>
                        </div>
                        <span class="text-2xl font-extrabold font-mono text-transparent bg-clip-text bg-gradient-to-r from-cyber-cyan to-cyber-gold">
                            {{ $stat['value'] }}
                        </span>
                    </div>
                @endforeach
            </div>

        </div>
    </section>

    <!-- ===================================================================== -->
    <!-- 2. SPECIAL INTERACTIVE WIDGETS (Tailored to Service Type)             -->
    <!-- ===================================================================== -->
    <section id="special-widget-section" class="scroll-mt-24">
        
        <!-- WIDGET A: Interactive Hourly & Visit Calculator (Maintenance & Cybersecurity) -->
        @if(in_array($service->slug, ['maintenance', 'cybersecurity']))
            <div class="glass-obsidian rounded-3xl border border-cyber-cyan/40 p-6 sm:p-8 lg:p-10 shadow-neon-cyan space-y-6"
                 x-data="hourlyCalculatorEngine({
                    packages: @json($service->packages),
                    isMaintenance: {{ $service->slug === 'maintenance' ? 'true' : 'false' }}
                 })">
                
                <div class="text-center max-w-2xl mx-auto space-y-2">
                    <span class="px-3.5 py-1 rounded-full text-xs font-mono font-bold bg-cyber-cyan/10 border border-cyber-cyan/40 text-cyber-cyan">
                        حاسبة التكلفة والضمان الفوري · 0ms CALC
                    </span>
                    <h3 class="text-2xl sm:text-3xl font-extrabold text-white font-cairo">
                        احسب تكلفة زيارتك الهندسية والضمان بدقة
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-300 font-readex">
                        حدد الباقة المطلوبة وعدد الساعات، وشاهد الخصم المباشر (خصم 50% للزيارة الأولى في الصيانة) وفترة الضمان الهندسي المعتمدة.
                    </p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center pt-4">
                    <!-- Controls -->
                    <div class="lg:col-span-7 space-y-5">
                        <!-- Package Select -->
                        <div class="space-y-2">
                            <label class="text-xs font-mono text-slate-300 font-bold block">1. اختر باقة الخدمة الهندسية:</label>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                <template x-for="(pkg, idx) in packages" :key="pkg.id">
                                    <button @click="selectedPackageId = pkg.id" 
                                            type="button"
                                            class="p-3 rounded-xl border text-right transition-all font-cairo text-xs"
                                            :class="selectedPackageId === pkg.id ? 
                                                'bg-obsidian-surface border-cyber-cyan text-white shadow-neon-cyan' : 
                                                'bg-obsidian-base border-obsidian-border text-slate-400 hover:text-white'">
                                        <div class="font-bold text-xs" x-text="pkg.name"></div>
                                        <div class="text-[10px] text-cyber-gold font-mono mt-1" x-text="`${Math.floor(pkg.price)} EGP / ساعة`"></div>
                                    </button>
                                </template>
                            </div>
                        </div>

                        <!-- Hours Slider -->
                        <div class="space-y-2">
                            <div class="flex items-center justify-between text-xs font-mono">
                                <span class="text-slate-300 font-bold">2. عدد الساعات المطلوبة بمقرك:</span>
                                <span class="text-cyber-cyan font-bold text-base" x-text="`${hours} ساعات`"></span>
                            </div>
                            <input type="range" 
                                   :min="minHours" 
                                   max="12" 
                                   step="1" 
                                   x-model="hours" 
                                   class="w-full h-2 bg-obsidian-surface rounded-lg appearance-none cursor-pointer accent-cyber-cyan">
                            <div class="flex justify-between text-[10px] font-mono text-slate-500">
                                <span x-text="`الحد الأدنى: ${minHours} ساعات`"></span>
                                <span>12 ساعة (يوم كامل)</span>
                            </div>
                        </div>

                        <!-- 50% First Visit Toggle (For Maintenance) -->
                        <template x-if="isMaintenance">
                            <div class="p-3.5 rounded-xl bg-gradient-to-r from-cyber-cyan/10 to-obsidian-surface border border-cyber-cyan/30 flex items-center justify-between gap-3">
                                <div>
                                    <span class="text-xs font-bold text-white font-cairo block">خصم 50% للزيارة الأولى</span>
                                    <span class="text-[11px] text-slate-400">تطبيق الخصم التشجيعي فورياً على إجمالي ساعات الزيارة.</span>
                                </div>
                                <span class="px-3 py-1 rounded-full text-xs font-mono font-bold bg-cyber-cyan text-obsidian-base">مفعل آلياً ✓</span>
                            </div>
                        </template>
                    </div>

                    <!-- Calculated Result Card -->
                    <div class="lg:col-span-5 p-6 rounded-2xl bg-gradient-to-br from-obsidian-surface to-obsidian-card border border-cyber-cyan/40 space-y-5">
                        <div class="border-b border-obsidian-border pb-3 flex items-center justify-between">
                            <span class="text-xs font-mono text-slate-400">ملخص التكلفة التقديرية</span>
                            <span class="text-xs font-mono text-emerald-400" x-text="`ضمان ${activePackage.warranty_days || 14} يوماً`"></span>
                        </div>

                        <div class="space-y-2 font-mono text-xs">
                            <div class="flex justify-between text-slate-400">
                                <span>سعر الساعة:</span>
                                <span x-text="`${Math.floor(activePackage.price)} EGP`"></span>
                            </div>
                            <div class="flex justify-between text-slate-400">
                                <span>عدد الساعات:</span>
                                <span x-text="`${hours} ساعات`"></span>
                            </div>
                            <template x-if="isMaintenance">
                                <div class="flex justify-between text-rose-400">
                                    <span>وفرت بالخصم (50%):</span>
                                    <span x-text="`-${calculatedSavings} EGP`"></span>
                                </div>
                            </template>
                            <div class="border-t border-obsidian-border pt-3 flex justify-between items-baseline text-white">
                                <span class="text-sm font-bold font-cairo">الإجمالي النهائي:</span>
                                <div class="text-right">
                                    <span class="text-2xl sm:text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-cyber-cyan to-cyber-gold" 
                                          x-text="`${calculatedTotal} EGP`"></span>
                                </div>
                            </div>
                        </div>

                        <a :href="`{{ url('/portal#booking') }}?service={{ $service->slug }}&package=${activePackage.slug}&hours=${hours}`" 
                           class="w-full py-3 rounded-xl bg-gradient-to-r from-cyber-cyan to-cyber-purple text-obsidian-base font-extrabold text-xs font-cairo text-center block shadow-neon-cyan hover:scale-[1.02] active:scale-95 transition-all">
                            احجز الزيارة الهندسية فوراً ⇥
                        </a>
                    </div>
                </div>

            </div>
        @endif

        <!-- WIDGET B: BrandERP Interactive Package Recommender -->
        @if($service->slug === 'brand-erp')
            <div class="glass-obsidian rounded-3xl border border-cyber-cyan/40 p-6 sm:p-8 lg:p-10 shadow-neon-cyan space-y-6"
                 x-data="erpRecommenderEngine()">
                <div class="text-center max-w-2xl mx-auto space-y-2">
                    <span class="px-3.5 py-1 rounded-full text-xs font-mono font-bold bg-cyber-cyan/10 border border-cyber-cyan/40 text-cyber-cyan">
                        مستشار الباقة المثالية · AI RECOMMENDER
                    </span>
                    <h3 class="text-2xl sm:text-3xl font-extrabold text-white font-cairo">
                        اكتشف باقة BrandERP الأنسب لنشاطك وحجم مبيعاتك
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-300 font-readex">
                        أجب عن سؤالين بسيطين وسيوجهك المستشار الذكي للباقة المناسبة لتفادي دفع أي تكاليف زائدة.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 max-w-4xl mx-auto">
                    <!-- Step 1: Business Activity -->
                    <div class="p-5 rounded-2xl bg-obsidian-surface border border-obsidian-border space-y-3">
                        <label class="text-xs font-bold text-white font-cairo block">1. ما هو طبيعة نشاطك التجاري؟</label>
                        <select x-model="activityType" class="w-full bg-obsidian-base border border-obsidian-border text-xs text-slate-200 rounded-xl p-3 focus:border-cyber-cyan focus:outline-none font-cairo">
                            <option value="retail">محل تجاري / كاشير / سوبر ماركت / ملابس</option>
                            <option value="multi_branch">سلسلة فروع / تجارة جملة وتوزيع</option>
                            <option value="manufacturing">مصنع / ورشة تصنيع وخط إنتاج</option>
                        </select>
                    </div>

                    <!-- Step 2: Branch Count -->
                    <div class="p-5 rounded-2xl bg-obsidian-surface border border-obsidian-border space-y-3">
                        <label class="text-xs font-bold text-white font-cairo block">2. كم عدد الفروع والمخازن لديك؟</label>
                        <select x-model="branchCount" class="w-full bg-obsidian-base border border-obsidian-border text-xs text-slate-200 rounded-xl p-3 focus:border-cyber-cyan focus:outline-none font-cairo">
                            <option value="1">فرع واحد فقط</option>
                            <option value="2_5">من 2 إلى 5 فروع</option>
                            <option value="6_plus">أكثر من 5 فروع ومستودع مركزي</option>
                        </select>
                    </div>
                </div>

                <!-- Recommendation Output -->
                <div class="max-w-4xl mx-auto p-6 rounded-2xl bg-gradient-to-r from-obsidian-surface via-obsidian-card to-obsidian-surface border border-cyber-gold/50 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="space-y-1 text-right">
                        <span class="text-[10px] font-mono text-cyber-gold uppercase font-bold">الباقة الموصى بها لنشاطك:</span>
                        <h4 class="text-xl font-extrabold text-white font-cairo" x-text="recommendation.name"></h4>
                        <p class="text-xs text-slate-300 font-readex" x-text="recommendation.reason"></p>
                    </div>

                    <a :href="`{{ route('services.show', 'brand-erp') }}/${recommendation.slug}`" 
                       class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-cyber-cyan to-cyber-gold text-obsidian-base font-extrabold font-cairo text-xs shadow-neon-cyan shrink-0 hover:scale-105 active:scale-95 transition-all">
                        عرض تفاصيل الباقة ⇥
                    </a>
                </div>
            </div>
        @endif

        <!-- WIDGET C: Academy Interactive Course Curriculum Accordion -->
        @if($service->slug === 'academy')
            <div class="glass-obsidian rounded-3xl border border-cyber-cyan/40 p-6 sm:p-8 lg:p-10 shadow-neon-cyan space-y-6"
                 x-data="{ activeWeek: 1 }">
                <div class="text-center max-w-2xl mx-auto space-y-2">
                    <span class="px-3.5 py-1 rounded-full text-xs font-mono font-bold bg-cyber-cyan/10 border border-cyber-cyan/40 text-cyber-cyan">
                        الخطة الدراسية والمعامل الحية · CURRICULUM
                    </span>
                    <h3 class="text-2xl sm:text-3xl font-extrabold text-white font-cairo">
                        خريطة التدريب العملي: من الصفر حتى سوق العمل
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-300 font-readex">
                        استعرض مراحل المعسكرات التدريبية وتعرف على اللابات الحية والمشاريع التي ستنجزها بيدك.
                    </p>
                </div>

                <div class="max-w-3xl mx-auto space-y-3 pt-4">
                    <!-- Week 1-2 -->
                    <div class="rounded-2xl bg-obsidian-surface border border-obsidian-border overflow-hidden">
                        <button @click="activeWeek = (activeWeek === 1 ? 0 : 1)" 
                                class="w-full p-4 text-right flex items-center justify-between font-cairo font-bold text-sm text-white hover:text-cyber-cyan transition-colors">
                            <span class="flex items-center gap-3">
                                <span class="w-7 h-7 rounded-lg bg-cyber-cyan/10 text-cyber-cyan flex items-center justify-center font-mono text-xs">01</span>
                                <span>المرحلة الأولى: الأساسيات الصلبة وهندسة الأدوات السحابية</span>
                            </span>
                            <span x-text="activeWeek === 1 ? '▲' : '▼'" class="text-xs text-slate-500"></span>
                        </button>
                        <div x-show="activeWeek === 1" x-collapse class="p-4 border-t border-obsidian-border/60 text-xs text-slate-300 space-y-2 font-readex">
                            <p>تثبيت بيئات العمل الافتراضية، فهم بروتوكولات الشبكات، إعداد أدوات بايثون، وكسر حواجز الخوف من الأكواد والترمينال.</p>
                            <span class="text-cyber-cyan font-mono block">المعمل الحي: تشغيل أول سيرفر محلي معزول وتدريعه بنجاح.</span>
                        </div>
                    </div>

                    <!-- Week 3-5 -->
                    <div class="rounded-2xl bg-obsidian-surface border border-obsidian-border overflow-hidden">
                        <button @click="activeWeek = (activeWeek === 2 ? 0 : 2)" 
                                class="w-full p-4 text-right flex items-center justify-between font-cairo font-bold text-sm text-white hover:text-cyber-cyan transition-colors">
                            <span class="flex items-center gap-3">
                                <span class="w-7 h-7 rounded-lg bg-cyber-purple/20 text-cyber-purple flex items-center justify-center font-mono text-xs">02</span>
                                <span>المرحلة الثانية: بناء الأنظمة المتقدمة (AI Agents & OWASP Labs)</span>
                            </span>
                            <span x-text="activeWeek === 2 ? '▲' : '▼'" class="text-xs text-slate-500"></span>
                        </button>
                        <div x-show="activeWeek === 2" x-collapse class="p-4 border-t border-obsidian-border/60 text-xs text-slate-300 space-y-2 font-readex">
                            <p>بناء وكلاء الذكاء الاصطناعي وربط قواعد البيانات المتجهة Vector DBs، واختبار اختراق تطبيقات الويب وسحب قواعد البيانات عملياً.</p>
                            <span class="text-cyber-gold font-mono block">المعمل الحي: اختراق موقع تجريبي بنكي واكتشاف ثغرة SQLi وسدها فوراً.</span>
                        </div>
                    </div>

                    <!-- Week 6-8 -->
                    <div class="rounded-2xl bg-obsidian-surface border border-obsidian-border overflow-hidden">
                        <button @click="activeWeek = (activeWeek === 3 ? 0 : 3)" 
                                class="w-full p-4 text-right flex items-center justify-between font-cairo font-bold text-sm text-white hover:text-cyber-cyan transition-colors">
                            <span class="flex items-center gap-3">
                                <span class="w-7 h-7 rounded-lg bg-cyber-gold/20 text-cyber-gold flex items-center justify-center font-mono text-xs">03</span>
                                <span>المرحلة الثالثة: مشاريع التخرج، GitHub Portfolio، والشهادة</span>
                            </span>
                            <span x-text="activeWeek === 3 ? '▲' : '▼'" class="text-xs text-slate-500"></span>
                        </button>
                        <div x-show="activeWeek === 3" x-collapse class="p-4 border-t border-obsidian-border/60 text-xs text-slate-300 space-y-2 font-readex">
                            <p>بناء 4 مشاريع برمجية متكاملة لشركات حقيقية، توثيقها على GitHub، واجتياز اختبار التقييم والحصول على الشهادة المعتمدة برمز QR رسمي.</p>
                            <span class="text-emerald-400 font-mono block">النتيجة: بورتفوليو احترافي جاهز للتقديم في كبرى الشركات المحلية والدولية.</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </section>

    <!-- ===================================================================== -->
    <!-- 3. SEARCHABLE & FILTERABLE INTERACTIVE ICON MATRIX                    -->
    <!-- ===================================================================== -->
    <section id="icons-matrix-section" class="space-y-6 scroll-mt-24">
        
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-obsidian-border pb-4">
            <div>
                <h3 class="text-2xl font-extrabold text-white font-cairo flex items-center gap-2.5">
                    <span class="w-3 h-3 rounded-full" style="background-color: {{ $service->accent_color }};"></span>
                    <span>مصفوفة المزايا والقدرات التشغيلية ({{ $service->icons->count() }} عنصر)</span>
                </h3>
                <p class="text-xs text-slate-400 font-readex mt-1">
                    انقر على أي ميزة أو نشاط لفتح نافذة التفاصيل الفورية بمحرك الـ 0ms في المتصفح.
                </p>
            </div>

            <!-- Search & Filters -->
            <div class="flex items-center gap-2">
                <input type="text" 
                       x-model="iconSearchQuery" 
                       placeholder="ابحث داخل المزايا..." 
                       class="bg-obsidian-surface text-xs text-slate-200 placeholder-slate-500 rounded-xl px-4 py-2 border border-obsidian-border focus:border-cyber-cyan focus:outline-none font-cairo w-48 sm:w-60">
            </div>
        </div>

        <!-- Filtered Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
            @foreach($service->icons as $icon)
                <button type="button" 
                        data-icon-popup
                        data-icon-id="{{ $icon->id }}"
                        data-icon-title="{{ $icon->title }}"
                        data-icon-badge="{{ $icon->badge_text }}"
                        data-icon-summary="{{ $icon->popup_summary }}"
                        data-icon-bullets="{{ json_encode($icon->popup_bullets_json) }}"
                        data-service-name="{{ $service->name }}"
                        data-cta-text="{{ $icon->popup_cta_text }}"
                        x-show="matchesIconSearch('{{ addslashes($icon->title) }}', '{{ addslashes($icon->popup_summary) }}')"
                        class="p-4 rounded-2xl bg-obsidian-surface hover:bg-obsidian-card border border-obsidian-border hover:border-cyber-cyan/50 text-right space-y-2 group active:scale-95 transition-all">
                    <div class="flex items-center justify-between">
                        <div class="w-8 h-8 rounded-xl bg-obsidian-surface border border-cyber-cyan/30 flex items-center justify-center text-cyber-cyan group-hover:scale-110 transition-transform shadow-[0_0_10px_rgba(0,245,212,0.15)]">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <span class="text-[9px] font-mono font-bold px-2 py-0.5 rounded-full bg-cyber-cyan/15 text-cyber-cyan border border-cyber-cyan/30 shadow-[0_0_8px_rgba(0,245,212,0.15)] group-hover:bg-cyber-cyan/25 group-hover:border-cyber-cyan group-hover:shadow-neon-cyan transition-all">
                            {{ $icon->badge_text ?? 'جاهز' }}
                        </span>
                    </div>
                    <span class="text-xs font-bold text-slate-200 group-hover:text-white font-cairo block leading-snug">
                        {{ $icon->title }}
                    </span>
                </button>
            @endforeach
        </div>

    </section>

    <!-- ===================================================================== -->
    <!-- 4. SIDE-BY-SIDE PACKAGES SHOWCASE & DIRECT BOOKING                    -->
    <!-- ===================================================================== -->
    <section id="packages-section" class="space-y-6 scroll-mt-24">
        
        <div class="text-center max-w-2xl mx-auto space-y-2">
            <span class="text-[11px] font-mono uppercase tracking-wider text-cyber-purple font-bold">خطط الاشتراك والاستثمار</span>
            <h3 class="text-2xl sm:text-3xl font-extrabold text-white font-cairo">
                باقات {{ $service->name }} الشاملة
            </h3>
            <p class="text-xs sm:text-sm text-slate-400 font-readex">
                كل باقة تحتوي على صفحة مخصصة توضح البنية الهندسية، شروط الضمان، والأسئلة الشائعة.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 pt-2">
            @foreach($service->packages as $pkg)
                <div class="relative rounded-3xl glass-obsidian border p-6 sm:p-7 flex flex-col justify-between transition-all duration-300 {{ $pkg->is_featured ? 'border-cyber-cyan/60 shadow-neon-cyan bg-obsidian-surface/90' : 'border-obsidian-border hover:border-obsidian-border/90' }}">
                    
                    @if($pkg->is_featured)
                        <div class="absolute -top-3 left-6">
                            <span class="px-3 py-1 rounded-full text-[10px] font-mono font-extrabold bg-gradient-to-r from-cyber-cyan to-cyber-purple text-obsidian-base shadow-md uppercase">
                                {{ $pkg->badge_text ?? 'الأكثر طلباً' }}
                            </span>
                        </div>
                    @endif

                    <div class="space-y-5">
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-obsidian-surface text-slate-300 border border-obsidian-border/80">
                                    {{ $pkg->billing_type }}
                                </span>
                                @if($pkg->warranty_days)
                                    <span class="text-[10px] font-mono text-emerald-400 flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                        <span>ضمان {{ $pkg->warranty_days }} يوم</span>
                                    </span>
                                @endif
                            </div>
                            <h4 class="text-xl font-extrabold text-white font-cairo">
                                {{ $pkg->name }}
                            </h4>
                            @if($pkg->subtitle)
                                <p class="text-xs text-slate-400 font-readex leading-relaxed">{{ $pkg->subtitle }}</p>
                            @endif
                        </div>

                        <!-- Pricing Block -->
                        <div class="p-4 rounded-2xl bg-obsidian-surface/90 border border-obsidian-border space-y-1">
                            @if($pkg->original_price && $pkg->original_price > $pkg->price)
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-mono text-slate-500 line-through">{{ number_format($pkg->original_price, 0) }} EGP</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-rose-500/10 text-rose-400 border border-rose-500/30">
                                        وفر {{ number_format($pkg->original_price - $pkg->price, 0) }} EGP
                                    </span>
                                </div>
                            @endif

                            <div class="flex items-baseline gap-2">
                                <span class="text-3xl font-extrabold font-mono text-transparent bg-clip-text bg-gradient-to-r from-cyber-cyan to-cyber-gold">
                                    {{ number_format($pkg->price, 0) }}
                                </span>
                                <span class="text-xs font-mono text-slate-300">EGP</span>
                                @if($pkg->unit_label)
                                    <span class="text-xs text-slate-400 font-cairo">/ {{ $pkg->unit_label }}</span>
                                @endif
                            </div>
                        </div>

                        <!-- Features List -->
                        <ul class="space-y-2">
                            @foreach($pkg->features_json as $feat)
                                <li class="flex items-start gap-2 text-xs text-slate-300">
                                    <span class="w-1.5 h-1.5 rounded-full bg-cyber-cyan mt-1.5 shrink-0"></span>
                                    <span>{{ $feat }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Package Actions: Sub-page & Direct Booking -->
                    <div class="pt-6 border-t border-obsidian-border/80 mt-6 space-y-2">
                        <a href="{{ route('services.package', [$service->slug, $pkg->slug]) }}" 
                           class="w-full py-2.5 rounded-xl bg-obsidian-surface hover:bg-obsidian-card border border-obsidian-border hover:border-cyber-cyan/50 text-white font-cairo font-bold text-xs text-center block transition-all">
                            صفحة مواصفات الباقة الكاملة ⇥
                        </a>
                        <a href="{{ url('/portal#booking') }}?service={{ $service->slug }}&package={{ $pkg->slug }}" 
                           class="w-full py-2.5 rounded-xl bg-gradient-to-r from-cyber-cyan to-cyber-purple text-obsidian-base font-extrabold font-cairo text-xs text-center block shadow-neon-cyan hover:scale-[1.01] active:scale-95 transition-all">
                            احجز الآن عبر المحفظة
                        </a>
                    </div>

                </div>
            @endforeach
        </div>

    </section>

</div>

@push('scripts')
<script>
    function serviceLandingController() {
        return {
            iconSearchQuery: '',

            matchesIconSearch(title, summary) {
                if (!this.iconSearchQuery.trim()) return true;
                const query = this.iconSearchQuery.toLowerCase();
                return title.toLowerCase().includes(query) || summary.toLowerCase().includes(query);
            }
        };
    }

    function hourlyCalculatorEngine(config) {
        return {
            packages: config.packages || [],
            isMaintenance: config.isMaintenance || false,
            selectedPackageId: config.packages.length > 0 ? config.packages[0].id : null,
            hours: config.isMaintenance ? 3 : 2,

            get minHours() {
                return this.activePackage.min_units || (this.isMaintenance ? 3 : 2);
            },

            get activePackage() {
                return this.packages.find(p => p.id === this.selectedPackageId) || this.packages[0] || {};
            },

            get calculatedTotal() {
                const rate = parseFloat(this.activePackage.price) || 0;
                return Math.floor(rate * this.hours);
            },

            get calculatedSavings() {
                if (!this.isMaintenance) return 0;
                const originalRate = parseFloat(this.activePackage.original_price) || (parseFloat(this.activePackage.price) * 2);
                const originalTotal = originalRate * this.hours;
                return Math.floor(originalTotal - this.calculatedTotal);
            }
        };
    }

    function erpRecommenderEngine() {
        return {
            activityType: 'retail',
            branchCount: '1',

            get recommendation() {
                if (this.activityType === 'manufacturing' || this.branchCount === '6_plus') {
                    return {
                        name: 'باقة المصانع والمؤسسات (Enterprise)',
                        slug: 'enterprise',
                        reason: 'تتضمن وحدة التصنيع وتكاليف الإنتاج الشاملة (BOM) وإدارة الفروع والمستودعات غير المحدودة.'
                    };
                }
                if (this.branchCount === '2_5' || this.activityType === 'multi_branch') {
                    return {
                        name: 'باقة النمو للشركات (Growth Pro)',
                        slug: 'growth',
                        reason: 'مثالية للربط بين حتى 5 فروع، شجرة حسابات متقدمة، موديول الأقساط وشؤون الموظفين HR.'
                    };
                }
                return {
                    name: 'باقة الانطلاق السحابي (Starter)',
                    slug: 'starter',
                    reason: 'أفضل حل للمتاجر والأنشطة الناشئة: كاشير POS فائق، إدارة المخزون، والربط الضريبي المعتمد بأقل تكلفة.'
                };
            }
        };
    }
</script>
@endpush
@endsection
