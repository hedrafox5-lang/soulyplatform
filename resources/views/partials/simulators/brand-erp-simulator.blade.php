<div class="glass-obsidian rounded-3xl border border-cyber-cyan/40 p-5 sm:p-7 lg:p-8 space-y-6 shadow-[0_4px_30px_rgba(0,245,212,0.08)]"
     x-data="erpRoiSimulatorEngine()">
    
    <!-- Title inside dedicated card banner (Requirement 13) -->
    <div class="glass-obsidian rounded-2xl border border-obsidian-border/80 p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-sm">
        <div class="space-y-1 text-center sm:text-right">
            <h4 class="text-base sm:text-lg font-black text-white font-cairo flex items-center justify-center sm:justify-start gap-2.5">
                <span class="w-2.5 h-2.5 rounded-full bg-cyber-cyan shrink-0 animate-pulse"></span>
                <span>مستشار الباقة المثالية ومحاكي العائد الاستثماري (BrandERP ROI Recommender)</span>
            </h4>
            <p class="text-xs text-slate-400 font-cairo">
                اكتشف باقة BrandERP الأنسب لنشاطك وحجم مبيعاتك، وشاهد فورياً عدد الساعات المهدرة التي ستوفرها والتكلفة السنوية المحمية.
            </p>
        </div>
        <span class="text-xs font-cairo text-cyber-cyan font-bold bg-cyber-cyan/15 px-3.5 py-1.5 rounded-full border border-cyber-cyan/30 self-center sm:self-auto shrink-0 shadow-sm">
            حاسبة أرباح وتوفير لحظية 0ms
        </span>
    </div>

    <!-- Simulator Interactive Stage -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
        
        <!-- Controls Column (7 Cols on LG) -->
        <div class="lg:col-span-7 space-y-5">
            
            <!-- Activity Selector -->
            <div class="space-y-2">
                <label class="text-xs font-bold text-white font-cairo block text-center sm:text-right">
                    1. قطاع ونشاط مؤسستك:
                </label>
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

            <!-- Branches Slider -->
            <div class="space-y-2">
                <div class="flex items-center justify-between text-xs font-cairo">
                    <span class="text-slate-300 font-bold">2. عدد الفروع والمستودعات:</span>
                    <span class="text-cyber-cyan font-mono font-bold text-sm bg-obsidian-surface px-2.5 py-0.5 rounded-lg border border-cyber-cyan/30" 
                          x-text="`${branches} ${branches == 1 ? 'فرع رئيسي' : 'فروع ومخازن'}`"></span>
                </div>
                <input type="range" 
                       min="1" 
                       max="15" 
                       step="1" 
                       x-model.number="branches" 
                       class="w-full h-2 bg-obsidian-surface rounded-lg appearance-none cursor-pointer accent-cyber-cyan">
                <div class="flex justify-between text-[10px] font-mono text-slate-500">
                    <span>1 فرع</span>
                    <span>5 فروع</span>
                    <span>15 فرع ومستودع</span>
                </div>
            </div>

            <!-- Staff / Cashiers Slider -->
            <div class="space-y-2">
                <div class="flex items-center justify-between text-xs font-cairo">
                    <span class="text-slate-300 font-bold">3. عدد الكاشيرات والمستخدمين النشطين:</span>
                    <span class="text-cyber-gold font-mono font-bold text-sm bg-obsidian-surface px-2.5 py-0.5 rounded-lg border border-cyber-gold/30" 
                          x-text="`${staff} مستخدم وكاشير`"></span>
                </div>
                <input type="range" 
                       min="1" 
                       max="35" 
                       step="1" 
                       x-model.number="staff" 
                       class="w-full h-2 bg-obsidian-surface rounded-lg appearance-none cursor-pointer accent-cyber-gold">
                <div class="flex justify-between text-[10px] font-mono text-slate-500">
                    <span>1 كاشير</span>
                    <span>10 مستخدمين</span>
                    <span>35 مستخدم</span>
                </div>
            </div>

            <!-- Estimated Monthly Order Volume Slider -->
            <div class="space-y-2">
                <div class="flex items-center justify-between text-xs font-cairo">
                    <span class="text-slate-300 font-bold">4. حجم الفواتير والمعاملات الشهرية التقريبي:</span>
                    <span class="text-emerald-400 font-mono font-bold text-sm bg-obsidian-surface px-2.5 py-0.5 rounded-lg border border-emerald-500/30" 
                          x-text="`${orders.toLocaleString()} معاملة/شهر`"></span>
                </div>
                <input type="range" 
                       min="300" 
                       max="15000" 
                       step="300" 
                       x-model.number="orders" 
                       class="w-full h-2 bg-obsidian-surface rounded-lg appearance-none cursor-pointer accent-emerald-400">
                <div class="flex justify-between text-[10px] font-mono text-slate-500">
                    <span>300 فاتورة</span>
                    <span>5,000 فاتورة</span>
                    <span>15,000+ فاتورة</span>
                </div>
            </div>

        </div>

        <!-- Calculated Live Metrics & Recommendation Card (5 Cols on LG) -->
        <div class="lg:col-span-5 p-5 sm:p-6 rounded-2xl bg-gradient-to-br from-obsidian-surface via-obsidian-card to-obsidian-surface border border-cyber-cyan/50 space-y-5 text-center sm:text-right shadow-lg">
            
            <div class="border-b border-obsidian-border/80 pb-3 flex items-center justify-between">
                <span class="text-xs font-cairo text-slate-400 font-bold">العائد الاستثماري المتوقع</span>
                <span class="text-[10px] font-cairo font-bold text-cyber-cyan px-2 py-0.5 rounded bg-cyber-cyan/10 border border-cyber-cyan/30">
                    مؤشرات واقعية دقيقة
                </span>
            </div>

            <!-- Big Metric Blocks -->
            <div class="grid grid-cols-2 gap-3">
                <div class="p-3.5 rounded-xl bg-obsidian-base/80 border border-obsidian-border text-center">
                    <span class="text-[11px] text-slate-400 font-cairo block mb-1">ساعات عمل توفرها شهرياً</span>
                    <span class="text-xl sm:text-2xl font-black font-mono text-cyber-cyan" x-text="`${calculatedHoursSaved}h`"></span>
                    <span class="text-[10px] text-slate-500 font-cairo block mt-0.5">من الإدخال اليدوي المكرر</span>
                </div>

                <div class="p-3.5 rounded-xl bg-obsidian-base/80 border border-obsidian-border text-center">
                    <span class="text-[11px] text-slate-400 font-cairo block mb-1">هدر مالي وجردي تحميه سنوياً</span>
                    <span class="text-xl sm:text-2xl font-black font-mono text-cyber-gold" x-text="`${calculatedAnnualSavings.toLocaleString()} EGP`"></span>
                    <span class="text-[10px] text-slate-500 font-cairo block mt-0.5">فروقات جرد وسرقات وأخطاء</span>
                </div>
            </div>

            <!-- Recommended Package Banner -->
            <div class="p-3.5 rounded-xl bg-gradient-to-r from-cyber-cyan/10 via-obsidian-card to-cyber-gold/10 border border-cyber-cyan/40 space-y-1 text-center sm:text-right">
                <div class="flex items-center justify-center sm:justify-start gap-2">
                    <span class="w-2 h-2 rounded-full bg-cyber-cyan"></span>
                    <span class="text-[11px] font-cairo font-bold text-slate-300">الباقة الهندسية الأنسب لنشاطك:</span>
                </div>
                <h5 class="text-sm sm:text-base font-extrabold text-white font-cairo" x-text="recommendedPackage.name"></h5>
                <p class="text-[11px] text-slate-400 font-cairo" x-text="recommendedPackage.reason"></p>
                <div class="pt-1 flex items-baseline justify-center sm:justify-start gap-1 font-mono">
                    <span class="text-base font-bold text-cyber-cyan" x-text="`${recommendedPackage.price.toLocaleString()} EGP`"></span>
                    <span class="text-[10px] text-slate-400" x-text="`/${recommendedPackage.unit}`"></span>
                </div>
            </div>

            <!-- Highlighted Modules Included -->
            <div class="space-y-1.5 text-center sm:text-right">
                <span class="text-[11px] font-cairo font-bold text-slate-400 block">الموديولات المفعلة لنشاطك:</span>
                <div class="flex flex-wrap items-center justify-center sm:justify-start gap-1.5">
                    <template x-for="(m, mIdx) in recommendedPackage.modules" :key="mIdx">
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-cairo font-semibold bg-obsidian-base border border-obsidian-border text-slate-200" 
                              x-text="m"></span>
                    </template>
                </div>
            </div>

            <!-- 1-Click Direct Booking CTA -->
            <div class="pt-2">
                @auth
                    <a :href="`{{ route('portal.bookings') }}?service=brand-erp&package=${recommendedPackage.slug}&branches=${branches}&staff=${staff}`" 
                       class="w-full py-3 rounded-xl btn-citadel-primary text-xs font-cairo font-bold text-center block shadow-md transition-all active:scale-95">
                        احجز باقة <span x-text="recommendedPackage.name.split(' ')[1] || 'BrandERP'"></span> فوراً عبر المحفظة ⇥
                    </a>
                @else
                    <a :href="`{{ route('portal.bookings') }}?service=brand-erp&package=${recommendedPackage.slug}&branches=${branches}&staff=${staff}`" 
                       @click.prevent="$dispatch('open-express-booking', {
                           serviceSlug: 'brand-erp',
                           serviceName: 'نظام BrandERP المتكامل',
                           packageSlug: recommendedPackage.slug,
                           packageName: recommendedPackage.name,
                           price: recommendedPackage.price
                       })"
                       class="w-full py-3 rounded-xl btn-citadel-primary text-xs font-cairo font-bold text-center block shadow-md transition-all active:scale-95">
                        احجز باقة <span x-text="recommendedPackage.name.split(' ')[1] || 'BrandERP'"></span> فوراً عبر المحفظة ⇥
                    </a>
                @endauth
            </div>

        </div>

    </div>

