@extends(request()->header('sec-ch-ua-mobile') === '?1' || request()->query('view') === 'mobile' ? 'layouts.mobile' : 'layouts.desktop')

@section('title', 'متجر BrandERP الموديلار الشامل (+500 ميزة و16 موديولاً) | سولي')
@section('page-title', 'متجر BrandERP الموديلار المعتمد')

@section('content')
@php
    $catalogData = [
        'core'       => $coreItem ? [
            'id'             => $coreItem->id,
            'code'           => $coreItem->code,
            'slug'           => $coreItem->slug,
            'name'           => $coreItem->name_ar,
            'price'          => (float) $coreItem->price,
            'desc'           => $coreItem->short_desc_ar,
            'roi'            => $coreItem->roi_time_saved_ar,
        ] : null,
        'modules'    => $modules->map(fn($m) => [
            'id'             => $m->id,
            'code'           => $m->code,
            'slug'           => $m->slug,
            'name'           => $m->name_ar,
            'price'          => (float) $m->price,
            'badge'          => $m->badge,
            'desc'           => $m->short_desc_ar,
            'full_details'   => $m->full_details_ar,
            'roi'            => $m->roi_time_saved_ar,
            'icon'           => $m->icon_name ?? '📦',
            'industries'     => $m->compatible_industries ?? [],
            'activations'    => $m->activations_count,
            'is_sales_pick'  => (bool) $m->is_sales_engineer_pick,
        ])->values()->toArray(),
        'features'   => $features->map(fn($f) => [
            'id'             => $f->id,
            'code'           => $f->code,
            'slug'           => $f->slug,
            'name'           => $f->name_ar,
            'price'          => (float) $f->price,
            'original_price' => $f->original_price ? (float) $f->original_price : null,
            'badge'          => $f->badge,
            'department'     => $f->department_ar,
            'desc'           => $f->short_desc_ar,
            'full_details'   => $f->full_details_ar,
            'roi'            => $f->roi_time_saved_ar,
            'icon'           => $f->icon_name ?? '⚡',
            'industries'     => $f->compatible_industries ?? [],
            'activations'    => $f->activations_count,
            'is_flash'       => (bool) $f->is_flash_deal,
            'is_sales_pick'  => (bool) $f->is_sales_engineer_pick,
            'hardware_slug'  => $f->related_hardware_slug,
        ])->values()->toArray(),
        'bundles'    => $bundles->map(fn($b) => [
            'id'             => $b->id,
            'code'           => $b->code,
            'slug'           => $b->slug,
            'name'           => $b->name_ar,
            'price'          => (float) $b->price,
            'original_price' => $b->original_price ? (float) $b->original_price : null,
            'badge'          => $b->badge,
            'desc'           => $b->short_desc_ar,
            'features'       => $b->features_json ?? [],
        ])->values()->toArray(),
        'userWishlist' => $userWishlistIds ?? [],
        'userTrials'   => $userActiveTrialItemIds ?? [],
    ];
@endphp

