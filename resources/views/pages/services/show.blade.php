@extends(request()->header('sec-ch-ua-mobile') === '?1' || request()->query('view') === 'mobile' ? 'layouts.mobile' : 'layouts.desktop')

@section('title', $service->name . ' | سولي')
@section('page-title', $service->name)

@php
    $themeMap = [
        'brand-erp' => [
            'primary' => '#00F5D4',
            'secondary' => '#7B2CBF',
            'glow' => '#00F5D4',
            'border' => 'border-cyber-cyan/40',
            'border_hover' => 'hover:border-cyber-cyan',
            'text' => 'text-cyber-cyan',
            'bg_badge' => 'bg-cyber-cyan/15',
            'border_badge' => 'border-cyber-cyan/30',
            'shadow' => 'shadow-[0_4px_30px_rgba(0,245,212,0.12)]',
            'gradient' => 'from-cyber-cyan to-teal-400',
        ],
        'ai-agents' => [
            'primary' => '#9D4EDD',
            'secondary' => '#7B2CBF',
            'glow' => '#9D4EDD',
            'border' => 'border-purple-500/40',
            'border_hover' => 'hover:border-purple-400',
            'text' => 'text-purple-400',
            'bg_badge' => 'bg-purple-500/15',
            'border_badge' => 'border-purple-500/30',
            'shadow' => 'shadow-[0_4px_30px_rgba(157,78,221,0.12)]',
            'gradient' => 'from-purple-400 to-indigo-500',
        ],
        'cybersecurity' => [
            'primary' => '#FF2A55',
            'secondary' => '#9D4EDD',
            'glow' => '#FF2A55',
            'border' => 'border-rose-500/40',
            'border_hover' => 'hover:border-rose-400',
            'text' => 'text-rose-400',
            'bg_badge' => 'bg-rose-500/15',
            'border_badge' => 'border-rose-500/30',
            'shadow' => 'shadow-[0_4px_30px_rgba(255,42,85,0.12)]',
            'gradient' => 'from-rose-500 to-red-600',
        ],
        'maintenance' => [
            'primary' => '#FFB800',
            'secondary' => '#FF8C00',
            'glow' => '#FFB800',
            'border' => 'border-amber-400/40',
            'border_hover' => 'hover:border-amber-400',
            'text' => 'text-amber-400',
            'bg_badge' => 'bg-amber-400/15',
            'border_badge' => 'border-amber-400/30',
            'shadow' => 'shadow-[0_4px_30px_rgba(255,184,0,0.12)]',
            'gradient' => 'from-amber-400 to-yellow-500',
        ],
        'business-consulting' => [
            'primary' => '#10B981',
            'secondary' => '#059669',
            'glow' => '#10B981',
            'border' => 'border-emerald-500/40',
            'border_hover' => 'hover:border-emerald-400',
            'text' => 'text-emerald-400',
            'bg_badge' => 'bg-emerald-500/15',
            'border_badge' => 'border-emerald-500/30',
            'shadow' => 'shadow-[0_4px_30px_rgba(16,185,129,0.12)]',
            'gradient' => 'from-emerald-400 to-teal-500',
        ],
        'academy' => [
            'primary' => '#3B82F6',
            'secondary' => '#1D4ED8',
            'glow' => '#3B82F6',
            'border' => 'border-blue-500/40',
            'border_hover' => 'hover:border-blue-400',
            'text' => 'text-blue-400',
            'bg_badge' => 'bg-blue-500/15',
            'border_badge' => 'border-blue-500/30',
            'shadow' => 'shadow-[0_4px_30px_rgba(59,130,246,0.12)]',
            'gradient' => 'from-blue-400 to-cyan-500',
        ],
    ];

    $curTheme = $themeMap[$service->slug] ?? [
        'primary' => $service->accent_color ?? '#00F5D4',
        'secondary' => $service->secondary_color ?? '#7B2CBF',
        'glow' => $service->accent_color ?? '#00F5D4',
        'border' => 'border-cyber-cyan/40',
        'border_hover' => 'hover:border-cyber-cyan',
        'text' => 'text-cyber-cyan',
        'bg_badge' => 'bg-cyber-cyan/15',
        'border_badge' => 'border-cyber-cyan/30',
        'shadow' => 'shadow-neon-cyan',
        'gradient' => 'from-cyber-cyan to-teal-400',
    ];
@endphp

