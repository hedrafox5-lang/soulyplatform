{{--
    BrandERP Interactive Store Modal — Glassmorphic Multi-Tier Builder (Mobile-First Edition)
    Triggered by dispatching 'branderp-store-open' Alpine event.
--}}

@php
    $coreItem     = $erpCatalog['core'][0]  ?? null;
    $moduleItems  = $erpCatalog['module']   ?? collect();
    $featureItems = $erpCatalog['feature']  ?? collect();
    $bundleItems  = $erpCatalog['bundle']   ?? collect();

    $catalogJson = json_encode([
        'core'    => $coreItem ? [[
            'slug'  => $coreItem->slug,
            'name'  => $coreItem->name,
            'price' => (float) $coreItem->price,
        ]] : [],
        'modules' => $moduleItems->map(fn($m) => [
            'slug'  => $m->slug,
            'name'  => $m->name,
            'price' => (float) $m->price,
            'badge' => $m->badge,
        ])->values()->toArray(),
        'features' => $featureItems->map(fn($f) => [
            'slug'       => $f->slug,
            'name'       => $f->name,
            'price'      => (float) $f->price,
            'department' => $f->department,
            'badge'      => $f->badge,
        ])->values()->toArray(),
        'bundles' => $bundleItems->map(fn($b) => [
            'slug'           => $b->slug,
            'name'           => $b->name,
            'price'          => (float) $b->price,
            'original_price' => $b->original_price ? (float) $b->original_price : null,
            'badge'          => $b->badge,
            'features_json'  => $b->features_json ?? [],
        ])->values()->toArray(),
    ]);
@endphp

