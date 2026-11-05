<div class="glass-obsidian rounded-3xl border border-emerald-500/30 p-5 sm:p-7 lg:p-8 space-y-6 shadow-[0_4px_30px_rgba(16,185,129,0.08)]"
     x-data="maintenanceBenchmarkEngine()">
    
    <!-- Title inside dedicated card banner (Requirement 13) -->
    <div class="glass-obsidian rounded-2xl border border-obsidian-border/80 p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-sm">
        <div class="space-y-1 text-center sm:text-right">
            <h4 class="text-base sm:text-lg font-black text-white font-cairo flex items-center justify-center sm:justify-start gap-2.5">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 shrink-0 animate-pulse"></span>
                <span>محاكي الهبوط الحراري وحاسبة الزيارة الميدانية (حاسبة التكلفة والضمان الفوري)</span>
            </h4>
            <p class="text-xs text-slate-400 font-cairo">
                احسب تكلفة زيارتك الهندسية والضمان بدقة (400 ج.م/ساعة ثابتة بمقرك)، مع خصم 50% للزيارة الأولى بمقرك أو خصم الباقة المعتمد، وشاهد الفارق بين الاختناق الحراري والاستقرار بعد الصيانة.
                <span class="text-amber-400 block font-bold mt-1">⚠️ تنبيه هندسي: يُستثنى من الخدمة تماماً إصلاح البورد والدوائر الإلكترونية الدقيقة.</span>
            </p>
        </div>
        <span class="text-xs font-cairo text-emerald-400 font-bold bg-emerald-500/15 px-3.5 py-1.5 rounded-full border border-emerald-500/30 self-center sm:self-auto shrink-0 shadow-sm">
            400 ج.م/ساعة · حد أدنى 3 ساعات
        </span>
    </div>

    <!-- Simulator Interactive Stage -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
        
        <!-- Thermal Benchmark Slider Stage (7 Cols on LG) -->
        <div class="lg:col-span-7 space-y-5">
            
            <!-- Thermal Comparison Box -->
            <div class="p-4 rounded-2xl bg-obsidian-surface border border-obsidian-border space-y-3">
                <div class="flex items-center justify-between text-xs font-cairo">
                    <span class="text-slate-200 font-bold">درجة حرارة المعالج وكارت الشاشة الحالية أثناء الضغط:</span>
                    <span class="font-mono font-bold text-sm px-2.5 py-0.5 rounded-lg border"
                          :class="currentTemp >= 88 ? 'bg-rose-950/40 text-rose-400 border-rose-500/40' : (currentTemp >= 78 ? 'bg-amber-950/40 text-cyber-gold border-amber-500/40' : 'bg-emerald-950/40 text-emerald-400 border-emerald-500/40')"
                          x-text="`${currentTemp}°C`"></span>
                </div>

                <input type="range" 
                       min="65" 
                       max="99" 
                       step="1" 
                       x-model.number="currentTemp" 
                       class="w-full h-2 bg-obsidian-base rounded-lg appearance-none cursor-pointer accent-emerald-400">
                
                <div class="flex justify-between text-[10px] font-mono text-slate-500">
                    <span>65°C (حرارة مثالية)</span>
                    <span>82°C (بداية السخونة)</span>
                    <span class="text-rose-400 font-bold">99°C (اختناق حراري حاد)</span>
                </div>

                <!-- Live Before vs After Thermal Gauge -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                    <!-- Before Souly -->
                    <div class="p-3.5 rounded-xl bg-rose-950/20 border border-rose-900/40 space-y-1.5 text-center sm:text-right">
                        <div class="flex items-center justify-between text-xs text-rose-400 font-cairo font-bold">
                            <span>الوضع التقليدي (قبل سولي):</span>
                            <span class="font-mono" x-text="`${currentTemp}°C`"></span>
                        </div>
                        <ul class="text-[11px] text-slate-300 font-cairo space-y-1">
                            <li class="flex items-center gap-1.5">
                                <span class="text-rose-400 font-bold">✕</span>
                                <span x-text="currentTemp >= 88 ? 'اختناق حراري حاد (Thermal Throttling)' : 'هبوط ملحوظ في الأداء واستنزاف للمروحة'"></span>
                            </li>
                            <li class="flex items-center gap-1.5">
                                <span class="text-rose-400 font-bold">✕</span>
                                <span x-text="currentTemp >= 88 ? 'هبوط فريمات الريندر والألعاب حتى 42%' : 'معجون تجاري جاف يسرب الحرارة'"></span>
                            </li>
                        </ul>
                    </div>

                    <!-- After Souly -->
                    <div class="p-3.5 rounded-xl bg-emerald-950/20 border border-emerald-900/40 space-y-1.5 text-center sm:text-right shadow-[0_2px_14px_rgba(16,185,129,0.12)]">
                        <div class="flex items-center justify-between text-xs text-emerald-400 font-cairo font-bold">
                            <span>بعد هندسة سولي بمقرك:</span>
                            <span class="font-mono text-cyber-cyan" x-text="`${afterTemp}°C`"></span>
                        </div>
                        <ul class="text-[11px] text-slate-300 font-cairo space-y-1">
                            <li class="flex items-center gap-1.5">
                                <span class="text-emerald-400 font-bold">✓</span>
                                <span>انخفاض فوري بمقدار <strong class="text-cyber-cyan font-mono" x-text="`-${tempDrop}°C`"></strong> في الحرارة</span>
                            </li>
                            <li class="flex items-center gap-1.5">
                                <span class="text-emerald-400 font-bold">✓</span>
                                <span>معجون Arctic MX-6 / Grizzly فائق + هدوء تام</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Package Selection Radios -->
            <div class="space-y-2">
                <label class="text-xs font-bold text-white font-cairo block text-center sm:text-right">
                    اختر باقة الصيانة الميدانية بمقرك:
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                    <template x-for="pkg in packages" :key="pkg.slug">
                        <button type="button" 
                                @click="activePkgSlug = pkg.slug"
                                class="p-3 rounded-xl border text-center sm:text-right transition-all font-cairo"
                                :class="activePkgSlug === pkg.slug ? 'bg-obsidian-surface border-emerald-400 text-white shadow-neon-cyan' : 'bg-obsidian-base border-obsidian-border text-slate-400 hover:text-white'">
                            <span class="text-xs font-bold block" x-text="pkg.name"></span>
                            <div class="flex items-center justify-center sm:justify-start gap-1 font-mono text-[11px] mt-1">
                                <span class="text-slate-500 line-through" x-text="`${pkg.originalPrice} EGP`"></span>
                                <span class="text-emerald-400 font-bold" x-text="`${pkg.price} EGP/ساعة`"></span>
                            </div>
                        </button>
                    </template>
                </div>
            </div>

            <!-- Hours Slider -->
            <div class="space-y-2">
                <div class="flex items-center justify-between text-xs font-cairo">
                    <span class="text-slate-300 font-bold">عدد ساعات الزيارة الهندسية بمقرك:</span>
                    <span class="text-cyber-cyan font-mono font-bold text-sm bg-obsidian-surface px-2.5 py-0.5 rounded-lg border border-cyber-cyan/30" 
                          x-text="`${hours} ساعات عمل متواصلة`"></span>
                </div>
                <input type="range" 
                       min="3" 
                       max="8" 
                       step="1" 
                       x-model.number="hours" 
                       class="w-full h-2 bg-obsidian-surface rounded-lg appearance-none cursor-pointer accent-cyber-cyan">
                <div class="flex justify-between text-[10px] font-mono text-slate-500">
                    <span>3 ساعات (الحد الأدنى)</span>
                    <span>5 ساعات</span>
                    <span>8 ساعات (يوم هندسي كامل)</span>
                </div>
            </div>

        </div>

        <!-- Calculated 50% Discount Breakdown & Booking Card (5 Cols on LG) -->
        <div class="lg:col-span-5 p-5 sm:p-6 rounded-2xl bg-gradient-to-br from-obsidian-surface via-obsidian-card to-obsidian-surface border border-emerald-500/40 space-y-5 text-center sm:text-right shadow-lg">
            
            <div class="border-b border-obsidian-border/80 pb-3 flex items-center justify-between">
                <span class="text-xs font-cairo text-slate-400 font-bold">فاتورة الصيانة بعد الخصم المعتمد</span>
                <span class="text-xs font-mono font-bold text-emerald-400" x-text="`ضمان ${selectedPackage.warrantyDays} يوماً`"></span>
            </div>

            <!-- Price Breakdown Rows -->
            <div class="space-y-2.5 font-mono text-xs">
                <div class="flex justify-between text-slate-400">
                    <span class="font-cairo">سعر الساعة الأصلي:</span>
                    <span x-text="`${selectedPackage.originalPrice} EGP`"></span>
                </div>
                <div class="flex justify-between text-emerald-400">
                    <span class="font-cairo">سعر الساعة بعد خصم 50%:</span>
                    <span class="font-bold" x-text="`${selectedPackage.price} EGP`"></span>
                </div>
                <div class="flex justify-between text-slate-400">
                    <span class="font-cairo">إجمالي عدد الساعات:</span>
                    <span x-text="`${hours} ساعات`"></span>
                </div>
                <div class="flex justify-between text-rose-400 border-t border-obsidian-border/60 pt-2 font-bold">
                    <span class="font-cairo">إجمالي ما وفرته بخصم الزيارة الأولى (50%):</span>
                    <span x-text="`-${savingsTotal.toLocaleString()} EGP`"></span>
                </div>
                <div class="border-t border-obsidian-border pt-3 flex justify-between items-baseline text-white">
                    <span class="text-sm font-bold font-cairo">الإجمالي النهائي للزيارة:</span>
                    <div class="text-right">
                        <span class="text-2xl sm:text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 via-cyber-cyan to-cyber-gold" 
                              x-text="`${calculatedTotal.toLocaleString()} EGP`"></span>
                    </div>
                </div>
            </div>

            <!-- Warranty & Service Promise -->
            <div class="p-3 rounded-xl bg-obsidian-base border border-obsidian-border space-y-1 text-center sm:text-right">
                <div class="flex items-center justify-center sm:justify-start gap-2 text-xs font-cairo font-bold text-slate-200">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    <span>ضمان سولي المعتمد للاستقرار الحراري:</span>
                </div>
                <p class="text-[11px] text-slate-400 font-cairo">
                    صيانة بمقرك وأمام عينيك مع اختبارات ضغط شاقة (Stress Testing) وضمان معتمد لمدة <span class="text-emerald-400 font-bold" x-text="`${selectedPackage.warrantyDays} يوماً`"></span>.
                </p>
            </div>

            <!-- Pricing & Scope Policy -->
            <div class="p-3 rounded-xl bg-cyber-gold/10 border border-cyber-gold/30 text-right space-y-1">
                <div class="flex items-center gap-1.5 text-xs font-cairo font-bold text-cyber-gold">
                    <span>⚡</span>
                    <span>سياسة التسعير ونطاق الخدمة الهندسية:</span>
                </div>
                <p class="text-[11px] text-slate-300 font-cairo leading-relaxed">
                    • تسعير الساعة: <strong class="text-cyber-gold font-mono">400 ج.م/ساعة</strong> — الحد الأدنى للزيارة 3 ساعات متواصلة (1,200 ج.م).<br>
                    • لا يوجد أي خصم على الساعات الإضافية.<br>
                    • الخدمة لا تشمل صيانة قطع الماذر بورد الهاردوير (Motherboard hardware repairs).
                </p>
            </div>

            <!-- 1-Click Direct Booking CTA -->
            <div class="pt-2">
                @auth
                    <a :href="`{{ route('portal.bookings') }}?service=maintenance&package=${selectedPackage.slug}&hours=${hours}`" 
                       class="w-full py-3 rounded-xl btn-citadel-primary text-xs font-cairo font-bold text-center block shadow-md transition-all active:scale-95">
                        احجز زيارة الصيانة بمقرك بخصم 50% فوراً ⇥
                    </a>
                @else
                    <a :href="`{{ route('portal.bookings') }}?service=maintenance&package=${selectedPackage.slug}&hours=${hours}`" 
                       @click.prevent="$dispatch('open-express-booking', {
                           serviceSlug: 'maintenance',
                           serviceName: 'خدمات الصيانة الهندسية الميدانية',
                           packageSlug: selectedPackage.slug,
                           packageName: selectedPackage.name,
                           price: calculatedTotal,
                           hours: hours
                       })"
                       class="w-full py-3 rounded-xl btn-citadel-primary text-xs font-cairo font-bold text-center block shadow-md transition-all active:scale-95">
                        احجز زيارة الصيانة بمقرك بخصم 50% فوراً ⇥
                    </a>
                @endauth
            </div>

        </div>

    </div>

