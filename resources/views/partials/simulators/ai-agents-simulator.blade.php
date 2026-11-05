<div class="glass-obsidian rounded-3xl border border-cyber-cyan/40 p-5 sm:p-7 lg:p-8 space-y-6 shadow-[0_4px_30px_rgba(0,245,212,0.08)]"
     x-data="aiEmployeeSimulatorEngine()">
    
    <!-- Title inside dedicated card banner (Requirement 13) -->
    <div class="glass-obsidian rounded-2xl border border-obsidian-border/80 p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-sm">
        <div class="space-y-1 text-center sm:text-right">
            <h4 class="text-base sm:text-lg font-black text-white font-cairo flex items-center justify-center sm:justify-start gap-2.5">
                <span class="w-2.5 h-2.5 rounded-full bg-cyber-cyan shrink-0 animate-pulse"></span>
                <span>محاكي الموظف الرقمي وحاسبة التوفير التشغيلي (AI Employee ROI Simulator)</span>
            </h4>
            <p class="text-xs text-slate-400 font-cairo">
                قارن تكلفة توظيف فريق خدمة عملاء بشري مقابل موظف سولي الرقمي الذكي الذي يرد في 1.8 ثانية باللهجة المصرية 24/7.
            </p>
        </div>
        <span class="text-xs font-cairo text-cyber-gold font-bold bg-cyber-gold/15 px-3.5 py-1.5 rounded-full border border-cyber-gold/30 self-center sm:self-auto shrink-0 shadow-sm">
            وفر حتى 27,500 ج.م شهرياً
        </span>
    </div>

    <!-- Simulator Interactive Stage -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
        
        <!-- Controls & Live Chat Stage (7 Cols on LG) -->
        <div class="lg:col-span-7 space-y-5">
            
            <!-- Human Staff Count Slider -->
            <div class="p-4 rounded-2xl bg-obsidian-surface border border-obsidian-border space-y-2">
                <div class="flex items-center justify-between text-xs font-cairo">
                    <span class="text-slate-300 font-bold">1. عدد موظفي خدمة العملاء والمبيعات الحاليين:</span>
                    <span class="text-rose-400 font-mono font-bold text-sm bg-rose-950/40 px-2.5 py-0.5 rounded-lg border border-rose-500/30" 
                          x-text="`${humanStaff} موظف بشري (شفتات)`"></span>
                </div>
                <input type="range" 
                       min="1" 
                       max="5" 
                       step="1" 
                       x-model.number="humanStaff" 
                       class="w-full h-2 bg-obsidian-base rounded-lg appearance-none cursor-pointer accent-rose-500">
                <div class="flex justify-between text-[10px] font-mono text-slate-500">
                    <span>1 موظف (6,000 ج.م)</span>
                    <span>3 موظفين (18,000 ج.م)</span>
                    <span>5 موظفين (30,000 ج.م)</span>
                </div>
            </div>

            <!-- Interactive Egyptian Dialect Preview Box -->
            <div class="p-4 rounded-2xl bg-obsidian-surface border border-cyber-cyan/30 space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-obsidian-border pb-2.5">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                        <span class="text-xs font-cairo font-bold text-white">محاكاة حية للرد باللهجة المصرية الطبيعية:</span>
                    </div>
                    <div class="flex items-center gap-1 self-center sm:self-auto">
                        <template x-for="(sample, sIdx) in chatSamples" :key="sIdx">
                            <button type="button" 
                                    @click="activeSampleIdx = sIdx" 
                                    class="px-2.5 py-1 rounded-lg text-[10px] font-cairo font-bold transition-all"
                                    :class="activeSampleIdx === sIdx ? 'bg-cyber-cyan text-obsidian-base font-black' : 'bg-obsidian-card text-slate-400 hover:text-white'">
                                <span x-text="sample.topic"></span>
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Live Chat Balloon Stage -->
                <div class="space-y-2.5 font-cairo text-xs pt-1">
                    <!-- Customer Message -->
                    <div class="flex items-start gap-2.5 justify-end">
                        <div class="bg-obsidian-card border border-obsidian-border text-slate-200 p-3 rounded-2xl rounded-tr-none max-w-sm">
                            <span class="text-[10px] text-slate-500 block mb-0.5 text-left">عميل عبر واتساب · منذ لحظات</span>
                            <p class="leading-relaxed font-cairo" x-text="activeSample.customer"></p>
                        </div>
                        <div class="w-7 h-7 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center text-xs text-slate-300 font-bold shrink-0">
                            👤
                        </div>
                    </div>

                    <!-- AI Agent Message (Responds in 1.8s) -->
                    <div class="flex items-start gap-2.5 justify-start">
                        <div class="w-7 h-7 rounded-full bg-cyber-cyan/20 border border-cyber-cyan/50 flex items-center justify-center text-xs text-cyber-cyan font-bold shrink-0">
                            ⚡
                        </div>
                        <div class="bg-gradient-to-br from-cyber-cyan/15 to-obsidian-card border border-cyber-cyan/40 text-slate-100 p-3.5 rounded-2xl rounded-tl-none max-w-md shadow-neon-cyan">
                            <div class="flex items-center justify-between gap-3 text-[10px] text-cyber-cyan font-mono mb-1">
                                <span class="font-bold">سولي AI · رد فوري</span>
                                <span>⚡ استجابة في 1.8s</span>
                            </div>
                            <p class="leading-relaxed font-cairo" x-text="activeSample.aiReply"></p>
                        </div>
                    </div>
                </div>

                <!-- Channels Supported Pills -->
                <div class="pt-2 border-t border-obsidian-border/70 flex flex-wrap items-center justify-between gap-2 text-[11px] font-cairo text-slate-400">
                    <span>قنوات التشغيل المتصلة:</span>
                    <div class="flex items-center gap-1.5">
                        <span class="px-2 py-0.5 rounded-full bg-emerald-500/15 text-emerald-400 border border-emerald-500/30 font-bold">واتساب WAHA / Cloud API</span>
                        <span class="px-2 py-0.5 rounded-full bg-cyber-purple/20 text-purple-300 border border-cyber-purple/40 font-bold">إنستجرام & ماسنجر</span>
                        <span class="px-2 py-0.5 rounded-full bg-cyber-cyan/15 text-cyber-cyan border border-cyber-cyan/30 font-bold">شات الموقع</span>
                    </div>
                </div>
            </div>

        </div>

        <!-- Calculated Savings & ROI Results (5 Cols on LG) -->
        <div class="lg:col-span-5 p-5 sm:p-6 rounded-2xl bg-gradient-to-br from-obsidian-surface via-obsidian-card to-obsidian-surface border border-cyber-gold/50 space-y-5 text-center sm:text-right shadow-lg">
            
            <div class="border-b border-obsidian-border/80 pb-3 flex items-center justify-between">
                <span class="text-xs font-cairo text-slate-400 font-bold">جدوى الاستثمار والأرباح</span>
                <span class="text-[10px] font-cairo font-bold text-cyber-gold px-2 py-0.5 rounded bg-cyber-gold/10 border border-cyber-gold/30">
                    أرقام واقعية معتمدة
                </span>
            </div>

            <!-- Cost Comparison Box -->
            <div class="space-y-3 font-mono text-xs">
                <div class="p-3 rounded-xl bg-rose-950/20 border border-rose-900/40 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-cairo text-rose-300 font-bold block">تكلفة الفريق البشري شهرياً:</span>
                        <span class="text-[10px] text-slate-500 font-cairo" x-text="`${humanStaff} موظف × 6,000 ج.م راتب أساسي`"></span>
                    </div>
                    <span class="text-lg font-black text-rose-400" x-text="`${monthlyHumanCost.toLocaleString()} EGP`"></span>
                </div>

                <div class="p-3 rounded-xl bg-cyber-cyan/10 border border-cyber-cyan/30 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-cairo text-cyber-cyan font-bold block">تكلفة وكيل سولي للذكاء الاصطناعي:</span>
                        <span class="text-[10px] text-slate-400 font-cairo">يعمل 24/7 بدون إجازات أو مرضيات</span>
                    </div>
                    <span class="text-lg font-black text-cyber-cyan" x-text="`${aiMonthlyCost.toLocaleString()} EGP`"></span>
                </div>
            </div>

            <!-- Net Monthly & Annual Savings Highlights -->
            <div class="p-4 rounded-xl bg-gradient-to-r from-cyber-gold/15 to-cyber-cyan/15 border border-cyber-gold/40 text-center space-y-1">
                <span class="text-xs text-slate-300 font-cairo font-bold block">صافي التوفير المالي لشركتك شهرياً:</span>
                <span class="text-2xl sm:text-3xl font-black font-mono text-transparent bg-clip-text bg-gradient-to-r from-cyber-cyan to-cyber-gold block" 
                      x-text="`${monthlySavings.toLocaleString()} EGP`"></span>
                <span class="text-[11px] text-slate-400 font-cairo block">
                    (توفير سنوي يصل إلى <strong class="text-cyber-gold font-mono" x-text="`${annualSavings.toLocaleString()} EGP`"></strong> سنوياً)
                </span>
            </div>

            <!-- Live Latency & Reliability Indicators -->
            <div class="grid grid-cols-2 gap-2 text-center text-xs font-cairo">
                <div class="p-2.5 rounded-xl bg-obsidian-base border border-obsidian-border">
                    <span class="text-[10px] text-slate-400 block">زمن الاستجابة:</span>
                    <span class="text-sm font-bold text-cyber-cyan font-mono">1.8 ثانية</span>
                </div>
                <div class="p-2.5 rounded-xl bg-obsidian-base border border-obsidian-border">
                    <span class="text-[10px] text-slate-400 block">الجاهزية السنوية:</span>
                    <span class="text-sm font-bold text-emerald-400 font-mono">100% بدون غياب</span>
                </div>
            </div>

            <!-- 1-Click Direct Booking CTA -->
            <div class="pt-2">
                @auth
                    <a :href="`{{ route('portal.bookings') }}?service=ai-agents&package=whatsapp-sales&staff_replaced=${humanStaff}`" 
                       class="w-full py-3 rounded-xl btn-citadel-primary text-xs font-cairo font-bold text-center block shadow-md transition-all active:scale-95">
                        احجز وكيل الذكاء الاصطناعي الآن ووفر <span x-text="`${monthlySavings.toLocaleString()} ج.م`"></span> ⇥
                    </a>
                @else
                    <a :href="`{{ route('portal.bookings') }}?service=ai-agents&package=whatsapp-sales&staff_replaced=${humanStaff}`" 
                       @click.prevent="$dispatch('open-express-booking', {
                           serviceSlug: 'ai-agents',
                           serviceName: 'وكلاء الذكاء الاصطناعي وشات بوت المبيعات',
                           packageSlug: 'whatsapp-sales',
                           packageName: 'وكيل المبيعات وخدمة العملاء الذكي لواتساب',
                           price: 2450
                       })"
                       class="w-full py-3 rounded-xl btn-citadel-primary text-xs font-cairo font-bold text-center block shadow-md transition-all active:scale-95">
                        احجز وكيل الذكاء الاصطناعي الآن ووفر <span x-text="`${monthlySavings.toLocaleString()} ج.م`"></span> ⇥
                    </a>
                @endauth
            </div>

        </div>

    </div>