<div id="modal-branderp-store"
     x-data="erpStoreBuilder({{ $catalogJson }})"
     x-show="open"
     x-cloak
     @branderp-store-open.window="openModal()"
     @keydown.escape.window="closeModal()"
     @click.self="closeModal()"
     class="fixed inset-0 z-[99998] bg-black/85 backdrop-blur-md flex items-end sm:items-center justify-center p-0 sm:p-4 overflow-y-auto"
     dir="rtl">

    {{-- Inner Panel: Native Mobile Bottom Sheet / Centered Desktop Modal --}}
    <div class="relative w-full max-w-7xl glass-obsidian rounded-t-3xl sm:rounded-3xl border border-b-0 sm:border-b border-cyber-cyan/40 shadow-[0_8px_60px_rgba(0,245,212,0.15)] overflow-hidden max-h-[90dvh] sm:max-h-[92vh] flex flex-col"
         @click.stop
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-full sm:translate-y-6 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-full sm:translate-y-6 sm:scale-95">

        <!-- Mobile Drag Affordance Handle -->
        <div class="w-12 h-1.5 bg-slate-600 rounded-full mx-auto my-2.5 sm:hidden shrink-0"></div>

        {{-- ═══════════════ HEADER ═══════════════ --}}
        <div class="sticky top-0 z-20 bg-obsidian-base/95 backdrop-blur-xl border-b border-obsidian-border px-5 sm:px-7 py-3.5 sm:py-4 flex items-center justify-between gap-4 shrink-0">
            <div class="flex items-center gap-3">
                <span class="text-2xl p-1.5 rounded-xl bg-obsidian-surface border border-obsidian-border shrink-0">🏪</span>
                <div>
                    <h2 class="text-sm sm:text-base font-black text-white font-cairo leading-tight">BrandERP — متجر الإضافات وبناء النظام</h2>
                    <p class="text-[11px] sm:text-xs text-slate-400 font-cairo">اختر موديولاتك وميزاتك وابنِ نظامك المخصص بالكامل</p>
                </div>
            </div>
            <button type="button"
                    @click="closeModal()"
                    class="w-9 h-9 rounded-xl bg-obsidian-surface border border-obsidian-border hover:border-rose-500/50 text-slate-400 hover:text-rose-400 flex items-center justify-center transition-all shrink-0 cursor-pointer">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- ═══════════════ SCROLLABLE CONTENT BODY ═══════════════ --}}
        <div class="p-4 sm:p-6 space-y-5 flex-1 overflow-y-auto overscroll-contain pb-6">

            {{-- TIER 1 — CORE (always included) --}}
            <div class="rounded-2xl bg-gradient-to-l from-cyber-cyan/10 via-obsidian-card to-obsidian-surface border border-cyber-cyan/40 p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <span class="text-3xl shrink-0">🏪</span>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="text-sm font-black text-white font-cairo">النواة الأساسية (كاشير ومبيعات)</h3>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-cairo font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/40">مضاف تلقائياً ✓</span>
                        </div>
                        <p class="text-xs text-slate-300 font-cairo mt-0.5">كاشير مبيعات وفواتير — مخزون أساسي — تقارير يومية — باركود وطباعة حرارية</p>
                    </div>
                </div>
                <div class="text-left sm:text-right shrink-0">
                    <span class="text-xl font-black font-mono text-cyber-cyan">2,000</span>
                    <span class="text-xs text-slate-400 font-mono"> EGP</span>
                </div>
            </div>

            {{-- TAB NAVIGATION --}}
            <div class="flex items-center gap-1 bg-obsidian-surface rounded-2xl p-1 border border-obsidian-border">
                <button type="button"
                        @click="activeTab = 'modules'"
                        :class="activeTab === 'modules' ? 'bg-cyber-cyan text-obsidian-base font-black shadow-neon-cyan' : 'text-slate-400 hover:text-white'"
                        class="flex-1 py-2 sm:py-2.5 px-3 rounded-xl text-xs font-cairo font-bold transition-all cursor-pointer">
                    📦 الموديولات
                    <span class="mr-1 font-mono text-[10px]" x-text="`(${selectedModules.length}/${catalog.modules.length})`"></span>
                </button>
                <button type="button"
                        @click="activeTab = 'features'"
                        :class="activeTab === 'features' ? 'bg-cyber-cyan text-obsidian-base font-black shadow-neon-cyan' : 'text-slate-400 hover:text-white'"
                        class="flex-1 py-2 sm:py-2.5 px-3 rounded-xl text-xs font-cairo font-bold transition-all cursor-pointer">
                    ⚡ المميزات
                    <span class="mr-1 font-mono text-[10px]" x-text="`(${selectedFeatures.length}/${catalog.features.length})`"></span>
                </button>
                <button type="button"
                        @click="activeTab = 'bundles'"
                        :class="activeTab === 'bundles' ? 'bg-cyber-gold text-obsidian-base font-black' : 'text-slate-400 hover:text-white'"
                        class="flex-1 py-2 sm:py-2.5 px-3 rounded-xl text-xs font-cairo font-bold transition-all cursor-pointer">
                    🎁 الباقات الجاهزة
                </button>
            </div>

            {{-- TAB: MODULES --}}
            <div x-show="activeTab === 'modules'" x-cloak>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    <template x-for="mod in catalog.modules" :key="mod.slug">
                        <div @click="toggleModule(mod.slug)"
                             :class="isModuleSelected(mod.slug)
                                ? 'border-cyber-cyan bg-cyber-cyan/10 shadow-[0_0_20px_rgba(0,245,212,0.15)]'
                                : 'border-obsidian-border hover:border-cyber-cyan/60 bg-obsidian-card'"
                             class="rounded-2xl border p-3.5 sm:p-4 cursor-pointer transition-all duration-200 relative">
                            <div class="flex items-start justify-between gap-2 mb-2">
                                <span class="text-xl">📦</span>
                                <div x-show="isModuleSelected(mod.slug)"
                                     class="w-5 h-5 rounded-full bg-cyber-cyan flex items-center justify-center shrink-0">
                                    <svg class="w-3 h-3 text-obsidian-base" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                            </div>
                            <h4 class="text-xs font-bold text-white font-cairo leading-snug mb-2" x-text="mod.name"></h4>
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-black font-mono text-cyber-cyan" x-text="`${mod.price.toLocaleString()} EGP`"></span>
                                <span x-show="mod.badge"
                                      class="px-2 py-0.5 rounded-full text-[9px] font-cairo font-bold bg-cyber-gold/20 text-cyber-gold border border-cyber-gold/40"
                                      x-text="mod.badge"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- TAB: FEATURES --}}
            <div x-show="activeTab === 'features'" x-cloak class="space-y-4">
                {{-- Smart Upsell Recommender Bar --}}
                <div x-show="selectedModules.length > 0 && recommendedFeatures.length > 0"
                     class="p-3.5 rounded-xl bg-gradient-to-l from-cyber-cyan/15 via-obsidian-card to-obsidian-surface border border-cyber-cyan/50 shadow-sm">
                    <div class="flex items-start gap-2">
                        <span class="text-lg shrink-0 animate-pulse">💡</span>
                        <div class="flex-1">
                            <p class="text-xs font-bold text-cyber-cyan font-cairo mb-2">اقتراح مهندس المبيعات لتكوينك الحالي:</p>
                            <div class="flex flex-wrap gap-1.5">
                                <template x-for="slug in recommendedFeatures" :key="slug">
                                    <button type="button"
                                            @click="addFeatureBySlug(slug)"
                                            x-show="!isFeatureSelected(slug)"
                                            class="px-2.5 py-1 rounded-lg text-[10px] font-cairo font-bold bg-cyber-cyan/20 text-cyber-cyan border border-cyber-cyan/40 hover:bg-cyber-cyan/30 transition-all cursor-pointer"
                                            x-text="getFeatureName(slug) + ' — أضف'"></button>
                                    <span x-show="isFeatureSelected(slug)"
                                          class="px-2.5 py-1 rounded-lg text-[10px] font-cairo font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30"
                                          x-text="getFeatureName(slug) + ' ✓'"></span>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Search & Department Filter --}}
                <div class="flex flex-col sm:flex-row gap-2.5">
                    <input type="text"
                           x-model="searchQuery"
                           placeholder="ابحث في الميزات..."
                           class="flex-1 bg-obsidian-surface border border-obsidian-border text-xs text-slate-200 placeholder-slate-500 rounded-xl px-3.5 py-2.5 focus:border-cyber-cyan focus:outline-none font-cairo">
                    <select x-model="activeDepartment"
                            class="sm:w-56 bg-obsidian-surface border border-obsidian-border text-xs text-slate-200 rounded-xl px-3 py-2.5 focus:border-cyber-cyan focus:outline-none font-cairo">
                        <option value="all">كل الأقسام</option>
                        <template x-for="dept in departments" :key="dept">
                            <option :value="dept" x-text="dept"></option>
                        </template>
                    </select>
                </div>

                {{-- Features Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                    <template x-for="feat in filteredFeatures" :key="feat.slug">
                        <div @click="toggleFeature(feat.slug)"
                             :class="isFeatureSelected(feat.slug)
                                ? 'border-cyber-cyan bg-cyber-cyan/10 shadow-[0_0_12px_rgba(0,245,212,0.1)]'
                                : 'border-obsidian-border hover:border-cyber-cyan/40 bg-obsidian-card'"
                             class="rounded-xl border px-3 py-2.5 cursor-pointer transition-all duration-150 flex items-center justify-between gap-2">
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-bold text-white font-cairo leading-snug truncate" x-text="feat.name"></p>
                                <div class="flex items-center gap-1.5 mt-0.5 flex-wrap">
                                    <span class="text-[9px] font-cairo text-slate-400 truncate" x-text="feat.department"></span>
                                    <span x-show="feat.badge"
                                          class="text-[9px] font-cairo font-bold px-1.5 py-0.5 rounded bg-cyber-gold/20 text-cyber-gold border border-cyber-gold/30 shrink-0"
                                          x-text="feat.badge"></span>
                                </div>
                            </div>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <span class="text-xs font-black font-mono text-cyber-cyan" x-text="`${feat.price}`"></span>
                                <span class="text-[9px] text-slate-500 font-mono">EGP</span>
                                <div x-show="isFeatureSelected(feat.slug)"
                                     class="w-4 h-4 rounded-full bg-cyber-cyan flex items-center justify-center">
                                    <svg class="w-2.5 h-2.5 text-obsidian-base" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
                <p x-show="filteredFeatures.length === 0" class="text-center text-xs text-slate-500 font-cairo py-6">لا توجد ميزات مطابقة لبحثك</p>
            </div>

            {{-- TAB: BUNDLES --}}
            <div x-show="activeTab === 'bundles'" x-cloak>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <template x-for="bundle in catalog.bundles" :key="bundle.slug">
                        <div class="rounded-2xl bg-obsidian-card border border-obsidian-border hover:border-cyber-gold/60 p-4 sm:p-5 space-y-3 relative transition-all">
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 flex-wrap mb-1">
                                        <span x-show="bundle.badge"
                                              class="px-2.5 py-0.5 rounded-full text-[10px] font-cairo font-bold bg-cyber-gold/20 text-cyber-gold border border-cyber-gold/40"
                                              x-text="bundle.badge"></span>
                                    </div>
                                    <h4 class="text-sm font-black text-white font-cairo leading-snug" x-text="bundle.name"></h4>
                                </div>
                                <span class="text-2xl shrink-0">🎁</span>
                            </div>

                            <div class="space-y-1">
                                <div x-show="bundle.original_price" class="flex items-center gap-2">
                                    <span class="text-xs text-slate-500 line-through font-mono" x-text="`${bundle.original_price?.toLocaleString()} EGP`"></span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-rose-500/10 text-rose-400 border border-rose-500/30"
                                          x-text="bundle.original_price ? `وفر ${(bundle.original_price - bundle.price).toLocaleString()} EGP` : ''"></span>
                                </div>
                                <div class="flex items-baseline gap-1">
                                    <span class="text-2xl font-extrabold font-mono text-transparent bg-clip-text bg-gradient-to-r from-cyber-cyan to-cyber-gold"
                                          x-text="bundle.price.toLocaleString()"></span>
                                    <span class="text-xs text-slate-400 font-mono">EGP</span>
                                </div>
                            </div>

                            <ul class="space-y-1">
                                <template x-for="(feat, fi) in bundle.features_json" :key="fi">
                                    <li class="flex items-center gap-2 text-xs text-slate-300 font-cairo">
                                        <span class="w-1.5 h-1.5 rounded-full bg-cyber-cyan shrink-0"></span>
                                        <span x-text="feat"></span>
                                    </li>
                                </template>
                            </ul>

                            <button type="button"
                                    @click="bookBundle(bundle)"
                                    class="w-full py-2.5 rounded-xl btn-citadel-primary text-xs font-cairo font-bold text-center transition-all active:scale-95 cursor-pointer">
                                احجز هذه الباقة 🚀
                            </button>
                        </div>
                    </template>
                </div>
            </div>

        </div>

        {{-- ═══════════════ STICKY SAFE BOTTOM CART BAR (NEVER CUT OFF) ═══════════════ --}}
        <div class="sticky bottom-0 inset-x-0 bg-obsidian-base/95 backdrop-blur-xl border-t border-cyber-cyan/40 py-3 px-4 sm:px-6 z-30 shrink-0 safe-bottom shadow-[0_-10px_30px_rgba(0,0,0,0.85)]">
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                <div class="flex items-center justify-between sm:justify-start gap-2 text-xs font-cairo">
                    <div class="flex items-center gap-1.5">
                        <span class="text-slate-400">النواة:</span>
                        <span class="text-cyber-cyan font-mono font-bold">2,000 ج.م</span>
                        <span class="text-slate-600">+</span>
                        <span class="text-slate-300">
                            <span class="text-cyber-cyan font-bold font-mono" x-text="selectedModules.length"></span> مود
                        </span>
                        <span class="text-slate-600">+</span>
                        <span class="text-slate-300">
                            <span class="text-cyber-cyan font-bold font-mono" x-text="selectedFeatures.length"></span> ميزة
                        </span>
                    </div>
                    <div class="sm:hidden text-left">
                        <span class="text-sm font-black font-mono text-transparent bg-clip-text bg-gradient-to-r from-cyber-cyan to-cyber-gold"
                              x-text="`${cartTotal.toLocaleString()} EGP`"></span>
                    </div>
                </div>

                <div class="flex items-center gap-3 justify-between sm:justify-end">
                    <span class="hidden sm:inline-block text-base sm:text-lg font-extrabold font-mono text-transparent bg-clip-text bg-gradient-to-r from-cyber-cyan to-cyber-gold"
                          x-text="`${cartTotal.toLocaleString()} ج.م`"></span>

                    <button type="button"
                            @click="bookConfiguration()"
                            class="w-full sm:w-auto px-6 py-2.5 rounded-xl btn-citadel-primary text-xs font-cairo font-black transition-all active:scale-95 shadow-neon-cyan cursor-pointer text-center">
                        احجز تكوينك المخصص 🚀
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
function erpStoreBuilder(catalog) {
    return {
        open: false,
        catalog: catalog,
        activeTab: 'modules',
        searchQuery: '',
        activeDepartment: 'all',
        selectedModules: [],
        selectedFeatures: [],

        moduleRecommendations: {
            'erp-module-warehouses':         ['feat-expiry-alert', 'feat-auto-reorder', 'feat-multi-unit'],
            'erp-module-accounting':         ['feat-bank-reconciliation', 'feat-tax-filing-report', 'feat-profit-loss-live'],
            'erp-module-hr-payroll':         ['feat-gps-attendance', 'feat-payslip-whatsapp', 'feat-leave-portal'],
            'erp-module-manufacturing':      ['feat-bom-recipe', 'feat-production-order', 'feat-unit-cost-live'],
            'erp-module-whatsapp':           ['feat-whatsapp-invoice-pdf', 'feat-payslip-whatsapp', 'feat-birthday-auto'],
            'erp-module-ecommerce':          ['feat-ecommerce-sync', 'feat-product-catalog-link', 'feat-payment-gateway'],
            'erp-module-maintenance':        ['feat-serial-tracking', 'feat-quality-control', 'feat-contracts-docs'],
            'erp-module-restaurant':         ['feat-loyalty-points', 'feat-customer-screen', 'feat-sms-bulk'],
            'erp-module-ai-predictive':      ['feat-ai-sales-forecast', 'feat-dashboard-custom', 'feat-competitor-price'],
            'erp-module-etax':               ['feat-tax-filing-report', 'feat-invoice-profit-realtime', 'feat-export-excel-custom'],
            'erp-module-crm-advanced':       ['feat-customer-segmentation', 'feat-referral-program', 'feat-loyalty-points'],
            'erp-module-realtime-dashboard': ['feat-dashboard-custom', 'feat-branch-comparison', 'feat-abc-analysis'],
        },

        openModal() {
            this.open = true;
            if (window.SoulyUI) window.SoulyUI.lockScroll('branderp-store');
        },

        closeModal() {
            this.open = false;
            if (window.SoulyUI) window.SoulyUI.unlockScroll('branderp-store');
            window.dispatchEvent(new CustomEvent('branderp-store-close'));
        },

        get departments() {
            return [...new Set(this.catalog.features.map(f => f.department).filter(Boolean))];
        },

        get filteredFeatures() {
            return this.catalog.features.filter(f => {
                const matchesDept = this.activeDepartment === 'all' || f.department === this.activeDepartment;
                const query = this.searchQuery.trim().toLowerCase();
                const matchesSearch = !query || f.name.toLowerCase().includes(query) || (f.department || '').toLowerCase().includes(query);
                return matchesDept && matchesSearch;
            });
        },

        get recommendedFeatures() {
            const slugSet = new Set();
            this.selectedModules.forEach(modSlug => {
                const recs = this.moduleRecommendations[modSlug] || [];
                recs.forEach(s => slugSet.add(s));
            });
            return [...slugSet].slice(0, 3);
        },

        get cartTotal() {
            const moduleTotal = this.selectedModules.reduce((sum, slug) => {
                const mod = this.catalog.modules.find(m => m.slug === slug);
                return sum + (mod ? mod.price : 0);
            }, 0);
            const featureTotal = this.selectedFeatures.reduce((sum, slug) => {
                const feat = this.catalog.features.find(f => f.slug === slug);
                return sum + (feat ? feat.price : 0);
            }, 0);
            return 2000 + moduleTotal + featureTotal;
        },

        toggleModule(slug) {
            const idx = this.selectedModules.indexOf(slug);
            if (idx === -1) this.selectedModules.push(slug);
            else this.selectedModules.splice(idx, 1);
        },

        isModuleSelected(slug) {
            return this.selectedModules.includes(slug);
        },

        toggleFeature(slug) {
            const idx = this.selectedFeatures.indexOf(slug);
            if (idx === -1) this.selectedFeatures.push(slug);
            else this.selectedFeatures.splice(idx, 1);
        },

        isFeatureSelected(slug) {
            return this.selectedFeatures.includes(slug);
        },

        addFeatureBySlug(slug) {
            if (!this.isFeatureSelected(slug)) this.selectedFeatures.push(slug);
        },

        getFeatureName(slug) {
            const feat = this.catalog.features.find(f => f.slug === slug);
            return feat ? feat.name : slug;
        },

        bookBundle(bundle) {
            this.closeModal();
            this.$dispatch('open-express-booking', {
                serviceSlug: 'brand-erp',
                serviceName: 'نظام BrandERP المتكامل',
                packageSlug: bundle.slug,
                packageName: bundle.name,
                price: bundle.price,
                notes: `باقة جاهزة: ${bundle.name} — الإجمالي: ${bundle.price.toLocaleString()} EGP`,
            });
        },

        bookConfiguration() {
            const moduleNames = this.selectedModules.map(slug => {
                const m = this.catalog.modules.find(x => x.slug === slug);
                return m ? m.name : slug;
            });
            const featureNames = this.selectedFeatures.map(slug => {
                const f = this.catalog.features.find(x => x.slug === slug);
                return f ? f.name : slug;
            });

            const notes = [
                `النظام الأساسي: النواة الأساسية (كاشير ومبيعات) — 2,000 EGP`,
                moduleNames.length ? `الموديولات المختارة (${moduleNames.length}): ${moduleNames.join(' | ')}` : null,
                featureNames.length ? `الميزات المختارة (${featureNames.length}): ${featureNames.join(' | ')}` : null,
                `الإجمالي: ${this.cartTotal.toLocaleString()} EGP`,
            ].filter(Boolean).join('\n');

            this.closeModal();
            this.$dispatch('open-express-booking', {
                serviceSlug: 'brand-erp',
                serviceName: 'نظام BrandERP المتكامل',
                packageSlug: 'custom-build',
                packageName: `BrandERP مخصص — ${this.selectedModules.length} موديول + ${this.selectedFeatures.length} ميزة`,
                price: this.cartTotal,
                notes: notes,
            });
        },
    };
}
</script>
@endpush