<div class="space-y-12" x-data="brandErpMarketplace({{ json_encode($catalogData) }})" dir="rtl">

    {{-- ===================================================================== --}}
    {{-- 1. STORE HERO BANNER & REAL COMPETITIVE ADVANTAGES                    --}}
    {{-- ===================================================================== --}}
    <section class="relative overflow-hidden rounded-3xl glass-obsidian border border-cyber-cyan/40 p-6 sm:p-10 lg:p-12 shadow-[0_4px_40px_rgba(0,245,212,0.12)]">
        <div class="absolute -top-32 -left-32 w-96 h-96 rounded-full blur-[140px] opacity-25 bg-cyber-cyan pointer-events-none"></div>
        <div class="absolute -bottom-32 -right-32 w-96 h-96 rounded-full blur-[140px] opacity-20 bg-purple-600 pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-8">
            <div class="space-y-4 max-w-3xl">
                <div class="flex flex-wrap items-center gap-2 text-xs">
                    <a href="{{ route('services.index') }}" class="text-slate-400 hover:text-cyber-cyan transition-colors font-cairo">بوابة الخدمات</a>
                    <span class="text-slate-600">/</span>
                    <a href="{{ route('services.show', 'brand-erp') }}" class="text-slate-400 hover:text-cyber-cyan transition-colors font-cairo">منظومة BrandERP</a>
                    <span class="text-slate-600">/</span>
                    <span class="px-3 py-1 rounded-full font-cairo font-bold text-xs bg-cyber-cyan/15 border border-cyber-cyan/30 text-cyber-cyan">
                        المتجر الموديلار المستقل (Marketplace)
                    </span>
                </div>

                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white font-cairo leading-tight">
                    متجر <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyber-cyan to-teal-400">BrandERP</span> الموديلار الشامل
                </h1>

                <p class="text-sm sm:text-base text-slate-300 font-cairo leading-relaxed">
                    منظومة تشغيل وإدارة تجارية مستلهمة من أحدث معماريات UltimatePOS السحابية ومكيفة بالكامل للسوق المصري:
                    ابدأ بنواتك الأساسية (كاشير ومبيعات ومخزون) بـ <strong class="text-cyber-cyan font-bold font-mono">2,000 ج.م فقط</strong>، ووسّع نظامك بالـ 16 موديولاً تخصصياً أو من بين 500 ميزة دقيقة، أو اعتمد باقة القلعة الشاملة للمؤسسات Class-A (155,000 ج.م).
                </p>

                <!-- Action links -->
                <div class="flex flex-wrap items-center gap-3 pt-2">
                    <a href="{{ route('services.show', 'brand-erp') }}" 
                       class="px-5 py-2.5 rounded-xl btn-citadel-secondary text-xs flex items-center gap-2">
                        <span>← صفحة تعريف BrandERP</span>
                    </a>
                    @auth
                        <a href="{{ route('portal.erp_store') }}" 
                           class="px-5 py-2.5 rounded-xl bg-purple-900/40 hover:bg-purple-900/60 border border-purple-500/50 text-purple-300 text-xs font-cairo font-bold flex items-center gap-2 transition-all">
                            <span>📊 بوابة العميل وإدارة ترخيصي</span>
                        </a>
                    @endauth
                    <button type="button" 
                            @click="$dispatch('branderp-roi-open')"
                            class="px-5 py-2.5 rounded-xl bg-obsidian-surface hover:bg-obsidian-card border border-cyber-gold/40 text-cyber-gold text-xs font-cairo font-bold flex items-center gap-2 transition-all">
                        <span>📊 حاسبة العائد الاستثماري ROI</span>
                    </button>
                    <a href="#custom-request-section" 
                       class="px-5 py-2.5 rounded-xl bg-obsidian-surface hover:bg-obsidian-card border border-slate-700 text-slate-300 text-xs font-cairo font-bold transition-all">
                        <span>طلب ميزة خاصة لنشاطك ✍️</span>
                    </a>
                </div>
            </div>

            {{-- Core Base Highlight Box --}}
            <div class="p-6 rounded-2xl bg-obsidian-surface/90 border border-cyber-cyan/40 shadow-neon-cyan flex flex-col justify-between gap-4 w-full lg:w-80 shrink-0">
                <div class="space-y-1">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-mono font-bold text-cyber-cyan px-2 py-0.5 rounded bg-cyber-cyan/10">CORE BASE · TIER 1</span>
                        <span class="text-xl">🏪</span>
                    </div>
                    <h3 class="text-base font-black text-white font-cairo">النواة الأساسية (كاشير ومبيعات)</h3>
                    <p class="text-xs text-slate-400 font-cairo leading-relaxed">كاشير POS أوفلاين وأونلاين، إدارة أصناف، باركود وطباعة حرارية، وحركة الخزينة.</p>
                </div>
                <div class="pt-3 border-t border-obsidian-border flex items-baseline justify-between">
                    <span class="text-2xl font-black font-mono text-cyber-cyan">2,000 <span class="text-xs font-cairo text-slate-400">ج.م</span></span>
                    <span class="text-xs font-cairo text-emerald-400 font-bold">مضافة في السلة تلقائياً ✓</span>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================================================================== --}}
    {{-- 2. DAILY FLASH FEATURE DEAL BANNER WITH COUNTDOWN                     --}}
    {{-- ===================================================================== --}}
    @if($flashDeal)
        <div class="rounded-3xl bg-gradient-to-r from-rose-950/40 via-obsidian-card to-purple-950/40 border border-rose-500/50 p-5 sm:p-6 flex flex-col md:flex-row items-center justify-between gap-6 shadow-[0_4px_30px_rgba(255,42,85,0.15)] relative overflow-hidden">
            <div class="absolute -right-16 -top-16 w-48 h-48 bg-rose-500/10 rounded-full blur-2xl pointer-events-none"></div>

            <div class="flex items-center gap-4 z-10 text-center sm:text-right flex-col sm:flex-row">
                <div class="w-14 h-14 rounded-2xl bg-rose-500/20 border border-rose-500/40 flex items-center justify-center text-3xl shrink-0 shadow-lg animate-pulse">
                    ⚡
                </div>
                <div class="space-y-1">
                    <div class="inline-flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black font-cairo bg-rose-500 text-white shadow-sm">
                            عرض الفلاش اليومي 50% خصم
                        </span>
                        <span class="text-xs font-mono text-rose-300 font-bold">ينتهي العرض عند منتصف الليل</span>
                    </div>
                    <h3 class="text-base sm:text-lg font-black text-white font-cairo">
                        {{ $flashDeal->name_ar }}
                    </h3>
                    <p class="text-xs text-slate-300 font-cairo">{{ $flashDeal->short_desc_ar }} — {{ $flashDeal->roi_time_saved_ar }}</p>
                </div>
            </div>

            {{-- Timer and Action --}}
            <div class="flex items-center gap-4 shrink-0 z-10 flex-wrap justify-center">
                {{-- Countdown timer --}}
                <div class="flex items-center gap-1.5 font-mono text-xs">
                    <div class="bg-black/60 border border-rose-500/30 px-2.5 py-1.5 rounded-xl text-center">
                        <span class="text-base font-black text-rose-400" x-text="countdown.hours">03</span>
                        <span class="text-[9px] text-slate-400 block font-cairo">ساعة</span>
                    </div>
                    <span class="text-rose-400 font-bold">:</span>
                    <div class="bg-black/60 border border-rose-500/30 px-2.5 py-1.5 rounded-xl text-center">
                        <span class="text-base font-black text-rose-400" x-text="countdown.minutes">45</span>
                        <span class="text-[9px] text-slate-400 block font-cairo">دقيقة</span>
                    </div>
                    <span class="text-rose-400 font-bold">:</span>
                    <div class="bg-black/60 border border-rose-500/30 px-2.5 py-1.5 rounded-xl text-center">
                        <span class="text-base font-black text-rose-400" x-text="countdown.seconds">20</span>
                        <span class="text-[9px] text-slate-400 block font-cairo">ثانية</span>
                    </div>
                </div>

                {{-- Price & Button --}}
                <div class="text-center sm:text-right">
                    <span class="text-xs font-mono text-slate-400 line-through block">{{ number_format($flashDeal->price * 2, 0) }} EGP</span>
                    <span class="text-xl font-black font-mono text-rose-400">{{ number_format($flashDeal->price, 0) }} <span class="text-xs font-cairo text-slate-300">ج.م</span></span>
                </div>

                <button type="button"
                        @click="addFlashDeal('{{ $flashDeal->slug }}', {{ (float) $flashDeal->price }})"
                        class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-500 hover:to-red-500 text-white font-cairo font-black text-xs transition-all shadow-lg active:scale-95">
                    أضف للسلة بخصم 50% ⚡
                </button>
            </div>
        </div>
    @endif

    {{-- ===================================================================== --}}
    {{-- 3. INDUSTRY BLUEPRINT 1-CLICK FILTERS (1-Click Preset)                --}}
    {{-- ===================================================================== --}}
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-black text-white font-cairo flex items-center gap-2">
                <span>🎯 خريطة الأنشطة الجاهزة (Industry Blueprint 1-Click Filters):</span>
            </h3>
            <span class="text-xs text-slate-400 font-cairo hidden sm:inline">انقر على نشاطك لتصفية الـ 500 ميزة إلى الباقة المثالية له</span>
        </div>

        <div class="flex items-center gap-2.5 overflow-x-auto pb-2 scrollbar-none">
            @foreach($industries as $ind)
                <button type="button"
                        @click="filterIndustry('{{ $ind['id'] }}')"
                        :class="selectedIndustry === '{{ $ind['id'] }}' ? 'bg-cyber-cyan text-obsidian-base font-black shadow-neon-cyan border-cyber-cyan' : 'bg-obsidian-surface text-slate-300 hover:text-white border-obsidian-border'"
                        class="px-4 py-2 rounded-xl text-xs font-cairo font-bold border transition-all flex items-center gap-2 shrink-0">
                    <span>{{ $ind['icon'] }}</span>
                    <span>{{ $ind['name'] }}</span>
                </button>
            @endforeach
        </div>
    </div>

    {{-- ===================================================================== --}}
    {{-- 4. CONTROLS BAR: SEARCH (Slang), PRICE TIERS, ENGINEER PICKS          --}}
    {{-- ===================================================================== --}}
    <div class="glass-obsidian rounded-2xl border border-obsidian-border p-4 flex flex-col lg:flex-row items-center justify-between gap-4 shadow-sm">
        
        {{-- Search Input with Egyptian Commercial Slang Parser --}}
        <div class="relative w-full lg:w-80">
            <input type="text"
                   x-model="searchQuery"
                   @input="handleSearchInput()"
                   placeholder="بحث ذكي: شكك، آجل، باركود ميزان، درج، شفت، جرد..."
                   class="w-full bg-obsidian-surface text-xs text-slate-200 placeholder-slate-500 rounded-xl px-9 py-2.5 border border-obsidian-border focus:border-cyber-cyan focus:outline-none transition-all font-cairo">
            <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-cyber-cyan">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <button x-show="searchQuery" @click="searchQuery = ''; activeFilterMode = 'all'" class="absolute inset-y-0 left-3 flex items-center text-slate-500 hover:text-white text-xs">✕</button>
        </div>

        {{-- Filter Mode Pills --}}
        <div class="flex items-center gap-2 overflow-x-auto w-full lg:w-auto pb-2 lg:pb-0 scrollbar-none">
            <button type="button"
                    @click="activeFilterMode = 'all'; selectedPriceTier = 'all'"
                    :class="activeFilterMode === 'all' && selectedPriceTier === 'all' ? 'bg-cyber-cyan text-obsidian-base font-black' : 'bg-obsidian-surface text-slate-400 hover:text-white'"
                    class="px-3.5 py-1.5 rounded-lg text-xs font-cairo font-bold transition-all shrink-0">
                كافة العناصر (500)
            </button>

            <button type="button"
                    @click="activeFilterMode = 'sales_picks'"
                    :class="activeFilterMode === 'sales_picks' ? 'bg-purple-600 text-white font-black' : 'bg-obsidian-surface text-purple-300 hover:text-white border border-purple-500/30'"
                    class="px-3.5 py-1.5 rounded-lg text-xs font-cairo font-bold transition-all shrink-0 flex items-center gap-1.5">
                <span>⭐ ترشيحات مهندس المبيعات</span>
            </button>

            <button type="button"
                    @click="activeFilterMode = 'wishlist'"
                    :class="activeFilterMode === 'wishlist' ? 'bg-rose-600 text-white font-black' : 'bg-obsidian-surface text-rose-300 hover:text-white border border-rose-500/30'"
                    class="px-3.5 py-1.5 rounded-lg text-xs font-cairo font-bold transition-all shrink-0 flex items-center gap-1.5">
                <span>❤️ مفضلتي (<span x-text="wishlistIds.length"></span>)</span>
            </button>
        </div>

        {{-- Price Tier Filter --}}
        <div class="flex items-center gap-1.5 text-xs font-cairo overflow-x-auto w-full lg:w-auto">
            <span class="text-slate-500 text-[11px] shrink-0">فئة السعر:</span>
            <template x-for="tierPrice in [100, 200, 350, 500, 750]" :key="tierPrice">
                <button type="button"
                        @click="selectedPriceTier = (selectedPriceTier === tierPrice ? 'all' : tierPrice)"
                        :class="selectedPriceTier === tierPrice ? 'bg-cyber-gold text-obsidian-base font-black' : 'bg-obsidian-surface text-slate-400 hover:text-white'"
                        class="px-2.5 py-1 rounded-lg text-[11px] font-mono font-bold transition-all shrink-0 border border-obsidian-border"
                        x-text="tierPrice + ' ج.م'">
                </button>
            </template>
        </div>

    </div>

    {{-- ===================================================================== --}}
    {{-- 5. MAIN MARKETPLACE BODY: DEPARTMENTS SIDEBAR + TABS + 500 FEATURES   --}}
    {{-- ===================================================================== --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        {{-- Left Navigation / Main Workspace (Col 8) --}}
        <div class="lg:col-span-8 space-y-6">

            {{-- Main Tabs: 16 Modules | 500 Features | 6 Bundles --}}
            <div class="glass-obsidian rounded-2xl border border-obsidian-border p-2 flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2 overflow-x-auto">
                    <button type="button"
                            @click="activeMainTab = 'features'"
                            :class="activeMainTab === 'features' ? 'bg-cyber-gold text-obsidian-base font-black shadow-sm' : 'bg-obsidian-surface text-slate-300 hover:text-white'"
                            class="px-5 py-2.5 rounded-xl text-xs font-cairo font-bold transition-all flex items-center gap-2 shrink-0">
                        <span>⚡ متجر الـ 500 ميزة دقيقة</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold" :class="activeMainTab === 'features' ? 'bg-black/20 text-black' : 'bg-slate-800 text-slate-400'" x-text="filteredFeatures.length">500</span>
                    </button>

                    <button type="button"
                            @click="activeMainTab = 'modules'"
                            :class="activeMainTab === 'modules' ? 'bg-cyber-cyan text-obsidian-base font-black shadow-sm' : 'bg-obsidian-surface text-slate-300 hover:text-white'"
                            class="px-5 py-2.5 rounded-xl text-xs font-cairo font-bold transition-all flex items-center gap-2 shrink-0">
                        <span>📦 الـ 16 موديولاً الرئيسية</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold" :class="activeMainTab === 'modules' ? 'bg-black/20 text-black' : 'bg-slate-800 text-slate-400'">16</span>
                    </button>

                    <button type="button"
                            @click="activeMainTab = 'bundles'"
                            :class="activeMainTab === 'bundles' ? 'bg-rose-500 text-white font-black shadow-sm' : 'bg-obsidian-surface text-slate-300 hover:text-white'"
                            class="px-5 py-2.5 rounded-xl text-xs font-cairo font-bold transition-all flex items-center gap-2 shrink-0">
                        <span>👑 باقات المؤسسات والأنشطة (6)</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold" :class="activeMainTab === 'bundles' ? 'bg-black/20 text-white' : 'bg-slate-800 text-slate-400'">6</span>
                    </button>
                </div>

                {{-- Result Counter --}}
                <div class="text-xs text-slate-400 font-cairo">
                    يُعرض: <span class="font-mono font-bold text-cyber-cyan" x-text="paginatedFeatures.length"></span> من <span class="font-mono font-bold text-white" x-text="filteredFeatures.length"></span> ميزة
                </div>
            </div>

            {{-- 1. TAB: 500 MICRO-FEATURES --}}
            <div x-show="activeMainTab === 'features'" x-transition class="space-y-6">

                {{-- Department Pills Filter --}}
                <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none">
                    <button type="button"
                            @click="selectedDepartment = 'all'"
                            :class="selectedDepartment === 'all' ? 'bg-cyber-cyan text-obsidian-base font-black shadow-sm' : 'bg-obsidian-surface text-slate-400 hover:text-white border border-obsidian-border'"
                            class="px-3.5 py-1.5 rounded-xl text-xs font-cairo font-bold transition-all shrink-0">
                        كافة الأقسام الـ 16
                    </button>
                    @foreach($departments as $dept)
                        <button type="button"
                                @click="selectedDepartment = '{{ $dept }}'"
                                :class="selectedDepartment === '{{ $dept }}' ? 'bg-cyber-cyan text-obsidian-base font-black shadow-sm' : 'bg-obsidian-surface text-slate-400 hover:text-white border border-obsidian-border'"
                                class="px-3.5 py-1.5 rounded-xl text-xs font-cairo font-bold transition-all shrink-0">
                            {{ $dept }}
                        </button>
                    @endforeach
                </div>

                {{-- 500 Features Grid (Paginated 24 per page for zero lag) --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3.5">
                    <template x-for="item in paginatedFeatures" :key="item.code">
                        <div class="p-4 rounded-2xl glass-obsidian border border-obsidian-border hover:border-cyber-cyan/50 flex flex-col justify-between gap-3 transition-all group relative overflow-hidden">
                            
                            {{-- Top row: Code + Badges + Wishlist --}}
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex items-center gap-1.5">
                                    <span class="text-[10px] font-mono font-bold text-slate-400 bg-obsidian-surface px-2 py-0.5 rounded border border-obsidian-border" x-text="item.code"></span>
                                    <span x-show="item.badge" class="text-[9px] font-cairo font-bold px-2 py-0.5 rounded-full bg-cyber-cyan/15 text-cyber-cyan border border-cyber-cyan/30" x-text="item.badge"></span>
                                    <span x-show="item.is_sales_pick" class="text-[9px] font-cairo font-bold px-1.5 py-0.5 rounded bg-purple-500/20 text-purple-300" title="ترشيح مهندس المبيعات">⭐ مهندس</span>
                                </div>
                                <button type="button" 
                                        @click="toggleWishlist(item.id)" 
                                        class="text-xs transition-transform active:scale-125"
                                        :class="isWishlisted(item.id) ? 'text-rose-500' : 'text-slate-600 hover:text-rose-400'"
                                        title="إضافة لمفضلة التطوير">
                                    <span x-text="isWishlisted(item.id) ? '❤️' : '🤍'"></span>
                                </button>
                            </div>

                            {{-- Title & Short Description --}}
                            <div class="space-y-1.5 cursor-pointer" @click="openFeatureModal(item)">
                                <h4 class="text-xs font-bold text-white font-cairo group-hover:text-cyber-cyan transition-colors leading-snug" x-text="item.name"></h4>
                                <p class="text-[11px] text-slate-400 font-cairo leading-relaxed line-clamp-2" x-text="item.desc"></p>
                            </div>

                            {{-- ROI Time Saved & Hardware Link --}}
                            <div class="space-y-1 pt-1">
                                <div class="flex items-center justify-between text-[10px] font-cairo text-emerald-400">
                                    <span class="flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                        <span x-text="item.roi"></span>
                                    </span>
                                </div>
                                <div x-show="item.hardware_slug" class="text-[10px] text-cyber-cyan font-cairo flex items-center gap-1 font-bold">
                                    <span>🖨️ عتاد متوافق متاح</span>
                                </div>
                            </div>

                            {{-- Pricing & Action Buttons --}}
                            <div class="pt-2 border-t border-obsidian-border/80 flex items-center justify-between gap-2">
                                <div>
                                    <span class="text-sm font-black font-mono text-cyber-gold" x-text="item.price.toLocaleString()"></span>
                                    <span class="text-[10px] font-mono text-slate-400"> EGP</span>
                                </div>

                                <div class="flex items-center gap-1.5">
                                    {{-- 48h Free Trial --}}
                                    <button type="button"
                                            @click="startTrial(item.id)"
                                            :disabled="isTrialActive(item.id)"
                                            class="px-2.5 py-1 rounded-lg text-[10px] font-cairo font-bold transition-all border"
                                            :class="isTrialActive(item.id) ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30 cursor-not-allowed' : 'bg-obsidian-surface hover:bg-obsidian-card text-slate-300 border-obsidian-border'">
                                        <span x-text="isTrialActive(item.id) ? 'تجربة نشطة ✓' : 'تجربة 48h'"></span>
                                    </button>

                                    {{-- Add to Cart --}}
                                    <button type="button"
                                            @click="toggleFeatureCart(item)"
                                            :class="isFeatureInCart(item.slug) ? 'bg-rose-500/20 text-rose-300 border-rose-500/40' : 'btn-citadel-primary'"
                                            class="px-3 py-1 rounded-lg text-[10px] font-cairo font-bold transition-all border">
                                        <span x-text="isFeatureInCart(item.slug) ? '✕ إزالة' : '+ أضف'"></span>
                                    </button>
                                </div>
                            </div>

                        </div>
                    </template>
                </div>

                {{-- Pagination Controls (for 500 features) --}}
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 p-4 rounded-2xl glass-obsidian border border-obsidian-border" x-show="totalPages > 1">
                    <div class="text-xs text-slate-400 font-cairo">
                        الصفحة <span class="font-mono font-bold text-white" x-text="currentPage"></span> من <span class="font-mono font-bold text-white" x-text="totalPages"></span> (<span class="font-mono" x-text="filteredFeatures.length"></span> ميزة إجمالية)
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" 
                                @click="prevPage()" 
                                :disabled="currentPage === 1"
                                class="px-3.5 py-1.5 rounded-xl text-xs font-cairo font-bold bg-obsidian-surface border border-obsidian-border text-slate-300 hover:text-white disabled:opacity-40 transition-all">
                            السابق ←
                        </button>

                        <div class="flex items-center gap-1 font-mono text-xs">
                            <template x-for="p in visiblePages" :key="p">
                                <button type="button"
                                        @click="goToPage(p)"
                                        :class="currentPage === p ? 'bg-cyber-cyan text-obsidian-base font-black shadow-sm' : 'bg-obsidian-surface text-slate-400 hover:text-white'"
                                        class="w-8 h-8 rounded-xl font-bold transition-all flex items-center justify-center border border-obsidian-border"
                                        x-text="p">
                                </button>
                            </template>
                        </div>

                        <button type="button" 
                                @click="nextPage()" 
                                :disabled="currentPage === totalPages"
                                class="px-3.5 py-1.5 rounded-xl text-xs font-cairo font-bold bg-obsidian-surface border border-obsidian-border text-slate-300 hover:text-white disabled:opacity-40 transition-all">
                            التالي →
                        </button>
                    </div>
                </div>

            </div>

            {{-- 2. TAB: 16 MAJOR MODULES --}}
            <div x-show="activeMainTab === 'modules'" x-transition class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <template x-for="mod in catalog.modules" :key="mod.code">
                        <div class="p-5 rounded-2xl glass-obsidian border border-obsidian-border hover:border-cyber-cyan/50 flex flex-col justify-between gap-4 transition-all">
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="text-2xl" x-text="mod.icon"></span>
                                        <span class="px-2.5 py-0.5 rounded text-[10px] font-mono font-bold bg-purple-500/15 text-purple-300 border border-purple-500/30" x-text="mod.code"></span>
                                    </div>
                                    <span class="text-base font-black font-mono text-white" x-text="mod.price.toLocaleString() + ' ج.م'"></span>
                                </div>
                                <h4 class="text-base font-bold text-white font-cairo" x-text="mod.name"></h4>
                                <p class="text-xs text-slate-400 font-cairo leading-relaxed" x-text="mod.desc"></p>
                                <div class="text-[11px] font-cairo text-emerald-400 flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                    <span x-text="mod.roi"></span>
                                </div>
                            </div>

                            <button type="button"
                                    @click="toggleModuleCart(mod)"
                                    :class="isModuleInCart(mod.slug) ? 'bg-rose-500/20 text-rose-300 border-rose-500/40 hover:bg-rose-500/30' : 'btn-citadel-primary'"
                                    class="w-full py-2 rounded-xl text-xs font-cairo font-bold transition-all border flex items-center justify-center gap-1.5">
                                <span x-text="isModuleInCart(mod.slug) ? '✕ إزالة من المنظومة' : '+ إضافة موديول ' + mod.name"></span>
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            {{-- 3. TAB: 6 CURATED BUNDLES --}}
            <div x-show="activeMainTab === 'bundles'" x-transition class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <template x-for="b in catalog.bundles" :key="b.code">
                        <div class="p-6 rounded-3xl glass-obsidian border border-rose-500/40 hover:border-rose-500 flex flex-col justify-between gap-5 transition-all shadow-md">
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="px-3 py-1 rounded-full text-xs font-cairo font-bold bg-rose-500/15 text-rose-300 border border-rose-500/30" x-text="b.badge"></span>
                                    <span x-show="b.original_price" class="text-xs font-mono text-slate-500 line-through" x-text="b.original_price ? b.original_price.toLocaleString() + ' EGP' : ''"></span>
                                </div>
                                <h4 class="text-lg font-black text-white font-cairo" x-text="b.name"></h4>
                                <p class="text-xs text-slate-400 font-cairo leading-relaxed" x-text="b.desc"></p>
                                
                                <ul class="space-y-1.5 pt-2 border-t border-obsidian-border/60">
                                    <template x-for="f in b.features" :key="f">
                                        <li class="flex items-center gap-2 text-xs text-slate-300 font-cairo">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-400 shrink-0"></span>
                                            <span x-text="f"></span>
                                        </li>
                                    </template>
                                </ul>
                            </div>

                            <div class="pt-4 border-t border-obsidian-border space-y-3">
                                <div class="flex items-baseline justify-between">
                                    <span class="text-2xl font-black font-mono text-rose-300" x-text="b.price.toLocaleString() + ' ج.م'"></span>
                                    <span class="text-xs font-cairo text-emerald-400 font-bold">شاملة التركيب والتدريب</span>
                                </div>
                                <button type="button"
                                        @click="applyBundleDirect(b)"
                                        class="w-full py-2.5 rounded-xl bg-gradient-to-r from-rose-600 to-purple-600 hover:from-rose-500 hover:to-purple-500 text-white font-cairo font-black text-xs transition-all shadow-md">
                                    اختيار باقة <span x-text="b.name"></span> فوراً
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

        </div>

        {{-- Right / Cart & Checkout Sidebar (Col 4) --}}
        <div class="lg:col-span-4 sticky top-28 space-y-6">

            {{-- Floating Builder Cart --}}
            <div class="glass-obsidian rounded-3xl border border-cyber-cyan/30 p-6 space-y-6 shadow-[0_8px_30px_rgba(0,245,212,0.08)]">
                
                <div class="flex items-center justify-between border-b border-obsidian-border pb-4">
                    <div class="flex items-center gap-2.5">
                        <span class="text-xl">🛒</span>
                        <h3 class="text-base font-black text-white font-cairo">سلة المنظومة المخصصة</h3>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-cyber-cyan/20 text-cyber-cyan" x-text="(selectedModules.length + selectedFeatures.length + 1) + ' عناصر'"></span>
                </div>

                {{-- Selected Items List --}}
                <div class="space-y-3 max-h-80 overflow-y-auto custom-scrollbar pr-1">
                    {{-- Core item --}}
                    <div class="p-3 rounded-xl bg-obsidian-surface border border-cyber-cyan/30 flex items-center justify-between text-xs">
                        <div>
                            <span class="font-bold text-white font-cairo block">النواة الأساسية (Core POS)</span>
                            <span class="text-[10px] text-emerald-400 font-cairo">كاشير ومبيعات ومخزون</span>
                        </div>
                        <span class="font-mono font-black text-cyber-cyan">2,000 EGP</span>
                    </div>

                    {{-- Selected Modules --}}
                    <template x-for="mod in selectedModules" :key="mod.slug">
                        <div class="p-3 rounded-xl bg-obsidian-surface border border-purple-500/30 flex items-center justify-between text-xs group">
                            <div class="flex items-center gap-2">
                                <button type="button" @click="removeModule(mod.slug)" class="text-slate-500 hover:text-rose-400 font-bold transition-colors">✕</button>
                                <span class="font-bold text-white font-cairo text-[11px]" x-text="mod.name"></span>
                            </div>
                            <span class="font-mono font-black text-purple-300" x-text="mod.price.toLocaleString() + ' EGP'"></span>
                        </div>
                    </template>

                    {{-- Selected Features --}}
                    <template x-for="feat in selectedFeatures" :key="feat.slug">
                        <div class="p-3 rounded-xl bg-obsidian-surface border border-cyber-gold/30 flex items-center justify-between text-xs group">
                            <div class="flex items-center gap-2">
                                <button type="button" @click="removeFeature(feat.slug)" class="text-slate-500 hover:text-rose-400 font-bold transition-colors">✕</button>
                                <span class="font-bold text-white font-cairo text-[11px]" x-text="feat.name"></span>
                            </div>
                            <span class="font-mono font-black text-cyber-gold" x-text="feat.price.toLocaleString() + ' EGP'"></span>
                        </div>
                    </template>
                </div>

                {{-- Cart Total Breakdown --}}
                <div class="p-4 rounded-2xl bg-obsidian-surface/90 border border-obsidian-border space-y-2">
                    <div class="flex items-center justify-between text-xs font-cairo text-slate-400">
                        <span>قيمة النواة:</span>
                        <span class="font-mono text-cyber-cyan">2,000 EGP</span>
                    </div>
                    <div class="flex items-center justify-between text-xs font-cairo text-slate-400" x-show="selectedModules.length > 0">
                        <span>الموديولات (<span x-text="selectedModules.length"></span>):</span>
                        <span class="font-mono text-purple-300" x-text="modulesSubtotal.toLocaleString() + ' EGP'"></span>
                    </div>
                    <div class="flex items-center justify-between text-xs font-cairo text-slate-400" x-show="selectedFeatures.length > 0">
                        <span>الميزات (<span x-text="selectedFeatures.length"></span>):</span>
                        <span class="font-mono text-cyber-gold" x-text="featuresSubtotal.toLocaleString() + ' EGP'"></span>
                    </div>
                    <div class="flex items-center justify-between text-xs font-cairo text-emerald-400" x-show="bundleDiscount > 0">
                        <span>خصم الباقة المجمعة (35%):</span>
                        <span class="font-mono" x-text="'- ' + bundleDiscount.toLocaleString() + ' EGP'"></span>
                    </div>
                    <div class="pt-2 border-t border-obsidian-border/80 flex items-baseline justify-between">
                        <span class="text-sm font-black text-white font-cairo">الإجمالي النهائي:</span>
                        <div class="text-left">
                            <span class="text-2xl font-black font-mono text-transparent bg-clip-text bg-gradient-to-r from-cyber-cyan to-cyber-gold" x-text="grandTotal.toLocaleString()"></span>
                            <span class="text-xs font-mono text-slate-400"> EGP</span>
                        </div>
                    </div>
                </div>

                {{-- Checkout & Quotation Actions --}}
                <div class="space-y-2.5 pt-2">
                    <button type="button"
                            @click="openCheckoutModal()"
                            class="w-full py-3 rounded-xl btn-citadel-primary text-xs text-center block font-cairo font-bold shadow-neon-cyan active:scale-95 transition-all">
                        تأكيد وحجز المنظومة (1-Click) 🚀
                    </button>

                    <button type="button"
                            @click="exportQuotation()"
                            class="w-full py-2.5 rounded-xl bg-obsidian-surface hover:bg-obsidian-card border border-slate-700 text-slate-300 hover:text-white text-xs font-cairo font-bold flex items-center justify-center gap-2 transition-all">
                        <span>🖨️ تصدير عرض سعر رسمي معتمد (PDF)</span>
                    </button>

                    <button type="button"
                            @click="copyShareLink()"
                            class="w-full py-2 rounded-xl text-[11px] text-slate-400 hover:text-cyber-cyan font-cairo transition-colors flex items-center justify-center gap-1.5">
                        <span>🔗 نسخ رابط تشكيلة السلة للمشاركة</span>
                    </button>
                </div>

            </div>

        </div>

    </div>

    {{-- ===================================================================== --}}
    {{-- 6. CUSTOM FEATURE PROGRAMMING REQUEST FORM                            --}}
    {{-- ===================================================================== --}}
    <section id="custom-request-section" class="scroll-mt-24 rounded-3xl glass-obsidian border border-obsidian-border p-6 sm:p-10 space-y-6">
        <div class="max-w-2xl space-y-2">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-mono font-bold bg-cyber-purple/20 text-purple-300 border border-purple-500/30">
                <span>CUSTOM ERP FEATURE PROGRAMMING</span>
            </div>
            <h3 class="text-2xl font-black text-white font-cairo">اطلب برمجة ميزة خاصة لنشاطك (Tailored Solution)</h3>
            <p class="text-xs sm:text-sm text-slate-400 font-cairo leading-relaxed">
                هل لديك دورة عمل فريدة أو فكرة نظام مخصصة لا توجد في أي برنامج جاهز؟ يقوم فريق مهندسي برمجيات سولي ببرمجتها وربطها بنواتك السحابية باحترافية وسرعة فائقة.
            </p>
        </div>

        <form @submit.prevent="submitCustomRequestForm()" class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs font-cairo">
            <div class="space-y-1">
                <label class="text-slate-300 font-bold">الاسم الكريم أو اسم المؤسسة *</label>
                <input type="text" x-model="customForm.name" required placeholder="مثال: أ/ محمد عبد الرحمن — شركة النيل" class="w-full bg-obsidian-surface text-slate-200 rounded-xl px-4 py-2.5 border border-obsidian-border focus:border-cyber-cyan focus:outline-none">
            </div>

            <div class="space-y-1">
                <label class="text-slate-300 font-bold">رقم الهاتف (واتساب للتواصل الفوري) *</label>
                <input type="text" x-model="customForm.phone" required placeholder="01xxxxxxxxx" class="w-full bg-obsidian-surface text-slate-200 rounded-xl px-4 py-2.5 border border-obsidian-border focus:border-cyber-cyan focus:outline-none">
            </div>

            <div class="space-y-1">
                <label class="text-slate-300 font-bold">طبيعة النشاط التجاري *</label>
                <select x-model="customForm.industry" required class="w-full bg-obsidian-surface text-slate-200 rounded-xl px-4 py-2.5 border border-obsidian-border focus:border-cyber-cyan focus:outline-none">
                    <option value="supermarket">سوبر ماركت وهايبر</option>
                    <option value="pharmacy">صيدلية ومستودع أدوية</option>
                    <option value="restaurant">مطعم أو كافيه</option>
                    <option value="factory">مصنع أو ورشة إنتاج</option>
                    <option value="clothing">ملابس ومحلات تجارية</option>
                    <option value="maintenance">مركز صيانة ودعم فني</option>
                    <option value="other">نشاط آخر خاص</option>
                </select>
            </div>

            <div class="space-y-1">
                <label class="text-slate-300 font-bold">عنوان الميزة أو الآلية المطلوبة *</label>
                <input type="text" x-model="customForm.title" required placeholder="مثال: ربط ماكينة تقطيع الكابلات آلياً بنقطة البيع" class="w-full bg-obsidian-surface text-slate-200 rounded-xl px-4 py-2.5 border border-obsidian-border focus:border-cyber-cyan focus:outline-none">
            </div>

            <div class="space-y-1 md:col-span-2">
                <label class="text-slate-300 font-bold">شرح تفصيلي لدورة العمل المطلوبة *</label>
                <textarea x-model="customForm.description" rows="3" required placeholder="اشرح لنا كيف تريد أن تعمل هذه الميزة بالتحديد وما هي النتيجة المرجوة..." class="w-full bg-obsidian-surface text-slate-200 rounded-xl p-4 border border-obsidian-border focus:border-cyber-cyan focus:outline-none"></textarea>
            </div>

            <div class="md:col-span-2 flex items-center justify-between pt-2">
                <span class="text-[11px] text-slate-500">سيتواصل معك كبير مهندسي الحلول خلال ساعتين مع دراسة جدوى مبدئية.</span>
                <button type="submit" :disabled="customFormSubmitting" class="px-6 py-2.5 rounded-xl btn-citadel-primary text-xs font-bold font-cairo shadow-neon-cyan">
                    <span x-text="customFormSubmitting ? 'جاري الإرسال...' : 'إرسال طلب البرمجة الخاصة 🚀'"></span>
                </button>
            </div>
        </form>
    </section>

    {{-- ===================================================================== --}}
    {{-- 7. FEATURE DETAIL & LIVE UI PREVIEW MODAL (Feature 8)                 --}}
    {{-- ===================================================================== --}}
    <div id="modal-feature-preview"
         x-show="previewModalOpen"
         x-cloak
         @keydown.escape.window="previewModalOpen = false"
         class="fixed inset-0 z-[99999] bg-black/85 backdrop-blur-md flex items-center justify-center p-4"
         dir="rtl">
        
        <div class="relative w-full max-w-2xl glass-obsidian rounded-3xl border border-cyber-cyan/40 p-6 sm:p-8 space-y-6 shadow-2xl bg-obsidian-base"
             @click.outside="previewModalOpen = false"
             x-show="activePreviewItem">
            
            <div class="flex items-start justify-between gap-4 border-b border-obsidian-border pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-cyber-cyan/20 border border-cyber-cyan/40 flex items-center justify-center text-2xl text-cyber-cyan">
                        ⚡
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-mono font-bold text-cyber-cyan" x-text="activePreviewItem ? activePreviewItem.code : ''"></span>
                            <span class="text-[10px] font-cairo font-bold px-2 py-0.5 rounded bg-slate-800 text-slate-300" x-text="activePreviewItem ? activePreviewItem.department : ''"></span>
                        </div>
                        <h3 class="text-lg font-black text-white font-cairo" x-text="activePreviewItem ? activePreviewItem.name : ''"></h3>
                    </div>
                </div>

                <button @click="previewModalOpen = false" class="text-slate-400 hover:text-rose-400 text-lg font-bold">✕</button>
            </div>

            {{-- Mock UI Preview Window --}}
            <div class="p-4 rounded-2xl bg-black/60 border border-slate-800 space-y-2 font-mono text-xs">
                <div class="flex items-center justify-between text-slate-500 pb-2 border-b border-slate-800 text-[10px]">
                    <span class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500 inline-block"></span>
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500 inline-block"></span>
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block"></span>
                        <span class="font-cairo">محاكاة واجهة التشغيل الفعلية داخل BrandERP</span>
                    </span>
                    <span>LIVE UI PREVIEW</span>
                </div>
                <p class="text-slate-300 font-cairo text-xs leading-relaxed pt-1" x-text="activePreviewItem ? activePreviewItem.full_details : ''"></p>
                
                {{-- Key telemetry metrics --}}
                <div class="grid grid-cols-2 gap-3 pt-3">
                    <div class="p-2.5 rounded-xl bg-obsidian-surface border border-obsidian-border text-center">
                        <span class="text-[10px] font-cairo text-slate-400 block">العائد والوقت الموفر:</span>
                        <span class="text-xs font-bold font-cairo text-emerald-400" x-text="activePreviewItem ? activePreviewItem.roi : ''"></span>
                    </div>
                    <div class="p-2.5 rounded-xl bg-obsidian-surface border border-obsidian-border text-center">
                        <span class="text-[10px] font-cairo text-slate-400 block">اعتمادية الميزة:</span>
                        <span class="text-xs font-bold font-cairo text-cyber-cyan" x-text="'مُفعّلة في +' + (activePreviewItem ? activePreviewItem.activations : 0) + ' منشأة'"></span>
                    </div>
                </div>
            </div>

            {{-- Modal Actions --}}
            <div class="flex items-center justify-between gap-3 pt-2">
                <div>
                    <span class="text-xl font-black font-mono text-cyber-gold" x-text="activePreviewItem ? activePreviewItem.price.toLocaleString() : 0"></span>
                    <span class="text-xs font-mono text-slate-400"> EGP</span>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button"
                            @click="if(activePreviewItem) startTrial(activePreviewItem.id)"
                            class="px-4 py-2 rounded-xl bg-obsidian-surface hover:bg-obsidian-card border border-obsidian-border text-xs font-cairo font-bold text-slate-300">
                        تجربة 48 ساعة مجاناً ⏱️
                    </button>
                    <button type="button"
                            @click="if(activePreviewItem) toggleFeatureCart(activePreviewItem); previewModalOpen = false;"
                            class="px-5 py-2 rounded-xl btn-citadel-primary text-xs font-cairo font-black shadow-neon-cyan">
                        إضافة للمنظومة ⚡
                    </button>
                </div>
            </div>

        </div>
    </div>

    {{-- ===================================================================== --}}
    {{-- 8. CHECKOUT MODAL (Wallet / Direct Order)                             --}}
    {{-- ===================================================================== --}}
    <div id="modal-checkout"
         x-show="checkoutModalOpen"
         x-cloak
         @keydown.escape.window="checkoutModalOpen = false"
         class="fixed inset-0 z-[99999] bg-black/85 backdrop-blur-md flex items-center justify-center p-4"
         dir="rtl">
        
        <div class="relative w-full max-w-lg glass-obsidian rounded-3xl border border-cyber-cyan/40 p-6 sm:p-8 space-y-6 shadow-2xl bg-obsidian-base"
             @click.outside="checkoutModalOpen = false">
            
            <div class="flex items-center justify-between border-b border-obsidian-border pb-4">
                <div class="flex items-center gap-2.5">
                    <span class="text-2xl">🚀</span>
                    <div>
                        <h3 class="text-base font-black text-white font-cairo">تأكيد حجز منظومة BrandERP المخصصة</h3>
                        <p class="text-[11px] text-slate-400 font-cairo">إجمالي البنود: <span class="font-mono text-cyber-cyan" x-text="(selectedModules.length + selectedFeatures.length + 1)"></span> مكونات</p>
                    </div>
                </div>
                <button @click="checkoutModalOpen = false" class="text-slate-400 hover:text-rose-400 text-lg font-bold">✕</button>
            </div>

            {{-- Total Amount Summary --}}
            <div class="p-4 rounded-2xl bg-obsidian-surface border border-cyber-cyan/30 flex items-center justify-between">
                <span class="text-xs font-cairo font-bold text-slate-300">المبلغ المطلوب سداده:</span>
                <span class="text-2xl font-black font-mono text-cyber-cyan" x-text="grandTotal.toLocaleString() + ' EGP'"></span>
            </div>

            <form @submit.prevent="submitCheckoutOrder()" class="space-y-4 text-xs font-cairo">
                @guest
                    <div class="space-y-1">
                        <label class="text-slate-300 font-bold">الاسم الكريم *</label>
                        <input type="text" x-model="checkoutForm.name" required placeholder="اسم العميل أو المؤسسة" class="w-full bg-obsidian-surface text-slate-200 rounded-xl px-4 py-2.5 border border-obsidian-border focus:border-cyber-cyan focus:outline-none">
                    </div>
                    <div class="space-y-1">
                        <label class="text-slate-300 font-bold">رقم الهاتف (واتساب) *</label>
                        <input type="text" x-model="checkoutForm.phone" required placeholder="01xxxxxxxxx" class="w-full bg-obsidian-surface text-slate-200 rounded-xl px-4 py-2.5 border border-obsidian-border focus:border-cyber-cyan focus:outline-none">
                    </div>
                @endguest

                <div class="space-y-2">
                    <label class="text-slate-300 font-bold block">اختر وسيلة الدفع والتفعيل:</label>
                    
                    @auth
                        <label class="flex items-center justify-between p-3 rounded-xl bg-obsidian-surface border border-obsidian-border cursor-pointer hover:border-cyber-cyan/50">
                            <div class="flex items-center gap-2.5">
                                <input type="radio" x-model="checkoutForm.payment_method" value="wallet" class="text-cyber-cyan">
                                <div>
                                    <span class="text-xs font-bold text-white block">الدفع المباشر عبر المحفظة الرقمية</span>
                                    <span class="text-[10px] text-emerald-400">تفعيل فوري للمنظومة وإصدار مفتاح الترخيص خلال 0 ثانية</span>
                                </div>
                            </div>
                            <span class="text-xs font-mono font-bold text-cyber-cyan">{{ number_format(Auth::user()->wallet->balance ?? 0, 2) }} EGP</span>
                        </label>
                    @endauth

                    <label class="flex items-center justify-between p-3 rounded-xl bg-obsidian-surface border border-obsidian-border cursor-pointer hover:border-cyber-cyan/50">
                        <div class="flex items-center gap-2.5">
                            <input type="radio" x-model="checkoutForm.payment_method" value="direct_transfer" class="text-cyber-cyan">
                            <div>
                                <span class="text-xs font-bold text-white block">طلب أمر شراء / تحويل بنكي / فودافون كاش</span>
                                <span class="text-[10px] text-slate-400">يتواصل معك مهندس الحسابات لإرسال بيانات التحويل والفاتورة</span>
                            </div>
                        </div>
                        <span class="text-xs">🏦</span>
                    </label>
                </div>

                <button type="submit" 
                        :disabled="checkoutSubmitting"
                        class="w-full py-3 rounded-xl btn-citadel-primary text-xs font-cairo font-black shadow-neon-cyan">
                    <span x-text="checkoutSubmitting ? 'جاري تأكيد المنظومة...' : 'تأكيد الحجز والتشغيل النهائي 🚀'"></span>
                </button>
            </form>

        </div>
    </div>

    <!-- ===================================================================== -->
    <!-- FLOATING MOBILE BRANDERP CHECKOUT BAR (Sticky at bottom-16)           -->
    <!-- ===================================================================== -->
    <div class="fixed bottom-16 inset-x-0 z-40 px-3 py-2 pointer-events-none lg:hidden">
        <div class="max-w-md mx-auto pointer-events-auto bg-[#070914]/95 backdrop-blur-2xl border border-cyber-cyan/40 rounded-2xl p-2.5 shadow-[0_-5px_25px_rgba(0,0,0,0.85)] flex items-center justify-between gap-3">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="relative p-2 rounded-xl bg-cyber-cyan/15 border border-cyber-cyan/30 text-cyber-cyan text-base shrink-0">
                    🛒
                    <span class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-cyber-cyan text-obsidian-base font-black text-[9px] flex items-center justify-center font-mono"
                          x-text="selectedModules.length + selectedFeatures.length + 1"></span>
                </div>
                <div class="min-w-0">
                    <span class="text-[10px] text-slate-400 font-cairo block">إجمالي المنظومة:</span>
                    <div class="flex items-baseline gap-1">
                        <span class="text-sm font-black font-mono text-transparent bg-clip-text bg-gradient-to-r from-cyber-cyan to-cyber-gold"
                              x-text="grandTotal.toLocaleString()"></span>
                        <span class="text-[9px] text-slate-400 font-mono">EGP</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-1.5 shrink-0">
                <button type="button"
                        @click="openCheckoutModal()"
                        class="px-3.5 py-2 rounded-xl btn-citadel-primary text-xs font-black font-cairo shadow-neon-cyan flex items-center gap-1.5 active:scale-95 transition-all">
                    <span>تأكيد وحجز 🚀</span>
                </button>
            </div>
        </div>
    </div>

</div>

{{-- Include ROI Modal --}}
@include('partials.branderp-roi-modal')

@push('scripts')
<script>
function brandErpMarketplace(catalogData) {
    return {
        catalog: catalogData,
        activeMainTab: 'features',
        selectedIndustry: 'all',
        selectedDepartment: 'all',
        selectedPriceTier: 'all',
        activeFilterMode: 'all',
        searchQuery: '',
        
        // Cart state
        selectedModules: [],
        selectedFeatures: [],

        // Pagination state
        currentPage: 1,
        perPage: 24,

        // Modal states
        previewModalOpen: false,
        activePreviewItem: null,
        checkoutModalOpen: false,

        // Wishlist & Trial
        wishlistIds: catalogData.userWishlist || [],
        activeTrialIds: catalogData.userTrials || [],

        // Countdown timer for flash deal
        countdown: { hours: '03', minutes: '45', seconds: '20' },

        // Forms
        customForm: { name: '', phone: '', industry: 'supermarket', title: '', description: '' },
        customFormSubmitting: false,
        checkoutForm: { name: '', phone: '', payment_method: 'direct_transfer' },
        checkoutSubmitting: false,

        init() {
            // Live countdown timer to next midnight
            this.updateCountdown();
            setInterval(() => this.updateCountdown(), 1000);
        },

        updateCountdown() {
            const now = new Date();
            const midnight = new Date();
            midnight.setHours(24, 0, 0, 0);
            const diff = Math.max(0, midnight - now);

            const h = Math.floor(diff / (1000 * 60 * 60));
            const m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const s = Math.floor((diff % (1000 * 60)) / 1000);

            this.countdown.hours = String(h).padStart(2, '0');
            this.countdown.minutes = String(m).padStart(2, '0');
            this.countdown.seconds = String(s).padStart(2, '0');
        },

        // Egyptian commercial slang parser
        get parsedQuery() {
            let q = (this.searchQuery || '').trim().toLowerCase();
            if (q.includes('شكك') || q.includes('اجل') || q.includes('آجل')) return 'آجل';
            if (q.includes('ميزان') || q.includes('وزن')) return 'ميزان';
            if (q.includes('درج') || q.includes('نقدية')) return 'درج';
            if (q.includes('شفت') || q.includes('وردية')) return 'شفت';
            if (q.includes('جرد') || q.includes('عجز')) return 'جرد';
            if (q.includes('مرتجع') || q.includes('استرجاع')) return 'مرتجع';
            if (q.includes('هالك') || q.includes('تالف')) return 'تالف';
            if (q.includes('كوميشن') || q.includes('عمولة')) return 'عمولة';
            return q;
        },

        get filteredFeatures() {
            return this.catalog.features.filter(item => {
                // Industry filter
                if (this.selectedIndustry !== 'all' && (!item.industries || !item.industries.includes(this.selectedIndustry))) {
                    return false;
                }

                // Department filter
                if (this.selectedDepartment !== 'all' && item.department !== this.selectedDepartment) {
                    return false;
                }

                // Price tier filter
                if (this.selectedPriceTier !== 'all' && Number(item.price) !== Number(this.selectedPriceTier)) {
                    return false;
                }

                // Filter mode
                if (this.activeFilterMode === 'sales_picks' && !item.is_sales_pick) return false;
                if (this.activeFilterMode === 'wishlist' && !this.wishlistIds.includes(item.id)) return false;

                // Search query
                if (this.parsedQuery) {
                    const text = (item.name + ' ' + (item.desc || '') + ' ' + (item.department || '')).toLowerCase();
                    if (!text.includes(this.parsedQuery)) return false;
                }

                return true;
            });
        },

        get totalPages() {
            return Math.ceil(this.filteredFeatures.length / this.perPage) || 1;
        },

        get visiblePages() {
            const pages = [];
            const start = Math.max(1, this.currentPage - 2);
            const end = Math.min(this.totalPages, this.currentPage + 2);
            for (let i = start; i <= end; i++) pages.push(i);
            return pages;
        },

        get paginatedFeatures() {
            const start = (this.currentPage - 1) * this.perPage;
            return this.filteredFeatures.slice(start, start + this.perPage);
        },

        goToPage(p) {
            this.currentPage = p;
        },

        prevPage() {
            if (this.currentPage > 1) this.currentPage--;
        },

        nextPage() {
            if (this.currentPage < this.totalPages) this.currentPage++;
        },

        filterIndustry(indId) {
            this.selectedIndustry = indId;
            this.currentPage = 1;
        },

        handleSearchInput() {
            this.currentPage = 1;
        },

        // Cart calculations
        get modulesSubtotal() {
            return this.selectedModules.reduce((sum, m) => sum + m.price, 0);
        },

        get featuresSubtotal() {
            return this.selectedFeatures.reduce((sum, f) => sum + f.price, 0);
        },

        get rawTotal() {
            return 2000 + this.modulesSubtotal + this.featuresSubtotal;
        },

        get bundleDiscount() {
            // Smart upsell: if user picks 3 or more modules, apply 35% discount on modules
            if (this.selectedModules.length >= 3) {
                return Math.round(this.modulesSubtotal * 0.35);
            }
            return 0;
        },

        get grandTotal() {
            return Math.max(0, this.rawTotal - this.bundleDiscount);
        },

        // Module cart
        isModuleInCart(slug) {
            return this.selectedModules.some(m => m.slug === slug);
        },

        toggleModuleCart(mod) {
            const idx = this.selectedModules.findIndex(m => m.slug === mod.slug);
            if (idx === -1) this.selectedModules.push(mod);
            else this.selectedModules.splice(idx, 1);
        },

        removeModule(slug) {
            this.selectedModules = this.selectedModules.filter(m => m.slug !== slug);
        },

        // Feature cart
        isFeatureInCart(slug) {
            return this.selectedFeatures.some(f => f.slug === slug);
        },

        toggleFeatureCart(item) {
            const idx = this.selectedFeatures.findIndex(f => f.slug === item.slug);
            if (idx === -1) this.selectedFeatures.push(item);
            else this.selectedFeatures.splice(idx, 1);
        },

        removeFeature(slug) {
            this.selectedFeatures = this.selectedFeatures.filter(f => f.slug !== slug);
        },

        addFlashDeal(slug, price) {
            const feat = this.catalog.features.find(f => f.slug === slug);
            if (feat && !this.isFeatureInCart(slug)) {
                this.selectedFeatures.push(feat);
            }
        },

        applyBundleDirect(bundle) {
            this.$dispatch('open-express-booking', {
                serviceSlug: 'brand-erp',
                serviceName: 'نظام BrandERP المتكامل',
                packageSlug: bundle.slug,
                packageName: bundle.name,
                price: bundle.price,
                notes: `طلب باقة: ${bundle.name} — القيمة: ${bundle.price.toLocaleString()} EGP`,
            });
        },

        // Wishlist
        isWishlisted(id) {
            return this.wishlistIds.includes(id);
        },

        async toggleWishlist(id) {
            try {
                const res = await fetch('{{ route('store.branderp.wishlist') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ catalog_item_id: id })
                });
                const data = await res.json();
                if (data.success) {
                    if (data.status === 'added') this.wishlistIds.push(id);
                    else this.wishlistIds = this.wishlistIds.filter(x => x !== id);
                } else {
                    alert(data.message || 'يرجى تسجيل الدخول لحفظ المفضلة');
                }
            } catch (e) {
                alert('يرجى تسجيل الدخول للتمكن من حفظ الميزة في المفضلة.');
            }
        },

        // 48h Trial
        isTrialActive(id) {
            return this.activeTrialIds.includes(id);
        },

        async startTrial(id) {
            try {
                const res = await fetch('{{ route('store.branderp.trial') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ catalog_item_id: id })
                });
                const data = await res.json();
                if (data.success) {
                    this.activeTrialIds.push(id);
                    alert(data.message);
                } else {
                    alert(data.message);
                }
            } catch (e) {
                alert('يرجى تسجيل الدخول لتفعيل التجربة المجانية.');
            }
        },

        // Feature preview modal
        openFeatureModal(item) {
            this.activePreviewItem = item;
            this.previewModalOpen = true;
        },

        // Quotation Export
        exportQuotation() {
            const allSlugs = ['erp-core', ...this.selectedModules.map(m => m.slug), ...this.selectedFeatures.map(f => f.slug)];
            const url = '{{ route('store.branderp.quotation') }}?items=' + allSlugs.join(',');
            window.open(url, '_blank');
        },

        // Copy Share Link
        copyShareLink() {
            const allSlugs = [...this.selectedModules.map(m => m.slug), ...this.selectedFeatures.map(f => f.slug)];
            const url = window.location.origin + window.location.pathname + '?items=' + allSlugs.join(',');
            navigator.clipboard.writeText(url);
            alert('تم نسخ رابط تشكيلة منظومتك للحافظة بنجاح!');
        },

        // Checkout
        openCheckoutModal() {
            this.checkoutModalOpen = true;
        },

        async submitCheckoutOrder() {
            this.checkoutSubmitting = true;
            const items = [
                { slug: 'erp-core' },
                ...this.selectedModules.map(m => ({ slug: m.slug })),
                ...this.selectedFeatures.map(f => ({ slug: f.slug })),
            ];

            try {
                const res = await fetch('{{ route('store.branderp.checkout') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        items: items,
                        total_amount: this.grandTotal,
                        customer_name: this.checkoutForm.name,
                        customer_phone: this.checkoutForm.phone,
                        payment_method: this.checkoutForm.payment_method,
                    })
                });

                const data = await res.json();
                this.checkoutSubmitting = false;

                if (data.success) {
                    alert(data.message);
                    this.checkoutModalOpen = false;
                    if (data.redirect) window.location.href = data.redirect;
                } else {
                    alert(data.message || 'حدث خطأ أثناء تنفيذ الطلب.');
                }
            } catch (e) {
                this.checkoutSubmitting = false;
                alert('حدث خطأ في الاتصال بالخادم.');
            }
        },

        // Custom request
        async submitCustomRequestForm() {
            this.customFormSubmitting = true;
            try {
                const res = await fetch('{{ route('store.branderp.custom_request') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        customer_name: this.customForm.name,
                        customer_phone: this.customForm.phone,
                        industry: this.customForm.industry,
                        feature_title: this.customForm.title,
                        feature_description: this.customForm.description,
                    })
                });

                const data = await res.json();
                this.customFormSubmitting = false;

                if (data.success) {
                    alert(data.message);
                    this.customForm = { name: '', phone: '', industry: 'supermarket', title: '', description: '' };
                } else {
                    alert(data.message || 'حدث خطأ أثناء تسجيل طلب البرمجة.');
                }
            } catch (e) {
                this.customFormSubmitting = false;
                alert('حدث خطأ في الاتصال بالخادم.');
            }
        }
    };
}
</script>
@endpush
@endsection