@section('content')
<div class="space-y-16" x-data="serviceLandingController()">

    <!-- Sticky Mobile Sub-Bar (Visible on < 768px only for 1-tap rapid booking) -->
    <div class="sticky top-14 z-30 -mx-3 -mt-4 mb-4 px-3 py-2 bg-[#080a14]/90 backdrop-blur-md border-b border-white/10 flex items-center justify-between md:hidden shadow-md">
        <div class="flex items-center gap-2 min-w-0">
            <span class="w-2 h-2 rounded-full shrink-0" style="background-color: {{ $curTheme['primary'] }};"></span>
            <span class="text-xs font-black text-white font-cairo truncate">{{ $service->name }}</span>
            <span class="text-[9px] font-mono px-1.5 py-0.5 rounded font-bold" style="background-color: {{ $curTheme['primary'] }}20; color: {{ $curTheme['primary'] }};">
                {{ $service->packages->count() }} باقات
            </span>
        </div>
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
                class="px-2.5 py-1 rounded-xl text-obsidian-base text-[11px] font-black font-cairo shadow-sm flex items-center gap-1 active:scale-95 shrink-0"
                style="background-color: {{ $curTheme['primary'] }};">
            <span>احجز الآن</span>
            <span>⚡</span>
        </button>
    </div>

    {{-- ===================================================================== --}}
    {{-- 1. SERVICE HERO BANNER WITH DYNAMIC ACCENT COLORS                     --}}
    {{-- ===================================================================== --}}
    <section class="relative overflow-hidden rounded-3xl glass-obsidian border p-6 sm:p-10 lg:p-12"
             style="border-color: {{ $curTheme['primary'] }}40; box-shadow: 0 4px 40px {{ $curTheme['primary'] }}15;">
        
        <!-- Ambient Bioluminescent Glow -->
        <div class="absolute -top-32 -left-32 w-96 h-96 rounded-full blur-[140px] opacity-25 pointer-events-none" 
             style="background-color: {{ $curTheme['primary'] }};"></div>
        <div class="absolute -bottom-32 -right-32 w-96 h-96 rounded-full blur-[140px] opacity-20 pointer-events-none" 
             style="background-color: {{ $curTheme['secondary'] }};"></div>

        <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            
            <div class="lg:col-span-8 space-y-4">
                <!-- Breadcrumbs & Sector Badge -->
                <div class="flex flex-wrap items-center gap-2 text-xs">
                    <a href="{{ route('services.index') }}" class="text-slate-400 hover:text-white transition-colors font-cairo">كافة الخدمات</a>
                    <span class="text-slate-600">/</span>
                    <span class="px-3 py-1 rounded-full font-cairo font-bold text-xs"
                          style="background-color: {{ $curTheme['primary'] }}15; border: 1px solid {{ $curTheme['primary'] }}40; color: {{ $curTheme['primary'] }};">
                        {{ $service->hero_badge ?? 'منظومة سيادية معتمدة' }}
                    </span>
                </div>

                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white font-cairo leading-tight">
                    {{ $service->name }}
                </h1>

                <p class="text-sm sm:text-base lg:text-lg text-slate-300 font-cairo leading-relaxed">
                    {{ $service->tagline ?? $service->short_description }}
                </p>

                <!-- Navigation Jump Pills -->
                <div class="flex flex-wrap items-center gap-3 pt-3">
                    <a href="#packages-section" 
                       class="px-6 py-2.5 rounded-xl font-cairo font-bold text-xs flex items-center gap-2 transition-all active:scale-95 shadow-lg"
                       style="background-color: {{ $curTheme['primary'] }}; color: #050811;">
                        <span>استعراض الباقات والأسعار</span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </a>

                    <a href="#icons-matrix-section" 
                       class="px-5 py-2.5 rounded-xl btn-citadel-secondary text-xs">
                        مصفوفة المزايا ({{ $service->icons->count() }})
                    </a>

                    @if($service->slug === 'brand-erp')
                        <a href="{{ route('store.branderp') }}" 
                           class="px-5 py-2.5 rounded-xl bg-obsidian-surface hover:bg-obsidian-card border border-cyber-cyan/50 text-cyber-cyan text-xs font-cairo font-black transition-all flex items-center gap-1.5 shadow-neon-cyan">
                            <span>🏪</span>
                            <span>متجر BrandERP المستقل (شاشة كاملة) ⇥</span>
                        </a>
                    @else
                        <a href="#special-widget-section" 
                           class="px-5 py-2.5 rounded-xl bg-obsidian-surface hover:bg-obsidian-card border text-xs font-cairo font-bold transition-all flex items-center gap-1.5"
                           style="border-color: {{ $curTheme['primary'] }}40; color: {{ $curTheme['primary'] }};">
                            <span class="w-2 h-2 rounded-full animate-ping" style="background-color: {{ $curTheme['primary'] }};"></span>
                            <span>أداة التشخيص والمحاكاة الذكية</span>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Stats & Quick Telemetry HUD -->
            <div class="lg:col-span-4 flex flex-col gap-3">
                @foreach(($service->stats_json ?? []) as $stat)
                    <div class="p-4 rounded-2xl bg-obsidian-surface/90 border border-obsidian-border flex items-center justify-between gap-4">
                        <div>
                            <span class="text-xs text-slate-300 font-cairo font-bold block">{{ $stat['label'] }}</span>
                            <span class="text-[11px] text-slate-500 font-cairo">مؤشر أداء معتمد</span>
                        </div>
                        <span class="text-2xl font-extrabold font-mono" style="color: {{ $curTheme['primary'] }};">
                            {{ $stat['value'] }}
                        </span>
                    </div>
                @endforeach
            </div>

        </div>
    </section>

    {{-- ===================================================================== --}}
    {{-- 2. SPECIAL INTERACTIVE WIDGETS (Tailored to Service Type)             --}}
    {{-- ===================================================================== --}}
    <section id="special-widget-section" class="scroll-mt-24 space-y-6">
        @if($service->slug === 'brand-erp')
            {{-- BrandERP: 4-Tier Modular Architecture Showcase & Interactive Store / ROI triggers --}}
            <div class="glass-obsidian rounded-3xl border border-cyber-cyan/30 p-6 sm:p-8 space-y-6 shadow-[0_4px_30px_rgba(0,245,212,0.08)]">
                
                {{-- Banner Header --}}
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-obsidian-border/80 pb-5">
                    <div class="space-y-1 text-center md:text-right">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[10px] font-mono font-bold bg-cyber-cyan/15 text-cyber-cyan border border-cyber-cyan/30">
                            <span>TIER 1 TO TIER 4 · 4-TIER MODULAR PRICING</span>
                        </div>
                        <h3 class="text-xl sm:text-2xl font-black text-white font-cairo">
                            متجر BrandERP الموديلار: ابنِ منظومتك بالطبقات الأربع
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-300 font-cairo">
                            ابدأ بنواتك الأساسية بـ <strong class="text-cyber-cyan font-bold">2,000 ج.م فقط</strong>، ووسّع نظامك عبر 16 موديولاً كبيراً أو من بين متجر 500 ميزة دقيقة، أو اختر باقة المؤسسات الشاملة Class-A بـ 155,000 ج.م.
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center justify-center md:justify-end gap-3 shrink-0">
                        <a href="{{ route('store.branderp') }}"
                           class="px-5 py-2.5 rounded-xl btn-citadel-primary text-xs font-cairo font-black flex items-center gap-2 transition-all active:scale-95 shadow-neon-cyan">
                            <span>🏪</span>
                            <span>متجر BrandERP المستقل (شاشة كاملة)</span>
                        </a>
                        <button type="button"
                                @click="$dispatch('branderp-store-open')"
                                class="px-5 py-2.5 rounded-xl bg-obsidian-surface hover:bg-obsidian-card border border-cyber-cyan/40 text-cyber-cyan text-xs font-cairo font-bold flex items-center gap-2 transition-all active:scale-95">
                            <span>⚡</span>
                            <span>المتجر السريع الفوري (0ms Modal)</span>
                        </button>
                        <button type="button"
                                @click="$dispatch('branderp-roi-open')"
                                class="px-5 py-2.5 rounded-xl bg-obsidian-surface hover:bg-obsidian-card border border-cyber-gold/40 text-cyber-gold text-xs font-cairo font-bold flex items-center gap-2 transition-all active:scale-95">
                            <span class="w-2 h-2 rounded-full bg-cyber-gold animate-ping"></span>
                            <span>📊 حاسبة العائد الاستثماري ROI</span>
                        </button>
                    </div>
                </div>

                {{-- 4-Tier Interactive Preview Grid (Strictly 4 Cards: <= 6 cards rule) --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    {{-- Tier 1: Core --}}
                    <div class="p-5 rounded-2xl bg-obsidian-surface/90 border border-cyber-cyan/40 hover:border-cyber-cyan flex flex-col justify-between space-y-4 group transition-all">
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-mono font-bold text-cyber-cyan px-2.5 py-0.5 rounded bg-cyber-cyan/10">TIER 1</span>
                                <span class="text-xl">🏪</span>
                            </div>
                            <h4 class="text-base font-black text-white font-cairo">النواة الأساسية (BrandERP Core)</h4>
                            <p class="text-xs text-slate-400 font-cairo leading-relaxed">
                                كاشير POS فائق السرعة أوفلاين وأونلاين، إدارة مخزون وأصناف، باركود وطباعة حرارية، وحركة الخزينة اليومية.
                            </p>
                        </div>
                        <div class="pt-3 border-t border-obsidian-border/60 flex items-baseline justify-between">
                            <span class="text-xl font-black font-mono text-cyber-cyan">2,000 <span class="text-xs font-cairo text-slate-400">ج.م</span></span>
                            <span class="text-[10px] font-cairo text-emerald-400 font-bold">دفعة واحدة للنواة</span>
                        </div>
                    </div>

                    {{-- Tier 2: 16 Modules --}}
                    <div class="p-5 rounded-2xl bg-obsidian-surface/90 border border-obsidian-border hover:border-purple-500/50 flex flex-col justify-between space-y-4 group transition-all">
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-mono font-bold text-purple-400 px-2.5 py-0.5 rounded bg-purple-500/10">TIER 2</span>
                                <span class="text-xl">📦</span>
                            </div>
                            <h4 class="text-base font-black text-white font-cairo">16 موديولاً كبيراً للتوسع</h4>
                            <p class="text-xs text-slate-400 font-cairo leading-relaxed">
                                المخازن المتقدمة، الحسابات والقيود، HR والرواتب، التصنيع، واتساب، الفاتورة الإلكترونية، المطاعم والمتاجر.
                            </p>
                        </div>
                        <div class="pt-3 border-t border-obsidian-border/60 flex items-baseline justify-between">
                            <span class="text-xl font-black font-mono text-purple-300">1,500 - 7,500 <span class="text-xs font-cairo text-slate-400">ج.م</span></span>
                            <span class="text-[10px] font-cairo text-slate-400 font-bold">لكل موديول</span>
                        </div>
                    </div>

                    {{-- Tier 3: Micro-Features --}}
                    <div class="p-5 rounded-2xl bg-obsidian-surface/90 border border-obsidian-border hover:border-cyber-gold/50 flex flex-col justify-between space-y-4 group transition-all">
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-mono font-bold text-cyber-gold px-2.5 py-0.5 rounded bg-cyber-gold/10">TIER 3</span>
                                <span class="text-xl">⚡</span>
                            </div>
                            <h4 class="text-base font-black text-white font-cairo">متجر المميزات الدقيقة</h4>
                            <p class="text-xs text-slate-400 font-cairo leading-relaxed">
                                متجر يضم 500 ميزة دقيقة عبر أقسام التخصص، تشتري فقط ما تحتاجه دون إهدار أموالك على باقات غير مستغلة.
                            </p>
                        </div>
                        <div class="pt-3 border-t border-obsidian-border/60 flex items-baseline justify-between">
                            <span class="text-xl font-black font-mono text-cyber-gold">100 - 600 <span class="text-xs font-cairo text-slate-400">ج.م</span></span>
                            <span class="text-[10px] font-cairo text-slate-400 font-bold">500 ميزة</span>
                        </div>
                    </div>

                    {{-- Tier 4: Enterprise Suite --}}
                    <div class="p-5 rounded-2xl bg-gradient-to-br from-obsidian-card to-obsidian-surface border border-rose-500/40 hover:border-rose-500 flex flex-col justify-between space-y-4 group transition-all shadow-md">
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-mono font-bold text-rose-400 px-2.5 py-0.5 rounded bg-rose-500/10">TIER 4</span>
                                <span class="text-xl">👑</span>
                            </div>
                            <h4 class="text-base font-black text-white font-cairo">باقة المؤسسات (Class-A)</h4>
                            <p class="text-xs text-slate-400 font-cairo leading-relaxed">
                                المنظومة الشاملة: كافة الموديولات الـ 16 + جميع المميزات الدقيقة + فروع ومخازن غير محدودة وسيرفر خاص معزول.
                            </p>
                        </div>
                        <div class="pt-3 border-t border-obsidian-border/60 flex items-baseline justify-between">
                            <span class="text-xl font-black font-mono text-rose-300">155,000 <span class="text-xs font-cairo text-slate-400">ج.م</span></span>
                            <span class="text-[10px] font-cairo text-rose-400 font-bold">شاملة بالكامل</span>
                        </div>
                    </div>
                </div>

                {{-- Smart Upsell Engine Alert --}}
                <div class="rounded-2xl bg-gradient-to-r from-cyber-cyan/10 via-obsidian-surface to-purple-600/10 border border-cyber-cyan/30 p-4 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs font-cairo">
                    <div class="flex items-center gap-2.5">
                        <span class="text-xl">💡</span>
                        <span class="text-slate-300">
                            <strong>محرك الـ Smart Upsell التلقائي:</strong> عند اختيارك لأكثر من موديول في المتجر، يقترح النظام باقات مجمعة توفر ما يصل إلى <span class="text-cyber-cyan font-bold">35%</span> فوراً!
                        </span>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('store.branderp') }}" class="px-4 py-1.5 rounded-xl bg-cyber-cyan text-obsidian-base font-bold shrink-0 hover:opacity-90 transition-opacity">
                            فتح المتجر المستقل ⇥
                        </a>
                    </div>
                </div>

            </div>

            {{-- Include both modals --}}
            @if(isset($erpCatalog) && $erpCatalog)
                @include('partials.branderp-store-modal')
            @endif
            @include('partials.branderp-roi-modal')

        @elseif($service->slug === 'ai-agents')
            @include('partials.simulators.ai-agents-simulator')
        @elseif($service->slug === 'cybersecurity')
            <div class="rounded-2xl bg-rose-500/10 border border-rose-500/40 p-4 flex flex-col sm:flex-row items-center justify-between gap-3 font-cairo text-xs text-rose-200">
                <div class="flex items-center gap-3">
                    <span class="text-2xl">🛡️</span>
                    <div>
                        <strong class="text-white block text-sm">التدريع والأمن السيبراني VAPT وحماية الأسرة:</strong>
                        <span>باقات التدريع بالساعة من 700 إلى 5,000 ج.م/ساعة مع حلول الرقابة الأبوية وحماية ومراقبة هواتف الأبناء تحت 18 سنة.</span>
                    </div>
                </div>
                <div class="px-3 py-1.5 rounded-xl bg-obsidian-surface border border-rose-500/40 text-rose-400 font-bold text-[11px] shrink-0">
                    🔒 اتفاقية سرية وعدم إفصاح (NDA) معتمدة
                </div>
            </div>
            @include('partials.simulators.cybersecurity-scanner')
        @elseif($service->slug === 'maintenance')
            <div class="rounded-2xl bg-amber-500/10 border border-amber-500/40 p-4 flex flex-col sm:flex-row items-center justify-between gap-3 font-cairo text-xs text-amber-200">
                <div class="flex items-center gap-3">
                    <span class="text-2xl">⚡</span>
                    <div>
                        <strong class="text-white block text-sm">تسعير الزيارة الميدانية الهندسية (سعر ثابت معتمد):</strong>
                        <span>400 ج.م / ساعة بحد أدنى 3 ساعات للزيارة (إجمالي 1,200 ج.م) — سعر ثابت بدون أي خصم على الساعات الإضافية.</span>
                    </div>
                </div>
                <div class="px-3 py-1.5 rounded-xl bg-obsidian-surface border border-rose-500/40 text-rose-400 font-bold text-[11px] shrink-0">
                    ⚠️ تنبيه: يُستثنى تماماً صيانة البورد والدوائر الدقيقة
                </div>
            </div>
            @include('partials.simulators.maintenance-benchmark')
        @elseif($service->slug === 'business-consulting')
            @include('partials.simulators.business-consulting-diagnostic')
        @elseif($service->slug === 'academy')
            @include('partials.simulators.academy-curriculum-explorer')
        @endif
    </section>

    {{-- ===================================================================== --}}
    {{-- 3. SEARCHABLE & FILTERABLE ICONS MATRIX (Strictly Max 6 On-Page Cards) --}}
    {{-- ===================================================================== --}}
    <section id="icons-matrix-section" class="space-y-6 scroll-mt-24">
        
        <!-- Header banner -->
        <div class="glass-obsidian rounded-2xl border p-4 sm:p-5 flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-sm"
             style="border-color: {{ $curTheme['primary'] }}30;">
            <div class="space-y-1 text-center md:text-right">
                <h3 class="text-xl sm:text-2xl font-black text-white font-cairo flex items-center justify-center md:justify-start gap-2.5">
                    <span class="w-3 h-3 rounded-full shrink-0" style="background-color: {{ $curTheme['primary'] }};"></span>
                    <span>مصفوفة المزايا والقدرات التشغيلية ({{ $service->icons->count() }} عنصر)</span>
                </h3>
                <p class="text-xs text-slate-400 font-cairo">
                    يتم استعراض أبرز 6 قدرات مباشرة على الصفحة، مع إمكانية فتح المستكشف الكامل 0ms بضغطة زر.
                </p>
            </div>

            <!-- Quick Action -->
            <button type="button"
                    @click="explorerModalOpen = true"
                    class="px-4 py-2 rounded-xl text-xs font-cairo font-bold transition-all flex items-center justify-center gap-2"
                    style="background-color: {{ $curTheme['primary'] }}20; border: 1px solid {{ $curTheme['primary'] }}40; color: {{ $curTheme['primary'] }};">
                <span>⚡ فتح المستكشف الكامل ({{ $service->icons->count() }}+)</span>
            </button>
        </div>

        <!-- Capabilities Container: Responsive Grid on Desktop / Single-Card Snap Slider on Mobile (STRICTLY MAX 6 CARDS) -->
        <div class="relative" x-data="soulyMobileCarousel({ total: {{ count($service->icons->take(6)) }} })">
            <div x-ref="track"
                 @scroll.debounce.50ms="onScroll()"
                 @touchstart="pause()"
                 @touchend="resume()"
                 class="max-md:flex max-md:overflow-x-auto max-md:snap-x max-md:snap-mandatory max-md:scrollbar-none max-md:pb-2 md:grid md:grid-cols-6 gap-3" 
                 data-cards-container="capabilities">
                @foreach($service->icons->take(6) as $icon)
                    <button type="button" 
                            data-icon-card-preview
                            data-icon-popup
                            data-icon-id="{{ $icon->id }}"
                            data-icon-title="{{ $icon->title }}"
                            data-icon-badge="{{ $icon->badge_text }}"
                            data-icon-summary="{{ $icon->popup_summary }}"
                            data-icon-bullets="{{ json_encode($icon->popup_bullets_json) }}"
                            data-service-name="{{ $service->name }}"
                            data-cta-text="{{ $icon->popup_cta_text }}"
                            class="max-md:w-[85%] max-md:shrink-0 max-md:snap-center p-4 rounded-2xl bg-obsidian-surface/90 hover:bg-obsidian-card border border-obsidian-border {{ $curTheme['border_hover'] }} text-center sm:text-right flex flex-col items-center sm:items-start justify-between gap-3 group active:scale-95 transition-all w-full shadow-sm">
                        <div class="flex items-center justify-between w-full gap-2">
                            <div class="w-8 h-8 rounded-xl bg-obsidian-card border flex items-center justify-center group-hover:scale-110 transition-transform shadow-sm shrink-0"
                                 style="border-color: {{ $curTheme['primary'] }}40; color: {{ $curTheme['primary'] }};">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <span class="text-[10px] font-cairo font-bold px-2.5 py-0.5 rounded-full shadow-sm shrink-0"
                                  style="background-color: {{ $curTheme['primary'] }}15; border: 1px solid {{ $curTheme['primary'] }}30; color: {{ $curTheme['primary'] }};">
                                {{ $icon->badge_text ?? 'جاهز' }}
                            </span>
                        </div>
                        <span class="text-xs font-bold text-slate-200 group-hover:text-white font-cairo block leading-snug w-full text-center sm:text-right">
                            {{ $icon->title }}
                        </span>
                    </button>
                @endforeach
            </div>

            <!-- Mobile Dots Indicator -->
            <div class="md:hidden flex items-center justify-between pt-2 px-1">
                <div class="flex items-center gap-1.5">
                    @foreach($service->icons->take(6) as $dotIdx => $dotIcon)
                        <button type="button" 
                                @click="goTo({{ $dotIdx }})"
                                class="h-1.5 rounded-full transition-all duration-300"
                                :class="active === {{ $dotIdx }} ? 'w-6' : 'w-2 bg-slate-600'"
                                :style="active === {{ $dotIdx }} ? 'background-color: {{ $curTheme['primary'] }};' : ''"></button>
                    @endforeach
                </div>
                <div class="flex items-center gap-1">
                    <button type="button" @click="prev()" class="p-1 px-2 rounded-lg bg-obsidian-surface border border-obsidian-border text-slate-300 text-xs">‹</button>
                    <span class="text-[10px] font-mono text-slate-400" x-text="`${active + 1} / {{ count($service->icons->take(6)) }}`"></span>
                    <button type="button" @click="next()" class="p-1 px-2 rounded-lg bg-obsidian-surface border border-obsidian-border text-slate-300 text-xs">›</button>
                </div>
            </div>
        </div>

        <!-- Interactive Trigger Banner for Remaining Capabilities -->
        @if($service->icons->count() > 6)
            <div class="rounded-2xl p-5 border flex flex-col sm:flex-row items-center justify-between gap-4 transition-all"
                 style="background: linear-gradient(135deg, {{ $curTheme['primary'] }}0c, #0d121f); border-color: {{ $curTheme['primary'] }}35;">
                <div class="flex items-center gap-3 text-center sm:text-right">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg shrink-0"
                         style="background-color: {{ $curTheme['primary'] }}20; color: {{ $curTheme['primary'] }};">
                        ⚡
                    </div>
                    <div>
                        <h4 class="text-sm font-black text-white font-cairo">
                            متبقي {{ $service->icons->count() - 6 }}+ قدرة وميزة تشغيلية متقدمة في {{ $service->name }}
                        </h4>
                        <p class="text-xs text-slate-400 font-cairo">
                            تم حصر أول 6 مزايا على الصفحة للحفاظ على النظافة البصرية، استعرض الكتالوج الشامل داخل النافذة التفاعلية بدون مغادرة الصفحة.
                        </p>
                    </div>
                </div>

                <button type="button"
                        @click="explorerModalOpen = true"
                        class="px-6 py-2.5 rounded-xl font-cairo font-black text-xs flex items-center gap-2 transition-all active:scale-95 shrink-0 shadow-lg"
                        style="background-color: {{ $curTheme['primary'] }}; color: #050811;">
                    <span>⚡ استكشف كافة القدرات والمميزات ({{ $service->icons->count() }}+ عنصر) في نافذة تفاعلية 0ms</span>
                </button>
            </div>
        @endif

    </section>

    {{-- ===================================================================== --}}
    {{-- 4. SIDE-BY-SIDE PACKAGES SHOWCASE & DIRECT BOOKING                    --}}
    {{-- ===================================================================== --}}
    <section id="packages-section" class="space-y-6 scroll-mt-24">
        
        <!-- Title banner -->
        <div class="glass-obsidian rounded-2xl border border-obsidian-border/80 p-4 sm:p-5 flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-sm">
            <div class="space-y-1 text-center md:text-right">
                <h3 class="text-xl sm:text-2xl font-black text-white font-cairo flex items-center justify-center md:justify-start gap-2.5">
                    <span class="w-3 h-3 rounded-full shrink-0" style="background-color: {{ $curTheme['primary'] }};"></span>
                    <span>باقات {{ $service->name }} الشاملة</span>
                </h3>
                <p class="text-xs text-slate-400 font-cairo">
                    كل باقة تحتوي على صفحة مخصصة توضح البنية الهندسية، شروط الضمان، والأسئلة الشائعة.
                </p>
            </div>
            <span class="text-xs font-cairo font-bold px-3.5 py-1.5 rounded-full border self-center md:self-auto shrink-0 shadow-sm"
                  style="background-color: {{ $curTheme['primary'] }}15; border-color: {{ $curTheme['primary'] }}30; color: {{ $curTheme['primary'] }};">
                <span class="font-mono">{{ $service->packages->count() }}</span> باقات معتمدة
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 pt-2">
            @foreach($service->packages as $pkg)
                <div class="relative rounded-3xl glass-obsidian border p-5 sm:p-7 flex flex-col justify-between transition-all duration-300 w-full text-center sm:text-right items-center sm:items-start {{ $pkg->is_featured ? 'shadow-lg bg-obsidian-card/90' : 'border-obsidian-border hover:border-obsidian-border/90' }}"
                     style="{{ $pkg->is_featured ? 'border-color: ' . $curTheme['primary'] . '80;' : '' }}">
                    
                    @if($pkg->is_featured)
                        <div class="absolute -top-3 left-1/2 -translate-x-1/2 sm:translate-x-0 sm:left-6">
                            <span class="px-3 py-1 rounded-full text-[10px] font-cairo font-black shadow-md"
                                  style="background-color: {{ $curTheme['primary'] }}; color: #050811;">
                                {{ $pkg->badge_text ?? 'الأكثر طلباً' }}
                            </span>
                        </div>
                    @endif

                    <div class="space-y-5 w-full">
                        <div class="space-y-2 w-full">
                            <div class="flex items-center justify-between w-full">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-cairo font-bold bg-slate-800/80 text-slate-200 border border-slate-700/60">
                                    {{ $pkg->billing_type }}
                                </span>
                                @if($pkg->warranty_days)
                                    <span class="text-[10px] font-cairo font-bold text-emerald-400 flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                        <span>ضمان {{ $pkg->warranty_days }} يوم</span>
                                    </span>
                                @endif
                            </div>
                            <h4 class="text-xl font-extrabold text-white font-cairo w-full text-center sm:text-right">
                                {{ $pkg->name }}
                            </h4>
                            @if($pkg->subtitle)
                                <p class="text-xs text-slate-400 font-cairo leading-relaxed w-full text-center sm:text-right">{{ $pkg->subtitle }}</p>
                            @endif
                        </div>

                        <!-- Pricing Block -->
                        <div class="p-4 rounded-2xl bg-obsidian-surface/90 border border-obsidian-border space-y-1 w-full flex flex-col items-center sm:items-start text-center sm:text-right">
                            @if($pkg->original_price && $pkg->original_price > $pkg->price)
                                <div class="flex items-center justify-center sm:justify-start gap-2">
                                    <span class="text-xs font-mono text-slate-500 line-through">{{ number_format($pkg->original_price, 0) }} EGP</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-rose-500/10 text-rose-400 border border-rose-500/30">
                                        وفر {{ number_format($pkg->original_price - $pkg->price, 0) }} EGP
                                    </span>
                                </div>
                            @endif

                            <div class="flex items-baseline justify-center sm:justify-start gap-2 w-full">
                                <span class="text-3xl font-extrabold font-mono" style="color: {{ $curTheme['primary'] }};">
                                    {{ number_format($pkg->price, 0) }}
                                </span>
                                <span class="text-xs font-mono text-slate-300">EGP</span>
                                @if($pkg->unit_label)
                                    <span class="text-xs text-slate-400 font-cairo">/ {{ $pkg->unit_label }}</span>
                                @endif
                            </div>
                        </div>

                        <!-- Features List -->
                        <ul class="space-y-2 w-full">
                            @foreach($pkg->features_json as $feat)
                                <li class="flex items-center sm:items-start justify-center sm:justify-start gap-2 text-xs text-slate-300 font-cairo w-full text-center sm:text-right">
                                    <span class="w-1.5 h-1.5 rounded-full mt-1.5 shrink-0 hidden sm:inline-block" style="background-color: {{ $curTheme['primary'] }};"></span>
                                    <span>{{ $feat }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Package Actions: Sub-page & Direct Booking -->
                    <div class="pt-6 border-t border-obsidian-border/80 mt-6 space-y-2 w-full">
                        <a href="{{ route('services.package', [$service->slug, $pkg->slug]) }}" 
                           class="w-full py-2.5 rounded-xl btn-citadel-secondary text-xs text-center block">
                            صفحة مواصفات الباقة الكاملة ⇥
                        </a>
                        @auth
                            <a href="{{ route('portal.bookings') }}?service={{ $service->slug }}&package={{ $pkg->slug }}" 
                               class="w-full py-2.5 rounded-xl text-xs text-center block font-cairo font-bold transition-all shadow-md"
                               style="background-color: {{ $curTheme['primary'] }}; color: #050811;">
                                احجز الآن عبر المحفظة
                            </a>
                        @else
                            <a href="{{ route('portal.bookings') }}?service={{ $service->slug }}&package={{ $pkg->slug }}" 
                               @click.prevent="$dispatch('open-express-booking', {
                                   serviceId: {{ $service->id }},
                                   serviceSlug: '{{ $service->slug }}',
                                   serviceName: '{{ addslashes($service->name) }}',
                                   packageId: {{ $pkg->id }},
                                   packageSlug: '{{ $pkg->slug }}',
                                   packageName: '{{ addslashes($pkg->name) }}',
                                   price: {{ (float) $pkg->price }}
                                })"
                               class="w-full py-2.5 rounded-xl text-xs text-center block font-cairo font-bold transition-all shadow-md"
                               style="background-color: {{ $curTheme['primary'] }}; color: #050811;">
                                احجز الآن عبر المحفظة
                            </a>
                        @endauth
                    </div>

                </div>
            @endforeach
        </div>

    </section>

    {{-- ===================================================================== --}}
    {{-- 5. 0ms CAPABILITIES EXPLORER MODAL (Strict Fixed-Height & Scrollable) --}}
    {{-- ===================================================================== --}}
    <div id="modal-capabilities-explorer"
         x-show="explorerModalOpen"
         x-cloak
         @keydown.escape.window="explorerModalOpen = false"
         class="fixed inset-0 z-[99998] bg-black/85 backdrop-blur-md flex items-end md:items-center justify-center max-md:p-0 p-4 sm:p-6 mobile-bottom-sheet-modal"
         dir="rtl">
        
        <div class="relative w-full max-w-5xl max-h-[80vh] flex flex-col max-md:rounded-t-3xl max-md:rounded-b-none rounded-3xl glass-obsidian border max-md:border-b-0 shadow-2xl overflow-hidden bg-obsidian-base mobile-bottom-sheet-panel"
             style="border-color: {{ $curTheme['primary'] }}50;"
             @click.outside="explorerModalOpen = false">

            <!-- Mobile Drag Handle -->
            <div class="w-12 h-1.5 bg-slate-600 rounded-full mx-auto my-2 md:hidden"></div>

            <!-- 1. STICKY HEADER (Title + Search + Filter Pills + Close Button) -->
            <div class="sticky top-0 z-20 bg-obsidian-base/95 backdrop-blur-xl border-b border-obsidian-border p-4 sm:p-5 flex flex-col md:flex-row md:items-center justify-between gap-4 shrink-0">
                <div class="flex items-center gap-3">
                    <span class="w-3.5 h-3.5 rounded-full" style="background-color: {{ $curTheme['primary'] }};"></span>
                    <div>
                        <h3 class="text-base sm:text-lg font-black text-white font-cairo flex items-center gap-2">
                            <span>كتالوج قدرات ومميزات {{ $service->name }}</span>
                            <span class="text-xs font-mono px-2.5 py-0.5 rounded-full" style="background-color: {{ $curTheme['primary'] }}20; color: {{ $curTheme['primary'] }};">
                                {{ $service->icons->count() }} عنصر
                            </span>
                        </h3>
                        <p class="text-[11px] text-slate-400 font-cairo">ابحث أو صفِّ المزايا وانقر على أي عنصر لاستعراض بطاقته الفنية الفورية</p>
                    </div>
                </div>

                <!-- Search and Close -->
                <div class="flex items-center gap-2 w-full md:w-auto">
                    <input type="text"
                           x-model="explorerSearch"
                           placeholder="بحث فوري في المزايا..."
                           class="bg-obsidian-surface text-xs text-slate-200 placeholder-slate-500 rounded-xl px-4 py-2 border border-obsidian-border focus:outline-none w-full md:w-56 font-cairo"
                           style="border-color: {{ $curTheme['primary'] }}40;">

                    <button type="button" 
                            @click="explorerModalOpen = false"
                            class="w-9 h-9 rounded-xl bg-obsidian-surface border border-obsidian-border hover:border-rose-500/50 text-slate-400 hover:text-rose-400 flex items-center justify-center transition-all shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- 2. SCROLLABLE BODY (overflow-y-auto flex-1 custom-scrollbar p-6) -->
            <div class="overflow-y-auto flex-1 custom-scrollbar p-5 sm:p-6 space-y-4">
                
                <!-- Category Filter Pills -->
                @php
                    $groups = $service->icons->pluck('group_type')->unique()->filter();
                @endphp
                @if($groups->count() > 1)
                    <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none">
                        <button type="button"
                                @click="explorerGroup = 'all'"
                                :class="explorerGroup === 'all' ? 'font-black text-obsidian-base' : 'bg-obsidian-surface text-slate-400 hover:text-white'"
                                class="px-3.5 py-1.5 rounded-lg text-xs font-cairo font-bold transition-all shrink-0"
                                :style="explorerGroup === 'all' ? 'background-color: {{ $curTheme['primary'] }};' : ''">
                            كافة المجموعات ({{ $service->icons->count() }})
                        </button>
                        @foreach($groups as $grp)
                            <button type="button"
                                    @click="explorerGroup = '{{ $grp }}'"
                                    :class="explorerGroup === '{{ $grp }}' ? 'font-black text-obsidian-base' : 'bg-obsidian-surface text-slate-400 hover:text-white'"
                                    class="px-3.5 py-1.5 rounded-lg text-xs font-cairo font-bold transition-all shrink-0"
                                    :style="explorerGroup === '{{ $grp }}' ? 'background-color: {{ $curTheme['primary'] }};' : ''">
                                {{ $grp }}
                            </button>
                        @endforeach
                    </div>
                @endif

                <!-- Grid of all items inside modal -->
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
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
                                x-show="matchesModalSearch('{{ addslashes($icon->title) }}', '{{ addslashes($icon->popup_summary) }}', '{{ $icon->group_type }}')"
                                class="p-4 rounded-2xl bg-obsidian-surface/90 hover:bg-obsidian-card border border-obsidian-border {{ $curTheme['border_hover'] }} text-center sm:text-right flex flex-col items-center sm:items-start justify-between gap-3 group active:scale-95 transition-all w-full">
                            <div class="flex items-center justify-between w-full gap-2">
                                <div class="w-8 h-8 rounded-xl bg-obsidian-card border flex items-center justify-center group-hover:scale-110 transition-transform shadow-sm shrink-0"
                                     style="border-color: {{ $curTheme['primary'] }}40; color: {{ $curTheme['primary'] }};">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                </div>
                                <span class="text-[10px] font-cairo font-bold px-2.5 py-0.5 rounded-full shadow-sm shrink-0"
                                      style="background-color: {{ $curTheme['primary'] }}15; border: 1px solid {{ $curTheme['primary'] }}30; color: {{ $curTheme['primary'] }};">
                                    {{ $icon->badge_text ?? 'معتمد' }}
                                </span>
                            </div>
                            <div class="space-y-1 w-full text-center sm:text-right">
                                <span class="text-xs font-bold text-slate-200 group-hover:text-white font-cairo block leading-snug">
                                    {{ $icon->title }}
                                </span>
                                @if($icon->group_type)
                                    <span class="text-[10px] text-slate-500 font-mono block">{{ $icon->group_type }}</span>
                                @endif
                            </div>
                        </button>
                    @endforeach
                </div>

            </div>

            <!-- 3. STICKY FOOTER (Count + Package Jump + Close) -->
            <div class="sticky bottom-0 z-20 bg-obsidian-base/95 backdrop-blur-xl border-t border-obsidian-border p-4 px-6 flex items-center justify-between gap-4 shrink-0">
                <div class="text-xs font-cairo text-slate-400">
                    إجمالي قدرات القطاع: <span class="font-mono font-bold text-white">{{ $service->icons->count() }} عنصر</span>
                </div>

                <div class="flex items-center gap-3">
                    <a href="#packages-section" 
                       @click="explorerModalOpen = false"
                       class="px-5 py-2 rounded-xl text-xs font-cairo font-bold transition-all"
                       style="background-color: {{ $curTheme['primary'] }}; color: #050811;">
                        استعراض باقات هذا القطاع ⇥
                    </a>
                    <button type="button" 
                            @click="explorerModalOpen = false"
                            class="px-4 py-2 rounded-xl bg-obsidian-surface border border-obsidian-border text-xs font-cairo text-slate-300 hover:text-white transition-all">
                        إغلاق النافذة
                    </button>
                </div>
            </div>

        </div>

    </div>

</div>

@push('scripts')
<script>
    function serviceLandingController() {
        return {
            iconSearchQuery: '',
            explorerModalOpen: false,
            explorerSearch: '',
            explorerGroup: 'all',

            matchesIconSearch(title, summary) {
                if (!this.iconSearchQuery.trim()) return true;
                const query = this.iconSearchQuery.toLowerCase();
                return title.toLowerCase().includes(query) || summary.toLowerCase().includes(query);
            },

            matchesModalSearch(title, summary, group) {
                const matchesGrp = this.explorerGroup === 'all' || !group || group === this.explorerGroup;
                if (!this.explorerSearch.trim()) return matchesGrp;
                const q = this.explorerSearch.toLowerCase();
                const matchesText = title.toLowerCase().includes(q) || summary.toLowerCase().includes(q);
                return matchesGrp && matchesText;
            }
        };
    }
</script>
@endpush
@endsection
