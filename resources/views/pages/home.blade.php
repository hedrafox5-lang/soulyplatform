@extends((request()->query('view') === 'mobile' || request()->header('sec-ch-ua-mobile') === '?1' || preg_match('/(android|iphone|ipad|ipod|mobile|touch|fban|fbav|instagram)/i', request()->userAgent() ?? '')) ? 'layouts.mobile' : 'layouts.desktop')

@section('title', 'سولي | أنظمة وحلول الأعمال والذكاء الاصطناعي')
@section('page-title', 'سولي · المنظومة السيادية')

@section('content')
<div class="space-y-8 sm:space-y-14 lg:space-y-20 pb-24 sm:pb-32" x-data="citadelHomepageController()">

    <!-- ===================================================================== -->
    <!-- 0. LIVE TELEMETRY & SOCIAL PROOF BAR (NEW UX ENHANCEMENT)             -->
    <!-- ===================================================================== -->
    <div class="rounded-2xl bg-[#0B0C16]/95 border border-cyber-cyan/25 px-3.5 py-2 flex items-center justify-between gap-3 overflow-x-auto scrollbar-none shadow-sm">
        <div class="flex items-center gap-2 shrink-0">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
            <span class="text-[11px] font-cairo font-bold text-white">حالة المنظومة: جاهزية 100%</span>
        </div>
        <div class="flex items-center gap-4 text-[11px] font-cairo text-slate-300 shrink-0">
            <span class="flex items-center gap-1"><strong class="text-cyber-cyan font-mono">2,000 ج.م</strong> نواة BrandERP</span>
            <span class="text-slate-600">|</span>
            <span class="flex items-center gap-1"><strong class="text-purple-400 font-mono">1.8s</strong> سرعة وكيل AI</span>
            <span class="text-slate-600">|</span>
            <span class="flex items-center gap-1"><strong class="text-cyber-gold font-mono">400 ج.م/س</strong> صيانة بمقرك</span>
        </div>
    </div>

    <!-- ===================================================================== -->
    <!-- 1. MOBILE SECTOR STORY BAR (6 Native App Circular Stories)            -->
    <!-- ===================================================================== -->
    <div class="md:hidden">
        <div class="flex items-center justify-between pb-1.5 px-1">
            <span class="text-xs font-black text-white font-cairo flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-cyber-cyan"></span>
                <span>استكشف قطاعات سولي الستة</span>
            </span>
            <span class="text-[10px] text-cyber-cyan font-cairo">اضغط للانتقال السريع 👇</span>
        </div>
        <div class="flex items-center gap-3 overflow-x-auto scrollbar-none py-2 px-1">
            @foreach($services as $sIdx => $svc)
                <a href="#service-{{ $svc->slug }}" 
                   class="flex flex-col items-center gap-1.5 shrink-0 group focus:outline-none active:scale-90 transition-transform">
                    <div class="relative p-[2px] rounded-2xl shadow-md"
                         style="background: linear-gradient(135deg, {{ $svc->accent_color ?? '#00F5D4' }}, {{ $svc->secondary_color ?? '#7B2CBF' }});">
                        <div class="w-14 h-14 rounded-2xl bg-[#0B0C16] flex items-center justify-center text-2xl border border-black/40">
                            @if($svc->slug === 'brand-erp') 🏪
                            @elseif($svc->slug === 'ai-agents') 🤖
                            @elseif($svc->slug === 'cybersecurity') 🛡️
                            @elseif($svc->slug === 'maintenance') 🔧
                            @elseif($svc->slug === 'business-consulting') 📈
                            @elseif($svc->slug === 'academy') 🎓
                            @else ⚡ @endif
                        </div>
                    </div>
                    <span class="text-[10px] font-bold font-cairo text-slate-200 max-w-[72px] text-center truncate">
                        {{ $svc->name }}
                    </span>
                </a>
            @endforeach
        </div>
    </div>

    <!-- ===================================================================== -->
    <!-- 2. HERO STAGE (ZERO AUTO-SCROLL, FIXED HEIGHT STABILITY, TOUCH SWIPE) -->
    <!-- ===================================================================== -->
    <section class="relative rounded-3xl glass-obsidian border border-obsidian-border overflow-hidden p-5 sm:p-8 lg:p-10 flex flex-col justify-between"
             @touchstart.passive="handleTouchStart($event)"
             @touchend.passive="handleTouchEnd($event)">
        
        <!-- Ambient Glow -->
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute -top-24 -left-24 w-72 h-72 rounded-full blur-[80px] opacity-15" 
                 :style="`background-color: ${currentSlideData.accent_color || '#00F5D4'}`"></div>
            <div class="absolute -bottom-24 -right-24 w-72 h-72 rounded-full blur-[80px] opacity-15" 
                 :style="`background-color: ${currentSlideData.secondary_color || '#7B2CBF'}`"></div>
        </div>

        <!-- Top Selector Tabs -->
        <div class="relative z-10 flex items-center justify-between gap-3 pb-4 border-b border-white/10 overflow-x-auto scrollbar-none">
            <div class="flex items-center gap-1.5 shrink-0">
                <template x-for="(svc, idx) in slides" :key="svc.id">
                    <button @click="setSlide(idx)" 
                            type="button"
                            class="px-3 py-1.5 rounded-xl text-xs font-cairo font-bold transition-all flex items-center gap-1.5 shrink-0 cursor-pointer"
                            :class="activeSlide === idx ? 
                                'bg-cyber-cyan text-[#05050A] font-black shadow-neon-cyan' : 
                                'bg-[#121422] text-slate-300 hover:text-white border border-white/5'">
                        <span class="w-2 h-2 rounded-full shrink-0" 
                              :style="`background-color: ${activeSlide === idx ? '#05050A' : (svc.accent_color || '#00F5D4')}`"></span>
                        <span x-text="svc.name"></span>
                    </button>
                </template>
            </div>
        </div>

        <!-- Main Slide Copy (Stabilized height to prevent vertical layout shift) -->
        <div class="relative z-10 py-6 sm:py-8 grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-8 items-center min-h-[320px] sm:min-h-[340px]">
            
            <div class="lg:col-span-7 space-y-4">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[11px] sm:text-xs font-cairo font-bold border"
                     :style="`background-color: ${currentSlideData.accent_color || '#00F5D4'}15; border-color: ${currentSlideData.accent_color || '#00F5D4'}50; color: ${currentSlideData.accent_color || '#00F5D4'}`">
                    <span class="w-2 h-2 rounded-full" :style="`background-color: ${currentSlideData.accent_color || '#00F5D4'}`"></span>
                    <span x-text="currentSlideData.hero_badge || 'منظومة سولي السيادية'"></span>
                </div>

                <h1 class="text-2xl sm:text-4xl lg:text-5xl font-black text-white font-cairo leading-tight" 
                    x-text="currentSlideData.name"></h1>

                <p class="text-xs sm:text-base text-slate-300 font-cairo leading-relaxed line-clamp-3" 
                   x-text="currentSlideData.tagline || currentSlideData.short_description"></p>

                <div class="flex flex-wrap items-center gap-2.5 pt-2">
                    <button type="button"
                            @click="$dispatch('open-express-booking', {
                                serviceId: currentSlideData.id,
                                serviceSlug: currentSlideData.slug,
                                serviceName: currentSlideData.name,
                                packageName: 'باقة ' + currentSlideData.name,
                                price: 2000
                            })"
                            class="px-5 py-3 rounded-xl btn-citadel-primary text-xs sm:text-sm font-black shadow-neon-cyan cursor-pointer active:scale-95 transition-transform">
                        <span>احجز هذه المنظومة فوراً ⚡</span>
                    </button>

                    <a :href="`#service-${currentSlideData.slug}`" 
                       class="px-4 py-3 rounded-xl btn-citadel-secondary text-xs sm:text-sm font-bold">
                        <span>استعراض الباقات والمزايا ↓</span>
                    </a>
                </div>
            </div>

            <!-- Right: Quick Stats HUD Card -->
            <div class="lg:col-span-5 grid grid-cols-2 sm:grid-cols-1 gap-2.5">
                <template x-for="(stat, sIdx) in (currentSlideData.stats_json || []).slice(0, 3)" :key="sIdx">
                    <div class="p-3.5 rounded-2xl bg-[#121422]/90 border border-white/10 flex flex-col sm:flex-row sm:items-center justify-between gap-1 sm:gap-4">
                        <span class="text-[11px] sm:text-xs text-slate-300 font-cairo font-bold" x-text="stat.label"></span>
                        <span class="text-lg sm:text-2xl font-black font-mono text-cyber-cyan" x-text="stat.value"></span>
                    </div>
                </template>
            </div>

        </div>

        <!-- Bottom Manual Controls (User-Controlled Only, No Forced Auto-Jump) -->
        <div class="relative z-10 flex items-center justify-between gap-4 pt-3 border-t border-white/10">
            <div class="flex items-center gap-1.5">
                <template x-for="(s, i) in slides" :key="i">
                    <button type="button"
                            @click="setSlide(i)"
                            class="h-2 rounded-full transition-all duration-300 cursor-pointer" 
                            :class="activeSlide === i ? 'w-8 bg-cyber-cyan' : 'w-2 bg-slate-700'"></button>
                </template>
            </div>

            <div class="flex items-center gap-2">
                <span class="text-[11px] text-slate-400 font-cairo hidden sm:inline">اسحب أو اضغط للتنقل:</span>
                <button type="button" @click="prevSlide()" class="p-2 rounded-xl bg-[#121422] border border-white/10 text-slate-200 hover:text-cyber-cyan cursor-pointer">
                    ‹ السابق
                </button>
                <button type="button" @click="nextSlide()" class="p-2 rounded-xl bg-[#121422] border border-white/10 text-slate-200 hover:text-cyber-cyan cursor-pointer">
                    التالي ›
                </button>
            </div>
        </div>
    </section>

    <!-- ===================================================================== -->
    <!-- 3. NEW FEATURE: INTERACTIVE SOLUTION FINDER (1-Tap Guided Funnel)     -->
    <!-- ===================================================================== -->
    <section class="rounded-3xl bg-gradient-to-r from-[#121422] via-[#0B0C16] to-[#121422] border border-cyber-cyan/30 p-5 sm:p-7 space-y-4 shadow-lg">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <span class="text-[10px] font-mono font-bold text-cyber-cyan uppercase">SMART SOLUTION NAVIGATOR · مرشدك الفوري</span>
                <h2 class="text-base sm:text-xl font-black text-white font-cairo">ما الذي تبحث عنه اليوم لشركتك أو أجهزتك؟</h2>
            </div>
            <span class="text-xs text-slate-400 font-cairo">اختر هدفك للوصول الفوري للحل الأنسب:</span>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3">
            <button type="button"
                    @click="$dispatch('branderp-store-open')"
                    class="p-3.5 sm:p-4 rounded-2xl bg-[#05050A]/90 hover:bg-cyber-cyan/10 border border-cyber-cyan/30 hover:border-cyber-cyan text-right space-y-1.5 transition-all group cursor-pointer active:scale-95">
                <div class="flex items-center justify-between">
                    <span class="text-2xl">🏪</span>
                    <span class="text-[10px] font-mono text-cyber-cyan bg-cyber-cyan/10 px-2 py-0.5 rounded-full">2,000 ج.م</span>
                </div>
                <strong class="text-xs sm:text-sm font-black text-white font-cairo block group-hover:text-cyber-cyan">كاشير ونظام حسابات ومخازن</strong>
                <span class="text-[10px] text-slate-400 font-cairo block">افتح متجر BrandERP وفصّل نظامك</span>
            </button>

            <button type="button"
                    @click="openSectorModal('ai-agents', 'simulator')"
                    class="p-3.5 sm:p-4 rounded-2xl bg-[#05050A]/90 hover:bg-purple-500/10 border border-purple-500/30 hover:border-purple-400 text-right space-y-1.5 transition-all group cursor-pointer active:scale-95">
                <div class="flex items-center justify-between">
                    <span class="text-2xl">🤖</span>
                    <span class="text-[10px] font-mono text-purple-300 bg-purple-500/10 px-2 py-0.5 rounded-full">رد في 1.8s</span>
                </div>
                <strong class="text-xs sm:text-sm font-black text-white font-cairo block group-hover:text-purple-300">موظف ذكاء اصطناعي للمبيعات</strong>
                <span class="text-[10px] text-slate-400 font-cairo block">يرد 24/7 على واتساب وفيسبوك</span>
            </button>

            <button type="button"
                    @click="openSectorModal('maintenance', 'simulator')"
                    class="p-3.5 sm:p-4 rounded-2xl bg-[#05050A]/90 hover:bg-amber-500/10 border border-amber-500/30 hover:border-cyber-gold text-right space-y-1.5 transition-all group cursor-pointer active:scale-95">
                <div class="flex items-center justify-between">
                    <span class="text-2xl">🔧</span>
                    <span class="text-[10px] font-mono text-cyber-gold bg-amber-500/10 px-2 py-0.5 rounded-full">400 ج.م/س</span>
                </div>
                <strong class="text-xs sm:text-sm font-black text-white font-cairo block group-hover:text-cyber-gold">صيانة هندسية للأجهزة بمقرك</strong>
                <span class="text-[10px] text-slate-400 font-cairo block">تبريد ألماني واختبار ضغط أمام عينيك</span>
            </button>

            <button type="button"
                    @click="openSectorModal('cybersecurity', 'simulator')"
                    class="p-3.5 sm:p-4 rounded-2xl bg-[#05050A]/90 hover:bg-rose-500/10 border border-rose-500/30 hover:border-rose-400 text-right space-y-1.5 transition-all group cursor-pointer active:scale-95">
                <div class="flex items-center justify-between">
                    <span class="text-2xl">🛡️</span>
                    <span class="text-[10px] font-mono text-rose-400 bg-rose-500/10 px-2 py-0.5 rounded-full">ضمان رسمي</span>
                </div>
                <strong class="text-xs sm:text-sm font-black text-white font-cairo block group-hover:text-rose-400">تأمين الشبكة وحماية العائلة</strong>
                <span class="text-[10px] text-slate-400 font-cairo block">عزل الفدية وتشفير البيانات الحساسة</span>
            </button>
        </div>
    </section>

    <!-- ===================================================================== -->
    <!-- 4. SOVEREIGN COMMAND ROWS (ONE UNIFIED COMMAND ROW PER SECTOR)        -->
    <!-- ===================================================================== -->
    <div class="space-y-8 lg:space-y-12">
        @foreach($services as $index => $service)
            @include('partials.sector-command-row', ['service' => $service, 'index' => $index])
        @endforeach
    </div>

    <!-- ===================================================================== -->
    <!-- 5. SOVEREIGN 0ms SECTOR ACTION POPUPS CONTAINER                       -->
    <!-- ===================================================================== -->
    @include('partials.sector-modals', ['services' => $services])

    @if(isset($erpCatalog) && $erpCatalog)
        @include('partials.branderp-store-modal')
    @endif
    @include('partials.branderp-roi-modal')

