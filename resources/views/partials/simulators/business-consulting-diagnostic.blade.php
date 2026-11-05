<div class="glass-obsidian rounded-3xl border border-cyber-gold/40 p-5 sm:p-7 lg:p-8 space-y-6 shadow-[0_4px_30px_rgba(255,184,0,0.08)]"
     x-data="consultingDiagnosticEngine()">
    
    <!-- Title inside dedicated card banner (Requirement 13) -->
    <div class="glass-obsidian rounded-2xl border border-obsidian-border/80 p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-sm">
        <div class="space-y-1 text-center sm:text-right">
            <h4 class="text-base sm:text-lg font-black text-white font-cairo flex items-center justify-center sm:justify-start gap-2.5">
                <span class="w-2.5 h-2.5 rounded-full bg-cyber-gold shrink-0 animate-pulse"></span>
                <span>مصفوفة تشخيص النضج الرقمي وخارطة النمو المؤسسي (Diagnostic Matrix)</span>
            </h4>
            <p class="text-xs text-slate-400 font-cairo">
                أجب عن 4 أسئلة تشخيصية سريعة لتقييم كفاءة عمليات شركتك واكتشاف فرص مضاعفة أرباحك الصافية بنسبة تصل إلى +45%.
            </p>
        </div>
        <span class="text-xs font-cairo text-cyber-gold font-bold bg-cyber-gold/15 px-3.5 py-1.5 rounded-full border border-cyber-gold/30 self-center sm:self-auto shrink-0 shadow-sm">
            تشخيص استراتيجي فوري 0ms
        </span>
    </div>

    <!-- Diagnostic Interactive Stage -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
        
        <!-- 4 Diagnostic Questions (7 Cols on LG) -->
        <div class="lg:col-span-7 space-y-4">
            
            <template x-for="(q, qIdx) in questions" :key="q.id">
                <div class="p-3.5 sm:p-4 rounded-2xl bg-obsidian-surface border border-obsidian-border space-y-2 text-center sm:text-right">
                    <span class="text-xs font-bold text-white font-cairo block" x-text="`${qIdx + 1}. ${q.title}`"></span>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 pt-1">
                        <template x-for="opt in q.options" :key="opt.score">
                            <button type="button" 
                                    @click="q.selectedScore = opt.score" 
                                    class="p-2.5 rounded-xl border text-center sm:text-right transition-all font-cairo text-xs leading-snug flex flex-col justify-between gap-1"
                                    :class="q.selectedScore === opt.score ? 'bg-cyber-gold/15 border-cyber-gold text-white font-bold shadow-sm' : 'bg-obsidian-base border-obsidian-border text-slate-400 hover:text-white'">
                                <span x-text="opt.label"></span>
                                <span class="text-[10px] font-mono text-cyber-gold self-center sm:self-start" x-text="`+${opt.score} نقطة`"></span>
                            </button>
                        </template>
                    </div>
                </div>
            </template>

        </div>

        <!-- Diagnostic Score & 3-Step Growth Roadmap Card (5 Cols on LG) -->
        <div class="lg:col-span-5 p-5 sm:p-6 rounded-2xl bg-gradient-to-br from-obsidian-surface via-obsidian-card to-obsidian-surface border border-cyber-gold/50 space-y-5 text-center sm:text-right shadow-lg">
            
            <div class="border-b border-obsidian-border/80 pb-3 flex items-center justify-between">
                <span class="text-xs font-cairo text-slate-400 font-bold">تقرير النضج المؤسسي</span>
                <span class="text-xs font-mono font-bold text-cyber-gold" x-text="`تقييم ${totalScore}%`"></span>
            </div>

            <!-- Score & Growth Potential Pill -->
            <div class="space-y-2 text-center">
                <div class="flex items-baseline justify-center gap-1 font-mono">
                    <span class="text-4xl sm:text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-cyber-gold via-cyber-cyan to-white" 
                          x-text="`${totalScore}%`"></span>
                    <span class="text-xs text-slate-400">معدل النضج</span>
                </div>

                <!-- Animated Progress Bar -->
                <div class="w-full bg-obsidian-base rounded-full h-2.5 overflow-hidden border border-obsidian-border">
                    <div class="h-2.5 rounded-full bg-gradient-to-r from-rose-500 via-cyber-gold to-emerald-400 transition-all duration-500" 
                         :style="`width: ${totalScore}%`"></div>
                </div>

                <div class="p-2.5 rounded-xl bg-obsidian-base border border-cyber-gold/30 text-xs font-cairo space-y-0.5 text-center">
                    <span class="text-emerald-400 font-bold font-mono text-sm block" x-text="growthProjection"></span>
                    <span class="text-[11px] text-slate-400 font-cairo block">عائد صافي إضافي وتخفيض فوري للهدر التشغيلي</span>
                </div>
            </div>

            <!-- 3-Step Customized Roadmap -->
            <div class="space-y-2 text-center sm:text-right">
                <span class="text-xs font-cairo font-bold text-slate-300 block">خارطة التحول والتطوير المقترحة:</span>
                <div class="space-y-1.5 text-[11px] font-cairo">
                    <template x-for="(step, sIdx) in recommendedPlan.steps" :key="sIdx">
                        <div class="flex items-start gap-2 p-2 rounded-lg bg-obsidian-base border border-obsidian-border">
                            <span class="w-4 h-4 rounded-full bg-cyber-gold/20 text-cyber-gold flex items-center justify-center font-mono text-[10px] shrink-0 mt-0.5" 
                                  x-text="`0${sIdx+1}`"></span>
                            <span class="text-slate-200" x-text="step"></span>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Recommended Consultation Tier -->
            <div class="p-3.5 rounded-xl bg-gradient-to-r from-cyber-gold/10 via-obsidian-card to-cyber-cyan/10 border border-cyber-gold/40 space-y-1 text-center sm:text-right">
                <span class="text-[10px] font-cairo font-bold text-cyber-gold block">الحزمة الاستشارية الموصى بها لمشروعك:</span>
                <h5 class="text-sm font-extrabold text-white font-cairo" x-text="recommendedPlan.name"></h5>
                <p class="text-[11px] text-slate-400 font-cairo" x-text="recommendedPlan.desc"></p>
                <div class="pt-1 flex items-baseline justify-center sm:justify-start gap-1 font-mono">
                    <span class="text-base font-bold text-cyber-cyan" x-text="`${recommendedPlan.price.toLocaleString()} EGP`"></span>
                    <span class="text-[10px] text-slate-400 font-cairo" x-text="recommendedPlan.unit"></span>
                </div>
            </div>

            <!-- 1-Click Direct Booking CTA -->
            <div class="pt-2">
                @auth
                    <a :href="`{{ route('portal.bookings') }}?service=business-consulting&package=${recommendedPlan.slug}&maturity_score=${totalScore}`" 
                       class="w-full py-3 rounded-xl btn-citadel-primary text-xs font-cairo font-bold text-center block shadow-md transition-all active:scale-95">
                        احجز جلستك الاستشارية لبدء خطة النمو ⇥
                    </a>
                @else
                    <a :href="`{{ route('portal.bookings') }}?service=business-consulting&package=${recommendedPlan.slug}&maturity_score=${totalScore}`" 
                       @click.prevent="$dispatch('open-express-booking', {
                           serviceSlug: 'business-consulting',
                           serviceName: 'استشارات البيزنس والتحول الرقمي',
                           packageSlug: recommendedPlan.slug,
                           packageName: recommendedPlan.name,
                           price: recommendedPlan.price
                       })"
                       class="w-full py-3 rounded-xl btn-citadel-primary text-xs font-cairo font-bold text-center block shadow-md transition-all active:scale-95">
                        احجز جلستك الاستشارية لبدء خطة النمو ⇥
                    </a>
                @endauth
            </div>

        </div>

    </div>