</div>

<script>
    function maintenanceBenchmarkEngine() {
        return {
            currentTemp: 94,
            activePkgSlug: 'promax',
            hours: 3,

            packages: [
                {
                    slug: 'revive',
                    name: 'Souly Revive (للأجهزة اليومية)',
                    originalPrice: 800,
                    price: 400,
                    warrantyDays: 14
                },
                {
                    slug: 'promax',
                    name: 'Souly ProMax (لمحطات العمل والماك)',
                    originalPrice: 1500,
                    price: 750,
                    warrantyDays: 30
                },
                {
                    slug: 'elite-vip',
                    name: 'Souly Elite VIP (للأجهزة المتعددة)',
                    originalPrice: 2000,
                    price: 1000,
                    warrantyDays: 60
                }
            ],

            get afterTemp() {
                // Guaranteed drop to between 66C and 70C
                return Math.max(66, Math.round(this.currentTemp * 0.72));
            },

            get tempDrop() {
                return this.currentTemp - this.afterTemp;
            },

            get selectedPackage() {
                return this.packages.find(p => p.slug === this.activePkgSlug) || this.packages[1];
            },

            get calculatedTotal() {
                return this.selectedPackage.price * this.hours;
            },

            get savingsTotal() {
                return (this.selectedPackage.originalPrice - this.selectedPackage.price) * this.hours;
            }
        };
    }
</script>