<div class="glass-obsidian rounded-3xl border border-rose-500/30 p-5 sm:p-7 lg:p-8 space-y-6 shadow-[0_4px_30px_rgba(244,63,94,0.08)]"
     x-data="cyberSecurityScannerEngine()">
    
    <!-- Title inside dedicated card banner (Requirement 13) -->
    <div class="glass-obsidian rounded-2xl border border-obsidian-border/80 p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-sm">
        <div class="space-y-1 text-center sm:text-right">
            <h4 class="text-base sm:text-lg font-black text-white font-cairo flex items-center justify-center sm:justify-start gap-2.5">
                <span class="w-2.5 h-2.5 rounded-full bg-rose-500 shrink-0 animate-ping"></span>
                <span>الماسح التفاعلي للجاهزية والتدريع السيبراني (Cyber Shield Scanner · حاسبة التكلفة والضمان الفوري) · باقات 700 إلى 5,000 ج.م/ساعة مع توقيع NDA</span>
            </h4>
            <p class="text-xs text-slate-400 font-cairo">
                احسب تكلفة زيارتك الهندسية والضمان بدقة، وحدد ثغرات أجهزتك وشبكاتك وأجهزة الأبناء < 18 سنة لاكتشاف مؤشر الخطر الفعلي والباقة الهندسية الأنسب.
            </p>
        </div>
        <span class="text-xs font-cairo text-rose-400 font-bold bg-rose-500/15 px-3.5 py-1.5 rounded-full border border-rose-500/30 self-center sm:self-auto shrink-0 shadow-sm">
            باقات 700 - 5,000 ج.م/ساعة · اتفاقية NDA
        </span>
    </div>

    <!-- Scanner Interactive Stage -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
        
        <!-- Checklist Inputs (7 Cols on LG) -->
        <div class="lg:col-span-7 space-y-3">
            <span class="text-xs font-bold text-slate-200 font-cairo block text-center sm:text-right">
                حدد البنود التي تنطبق على حالتك أو شركتك حالياً:
            </span>

            <template x-for="(item, idx) in checklist" :key="item.id">
                <label class="p-3.5 rounded-2xl bg-obsidian-surface border transition-all cursor-pointer flex items-center justify-between gap-3 group"
                       :class="item.checked ? 'border-rose-500/60 bg-rose-950/20' : 'border-obsidian-border hover:border-slate-700'">
                    <div class="flex items-center gap-3">
                        <input type="checkbox" 
                               x-model="item.checked" 
                               class="w-4 h-4 rounded text-rose-500 bg-obsidian-base border-obsidian-border focus:ring-rose-500 focus:ring-offset-0 cursor-pointer">
                        <div class="space-y-0.5 text-right">
                            <span class="text-xs font-bold text-white font-cairo block group-hover:text-cyber-cyan transition-colors" x-text="item.label"></span>
                            <span class="text-[11px] text-slate-400 font-cairo block" x-text="item.desc"></span>
                        </div>
                    </div>
                    <span class="text-[10px] font-mono px-2 py-0.5 rounded font-bold shrink-0"
                          :class="item.checked ? 'bg-rose-500/20 text-rose-300 border border-rose-500/40' : 'bg-obsidian-card text-slate-500'">
                        <span x-text="item.checked ? 'معرض للخطر' : 'آمن نسبياً'"></span>
                    </span>
                </label>
            </template>
        </div>

        <!-- Dynamic Risk Meter & Recommended Package Card (5 Cols on LG) -->
        <div class="lg:col-span-5 p-5 sm:p-6 rounded-2xl bg-gradient-to-br from-obsidian-surface via-obsidian-card to-obsidian-surface border space-y-5 text-center sm:text-right shadow-lg transition-all"
             :class="riskStatus.borderClass">
            
            <div class="border-b border-obsidian-border/80 pb-3 flex items-center justify-between">
                <span class="text-xs font-cairo text-slate-400 font-bold">مؤشر التعرض للمخاطر (Risk Score)</span>
                <span class="text-xs font-mono font-bold text-emerald-400" x-text="`ضمان ${recommendedShield.warrantyDays} يوماً`"></span>
            </div>

            <!-- Risk Score Gauge / Dial -->
            <div class="space-y-2 text-center">
                <div class="flex items-baseline justify-center gap-1 font-mono">
                    <span class="text-4xl sm:text-5xl font-black transition-all"
                          :class="riskStatus.textClass" 
                          x-text="`${riskScore}%`"></span>
                    <span class="text-xs text-slate-400">معدل الخطورة</span>
                </div>

                <!-- Animated Progress Bar -->
                <div class="w-full bg-obsidian-base rounded-full h-2.5 overflow-hidden border border-obsidian-border">
                    <div class="h-2.5 rounded-full transition-all duration-500" 
                         :class="riskStatus.barClass"
                         :style="`width: ${riskScore}%`"></div>
                </div>

                <span class="text-xs font-cairo font-bold block pt-1" 
                      :class="riskStatus.textClass" 
                      x-text="riskStatus.headline"></span>
            </div>

            <!-- Recommended Cyber Package Box -->
            <div class="p-3.5 rounded-xl bg-obsidian-base/90 border border-obsidian-border space-y-1.5 text-center sm:text-right">
                <div class="flex items-center justify-center sm:justify-start gap-2">
                    <span class="w-2 h-2 rounded-full" :class="riskStatus.dotClass"></span>
                    <span class="text-[11px] font-cairo font-bold text-slate-300">الباقة السيبرانية الموصى بها لتدريعك:</span>
                </div>
                <h5 class="text-sm font-extrabold text-white font-cairo" x-text="recommendedShield.name"></h5>
                <p class="text-[11px] text-slate-400 font-cairo" x-text="recommendedShield.desc"></p>
                <div class="pt-1 flex items-baseline justify-center sm:justify-start gap-2 font-mono">
                    <span class="text-base font-bold text-cyber-cyan" x-text="`${recommendedShield.price.toLocaleString()} EGP`"></span>
                    <span class="text-[10px] text-slate-400 font-cairo" x-text="recommendedShield.unit"></span>
                </div>
            </div>

            <!-- Official Cyber Warranty Seal -->
            <div class="flex items-center justify-center sm:justify-start gap-2 text-xs font-cairo text-slate-300">
                <span class="w-5 h-5 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-xs font-bold">✓</span>
                <span>ضمان هندسي سيبراني معتمد رسمياً مع تقرير فني مختوم.</span>
            </div>

            <!-- 1-Click Direct Booking CTA -->
            <div class="pt-2">
                @auth
                    <a :href="`{{ route('portal.bookings') }}?service=cybersecurity&package=${recommendedShield.slug}&risk_score=${riskScore}`" 
                       class="w-full py-3 rounded-xl btn-citadel-primary text-xs font-cairo font-bold text-center block shadow-md transition-all active:scale-95">
                        احجز فحص التدريع السيبراني العاجل ⇥
                    </a>
                @else
                    <a :href="`{{ route('portal.bookings') }}?service=cybersecurity&package=${recommendedShield.slug}&risk_score=${riskScore}`" 
                       @click.prevent="$dispatch('open-express-booking', {
                           serviceSlug: 'cybersecurity',
                           serviceName: 'الأمن السيبراني والتحقيق الجنائي الرقمي',
                           packageSlug: recommendedShield.slug,
                           packageName: recommendedShield.name,
                           price: recommendedShield.price
                       })"
                       class="w-full py-3 rounded-xl btn-citadel-primary text-xs font-cairo font-bold text-center block shadow-md transition-all active:scale-95">
                        احجز فحص التدريع السيبراني العاجل ⇥
                    </a>
                @endauth
            </div>

        </div>

    </div>

