{{--
    Souly Sovereign Sector 0ms Action Popups Container Engine
    Instantaneous (0ms) interactive modal system with 5 unified tabs:
    1. Simulator / ROI Calculator
    2. Traditional vs. Souly Comparison
    3. Compatible Industries & Sectors
    4. Advanced Capabilities & Technical Features
    5. Packages Showcase & Modular Store
--}}

<div id="souly-sector-modal-container"
     x-show="activeSectorModal.open"
     x-cloak
     @click="closeSectorModal()"
     @keydown.escape.window="closeSectorModal()"
     class="fixed inset-0 z-[99990] bg-black/85 backdrop-blur-md flex items-end md:items-center justify-center max-md:p-0 p-3 sm:p-5 overflow-y-auto mobile-bottom-sheet-modal"
     dir="rtl">

    <!-- Modal Outer Dialog: Bottom Sheet on Mobile / Centered on Desktop -->
    <div class="relative w-full max-w-6xl glass-obsidian max-md:rounded-t-3xl max-md:rounded-b-none rounded-3xl border max-md:border-b-0 border-cyber-cyan/40 shadow-[0_10px_60px_rgba(0,0,0,0.9)] overflow-hidden max-h-[90dvh] md:max-h-[92vh] flex flex-col my-0 md:my-auto mobile-bottom-sheet-panel safe-bottom"
         @click.stop
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-full md:translate-y-0 md:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 md:scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 md:scale-100"
         x-transition:leave-end="opacity-0 translate-y-full md:translate-y-0 md:scale-95">

        <!-- Mobile Drag / Grip Handle -->
        <div class="w-12 h-1.5 bg-slate-600 rounded-full mx-auto my-2 md:hidden"></div>

        <!-- ═══════════════ MODAL STICKY HEADER & 5-TAB DOCK ═══════════════ -->
        <div class="sticky top-0 z-30 bg-obsidian-base/95 backdrop-blur-xl border-b border-obsidian-border p-4 sm:p-5 flex flex-col md:flex-row md:items-center justify-between gap-4 shrink-0">
            
            <!-- Sector Identification -->
            <div class="flex items-center gap-3">
                <span class="text-3xl p-2 rounded-2xl bg-obsidian-surface border border-obsidian-border shrink-0 shadow-sm"
                      x-text="currentSectorMeta.icon"></span>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h3 class="text-base sm:text-lg font-black text-white font-cairo leading-tight"
                            x-text="currentSectorMeta.title"></h3>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-cairo font-bold bg-cyber-cyan/15 text-cyber-cyan border border-cyber-cyan/30"
                              x-text="currentSectorMeta.badge"></span>
                    </div>
                    <p class="text-xs text-slate-400 font-cairo mt-0.5 hidden sm:block">
                        منظومة سولي السيادية المتطورة · استعراض القدرات التشغيلية الفورية 0ms
                    </p>
                </div>
            </div>

            <!-- 5 Dynamic Navigation Tabs & Close Button -->
            <div class="flex items-center gap-2 justify-between md:justify-end flex-wrap">
                <div class="flex items-center gap-1 bg-obsidian-surface/90 p-1 rounded-2xl border border-obsidian-border overflow-x-auto scrollbar-none">
                    <!-- Tab 1 -->
                    <button type="button"
                            @click="activeSectorModal.activeTab = 'simulator'"
                            :class="activeSectorModal.activeTab === 'simulator' ? 'bg-cyber-cyan text-obsidian-base font-black shadow-sm' : 'text-slate-400 hover:text-white'"
                            class="px-3 py-1.5 rounded-xl text-xs font-cairo font-bold transition-all whitespace-nowrap flex items-center gap-1 cursor-pointer">
                        <span>🎛️</span>
                        <span>المحاكي وحاسبة التوفير</span>
                    </button>

                    <!-- Tab 2 -->
                    <button type="button"
                            @click="activeSectorModal.activeTab = 'comparison'"
                            :class="activeSectorModal.activeTab === 'comparison' ? 'bg-cyber-cyan text-obsidian-base font-black shadow-sm' : 'text-slate-400 hover:text-white'"
                            class="px-3 py-1.5 rounded-xl text-xs font-cairo font-bold transition-all whitespace-nowrap flex items-center gap-1 cursor-pointer">
                        <span>⚔️</span>
                        <span>التقليدي مقابل سولي</span>
                    </button>

                    <!-- Tab 3 -->
                    <button type="button"
                            @click="activeSectorModal.activeTab = 'industries'"
                            :class="activeSectorModal.activeTab === 'industries' ? 'bg-cyber-cyan text-obsidian-base font-black shadow-sm' : 'text-slate-400 hover:text-white'"
                            class="px-3 py-1.5 rounded-xl text-xs font-cairo font-bold transition-all whitespace-nowrap flex items-center gap-1 cursor-pointer">
                        <span>🏢</span>
                        <span>القطاعات المتوافقة</span>
                    </button>

                    <!-- Tab 4 -->
                    <button type="button"
                            @click="activeSectorModal.activeTab = 'features'"
                            :class="activeSectorModal.activeTab === 'features' ? 'bg-cyber-cyan text-obsidian-base font-black shadow-sm' : 'text-slate-400 hover:text-white'"
                            class="px-3 py-1.5 rounded-xl text-xs font-cairo font-bold transition-all whitespace-nowrap flex items-center gap-1 cursor-pointer">
                        <span>⚡</span>
                        <span>المزايا والقدرات</span>
                    </button>

                    <!-- Tab 5 -->
                    <button type="button"
                            @click="activeSectorModal.activeTab = 'packages'"
                            :class="activeSectorModal.activeTab === 'packages' ? 'bg-cyber-gold text-obsidian-base font-black shadow-sm' : 'text-slate-400 hover:text-white'"
                            class="px-3 py-1.5 rounded-xl text-xs font-cairo font-bold transition-all whitespace-nowrap flex items-center gap-1 cursor-pointer">
                        <span>💎</span>
                        <span>الباقات والأسعار</span>
                    </button>
                </div>

                <!-- Close Button (✕) -->
                <button type="button"
                        @click="closeSectorModal()"
                        class="w-9 h-9 rounded-xl bg-obsidian-surface border border-obsidian-border hover:border-rose-500/50 text-slate-400 hover:text-rose-400 flex items-center justify-center transition-all shrink-0 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

        </div>

        <!-- ═══════════════ MODAL SCROLLABLE BODY ═══════════════ -->
        <div class="overflow-y-auto p-5 sm:p-7 space-y-6 flex-1">
            @foreach($services as $service)
                <div x-show="activeSectorModal.sectorSlug === '{{ $service->slug }}'" x-cloak class="space-y-6">

                    <!-- ────────────────── TAB 1: INTERACTIVE SIMULATOR / ROI ────────────────── -->
                    <div x-show="activeSectorModal.activeTab === 'simulator'" x-cloak class="space-y-4">
                        @if($service->slug === 'brand-erp')
                            @include('partials.simulators.brand-erp-simulator')
                        @elseif($service->slug === 'ai-agents')
                            @include('partials.simulators.ai-agents-simulator')
                        @elseif($service->slug === 'cybersecurity')
                            @include('partials.simulators.cybersecurity-scanner')
                        @elseif($service->slug === 'maintenance')
                            @include('partials.simulators.maintenance-benchmark')
                        @elseif($service->slug === 'business-consulting')
                            @include('partials.simulators.business-consulting-diagnostic')
                        @elseif($service->slug === 'academy')
                            @include('partials.simulators.academy-curriculum-explorer')
                        @endif
                    </div>

                    <!-- ────────────────── TAB 2: TRADITIONAL VS SOULY COMPARISON ────────────────── -->
                    <div x-show="activeSectorModal.activeTab === 'comparison'" x-cloak class="space-y-6">
                        @php $beforeAfterRow = $service->rows->where('row_type', 'before_after')->first(); @endphp
                        @if($beforeAfterRow && isset($beforeAfterRow->payload_json['before']))
                            <div class="glass-obsidian rounded-3xl border border-obsidian-border p-6 sm:p-8 space-y-6 shadow-lg">
                                <div class="text-center max-w-2xl mx-auto space-y-2">
                                    <span class="text-xs font-cairo text-cyber-gold font-bold">الفارق التشغيلي الحاسم</span>
                                    <h3 class="text-xl sm:text-2xl font-bold text-white font-cairo">
                                        {{ $beforeAfterRow->title }}
                                    </h3>
                                    @if($beforeAfterRow->subtitle)
                                        <p class="text-xs sm:text-sm text-slate-400 font-cairo">{{ $beforeAfterRow->subtitle }}</p>
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
                                                <li class="flex items-start gap-2.5 text-xs sm:text-sm text-slate-300 font-cairo">
                                                    <span class="text-rose-400 font-bold mt-0.5 shrink-0">✕</span>
                                                    <span>{{ $point }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>

                                    <!-- After Box (Cyan / Empowered) -->
                                    <div class="p-6 rounded-2xl bg-gradient-to-br from-cyber-cyan/10 to-obsidian-surface border border-cyber-cyan/40 space-y-4 shadow-[0_4px_24px_rgba(0,245,212,0.12)]">
                                        <div class="flex items-center gap-3 text-cyber-cyan border-b border-cyber-cyan/30 pb-3">
                                            <div class="w-8 h-8 rounded-lg bg-cyber-cyan/10 border border-cyber-cyan/30 flex items-center justify-center font-bold">✓</div>
                                            <h4 class="font-cairo font-bold text-base text-cyber-cyan">بعد تدريع سولي (السيادة والأتمتة السحابية)</h4>
                                        </div>
                                        <ul class="space-y-2.5">
                                            @foreach($beforeAfterRow->payload_json['after'] as $point)
                                                <li class="flex items-start gap-2.5 text-xs sm:text-sm text-slate-200 font-cairo">
                                                    <span class="text-cyber-cyan font-bold mt-0.5 shrink-0">✓</span>
                                                    <span>{{ $point }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="p-8 text-center text-slate-400 font-cairo text-sm">
                                لا توجد مقارنة متاحة حالياً لهذا القطاع.
                            </div>
                        @endif
                    </div>

                    <!-- ────────────────── TAB 3: COMPATIBLE INDUSTRIES ────────────────── -->
                    <div x-show="activeSectorModal.activeTab === 'industries'" x-cloak class="space-y-6">
                        @php $industryIcons = $service->icons->where('group_type', 'industry'); @endphp
                        
                        <div class="glass-obsidian rounded-2xl border border-obsidian-border/80 p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-sm">
                            <div class="space-y-1 text-center sm:text-right">
                                <h4 class="text-base sm:text-lg font-black text-white font-cairo flex items-center justify-center sm:justify-start gap-2.5">
                                    <span class="w-2.5 h-2.5 rounded-full bg-cyber-cyan shrink-0"></span>
                                    <span>القطاعات والأنشطة المتوافقة (انقر لأي نشاط للتفاصيل)</span>
                                </h4>
                                <p class="text-xs text-slate-400 font-cairo">يدعم نظام {{ $service->name }} أكثر من {{ $industryIcons->count() ?: 8 }} قطاع تجاري وصناعي متخصص.</p>
                            </div>
                            <span class="text-xs font-cairo text-cyber-cyan font-bold bg-cyber-cyan/15 px-3.5 py-1.5 rounded-full border border-cyber-cyan/30 self-center sm:self-auto shrink-0 shadow-sm">
                                <span class="font-mono">{{ $industryIcons->count() ?: 8 }}</span> قطاع معتمد
                            </span>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 gap-3.5">
                            @foreach($industryIcons as $icon)
                                <button type="button" 
                                        @click="$dispatch('open-icon-popup', {
                                            id: '{{ $icon->id }}',
                                            title: '{{ addslashes($icon->title) }}',
                                            badge_text: '{{ addslashes($icon->badge_text ?? 'جاهز') }}',
                                            popup_title: '{{ addslashes($icon->title) }}',
                                            popup_summary: '{{ addslashes($icon->popup_summary) }}',
                                            popup_bullets: {{ json_encode($icon->popup_bullets_json) }},
                                            service_name: '{{ addslashes($service->name) }}',
                                            popup_cta_text: '{{ addslashes($icon->popup_cta_text ?? 'احجز الآن') }}'
                                        })"
                                        class="p-4 rounded-2xl bg-obsidian-surface/90 hover:bg-obsidian-card border border-obsidian-border hover:border-cyber-cyan/50 text-right flex flex-col justify-between gap-3 group active:scale-95 transition-all w-full shadow-sm cursor-pointer">
                                    <div class="flex items-center justify-between w-full gap-2">
                                        <div class="w-8 h-8 rounded-xl bg-obsidian-card border border-cyber-cyan/30 flex items-center justify-center text-cyber-cyan group-hover:scale-110 transition-transform shadow-sm shrink-0">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                            </svg>
                                        </div>
                                        <span class="text-[10px] font-cairo font-bold px-2 py-0.5 rounded-full bg-cyber-cyan/15 text-cyber-cyan border border-cyber-cyan/30 shadow-sm shrink-0">
                                            {{ $icon->badge_text ?? 'جاهز' }}
                                        </span>
                                    </div>
                                    <span class="text-xs font-bold text-slate-200 group-hover:text-white font-cairo block leading-snug w-full">
                                        {{ $icon->title }}
                                    </span>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- ────────────────── TAB 4: ADVANCED CAPABILITIES ────────────────── -->
                    <div x-show="activeSectorModal.activeTab === 'features'" x-cloak class="space-y-6">
                        @php $featureIcons = $service->icons->where('group_type', '!=', 'industry'); @endphp

                        <div class="glass-obsidian rounded-2xl border border-obsidian-border/80 p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-sm">
                            <div class="space-y-1 text-center sm:text-right">
                                <h4 class="text-base sm:text-lg font-black text-white font-cairo flex items-center justify-center sm:justify-start gap-2.5">
                                    <span class="w-2.5 h-2.5 rounded-full bg-cyber-gold shrink-0"></span>
                                    <span>المزايا والقدرات التشغيلية المتقدمة (0ms Instant Popup)</span>
                                </h4>
                                <p class="text-xs text-slate-400 font-cairo">انقر على أي ميزة لاستعراض المواصفات التقنية والفوائد الفورية في نافذة التفاصيل.</p>
                            </div>
                            <span class="text-xs font-cairo text-cyber-gold font-bold bg-cyber-gold/15 px-3.5 py-1.5 rounded-full border border-cyber-gold/30 self-center sm:self-auto shrink-0 shadow-sm">
                                <span class="font-mono">{{ $featureIcons->count() }}</span> ميزة هندسية
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3.5">
                            @foreach($featureIcons as $feat)
                                <button type="button" 
                                        @click="$dispatch('open-icon-popup', {
                                            id: '{{ $feat->id }}',
                                            title: '{{ addslashes($feat->title) }}',
                                            badge_text: '{{ addslashes($feat->badge_text ?? 'ميزة تقنية') }}',
                                            popup_title: '{{ addslashes($feat->title) }}',
                                            popup_summary: '{{ addslashes($feat->popup_summary) }}',
                                            popup_bullets: {{ json_encode($feat->popup_bullets_json) }},
                                            service_name: '{{ addslashes($service->name) }}',
                                            popup_cta_text: '{{ addslashes($feat->popup_cta_text ?? 'استكشف التفاصيل') }}'
                                        })"
                                        class="p-4 rounded-2xl bg-obsidian-surface/90 hover:bg-obsidian-card border border-obsidian-border hover:border-cyber-gold/50 text-right flex flex-col justify-between gap-3 group active:scale-95 transition-all w-full shadow-sm cursor-pointer">
                                    <div class="space-y-2 w-full">
                                        <div class="flex items-center justify-between w-full">
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-cairo font-bold bg-cyber-gold/15 text-cyber-gold border border-cyber-gold/40 shadow-sm">
                                                {{ $feat->badge_text ?? 'ميزة تقنية' }}
                                            </span>
                                            <svg class="w-4 h-4 text-slate-500 group-hover:text-cyber-gold transition-colors shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                            </svg>
                                        </div>
                                        <h5 class="text-xs sm:text-sm font-bold text-slate-100 group-hover:text-white font-cairo leading-snug w-full">
                                            {{ $feat->title }}
                                        </h5>
                                        <p class="text-[11px] text-slate-400 font-cairo line-clamp-2 leading-relaxed w-full">
                                            {{ $feat->popup_summary }}
                                        </p>
                                    </div>
                                    <span class="text-[10px] font-cairo text-cyber-cyan font-semibold block pt-1 w-full">
                                        استعراض المواصفات ⇥
                                    </span>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- ────────────────── TAB 5: PACKAGES & MODULAR STORE ────────────────── -->
                    <div x-show="activeSectorModal.activeTab === 'packages'" x-cloak class="space-y-6">
                        
                        <!-- Header Banner -->
                        <div class="glass-obsidian rounded-2xl border border-obsidian-border/80 p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-sm">
                            <div class="space-y-1 text-center sm:text-right">
                                <h4 class="text-lg sm:text-xl font-black text-white font-cairo flex items-center justify-center sm:justify-start gap-2.5">
                                    <span class="w-3 h-3 rounded-full bg-cyber-purple shrink-0"></span>
                                    <span>باقات واشتراكات {{ $service->name }}</span>
                                </h4>
                                <p class="text-xs text-slate-400 font-cairo">اختر الباقة الأنسب لنمو مشروعك واحجز فوراً برصيد محفظتك الرقمية أو الدفع المباشر.</p>
                            </div>
                            <span class="text-xs font-cairo text-purple-300 font-bold bg-cyber-purple/20 px-3.5 py-1.5 rounded-full border border-cyber-purple/40 self-center sm:self-auto shrink-0 shadow-sm">
                                <span class="font-mono">{{ $service->packages->count() }}</span> باقات معتمدة
                            </span>
                        </div>

                        <!-- BrandERP Special Modular Store Launch Banner -->
                        @if($service->slug === 'brand-erp')
                            <div class="rounded-2xl bg-gradient-to-l from-cyber-cyan/15 via-obsidian-card to-obsidian-surface border border-cyber-cyan/40 p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-lg">
                                <div class="flex items-center gap-3">
                                    <span class="text-3xl">🏪</span>
                                    <div>
                                        <h4 class="text-sm sm:text-base font-black text-white font-cairo">متجر BrandERP التفاعلي — ابنِ نظامك المخصص</h4>
                                        <p class="text-xs text-slate-300 font-cairo mt-0.5">النواة الأساسية (2,000 ج.م) + 12 موديولاً كبيراً + 64 ميزة دقيقة من اختيارك التام</p>
                                    </div>
                                </div>
                                <button type="button"
                                        @click="closeSectorModal(); $dispatch('branderp-store-open')"
                                        class="px-5 py-2.5 rounded-xl btn-citadel-primary text-xs font-cairo font-black shrink-0 flex items-center gap-2 shadow-neon-cyan active:scale-95 cursor-pointer">
                                    <span>افتح متجر BrandERP التفاعلي</span>
                                    <span>⇥</span>
                                </button>
                            </div>
                        @endif

                        <!-- Packages Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                            @foreach($service->packages as $pkg)
                                <div class="relative rounded-3xl glass-obsidian border p-5 sm:p-6 flex flex-col justify-between transition-all duration-300 w-full text-right items-start {{ $pkg->is_featured ? 'border-cyber-cyan/70 shadow-[0_4px_24px_rgba(0,245,212,0.12)] bg-obsidian-card/90' : 'border-obsidian-border hover:border-obsidian-border/90' }}">
                                    
                                    @if($pkg->is_featured)
                                        <div class="absolute -top-3 left-6">
                                            <span class="px-3 py-1 rounded-full text-[10px] font-cairo font-black btn-citadel-primary shadow-md">
                                                {{ $pkg->badge_text ?? 'الأكثر طلباً' }}
                                            </span>
                                        </div>
                                    @endif

                                    <div class="space-y-4 w-full">
                                        <div class="space-y-1.5 w-full">
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
                                            <h5 class="text-base sm:text-lg font-extrabold text-white font-cairo leading-snug">
                                                {{ $pkg->name }}
                                            </h5>
                                            @if($pkg->subtitle)
                                                <p class="text-xs text-slate-400 font-cairo leading-relaxed">{{ $pkg->subtitle }}</p>
                                            @endif
                                        </div>

                                        <!-- Pricing Block -->
                                        <div class="p-3.5 rounded-2xl bg-obsidian-surface/90 border border-obsidian-border space-y-1 w-full text-right">
                                            @if($pkg->original_price && $pkg->original_price > $pkg->price)
                                                <div class="flex items-center gap-2">
                                                    <span class="text-xs font-mono text-slate-500 line-through">{{ number_format($pkg->original_price, 0) }} EGP</span>
                                                    <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-rose-500/10 text-rose-400 border border-rose-500/30">
                                                        وفر {{ number_format($pkg->original_price - $pkg->price, 0) }} EGP
                                                    </span>
                                                </div>
                                            @endif

                                            <div class="flex items-baseline gap-2 w-full">
                                                <span class="text-2xl font-extrabold font-mono text-transparent bg-clip-text bg-gradient-to-r from-cyber-cyan to-cyber-gold">
                                                    {{ number_format($pkg->price, 0) }}
                                                </span>
                                                <span class="text-xs font-mono text-slate-300">EGP</span>
                                                @if($pkg->unit_label)
                                                    <span class="text-xs text-slate-400 font-cairo">/ {{ $pkg->unit_label }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Features Bullets -->
                                        <div class="space-y-2 w-full">
                                            <span class="text-xs font-cairo text-slate-400 font-bold block">المحتويات والمزايا المضمنة:</span>
                                            <ul class="space-y-1.5 w-full">
                                                @foreach(array_slice($pkg->features_json ?? [], 0, 4) as $feature)
                                                    <li class="flex items-start gap-2 text-xs text-slate-300 font-cairo">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-cyber-cyan mt-1.5 shrink-0"></span>
                                                        <span class="leading-relaxed">{{ $feature }}</span>
                                                    </li>
                                                @endforeach
                                                @if(count($pkg->features_json ?? []) > 4)
                                                    <li class="text-[11px] text-cyber-cyan font-cairo font-bold pt-0.5">
                                                        + {{ count($pkg->features_json) - 4 }} مزايا وضمانات إضافية...
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>

                                    <!-- CTA Booking Button -->
                                    <div class="pt-5 border-t border-obsidian-border/80 mt-5 w-full">
                                        @auth
                                            <a href="{{ route('portal.bookings') }}?service={{ $service->slug }}&package={{ $pkg->slug }}" 
                                               class="w-full py-2.5 rounded-xl font-cairo font-bold text-xs text-center block transition-all active:scale-95 shadow-md {{ $pkg->is_featured ? 'btn-citadel-primary' : 'btn-citadel-secondary' }}">
                                                احجز هذه الباقة الآن ⚡
                                            </a>
                                        @else
                                            <a href="{{ route('portal.bookings') }}?service={{ $service->slug }}&package={{ $pkg->slug }}" 
                                               @click.prevent="closeSectorModal(); $dispatch('open-express-booking', {
                                                   serviceId: {{ $service->id }},
                                                   serviceSlug: '{{ $service->slug }}',
                                                   serviceName: '{{ addslashes($service->name) }}',
                                                   packageId: {{ $pkg->id }},
                                                   packageSlug: '{{ $pkg->slug }}',
                                                   packageName: '{{ addslashes($pkg->name) }}',
                                                   price: {{ (float) $pkg->price }}
                                               })"
                                               class="w-full py-2.5 rounded-xl font-cairo font-bold text-xs text-center block transition-all active:scale-95 shadow-md {{ $pkg->is_featured ? 'btn-citadel-primary' : 'btn-citadel-secondary' }}">
                                                احجز هذه الباقة الآن ⚡
                                            </a>
                                        @endauth
                                    </div>

                                </div>
                            @endforeach
                        </div>

                    </div>

                </div>
            @endforeach
        </div>

        <!-- ═══════════════ MODAL STICKY BOTTOM ACTION DOCK ═══════════════ -->
        <div class="sticky bottom-0 z-30 bg-obsidian-base/95 backdrop-blur-xl border-t border-obsidian-border px-5 py-3 flex items-center justify-between text-xs font-cairo shrink-0">
            <div class="flex items-center gap-2 text-slate-400">
                <kbd class="px-2 py-0.5 rounded bg-obsidian-surface border border-obsidian-border text-[10px] font-mono text-cyber-cyan">ESC</kbd>
                <span>للإغلاق السريع أو انقر خارج الإطار</span>
            </div>
            <div class="flex items-center gap-3">
                <a :href="'/services/' + activeSectorModal.sectorSlug" 
                   class="text-cyber-cyan hover:underline font-bold flex items-center gap-1">
                    <span>فتح الصفحة المخصصة للقطاع</span>
                    <span>←</span>
                </a>
                <button type="button" @click="closeSectorModal()" 
                        class="px-3.5 py-1.5 rounded-xl bg-obsidian-surface hover:bg-slate-800 border border-obsidian-border text-slate-300 hover:text-white transition-colors cursor-pointer">
                    إغلاق
                </button>
            </div>
        </div>

    </div>

</div>