</div>

<script>
    if (typeof window.erpRoiSimulatorEngine !== 'function') {
        window.erpRoiSimulatorEngine = function erpRoiSimulatorEngine() {
            return {
                activity: 'retail',
                branches: 1,
                staff: 2,
                orders: 1500,

                get calculatedHoursSaved() {
                    // Formula: base 25 hrs + 14 hrs per branch + 6 hrs per staff user + volume factor
                    return Math.round(25 + (this.branches * 14) + (this.staff * 6) + (this.orders / 300));
                },

                get calculatedAnnualSavings() {
                    // Estimated financial leakage prevented annually (inventory variance, manual double entry, lost tickets)
                    const base = this.branches * 18000;
                    const volumeFactor = this.orders * 4.5;
                    const manufacturingBonus = this.activity === 'manufacturing' ? 35000 : 0;
                    return Math.round(base + volumeFactor + manufacturingBonus);
                },

                get recommendedPackage() {
                    if (this.activity === 'manufacturing' || this.branches >= 6 || this.staff >= 15) {
                        return {
                            name: 'باقة المصانع والمؤسسات (Enterprise)',
                            slug: 'enterprise',
                            price: 26500,
                            unit: 'سنوياً',
                            reason: 'تتضمن وحدة التصنيع وهدر المواد (BOM) والمخازن غير المحدودة والربط المحاسبي الكامل.',
                            modules: ['كاشير POS السريع', 'الفاتورة الإلكترونية ETA', 'وحدة التصنيع وBOM', 'المخازن المركزية', 'شجرة الحسابات', 'الربط البرمجي API']
                        };
                    }
                    if (this.branches >= 2 || this.staff >= 4 || this.activity === 'multi_branch') {
                        return {
                            name: 'باقة النمو للشركات (Growth Pro)',
                            slug: 'growth',
                            price: 1850,
                            unit: 'شهرياً',
                            reason: 'مثالية للربط بين حتى 5 فروع، شجرة حسابات متقدمة، موديول الأقساط وشؤون الموظفين HR.',
                            modules: ['كاشير POS السريع', 'الفاتورة والإيصال ETA', 'ربط حتى 5 فروع', 'الأقساط والمديونيات', 'شؤون الموظفين HR', 'تطبيق الإدارة']
                        };
                    }
                    return {
                        name: 'باقة الانطلاق السحابي (Starter)',
                        slug: 'starter',
                        price: 850,
                        unit: 'شهرياً',
                        reason: 'الحل الأمثل للأنشطة الناشئة والمتاجر: كاشير ذكي، جرد الباركود، والربط الضريبي المعتمد.',
                        modules: ['كاشير POS أوفلاين/أونلاين', 'الفاتورة والإيصال ETA', 'إدارة المخزن والباركود', 'تقارير الأرباح والخزينة', 'نسخ احتياطي يومي']
                    };
                }
            };
        };
    }
</script>