</div>

<script>
    function cyberSecurityScannerEngine() {
        return {
            checklist: [
                {
                    id: 'phone',
                    label: 'أجهزة الهواتف والواتساب والمراسلات الحساسة',
                    desc: 'فتح روابط مجهولة، تطبيقات من خارج المتجر الرسمي، أو استخدام شبكات عامة.',
                    checked: true
                },
                {
                    id: 'family',
                    label: 'أجهزة الأطفال والأسرة وحماية المحتوى',
                    desc: 'اتصال مفتوح دون جدار حماية عائلي ومخاطر الابتزاز الإلكتروني والروابط الخبيثة.',
                    checked: false
                },
                {
                    id: 'crypto',
                    label: 'المحافظ الرقمية والبطاقات وتطبيقات البنوك',
                    desc: 'تسجيل الدخول من أجهزة متعددة دون توثيق أمني ثنائي أو مفاتيح أمنية فيزيائية.',
                    checked: true
                },
                {
                    id: 'servers',
                    label: 'سيرفرات وقواعد بيانات الشركة والموقع',
                    desc: 'عدم وجود جدار حماية WAF، نسخ احتياطي ثلاثي مقاوم للفدية، أو فحص للثغرات.',
                    checked: false
                },
                {
                    id: 'employees',
                    label: 'أجهزة الموظفين وشبكة الـ Wi-Fi الداخلية',
                    desc: 'مشاركة كلمات المرور عشوائياً، وعدم عزل شبكة الضيوف عن سيرفرات الإدارة.',
                    checked: false
                }
            ],

            get checkedCount() {
                return this.checklist.filter(i => i.checked).length;
            },

            get riskScore() {
                // Base risk is 15%, plus 17% per selected risk factor
                return Math.min(98, 15 + (this.checkedCount * 17));
            },

            get riskStatus() {
                if (this.riskScore >= 75) {
                    return {
                        headline: 'خطر حرج داهم · يتطلب تدخلاً هندسياً فورياً',
                        textClass: 'text-rose-500',
                        barClass: 'bg-rose-500',
                        borderClass: 'border-rose-500/60',
                        dotClass: 'bg-rose-500 animate-ping'
                    };
                }
                if (this.riskScore >= 45) {
                    return {
                        headline: 'مستوى تعرض مرتفع · توجد ثغرات مكشوفة للاختراق',
                        textClass: 'text-cyber-gold',
                        barClass: 'bg-cyber-gold',
                        borderClass: 'border-cyber-gold/50',
                        dotClass: 'bg-cyber-gold'
                    };
                }
                return {
                    headline: 'مستوى تعرض معتدل · مطلوب وقاية دورية وتحديث أمني',
                    textClass: 'text-emerald-400',
                    barClass: 'bg-emerald-400',
                    borderClass: 'border-emerald-500/40',
                    dotClass: 'bg-emerald-400'
                };
            },

            get recommendedShield() {
                if (this.riskScore >= 75) {
                    return {
                        name: 'القلعة السيبرانية للشركات (Enterprise Fortress)',
                        slug: 'enterprise-cyber-fortress',
                        price: 5000,
                        unit: 'ساعة (الحد الأدنى 4 ساعات - 5,000 ج.م/ساعة)',
                        warrantyDays: 60,
                        desc: 'اختبار اختراق شامل VAPT، مراقبة مدارة، وتدريع تام لكافة السيرفرات مع توقيع اتفاقية NDA معتمدة.'
                    };
                }
                if (this.riskScore >= 45) {
                    return {
                        name: 'التحقيق الجنائي الرقمي وكشف التجسس (Digital Forensics)',
                        slug: 'digital-forensics',
                        price: 2800,
                        unit: 'ساعة (الحد الأدنى 3 ساعات - 2,800 ج.م/ساعة)',
                        warrantyDays: 30,
                        desc: 'فحص عميق للذاكرة والقرص الصلب لكشف برمجيات التجسس وتطهير الأجهزة بالكامل.'
                    };
                }
                return {
                    name: 'باقة التدريع الشخصي وحماية الخصوصية (Personal Shield)',
                    slug: 'personal-shield',
                    price: 700,
                    unit: 'ساعة (الحد الأدنى ساعتان - 700 ج.م/ساعة)',
                    warrantyDays: 14,
                    desc: 'كشف برامج التجسس المخفية بالهاتف، سد الثغرات، وتأمين حسابات الواتساب وحماية الأبناء.'
                };
            }
        };
    }
</script>