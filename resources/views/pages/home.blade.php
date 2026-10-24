@extends(request()->header('sec-ch-ua-mobile') === '?1' || request()->query('view') === 'mobile' ? 'layouts.mobile' : 'layouts.desktop')

@section('title', 'سولي ديدجيتال سيتادل | قلعة الحلول التكنولوجية والذكاء الاصطناعي')
@section('page-title', 'المنصة الرئيسية · منظومة الخدمات السيادية الست')

@section('content')
<div class="space-y-16 lg:space-y-24" x-data="citadelHomepageController()">

    <!-- ===================================================================== -->
    <!-- 1. FULL-SCREEN CINEMATIC HERO SLIDER (6 INTERACTIVE SERVICE SLIDES)  -->
    <!-- ===================================================================== -->
    <section class="relative min-h-[520px] lg:min-h-[620px] rounded-3xl glass-obsidian border border-obsidian-border overflow-hidden p-6 sm:p-10 lg:p-12 flex flex-col justify-between">
        
        <!-- Ambient Neon Glow Backdrop for Active Slide -->
        <div class="absolute inset-0 pointer-events-none transition-all duration-700">
            <div class="absolute -top-32 -left-32 w-96 h-96 rounded-full blur-[140px] opacity-25" 
                 :style="`background-color: ${currentSlideData.accent_color || '#00F5D4'}`"></div>
            <div class="absolute -bottom-32 -right-32 w-96 h-96 rounded-full blur-[140px] opacity-20" 
                 :style="`background-color: ${currentSlideData.secondary_color || '#7B2CBF'}`"></div>
            <div class="absolute inset-0 bg-[radial-gradient(#1c1f36_1px,transparent_1px)] [background-size:28px_28px] opacity-30"></div>
        </div>

        <!-- Slider Top Navigation / Service Selector Tabs -->
        <div class="relative z-10 flex items-center justify-between gap-4 border-b border-obsidian-border/70 pb-4 overflow-x-auto">
            <div class="flex items-center gap-2 shrink-0">
                <template x-for="(svc, idx) in slides" :key="svc.id">
                    <button @click="setSlide(idx)" 
                            type="button"
                            class="px-3.5 py-1.5 rounded-xl text-xs font-cairo font-bold transition-all duration-300 flex items-center gap-2 shrink-0"
                            :class="activeSlide === idx ? 
                                'bg-obsidian-surface text-white border border-cyber-cyan/50 shadow-neon-cyan' : 
                                'text-slate-400 hover:text-slate-200 hover:bg-obsidian-surface/60 border border-transparent'">
                        <span class="w-2 h-2 rounded-full" :style="`background-color: ${svc.accent_color}`"></span>
                        <span x-text="svc.name"></span>
                    </button>
                </template>
            </div>

            <!-- Auto-Slide Countdown Indicator -->
            <div class="hidden sm:flex items-center gap-2 shrink-0 text-xs font-mono text-slate-400">
                <span class="text-cyber-cyan" x-text="`0${activeSlide + 1} / 0${slides.length}`"></span>
                <button @click="toggleAutoplay()" class="p-1 rounded text-slate-400 hover:text-white" :title="isAutoplay ? 'إيقاف مؤقت' : 'تشغيل تلقائي'">
                    <span x-text="isAutoplay ? '⏸' : '▶'"></span>
                </button>
            </div>
        </div>

        <!-- Active Slide Dynamic Content Stage -->
        <div class="relative z-10 my-auto py-8 lg:py-10 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            
            <!-- Left (RTL Right): Main Copy & CTAs -->
            <div class="lg:col-span-7 space-y-5">
                <!-- Badge -->
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-mono font-bold border shadow-sm transition-all"
                     :style="`background-color: ${currentSlideData.accent_color}15; border-color: ${currentSlideData.accent_color}50; color: ${currentSlideData.accent_color}`">
                    <span class="w-2 h-2 rounded-full animate-ping" :style="`background-color: ${currentSlideData.accent_color}`"></span>
                    <span x-text="currentSlideData.hero_badge || 'منظومة سولي السيادية'"></span>
                </div>

                <!-- Slide Title -->
                <h1 class="text-2xl sm:text-4xl lg:text-5xl font-extrabold text-white font-cairo leading-tight" 
                    x-text="currentSlideData.name"></h1>

                <!-- Slide Tagline -->
                <p class="text-sm sm:text-base lg:text-lg text-slate-300 font-readex leading-relaxed" 
                   x-text="currentSlideData.tagline || currentSlideData.short_description"></p>

                <!-- Action Controls -->
                <div class="flex flex-wrap items-center gap-3 pt-2">
                    <a :href="`#service-${currentSlideData.slug}`" 
                       class="px-6 py-3 rounded-xl bg-gradient-to-r from-cyber-cyan via-cyber-gold to-cyber-purple text-obsidian-base font-extrabold text-xs sm:text-sm font-cairo shadow-neon-cyan hover:scale-[1.02] active:scale-95 transition-all flex items-center gap-2">
                        <span>استكشف الباقات والمزايا</span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </a>

                    <button @click="$dispatch('open-icon-popup', {
                                id: 'hero_' + currentSlideData.id,
                                title: currentSlideData.name,
                                badge_text: currentSlideData.hero_badge,
                                popup_title: currentSlideData.name,
                                popup_summary: currentSlideData.short_description,
                                popup_bullets: currentSlideData.hero_slides_json ? currentSlideData.hero_slides_json.map(s => s.title + ': ' + s.desc) : [],
                                service_name: 'سولي سيتادل',
                                popup_cta_text: 'احجز استشارة فورية الآن'
                            })"
                            class="px-5 py-3 rounded-xl bg-obsidian-surface hover:bg-obsidian-card border border-obsidian-border hover:border-cyber-cyan/40 text-slate-200 text-xs sm:text-sm font-cairo font-bold transition-all">
                        نظرة سريعة وتفاصيل
                    </button>
                </div>
            </div>

            <!-- Right (RTL Left): Quick Stats HUD Card -->
            <div class="lg:col-span-5 flex flex-col gap-3">
                <template x-for="(stat, sIdx) in (currentSlideData.stats_json || [])" :key="sIdx">
                    <div class="p-4 rounded-2xl bg-obsidian-surface/90 border border-obsidian-border/90 hover:border-cyber-cyan/30 flex items-center justify-between gap-4 transition-all group">
                        <div>
                            <span class="text-xs text-slate-400 font-mono block" x-text="stat.label"></span>
                            <span class="text-xs text-slate-500 font-cairo">مؤشر موثوق ومعتمد</span>
                        </div>
                        <span class="text-2xl sm:text-3xl font-extrabold font-mono text-transparent bg-clip-text bg-gradient-to-r from-cyber-cyan to-cyber-gold group-hover:scale-105 transition-transform" 
                              x-text="stat.value"></span>
                    </div>
                </template>

                <!-- Slide Feature Highlight Pills -->
                <div class="p-4 rounded-2xl bg-gradient-to-br from-obsidian-surface to-obsidian-card border border-obsidian-border space-y-2">
                    <div class="flex items-center justify-between text-xs font-mono text-slate-400">
                        <span>جاهزية المنظومة:</span>
                        <span class="text-cyber-cyan font-bold">100% بنية سحابية وميدانية</span>
                    </div>
                    <div class="w-full bg-obsidian-base rounded-full h-1.5 overflow-hidden">
                        <div class="h-1.5 rounded-full w-[96%]" 
                             :style="`background: linear-gradient(to right, ${currentSlideData.secondary_color}, ${currentSlideData.accent_color})`"></div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Slider Bottom Track & Arrows -->
        <div class="relative z-10 flex items-center justify-between gap-4 pt-4 border-t border-obsidian-border/70">
            <div class="flex items-center gap-1.5 flex-1 max-w-xs">
                <template x-for="(s, i) in slides" :key="i">
                    <div class="h-1.5 rounded-full transition-all duration-300" 
                         :class="activeSlide === i ? 'w-10 bg-cyber-cyan shadow-neon-cyan' : 'w-2 bg-obsidian-border'"></div>
                </template>
            </div>

            <div class="flex items-center gap-2">
                <button @click="prevSlide()" class="p-2.5 rounded-xl bg-obsidian-surface border border-obsidian-border hover:border-cyber-cyan text-slate-300 hover:text-white transition-all">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                <button @click="nextSlide()" class="p-2.5 rounded-xl bg-obsidian-surface border border-obsidian-border hover:border-cyber-cyan text-slate-300 hover:text-white transition-all">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
            </div>
        </div>

    </section>

    <!-- ===================================================================== -->
    <!-- 2. QUICK JUMP CITADEL DOCK (Sticky Navigation Between 6 Services)     -->
    <!-- ===================================================================== -->
    <div class="sticky top-16 z-30 py-2">
        <div class="glass-obsidian rounded-2xl border border-obsidian-border p-2 max-w-5xl mx-auto shadow-lg overflow-x-auto">
            <div class="flex items-center justify-center gap-2 min-w-max px-2">
                @foreach($services as $svc)
                    <a href="#service-{{ $svc->slug }}" 
                       class="px-3.5 py-1.5 rounded-xl text-xs font-cairo font-semibold text-slate-300 hover:text-white hover:bg-obsidian-surface/90 border border-transparent hover:border-cyber-cyan/30 transition-all flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full" style="background-color: {{ $svc->accent_color }};"></span>
                        <span>{{ $svc->name }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <!-- ===================================================================== -->
    <!-- 3. MULTI-ROW SECTIONS FOR EVERY SERVICE (ALL 6 SERVICES)              -->
    <!-- ===================================================================== -->
    @foreach($services as $index => $service)
        <section id="service-{{ $service->slug }}" 
                 class="space-y-8 scroll-mt-28 relative">

            <!-- Service Master Section Header -->
            <div class="relative overflow-hidden rounded-3xl glass-obsidian border border-obsidian-border p-6 sm:p-8">
                <div class="absolute -top-20 -right-20 w-64 h-64 rounded-full blur-3xl opacity-15" style="background-color: {{ $service->accent_color }};"></div>

                <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="space-y-2">
                        <div class="flex items-center gap-3">
                            <span class="font-mono text-xs px-2.5 py-0.5 rounded-md font-bold text-obsidian-base" style="background-color: {{ $service->accent_color }};">
                                0{{ $index + 1 }} · CITADEL SERVICE
                            </span>
                            <span class="text-xs font-mono text-slate-400 uppercase tracking-widest">{{ $service->slug }}</span>
                        </div>
                        <h2 class="text-2xl sm:text-3xl font-extrabold text-white font-cairo">
                            {{ $service->name }}
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-300 font-readex max-w-3xl leading-relaxed">
                            {{ $service->tagline ?? $service->short_description }}
                        </p>
                    </div>

                    <div class="shrink-0 flex items-center gap-2">
                        <span class="px-3 py-1 rounded-full text-xs font-mono font-bold bg-obsidian-surface border border-obsidian-border text-slate-300">
                            {{ $service->packages->count() }} باقات
                        </span>
                        <span class="px-3 py-1 rounded-full text-xs font-mono font-bold bg-obsidian-surface border border-obsidian-border text-slate-300">
                            {{ $service->icons->count() }} ميزة ونشاط
                        </span>
                    </div>
                </div>
            </div>

            <!-- ------------------------------------------------------------- -->
            <!-- ROW A: Service Story & Before vs. After Comparison            -->
            <!-- ------------------------------------------------------------- -->
            @php $beforeAfterRow = $service->rows->where('row_type', 'before_after')->first(); @endphp
            @if($beforeAfterRow && isset($beforeAfterRow->payload_json['before']))
                <div class="glass-obsidian rounded-3xl border border-obsidian-border p-6 sm:p-8 space-y-6">
                    <div class="text-center max-w-2xl mx-auto space-y-2">
                        <span class="text-[11px] font-mono uppercase tracking-wider text-cyber-gold font-bold">الفارق التشغيلي الحاسم</span>
                        <h3 class="text-xl sm:text-2xl font-bold text-white font-cairo">
                            {{ $beforeAfterRow->title }}
                        </h3>
                        @if($beforeAfterRow->subtitle)
                            <p class="text-xs sm:text-sm text-slate-400 font-readex">{{ $beforeAfterRow->subtitle }}</p>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                        <!-- Before Box (Red / Inefficient) -->
                        <div class="p-6 rounded-2xl bg-gradient-to-br from-rose-950/20 to-obsidian-surface border border-rose-900/40 space-y-4">
                            <div class="flex items-center gap-3 text-rose-400 border-b border-rose-900/30 pb-3">
                                <div class="w-8 h-8 rounded-lg bg-rose-500/10 border border-rose-500/30 flex items-center justify-center font-bold">✕</div>
                                <h4 class="font-cairo font-bold text-base text-rose-300">قبل تطبيق سولي (الواقع التقليدي المعطل)</h4>
                            </div>
                            <ul class="space-y-2.5">
                                @foreach($beforeAfterRow->payload_json['before'] as $point)
                                    <li class="flex items-start gap-2.5 text-xs sm:text-sm text-slate-300">
                                        <span class="text-rose-400 font-bold mt-0.5 shrink-0">✕</span>
                                        <span>{{ $point }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <!-- After Box (Cyan / Empowered) -->
                        <div class="p-6 rounded-2xl bg-gradient-to-br from-cyber-cyan/10 to-obsidian-surface border border-cyber-cyan/40 space-y-4 shadow-neon-cyan">
                            <div class="flex items-center gap-3 text-cyber-cyan border-b border-cyber-cyan/30 pb-3">
                                <div class="w-8 h-8 rounded-lg bg-cyber-cyan/10 border border-cyber-cyan/30 flex items-center justify-center font-bold">✓</div>
                                <h4 class="font-cairo font-bold text-base text-cyber-cyan">بعد تدريع سولي (السيادة والأتمتة السحابية)</h4>
                            </div>
                            <ul class="space-y-2.5">
                                @foreach($beforeAfterRow->payload_json['after'] as $point)
                                    <li class="flex items-start gap-2.5 text-xs sm:text-sm text-slate-200">
                                        <span class="text-cyber-cyan font-bold mt-0.5 shrink-0">✓</span>
                                        <span>{{ $point }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <!-- ------------------------------------------------------------- -->
            <!-- ROW B: Interactive Icon Matrix (Industries & Features)        -->
            <!-- ------------------------------------------------------------- -->
            <div class="space-y-6">
                <!-- Group 1: Industries (If available) -->
                @php $industryIcons = $service->icons->where('group_type', 'industry'); @endphp
                @if($industryIcons->isNotEmpty())
                    <div class="glass-obsidian rounded-3xl border border-obsidian-border p-6 sm:p-8 space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-obsidian-border/70 pb-3">
                            <div>
                                <h4 class="text-base sm:text-lg font-bold text-white font-cairo flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $service->accent_color }};"></span>
                                    <span>القطاعات والأنشطة المتوافقة (انقر لأي نشاط للتفاصيل)</span>
                                </h4>
                                <p class="text-xs text-slate-400 font-readex">يدعم نظام {{ $service->name }} أكثر من {{ $industryIcons->count() }} قطاع تجاري وصناعي متخصص.</p>
                            </div>
                            <span class="text-[11px] font-mono text-slate-400">{{ $industryIcons->count() }} قطاع متوفر</span>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                            @foreach($industryIcons as $icon)
                                <button type="button" 
                                        data-icon-popup
                                        data-icon-id="{{ $icon->id }}"
                                        data-icon-title="{{ $icon->title }}"
                                        data-icon-badge="{{ $icon->badge_text }}"
                                        data-icon-summary="{{ $icon->popup_summary }}"
                                        data-icon-bullets="{{ json_encode($icon->popup_bullets_json) }}"
                                        data-service-name="{{ $service->name }}"
                                        data-cta-text="{{ $icon->popup_cta_text }}"
                                        class="p-3.5 rounded-2xl bg-obsidian-surface hover:bg-obsidian-card border border-obsidian-border hover:border-cyber-cyan/50 text-right space-y-2 group active:scale-95 transition-all">
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
                    </div>
                @endif

                <!-- Group 2: Features, Threats, Skills (Non-Industry Icons) -->
                @php $featureIcons = $service->icons->where('group_type', '!=', 'industry'); @endphp
                @if($featureIcons->isNotEmpty())
                    <div class="glass-obsidian rounded-3xl border border-obsidian-border p-6 sm:p-8 space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-obsidian-border/70 pb-3">
                            <div>
                                <h4 class="text-base sm:text-lg font-bold text-white font-cairo flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-cyber-gold"></span>
                                    <span>المزايا والقدرات التشغيلية المتقدمة (0ms Instant Popup)</span>
                                </h4>
                                <p class="text-xs text-slate-400 font-readex">انقر على أي ميزة لاستعراض المواصفات التقنية والفوائد الفورية.</p>
                            </div>
                            <span class="text-[11px] font-mono text-slate-400">{{ $featureIcons->count() }} ميزة هندسية</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3.5">
                            @foreach($featureIcons as $feat)
                                <button type="button" 
                                        data-icon-popup
                                        data-icon-id="{{ $feat->id }}"
                                        data-icon-title="{{ $feat->title }}"
                                        data-icon-badge="{{ $feat->badge_text }}"
                                        data-icon-summary="{{ $feat->popup_summary }}"
                                        data-icon-bullets="{{ json_encode($feat->popup_bullets_json) }}"
                                        data-service-name="{{ $service->name }}"
                                        data-cta-text="{{ $feat->popup_cta_text }}"
                                        class="p-4 rounded-2xl bg-obsidian-surface hover:bg-obsidian-card border border-obsidian-border hover:border-cyber-gold/50 text-right space-y-2 group active:scale-95 transition-all flex flex-col justify-between">
                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between">
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-cyber-gold/15 text-cyber-gold border border-cyber-gold/40 shadow-[0_0_8px_rgba(255,184,0,0.2)]">
                                                {{ $feat->badge_text ?? 'ميزة تقنية' }}
                                            </span>
                                            <svg class="w-4 h-4 text-slate-500 group-hover:text-cyber-gold transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                            </svg>
                                        </div>
                                        <h5 class="text-xs sm:text-sm font-bold text-slate-100 group-hover:text-white font-cairo leading-snug">
                                            {{ $feat->title }}
                                        </h5>
                                        <p class="text-[11px] text-slate-400 font-readex line-clamp-2 leading-relaxed">
                                            {{ $feat->popup_summary }}
                                        </p>
                                    </div>
                                    <span class="text-[10px] font-cairo text-cyber-cyan font-semibold block pt-1">
                                        استعراض التفاصيل الكاملة ⇥
                                    </span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- ------------------------------------------------------------- -->
            <!-- ROW C: Interactive Packages & Pricing Showcase                -->
            <!-- ------------------------------------------------------------- -->
            <div class="space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <h4 class="text-xl sm:text-2xl font-extrabold text-white font-cairo flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-cyber-purple"></span>
                            <span>باقات واشتراكات {{ $service->name }}</span>
                        </h4>
                        <p class="text-xs sm:text-sm text-slate-400 font-readex">اختر الباقة الأنسب لنمو مشروعك واحجز فوراً برصيد محفظتك الرقمية أو الدفع المباشر.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($service->packages as $pkg)
                        <div class="relative rounded-3xl glass-obsidian border p-6 sm:p-7 flex flex-col justify-between transition-all duration-300 {{ $pkg->is_featured ? 'border-cyber-cyan/60 shadow-neon-cyan bg-obsidian-surface/90' : 'border-obsidian-border hover:border-obsidian-border/90' }}">
                            
                            <!-- Featured Badge -->
                            @if($pkg->is_featured)
                                <div class="absolute -top-3 left-6">
                                    <span class="px-3 py-1 rounded-full text-[10px] font-mono font-extrabold bg-gradient-to-r from-cyber-cyan to-cyber-purple text-obsidian-base shadow-md uppercase">
                                        {{ $pkg->badge_text ?? 'الأكثر طلباً' }}
                                    </span>
                                </div>
                            @endif

                            <div class="space-y-5">
                                <!-- Package Header -->
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
                                    <h5 class="text-lg sm:text-xl font-extrabold text-white font-cairo leading-snug">
                                        {{ $pkg->name }}
                                    </h5>
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
                                        <span class="text-2xl sm:text-3xl font-extrabold font-mono text-transparent bg-clip-text bg-gradient-to-r from-cyber-cyan to-cyber-gold">
                                            {{ number_format($pkg->price, 0) }}
                                        </span>
                                        <span class="text-xs font-mono text-slate-300">EGP</span>
                                        @if($pkg->unit_label)
                                            <span class="text-xs text-slate-400 font-cairo">/ {{ $pkg->unit_label }}</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Features List -->
                                <div class="space-y-2.5">
                                    <span class="text-[11px] font-mono text-slate-400 font-bold block uppercase">المحتويات والمزايا المضمنة:</span>
                                    <ul class="space-y-2">
                                        @foreach($pkg->features_json as $feature)
                                            <li class="flex items-start gap-2.5 text-xs text-slate-300">
                                                <span class="w-1.5 h-1.5 rounded-full bg-cyber-cyan mt-1.5 shrink-0"></span>
                                                <span class="leading-relaxed">{{ $feature }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>

                            <!-- CTA Button -->
                            <div class="pt-6 border-t border-obsidian-border/80 mt-6">
                                <a href="{{ url('/portal#booking') }}?service={{ $service->slug }}&package={{ $pkg->slug }}" 
                                   class="w-full py-3 rounded-xl font-cairo font-bold text-xs sm:text-sm text-center block transition-all active:scale-95 shadow-md {{ $pkg->is_featured ? 'bg-gradient-to-r from-cyber-cyan via-cyber-gold to-cyber-purple text-obsidian-base hover:opacity-95' : 'bg-obsidian-surface hover:bg-obsidian-card text-white border border-obsidian-border hover:border-cyber-cyan/40' }}">
                                    احجز هذه الباقة الآن
                                </a>
                            </div>

                        </div>
                    @endforeach
                </div>
            </div>

        </section>
    @endforeach

</div>

@push('scripts')
<script>
    function citadelHomepageController() {
        return {
            slides: @json($services),
            activeSlide: 0,
            isAutoplay: true,
            autoplayInterval: null,

            get currentSlideData() {
                return this.slides[this.activeSlide] || this.slides[0] || {};
            },

            init() {
                this.startAutoplay();
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

            startAutoplay() {
                if (this.autoplayInterval) clearInterval(this.autoplayInterval);
                this.autoplayInterval = setInterval(() => {
                    if (this.isAutoplay) {
                        this.nextSlide();
                    }
                }, 6000);
            },

            toggleAutoplay() {
                this.isAutoplay = !this.isAutoplay;
            }
        };
    }
</script>
@endpush
@endsection