</div>

<script>
    function consultingDiagnosticEngine() {
        return {
            questions: [
                {
                    id: 'erp',
                    title: 'إدارة العمليات والمخزون والفواتير:',
                    selectedScore: 10,
                    options: [
                        { label: 'دفاتر ورقية أو إكسيل يدوي', score: 0 },
                        { label: 'برنامج قديم غير مربوط بالفروع', score: 10 },
                        { label: 'نظام سحابي مؤتمت بالكامل', score: 25 }
                    ]
                },
                {
                    id: 'marketing',
                    title: 'قياس عائد التسويق والإعلانات (ROAS):',
                    selectedScore: 0,
                    options: [
                        { label: 'لا نعرف تكلفة العميل الحقيقية', score: 0 },
                        { label: 'تتبع تقريبي بمدير الإعلانات', score: 10 },
                        { label: 'مسار مبيعات وتتبع لحظي ذكي', score: 25 }
                    ]
                },
                {
                    id: 'sales',
                    title: 'متابعة المبيعات وخدمة العملاء:',
                    selectedScore: 10,
                    options: [
                        { label: 'اعتماد على الشات العشوائي', score: 0 },
                        { label: 'شيت إكسيل بدون أتمتة ردود', score: 10 },
                        { label: 'نظام CRM ووكيل ذكاء اصطناعي', score: 25 }
                    ]
                },
                {
                    id: 'ops',
                    title: 'إجراءات العمل والتوسع (SOPs):',
                    selectedScore: 0,
                    options: [
                        { label: 'الشركة تتوقف إذا غاب المدير', score: 0 },
                        { label: 'تعليمات شفهية للموظفين', score: 10 },
                        { label: 'أدلة إجراءات موثقة وأتمتة مسارات', score: 25 }
                    ]
                }
            ],

            get totalScore() {
                return this.questions.reduce((acc, q) => acc + q.selectedScore, 0);
            },

            get growthProjection() {
                if (this.totalScore <= 35) return '+45% زيادة متوقعة في صافي الأرباح';
                if (this.totalScore <= 70) return '+32% نمو وتخفيض 40% في الهدر';
                return '+20% توسع ومضاعفة فروع';
            },

            get recommendedPlan() {
                if (this.totalScore <= 35) {
                    return {
                        name: 'باقة خارطة التحول الرقمي (Digital Blueprint)',
                        slug: 'digital-blueprint',
                        price: 2200,
                        unit: 'للجلسة التشخيصية والخريطة التنفيذية',
                        desc: 'جلسة استشارية مكثفة 1-on-1 لتشخيص مواطن الهدر وإعداد خطة عمل مكتوبة واضحة.',
                        steps: [
                            'تشخيص نقاط الاختناق والهدر المالي في نموذج العمل الحالي',
                            'إيقاف الصرف الإعلاني غير المجدي وترشيح الأنظمة المناسبة لميزانيتك',
                            'إعداد وثيقة خارطة طريق تنفيذية مكتوبة للبدء الفوري'
                        ]
                    };
                }
                if (this.totalScore <= 70) {
                    return {
                        name: 'باقة أتمتة العمليات وسلاسل الإمداد (Process Automation)',
                        slug: 'process-automation',
                        price: 12500,
                        unit: 'للمشروع المتكامل',
                        desc: 'هندسة وأتمتة مسارات العمل وتخفيض التكاليف التشغيلية بالـ AI و n8n والـ APIs.',
                        steps: [
                            'بناء أتمتة رقمية لربط الفواتير والمخازن والعملاء دون تدخل بشري',
                            'توفير أكثر من 80 ساعة عمل شهرياً من المهام اليدوية المكررة',
                            'تدريب فريق العمل وتقديم دعم فني ممتد لضمان استقرار التشغيل'
                        ]
                    };
                }
                return {
                    name: 'برنامج التوجيه التنفيذي وهيكلة النمو (Executive Growth)',
                    slug: 'executive-growth',
                    price: 9500,
                    unit: 'شهرياً (تعاقد ربع سنوي)',
                    desc: 'شراكة استشارية شهرية ترافق مجلس الإدارة لتنفيذ خطة التوسع وفتح الفروع الجديدة.',
                    steps: [
                        'متابعة دورية للوحات مؤشرات الأداء الحيوية (KPIs) وتحليل الأرباح',
                        'هيكلة مسارات التوسع وجذب الاستثمارات وتطوير فرق البيع',
                        'استشارات مفتوحة لمدير المؤسسة على مدار الشهر'
                    ]
                };
            }
        };
    }
</script>