</div>

<script>
    function aiEmployeeSimulatorEngine() {
        return {
            humanStaff: 3,
            salaryPerStaff: 6000,
            aiMonthlyCost: 2450,
            activeSampleIdx: 0,

            chatSamples: [
                {
                    topic: 'تجارة ومبيعات',
                    customer: 'مساء الخير، عندكم كوتشي مقاس 42 لون أبيض ومصاريف الشحن للإسكندرية كام؟',
                    aiReply: 'أهلاً بحضرتك يا فندم! 👟 مقاس 42 الأبيض متوفر حالياً في مخزننا وجاهز للشحن فوراً. مصاريف الشحن للإسكندرية 45 جنيه بس وبيوصلك خلال 48 ساعة. تحب أسجل لحضرتك الأوردر بالاسم ورقم التليفون والعنوان دلوقتي؟'
                },
                {
                    topic: 'تتبع شحنة',
                    customer: 'أنا طلبت أوردر كود #94820 من يومين وموصلش لحد دلوقتي ليه؟',
                    aiReply: 'حقك علينا يا فندم! راجعت سيستم الشحن برقم #94820، الشحنة وصلت محطة التوزيع وخرجت مع المندوب للتسليم لحضرتك النهاردة قبل الساعة 6 مساءً. هبعت لحضرتك رقم المندوب على الواتساب للمتابعة المباشرة.'
                },
                {
                    topic: 'حجز عيادة',
                    customer: 'عايز أحجز كشف باطنة عند الدكتور بكرة بعد الظهر ممكن تفيدني بالمواعيد؟',
                    aiReply: 'يا هلا بيك! المواعيد المتاحة عند دكتور الباطنة بكرة: المعاد الأول 4:30 م، والمعاد التاني 6:15 م. تحب أثبت لحضرتك معاد 4:30 م ونبعتلك تأكيد الحجز واللوكيشن؟'
                }
            ],

            get activeSample() {
                return this.chatSamples[this.activeSampleIdx] || this.chatSamples[0];
            },

            get monthlyHumanCost() {
                return this.humanStaff * this.salaryPerStaff;
            },

            get monthlySavings() {
                return Math.max(0, this.monthlyHumanCost - this.aiMonthlyCost);
            },

            get annualSavings() {
                return this.monthlySavings * 12;
            }
        };
    }
</script>