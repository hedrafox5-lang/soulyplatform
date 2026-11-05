{{--
    BrandERP ROI Recommender — Glassmorphic Popup Modal
    Triggered by: @branderp-roi-open.window Alpine event
--}}
<div id="modal-branderp-roi"
     x-data="{ 
         open: false,
         openModal() {
             this.open = true;
             if (window.SoulyUI) window.SoulyUI.lockScroll('branderp-roi');
         },
         closeModal() {
             this.open = false;
             if (window.SoulyUI) window.SoulyUI.unlockScroll('branderp-roi');
             window.dispatchEvent(new CustomEvent('branderp-roi-close'));
         }
     }"
     x-show="open"
     x-cloak
     @branderp-roi-open.window="openModal()"
     @keydown.escape.window="closeModal()"
     @click.self="closeModal()"
     class="fixed inset-0 z-[99997] bg-black/85 backdrop-blur-md flex items-end sm:items-center justify-center p-0 sm:p-4 overflow-y-auto"
     dir="rtl">

    <div class="relative w-full max-w-4xl glass-obsidian rounded-t-3xl sm:rounded-3xl border border-b-0 sm:border-b border-cyber-cyan/40 shadow-[0_8px_60px_rgba(0,245,212,0.15)] overflow-hidden max-h-[90dvh] sm:max-h-[90vh] flex flex-col my-0 sm:my-auto"
         @click.stop
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-full sm:translate-y-6 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-full sm:translate-y-6 sm:scale-95">

        <!-- Mobile Drag Affordance Handle -->
        <div class="w-12 h-1.5 bg-slate-600 rounded-full mx-auto my-2.5 sm:hidden shrink-0"></div>

        <!-- Header -->
        <div class="sticky top-0 z-20 bg-obsidian-base/95 backdrop-blur-xl border-b border-obsidian-border p-4 sm:p-5 flex items-center justify-between gap-3 shrink-0">
            <div class="flex items-center gap-2.5">
                <span class="w-2.5 h-2.5 rounded-full bg-cyber-cyan shrink-0 animate-pulse"></span>
                <div>
                    <h4 class="text-sm sm:text-base font-black text-white font-cairo">مستشار الباقة ومحاكي العائد (ROI)</h4>
                    <span class="text-[10px] text-slate-400 font-cairo hidden sm:inline">حاسبة فورية لتوفير الوقت والهدر المالي</span>
                </div>
            </div>

            <button type="button"
                    @click="closeModal()"
                    class="w-8 h-8 rounded-xl bg-obsidian-surface border border-obsidian-border hover:border-rose-500/50 text-slate-400 hover:text-rose-400 flex items-center justify-center transition-all shrink-0 cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- ROI Simulator Engine Body --}}
        <div x-data="erpRoiSimulatorEngine()" class="p-4 sm:p-7 space-y-6 flex-1 overflow-y-auto overscroll-contain safe-bottom">

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

                {{-- Controls Column --}}
                <div class="lg:col-span-7 space-y-4">
                    {{-- Activity Selector --}}
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-white font-cairo block text-right">1. قطاع ونشاط مؤسستك:</label>
                        <select x-model="activity"
                                class="w-full bg-obsidian-surface border border-obsidian-border text-xs text-slate-200 rounded-xl p-3 focus:border-cyber-cyan focus:outline-none font-cairo">
                            <option value="retail">محل تجاري / كاشير / سوبر ماركت / ملابس وأحذية</option>
                            <option value="multi_branch">سلسلة فروع تجارية / تجارة جملة ومستودعات</option>
                            <option value="restaurants">مطاعم / كافيهات / نقاط بيع دليفري وصالات</option>
                            <option value="pharmacy">صيدليات ومستلزمات طبية وتجميل</option>
                            <option value="manufacturing">مصانع وورش تصنيع وإنتاج وتكاليف (BOM)</option>
                            <option value="automotive">معارض سيارات ومراكز صيانة وقطع غيار</option>
                            <option value="contracting">شركات مقاولات ومشاريع وتشطيبات</option>
                            <option value="services">خدمات لوجستية وشحن ومؤسسات خدمية</option>
                        </select>
                    </div>

                    {{-- Branches Slider --}}
                    <div class="space-y-1.5 p-3 rounded-2xl bg-obsidian-surface/60 border border-obsidian-border">
                        <div class="flex items-center justify-between text-xs font-cairo">
                            <span class="text-slate-300 font-bold">2. عدد الفروع والمستودعات:</span>
                            <span class="text-cyber-cyan font-mono font-bold text-xs bg-obsidian-base px-2.5 py-0.5 rounded-lg border border-cyber-cyan/30"
                                  x-text="`${branches} ${branches == 1 ? 'فرع رئيسي' : 'فروع ومخازن'}`"></span>
                        </div>
                        <input type="range" min="1" max="15" step="1" x-model.number="branches"
                               class="w-full h-2 bg-obsidian-base rounded-lg appearance-none cursor-pointer accent-cyber-cyan">
                        <div class="flex justify-between text-[10px] font-mono text-slate-500">
                            <span>1 فرع</span><span>15 فرع ومستودع</span>
                        </div>
                    </div>

                    {{-- Staff Slider --}}
                    <div class="space-y-1.5 p-3 rounded-2xl bg-obsidian-surface/60 border border-obsidian-border">
                        <div class="flex items-center justify-between text-xs font-cairo">
                            <span class="text-slate-300 font-bold">3. عدد الكاشيرات والمستخدمين النشطين:</span>
                            <span class="text-cyber-gold font-mono font-bold text-xs bg-obsidian-base px-2.5 py-0.5 rounded-lg border border-cyber-gold/30"
                                  x-text="`${staff} مستخدم وكاشير`"></span>
                        </div>
                        <input type="range" min="1" max="35" step="1" x-model.number="staff"
                               class="w-full h-2 bg-obsidian-base rounded-lg appearance-none cursor-pointer accent-cyber-gold">
                        <div class="flex justify-between text-[10px] font-mono text-slate-500">
                            <span>1 كاشير</span><span>35 مستخدم</span>
                        </div>
                    </div>

                    {{-- Orders Volume Slider --}}
                    <div class="space-y-1.5 p-3 rounded-2xl bg-obsidian-surface/60 border border-obsidian-border">
                        <div class="flex items-center justify-between text-xs font-cairo">
                            <span class="text-slate-300 font-bold">4. حجم الفواتير والمعاملات الشهرية:</span>
                            <span class="text-emerald-400 font-mono font-bold text-xs bg-obsidian-base px-2.5 py-0.5 rounded-lg border border-emerald-500/30"
                                  x-text="`${orders.toLocaleString()} معاملة/شهر`"></span>
                        </div>
                        <input type="range" min="300" max="15000" step="300" x-model.number="orders"
                               class="w-full h-2 bg-obsidian-base rounded-lg appearance-none cursor-pointer accent-emerald-400">
                        <div class="flex justify-between text-[10px] font-mono text-slate-500">
                            <span>300 فاتورة</span><span>15,000+ فاتورة</span>
                        </div>
                    </div>
                </div>

                {{-- Recommendation Card --}}
                <div class="lg:col-span-5 p-4 sm:p-6 rounded-2xl bg-gradient-to-br from-obsidian-surface via-obsidian-card to-obsidian-surface border border-cyber-cyan/50 space-y-4 text-right shadow-lg">
                    <div class="grid grid-cols-2 gap-2.5">
                        <div class="p-3 rounded-xl bg-obsidian-base/80 border border-obsidian-border text-center">
                            <span class="text-[10px] text-slate-400 font-cairo block mb-1">ساعات توفرها شهرياً</span>
                            <span class="text-lg sm:text-2xl font-black font-mono text-cyber-cyan" x-text="`${calculatedHoursSaved}h`"></span>
                        </div>
                        <div class="p-3 rounded-xl bg-obsidian-base/80 border border-obsidian-border text-center">
                            <span class="text-[10px] text-slate-400 font-cairo block mb-1">هدر تحميه سنوياً</span>
                            <span class="text-lg sm:text-2xl font-black font-mono text-cyber-gold" x-text="`${calculatedAnnualSavings.toLocaleString()} EGP`"></span>
                        </div>
                    </div>

                    <div class="p-3.5 rounded-xl bg-obsidian-base/90 border border-cyber-cyan/40 space-y-1 text-right">
                        <span class="text-[11px] font-cairo font-bold text-cyber-cyan block">الباقة الأنسب لنشاطك:</span>
                        <h5 class="text-sm font-extrabold text-white font-cairo" x-text="recommendedPackage.name"></h5>
                        <p class="text-[11px] text-slate-400 font-cairo" x-text="recommendedPackage.reason"></p>
                        <div class="pt-1 flex items-baseline gap-1 font-mono">
                            <span class="text-base font-bold text-cyber-cyan" x-text="`${recommendedPackage.price.toLocaleString()} EGP`"></span>
                            <span class="text-[10px] text-slate-400" x-text="`/${recommendedPackage.unit}`"></span>
                        </div>
                    </div>

                    <div class="pt-1">
                        <button type="button"
                                @click="closeModal(); $dispatch('open-express-booking', {
                                    serviceSlug: 'brand-erp',
                                    serviceName: 'نظام BrandERP المتكامل',
                                    packageSlug: recommendedPackage.slug,
                                    packageName: recommendedPackage.name,
                                    price: recommendedPackage.price
                                })"
                                class="w-full py-3 rounded-xl btn-citadel-primary text-xs font-cairo font-bold text-center block shadow-md transition-all active:scale-95 cursor-pointer">
                            احجز باقة <span x-text="recommendedPackage.name.split(' ')[1] || 'BrandERP'"></span> فوراً ⇥
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>