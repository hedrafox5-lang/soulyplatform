<div class="glass-obsidian rounded-3xl border border-cyber-cyan/40 p-5 sm:p-7 lg:p-8 space-y-6 shadow-[0_4px_30px_rgba(0,245,212,0.08)]"
     x-data="academyCurriculumEngine()">
    
    <!-- Title inside dedicated card banner (Requirement 13) -->
    <div class="glass-obsidian rounded-2xl border border-obsidian-border/80 p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-sm">
        <div class="space-y-1 text-center sm:text-right">
            <h4 class="text-base sm:text-lg font-black text-white font-cairo flex items-center justify-center sm:justify-start gap-2.5">
                <span class="w-2.5 h-2.5 rounded-full bg-cyber-cyan shrink-0 animate-pulse"></span>
                <span>الخطة الدراسية والمعامل الحية · المعاينة الفورية لشهادة سولي المعتمدة</span>
            </h4>
            <p class="text-xs text-slate-400 font-cairo">
                خريطة التدريب العملي: من الصفر حتى سوق العمل، استكشف اللابات واكتب اسمك لتشاهد نموذج شهادتك المعتمدة مع رمز الـ QR الرسمي مباشرة.
            </p>
        </div>
        <span class="text-xs font-cairo text-cyber-cyan font-bold bg-cyber-cyan/15 px-3.5 py-1.5 rounded-full border border-cyber-cyan/30 self-center sm:self-auto shrink-0 shadow-sm">
            مختبرات حية 100% عملي
        </span>
    </div>

    <!-- Simulator Interactive Stage -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Track Switcher & Labs Accordion (7 Cols on LG) -->
        <div class="lg:col-span-7 space-y-4">
            
            <!-- Track Switcher Buttons -->
            <div class="flex items-center gap-2 p-1 bg-obsidian-surface/90 rounded-2xl border border-obsidian-border">
                <button type="button" 
                        @click="activeTrack = 'ai'" 
                        class="flex-1 py-2.5 px-3 rounded-xl text-xs font-cairo font-bold transition-all text-center"
                        :class="activeTrack === 'ai' ? 'bg-cyber-cyan text-obsidian-base font-black shadow-sm' : 'text-slate-400 hover:text-white'">
                    🤖 معسكر وكلاء الـ AI والأتمتة (10 أسابيع)
                </button>
                <button type="button" 
                        @click="activeTrack = 'hacking'" 
                        class="flex-1 py-2.5 px-3 rounded-xl text-xs font-cairo font-bold transition-all text-center"
                        :class="activeTrack === 'hacking' ? 'bg-cyber-gold text-obsidian-base font-black shadow-sm' : 'text-slate-400 hover:text-white'">
                    🛡️ معسكر الهاكينج الأخلاقي (12 أسبوعاً)
                </button>
            </div>

            <!-- Labs Accordion List -->
            <div class="space-y-2.5 pt-1">
                <template x-for="(lab, lIdx) in currentTrackLabs" :key="lIdx">
                    <div class="rounded-2xl bg-obsidian-surface border border-obsidian-border overflow-hidden transition-all">
                        <button type="button" 
                                @click="openLabIdx = (openLabIdx === lIdx ? -1 : lIdx)"
                                class="w-full p-3.5 sm:p-4 text-center sm:text-right flex flex-col sm:flex-row items-center justify-between gap-2 font-cairo text-xs sm:text-sm font-bold text-white hover:text-cyber-cyan transition-colors">
                            <div class="flex items-center gap-2.5">
                                <span class="w-6 h-6 rounded-lg text-xs font-mono font-bold flex items-center justify-center shrink-0"
                                      :class="activeTrack === 'ai' ? 'bg-cyber-cyan/15 text-cyber-cyan' : 'bg-cyber-gold/15 text-cyber-gold'" 
                                      x-text="`0${lIdx + 1}`"></span>
                                <span x-text="lab.title"></span>
                            </div>
                            <span class="text-xs text-slate-500 font-mono" x-text="openLabIdx === lIdx ? '▲ إخفاء' : '▼ استعراض'"></span>
                        </button>
                        
                        <div x-show="openLabIdx === lIdx" 
                             class="p-4 border-t border-obsidian-border/70 text-xs text-slate-300 font-cairo space-y-2 bg-obsidian-base/60 text-center sm:text-right">
                            <p class="leading-relaxed" x-text="lab.description"></p>
                            
                            <!-- Lab Practical Exercise Pill -->
                            <div class="p-2.5 rounded-xl bg-obsidian-surface border space-y-1"
                                 :class="activeTrack === 'ai' ? 'border-cyber-cyan/30 text-cyber-cyan' : 'border-cyber-gold/30 text-cyber-gold'">
                                <div class="flex items-center justify-center sm:justify-start gap-1.5 font-bold">
                                    <span>🔬 التجربة العملية المباشرة باللاب:</span>
                                </div>
                                <p class="text-slate-200 text-[11px]" x-text="lab.handsOn"></p>
                            </div>

                            <!-- Tools Used Tags -->
                            <div class="flex flex-wrap items-center justify-center sm:justify-start gap-1.5 pt-1">
                                <span class="text-[10px] text-slate-400 font-mono">الأدوات:</span>
                                <template x-for="tool in lab.tools" :key="tool">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-obsidian-card border border-obsidian-border text-slate-300" 
                                          x-text="tool"></span>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

        </div>

        <!-- Live Interactive Certificate Hologram (5 Cols on LG) -->
        <div class="lg:col-span-5 p-5 sm:p-6 rounded-2xl bg-gradient-to-br from-obsidian-surface via-obsidian-card to-obsidian-surface border border-cyber-cyan/50 space-y-5 text-center sm:text-right shadow-lg">
            
            <div class="border-b border-obsidian-border/80 pb-3 flex items-center justify-between">
                <span class="text-xs font-cairo text-slate-400 font-bold">شهادة التخرج المعتمدة رسمياً</span>
                <span class="text-[10px] font-mono text-cyber-cyan font-bold bg-cyber-cyan/10 px-2 py-0.5 rounded border border-cyber-cyan/30">
                    رمز QR رسمي معتمد
                </span>
            </div>

            <!-- Student Name Input -->
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-white font-cairo block text-center sm:text-right">
                    اكتب اسمك للمعاينة الحية على الشهادة:
                </label>
                <input type="text" 
                       x-model="studentName" 
                       placeholder="مثال: م. أحمد محمد محمود"
                       class="w-full bg-obsidian-base border border-obsidian-border text-xs text-white rounded-xl p-3 focus:border-cyber-cyan focus:outline-none font-cairo text-center">
            </div>

            <!-- The Live Holographic Certificate Card -->
            <div class="relative rounded-2xl p-4 sm:p-5 bg-gradient-to-br from-[#0c1020] via-obsidian-base to-[#121828] border-2 border-cyber-gold/60 shadow-[0_8px_32px_rgba(255,184,0,0.15)] space-y-3.5 text-center overflow-hidden">
                
                <!-- Ambient Watermark Crest in Background -->
                <div class="absolute inset-0 flex items-center justify-center opacity-5 pointer-events-none">
                    <x-souly-logo size="lg" :showText="false" />
                </div>

                <!-- Certificate Header -->
                <div class="relative z-10 flex items-center justify-between border-b border-cyber-gold/30 pb-2">
                    <div class="flex items-center gap-1.5">
                        <span class="w-6 h-6 rounded-full bg-cyber-gold/20 text-cyber-gold flex items-center justify-center text-xs">🎓</span>
                        <span class="text-[10px] font-cairo font-bold text-cyber-gold">أكاديمية سولي للتقنية</span>
                    </div>
                    <span class="text-[9px] font-mono text-slate-400" x-text="credentialId"></span>
                </div>

                <!-- Main Certificate Copy -->
                <div class="relative z-10 space-y-1.5 py-1">
                    <span class="text-[10px] font-cairo text-slate-400 block">تشهد الأكاديمية بأن المهندس /</span>
                    <h5 class="text-lg sm:text-xl font-black text-transparent bg-clip-text bg-gradient-to-r from-cyber-cyan via-white to-cyber-gold font-cairo" 
                        x-text="studentName.trim() || 'اسم المهندس المتدرب'"></h5>
                    <p class="text-[10px] text-slate-300 font-cairo leading-relaxed">
                        قد اجتاز بنجاح كافة الاختبارات والمشاريع العملية لمعسكر:
                    </p>
                    <span class="inline-block px-3 py-1 rounded-full text-[11px] font-cairo font-bold text-white bg-obsidian-card border border-cyber-cyan/40" 
                          x-text="activeTrackName"></span>
                </div>

                <!-- Verification Stamp, Seal & QR -->
                <div class="relative z-10 border-t border-cyber-gold/30 pt-3 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <!-- QR Mock Badge -->
                        <div class="w-9 h-9 rounded-lg bg-white p-0.5 flex items-center justify-center shadow-sm shrink-0">
                            <div class="w-full h-full bg-slate-900 rounded flex flex-col items-center justify-center text-[7px] text-cyber-cyan font-mono font-bold leading-none">
                                <span>QR</span>
                                <span>VERIF</span>
                            </div>
                        </div>
                        <div class="text-right text-[9px] font-cairo text-slate-400 leading-tight">
                            <span class="text-emerald-400 font-bold block">موثقة ومعتمدة</span>
                            <span>كود فحص فوري</span>
                        </div>
                    </div>

                    <div class="text-left text-[9px] font-mono text-slate-400">
                        <span class="text-cyber-gold block font-bold">SOULY ACCREDITED</span>
                        <span>YEAR 2026</span>
                    </div>
                </div>

            </div>

            <!-- Enrollment CTA -->
            <div class="pt-1">
                @auth
                    <a :href="`{{ route('portal.bookings') }}?service=academy&package=${activePackageSlug}&student=${encodeURIComponent(studentName)}`" 
                       class="w-full py-3 rounded-xl btn-citadel-primary text-xs font-cairo font-bold text-center block shadow-md transition-all active:scale-95">
                        سجل في هذا المعسكر الآن واحجز مقعدك ⇥
                    </a>
                @else
                    <a :href="`{{ route('portal.bookings') }}?service=academy&package=${activePackageSlug}&student=${encodeURIComponent(studentName)}`" 
                       @click.prevent="$dispatch('open-express-booking', {
                           serviceSlug: 'academy',
                           serviceName: 'أكاديمية سولي للتقنية',
                           packageSlug: activePackageSlug,
                           packageName: activeTrackName,
                           price: (activeTrack === 'ai' ? 4500 : 5500),
                           notes: (studentName ? 'اسم المتدرب: ' + studentName : '')
                       })"
                       class="w-full py-3 rounded-xl btn-citadel-primary text-xs font-cairo font-bold text-center block shadow-md transition-all active:scale-95">
                        سجل في هذا المعسكر الآن واحجز مقعدك ⇥
                    </a>
                @endauth
            </div>

        </div>

    </div>

