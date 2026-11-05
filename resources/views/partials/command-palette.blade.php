<!-- Souly Digital Citadel: Command Center Omni-Search Palette [Ctrl + K] -->
<div id="souly-command-palette" 
     x-data="soulyCommandPalette()" 
     x-show="$store.citadel && $store.citadel.commandPaletteOpen" 
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0 scale-95"
     x-transition:enter-end="opacity-100 scale-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100 scale-100"
     x-transition:leave-end="opacity-0 scale-95"
     class="fixed inset-0 z-[99999] flex items-start justify-center pt-8 sm:pt-20 px-3 sm:px-4 bg-black/80 backdrop-blur-xl"
     style="display: none;"
     @click.self="$store.citadel.commandPaletteOpen = false"
     dir="rtl">

    <div class="max-w-2xl w-full glass-obsidian border border-cyber-cyan/50 rounded-3xl p-4 sm:p-5 shadow-[0_10px_40px_rgba(0,0,0,0.85)] space-y-4 overflow-hidden"
         @click.stop>
        
        <!-- Search Input Bar -->
        <div class="relative">
            <input type="text" 
                   x-ref="searchInput"
                   x-model="searchQuery" 
                   @keydown.down.prevent="navigateDown()"
                   @keydown.up.prevent="navigateUp()"
                   @keydown.enter.prevent="selectActive()"
                   @keydown.escape="$store.citadel.commandPaletteOpen = false"
                   placeholder="بحث قيادي فوري في الخدمات والباقات..." 
                   class="w-full bg-obsidian-surface text-sm sm:text-base text-white placeholder-slate-400 rounded-2xl px-11 py-3 sm:py-3.5 border border-cyber-cyan/40 focus:outline-none focus:border-cyber-cyan focus:ring-2 focus:ring-cyber-cyan/30 font-cairo">
            
            <div class="absolute inset-y-0 right-3.5 flex items-center pointer-events-none text-cyber-cyan">
                <svg class="w-5 h-5 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <div class="absolute inset-y-0 left-3.5 flex items-center gap-1.5">
                <button type="button" 
                        @click="$store.citadel.commandPaletteOpen = false"
                        class="p-1 rounded-lg text-slate-400 hover:text-white cursor-pointer sm:hidden">✕</button>
                <kbd class="hidden sm:inline px-2 py-0.5 text-xs font-mono bg-obsidian-base text-slate-400 rounded border border-obsidian-border">ESC</kbd>
            </div>
        </div>

        <!-- Filtered Results Container -->
        <div class="max-h-72 sm:max-h-96 overflow-y-auto space-y-2 pr-1 overscroll-contain">
            <template x-for="(item, index) in filteredItems" :key="index">
                <div @click="goTo(item.url)" 
                     @mouseenter="activeIndex = index"
                     class="p-3 rounded-2xl border transition-all cursor-pointer flex items-center justify-between gap-3 group"
                     :class="activeIndex === index ? 'bg-cyber-cyan/15 border-cyber-cyan/60 shadow-sm' : 'bg-obsidian-surface/80 border-obsidian-border hover:border-cyber-cyan/30'">
                    
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-9 h-9 rounded-xl bg-obsidian-base border border-cyber-cyan/30 flex items-center justify-center text-cyber-cyan group-hover:scale-110 transition-transform shrink-0">
                            <span x-text="item.icon" class="text-base"></span>
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <h4 class="text-xs sm:text-sm font-bold text-white font-cairo truncate" x-text="item.title"></h4>
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-cairo font-bold shrink-0" 
                                      :class="item.tagClass" 
                                      x-text="item.category"></span>
                            </div>
                            <p class="text-[11px] text-slate-400 font-cairo line-clamp-1 mt-0.5" x-text="item.desc"></p>
                        </div>
                    </div>

                    <div class="flex items-center gap-1.5 shrink-0">
                        <span class="text-xs font-cairo text-slate-400 group-hover:text-cyber-cyan font-bold hidden sm:inline">انتقال</span>
                        <svg class="w-4 h-4 text-cyber-cyan group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </div>
                </div>
            </template>

            <!-- Zero Results Message -->
            <div x-show="filteredItems.length === 0" class="text-center py-8 space-y-2">
                <p class="text-sm font-cairo text-slate-400">لم يتم العثور على نتائج مطابقة لـ "<span x-text="searchQuery" class="text-cyber-cyan font-bold"></span>"</p>
                <p class="text-xs text-slate-500 font-cairo">جرّب البحث عن "ERP"، "صيانة"، أو "محفظة".</p>
            </div>
        </div>

        <!-- Command Palette Footer -->
        <div class="pt-2 sm:pt-3 border-t border-obsidian-border/80 flex items-center justify-between text-xs text-slate-400 font-cairo">
            <div class="flex items-center gap-2 text-[11px]">
                <span>↑↓ للتنقل</span>
                <span>·</span>
                <span>Enter للاختيار</span>
            </div>
            <span class="text-cyber-cyan font-bold text-[11px]"><span class="font-mono" x-text="filteredItems.length"></span> نتيجة</span>
        </div>

    </div>
</div>