</div>

@push('scripts')
<script>
    function citadelHomepageController() {
        return {
            slides: @json($heroSlides ?? $services),
            activeSlide: 0,
            isAutoplay: false, // STRICTLY DISABLED BY DEFAULT TO PREVENT UNWANTED PAGE SCROLLING
            touchStartX: 0,
            touchEndX: 0,

            get currentSlideData() {
                return this.slides[this.activeSlide] || this.slides[0] || {};
            },

            init() {
                // Force all sector modals closed on startup
                this.activeSectorModal.open = false;
            },

            setSlide(idx) {
                this.activeSlide = idx;
            },

            nextSlide() {
                this.activeSlide = (this.activeSlide + 1) % this.slides.length;
            },

            prevSlide() {
                this.activeSlide = (this.activeSlide - 1 + this.slides.length) % this.slides.length;
            },

            handleTouchStart(e) {
                this.touchStartX = e.changedTouches ? e.changedTouches[0].clientX : e.clientX;
            },

            handleTouchEnd(e) {
                this.touchEndX = e.changedTouches ? e.changedTouches[0].clientX : e.clientX;
                const deltaX = this.touchEndX - this.touchStartX;
                if (Math.abs(deltaX) > 50) {
                    if (deltaX < 0) {
                        this.nextSlide();
                    } else {
                        this.prevSlide();
                    }
                }
            },

            activeSectorModal: {
                open: false,
                sectorSlug: 'brand-erp',
                activeTab: 'simulator',
            },

            sectorMetas: {
                'brand-erp': {
                    title: 'نظام BrandERP المتكامل',
                    icon: '🏪',
                    badge: 'النواة التشغيلية والسيادة الرقمية · يبدأ من 2,000 ج.م',
                },
                'ai-agents': {
                    title: 'موظفو الذكاء الاصطناعي AI Agents',
                    icon: '🤖',
                    badge: 'القوة العاملة الرقمية 24/7 · رد في 1.8 ثانية',
                },
                'cybersecurity': {
                    title: 'الأمن السيبراني والتدريع الرقمي',
                    icon: '🛡️',
                    badge: 'درع الحماية والسيادة الرقمية · ضمان معتمد',
                },
                'maintenance': {
                    title: 'خدمات الصيانة الهندسية الميدانية الفائقة',
                    icon: '🔧',
                    badge: 'هندسة معتمدة بمقرك · 400 ج.م/ساعة',
                },
                'business-consulting': {
                    title: 'استشارات البيزنس والتحول الرقمي',
                    icon: '📈',
                    badge: 'خارطة النمو ومضاعفة الأرباح · ربحية +45%',
                },
                'academy': {
                    title: 'أكاديمية سولي لكورسات الـ AI والهاكينج الأخلاقي',
                    icon: '🎓',
                    badge: 'مختبرات احترافية 100% عملي · شهادة QR معتمدة',
                },
            },

            get currentSectorMeta() {
                return this.sectorMetas[this.activeSectorModal.sectorSlug] || {
                    title: 'منظومة سولي السيادية',
                    icon: '⚡',
                    badge: 'خدمة معتمدة',
                };
            },

            openSectorModal(slug, tab = 'simulator') {
                this.activeSectorModal.sectorSlug = slug;
                this.activeSectorModal.activeTab = tab;
                this.activeSectorModal.open = true;
                if (window.SoulyUI) window.SoulyUI.lockScroll('sector-modal');
            },

            closeSectorModal() {
                this.activeSectorModal.open = false;
                if (window.SoulyUI) window.SoulyUI.unlockScroll('sector-modal');
            }
        };
    }
</script>
@endpush
@endsection