</div>

<script>
    function academyCurriculumEngine() {
        return {
            activeTrack: 'ai',
            openLabIdx: 0,
            studentName: '',

            get activeTrackName() {
                return this.activeTrack === 'ai' 
                    ? 'بناء وكلاء الذكاء الاصطناعي والأتمتة (AI Agents Mastery)'
                    : 'الهاكينج الأخلاقي واختبار الاختراق (Ethical Hacking)';
            },

            get activePackageSlug() {
                return this.activeTrack === 'ai' ? 'ai-automation-bootcamp' : 'ethical-hacking-zero-to-hero';
            },

            get credentialId() {
                return this.activeTrack === 'ai' ? 'SOULY-AI-2026-8492' : 'SOULY-SEC-2026-3918';
            },

            get currentTrackLabs() {
                if (this.activeTrack === 'ai') {
                    return [
                        {
                            title: 'الأسبوع 1-3: هندسة الأوامر ونماذج LLMs المفتوحة',
                            description: 'إعداد بيئة بايثون السحابية، التعامل مع واجهات OpenAI وClaude وDeepSeek، وتطبيق استراتيجيات Chain-of-Thought.',
                            handsOn: 'تشغيل نموذج DeepSeek R1 محلياً على جهازك وربطه بـ API خاص.',
                            tools: ['Python 3.12', 'Ollama', 'FastAPI', 'LangChain']
                        },
                        {
                            title: 'الأسبوع 4-6: قواعد البيانات المتجهة (Vector DBs) و RAG',
                            description: 'تحويل وثائق الشركات وملفات الـ PDF لمتجهات وتخزينها، وبناء عقل معرفي متطور للوكيل لا ينسى ولا يخرف.',
                            handsOn: 'بناء محرك RAG يسترجع الإجابات من كتالوج شركة تجارية مكون من 10,000 صفحة في ثانية.',
                            tools: ['Pinecone', 'Qdrant', 'Sentence Transformers', 'ChromaDB']
                        },
                        {
                            title: 'الأسبوع 7-8: ربط قنوات واتساب وسوشيال ميديا وأتمتة n8n',
                            description: 'ربط الوكيل مع WhatsApp Cloud API و WAHA، والفيسبوك ماسنجر وإرسال الصور وروابط الدفع وتأكيد الحجوزات.',
                            handsOn: 'وكيل واتساب يستقبل الفويس نوت من العميل ويفهمها ويرد بفويس بشري مسجل.',
                            tools: ['WhatsApp API', 'WAHA Docker', 'n8n Workflows', 'Whisper STT']
                        },
                        {
                            title: 'الأسبوع 9-10: مشروع التخرج ومعرض الأعمال والنشر',
                            description: 'بناء ونشر فريق وكلاء متعاونين (Multi-Agent Swarm) لشركة واقعية، وتوثيق المشروع على GitHub للتأهيل للوظائف.',
                            handsOn: 'تسليم المشروع النهائي والمناقشة المباشرة والحصول على الشهادة المعتمدة.',
                            tools: ['Docker', 'AWS / VPS', 'GitHub Actions', 'Portainer']
                        }
                    ];
                }

                return [
                    {
                        title: 'الأسبوع 1-3: أساسيات شبكات وأنظمة كالي لينكس المعزولة',
                        description: 'تثبيت Kali Linux داخل بيئات افتراضية معزولة، فهم بروتوكولات TCP/IP، وحزم البيانات وجدران الحماية.',
                        handsOn: 'إعداد معمل اختبار اختراق افتراضي يضم سيرفر أهداف وويندوز وكالي.',
                        tools: ['Kali Linux', 'VirtualBox / VMware', 'Bash Scripting', 'Wireshark']
                    },
                    {
                        title: 'الأسبوع 4-7: فحص الشبكات واستغلال الثغرات Metasploit',
                        description: 'استكشاف الأجهزة والمنافذ المفتوحة بـ Nmap، البحث عن ثغرات الـ CVEs، واستغلال الثغرات عبر Metasploit Framework.',
                        handsOn: 'اختراق سيرفر تجريبي غير محدث واستخراج صلاحيات Root كاملة.',
                        tools: ['Nmap', 'Metasploit Pro', 'Netcat', 'SearchSploit']
                    },
                    {
                        title: 'الأسبوع 8-10: اختبار اختراق تطبيقات الويب (OWASP Top 10)',
                        description: 'فحص ثغرات المواقع الحساسة: حقن قواعد البيانات SQLi، هجمات XSS، ثغرات CSRF وSSRF وتجاوز بوابات الدفع.',
                        handsOn: 'محاكاة بنك إلكتروني واكتشاف ثغرة سحب أرصدة وتوثيق كود الحماية وسدها.',
                        tools: ['Burp Suite Pro', 'SQLMap', 'OWASP ZAP', 'Postman']
                    },
                    {
                        title: 'الأسبوع 11-12: كتابة التقارير المهنية ومشروع التخرج',
                        description: 'صياغة تقارير الـ Penetration Testing وفق المعايير الدولية للشركات ومنصات مكافآت الثغرات Bug Bounty.',
                        handsOn: 'إجراء فحص كامل لشبكة مؤسسة افتراضية وتقديم التقرير النهائي للجنة التقييم.',
                        tools: ['Dradis', 'CVSS Calculator', 'Report Templates', 'QR Seal']
                    }
                ];
            }
        };
    }
</script>