<script>
    function soulyCommandPalette() {
        return {
            searchQuery: '',
            activeIndex: 0,
            items: [
                { title: 'نظام BrandERP المتكامل', desc: 'إدارة الموارد ونقاط البيع السحابية (يبدأ من 2,000 ج.م)', category: 'خدمة سحابية', tagClass: 'bg-cyber-cyan/15 text-cyber-cyan border border-cyber-cyan/40', icon: '⚡', url: '/services/brand-erp' },
                { title: 'متجر BrandERP والمميزات الدقيقة', desc: 'اختر موديولاتك بدقة من بين +300 ميزة دقيقة', category: 'متجر سحابي', tagClass: 'bg-cyber-cyan/15 text-cyber-cyan border border-cyber-cyan/40', icon: '💎', url: '/store/branderp' },
                { title: 'حاسبة العائد الاستثماري لـ BrandERP (ROI)', desc: 'احسب توفير التكاليف ونمو الأرباح لقطاعك فورياً', category: 'محاكي وحاسبة', tagClass: 'bg-cyber-gold/15 text-cyber-gold border border-cyber-gold/40', icon: '🎛️', url: '/services/brand-erp#roi' },
                { title: 'موظفو ووكلاء الذكاء الاصطناعي (AI Agents)', desc: 'خدمة عملاء ومبيعات 24/7 برد فوري 1.8s', category: 'ذكاء اصطناعي', tagClass: 'bg-cyber-purple/20 text-cyber-purple border border-cyber-purple/40', icon: '🤖', url: '/services/ai-agents' },
                { title: 'التدريع السيبراني والتحقيقات الجنائية', desc: 'تأمين الشبكات وفحص الثغرات مع ضمان معتمد', category: 'أمن سيبراني', tagClass: 'bg-rose-500/15 text-rose-400 border border-rose-500/30', icon: '🛡️', url: '/services/cybersecurity' },
                { title: 'الصيانة الهندسية للأجهزة والشبكات', desc: 'صيانة ميدانية بمقرك (400 ج.م/ساعة بحد أدنى 3 ساعات)', category: 'صيانة هندسية', tagClass: 'bg-cyber-gold/15 text-cyber-gold border border-cyber-gold/40', icon: '🔧', url: '/services/maintenance' },
                { title: 'الاستشارات وتطوير الأعمال', desc: 'هندسة العمليات والتحول الرقمي وصياغة الاستراتيجيات', category: 'استشارات', tagClass: 'bg-blue-500/15 text-blue-400 border border-blue-500/30', icon: '📈', url: '/services/business-consulting' },
                { title: 'أكاديمية سولي للتعليم التقني', desc: 'معسكرات عملية في الذكاء الاصطناعي والأمن السيبراني', category: 'أكاديمية', tagClass: 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30', icon: '🎓', url: '/services/academy' },
                { title: 'محفظة سولي الرقمية والشحن الفوري', desc: 'إدارة الرصيد، شحن فودافون كاش وإنستاباي', category: 'محفظة', tagClass: 'bg-cyber-gold/15 text-cyber-gold border border-cyber-gold/40', icon: '💳', url: '/portal/wallet' },
                { title: 'حجز ومتابعة الخدمات الهندسية', desc: 'حجز زيارات الصيانة والاشتراكات بالخصم من المحفظة', category: 'حجوزات', tagClass: 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30', icon: '📅', url: '/portal/bookings' },
                { title: 'تذاكر الدعم الهندسي والاستشارات', desc: 'فتح ومتابعة التذاكر الهندسية والضمان', category: 'دعم فني', tagClass: 'bg-cyber-purple/20 text-cyber-purple border border-cyber-purple/40', icon: '🎫', url: '/portal/tickets' },
                { title: 'مركز القيادة Admin HQ', desc: 'إدارة الحجوزات، المحافظ، وخزينة الردود', category: 'إدارة عليا', tagClass: 'bg-cyber-gold/20 text-cyber-gold border border-cyber-gold/40', icon: '🛰️', url: '/admin' }
            ],

            get filteredItems() {
                if (!this.searchQuery.trim()) return this.items;
                const q = this.searchQuery.toLowerCase();
                return this.items.filter(item => 
                    item.title.toLowerCase().includes(q) || 
                    item.desc.toLowerCase().includes(q) || 
                    item.category.toLowerCase().includes(q)
                );
            },

            init() {
                this.$watch('$store.citadel.commandPaletteOpen', (val) => {
                    if (val) {
                        this.searchQuery = '';
                        this.activeIndex = 0;
                        setTimeout(() => this.$refs.searchInput.focus(), 60);
                    }
                });
            },

            navigateDown() {
                this.activeIndex = (this.activeIndex + 1) % this.filteredItems.length;
            },

            navigateUp() {
                this.activeIndex = (this.activeIndex - 1 + this.filteredItems.length) % this.filteredItems.length;
            },

            selectActive() {
                const item = this.filteredItems[this.activeIndex];
                if (item) this.goTo(item.url);
            },

            goTo(url) {
                if (Alpine.store('citadel')) Alpine.store('citadel').commandPaletteOpen = false;
                window.location.href = url;
            }
        };
    }
</script>