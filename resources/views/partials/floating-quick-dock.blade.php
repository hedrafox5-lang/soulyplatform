@if(request()->routeIs('home'))
<!-- ===================================================================== -->
<!-- SOULY DESKTOP FLOATING QUICK-JUMP SECTOR DOCK & SCROLL-PROGRESS RING   -->
<!-- ===================================================================== -->
<div x-data="soulyFloatingQuickDockController()" 
     x-cloak
     class="pointer-events-none">

    <!-- 1. Centered Pill Navigation (Strictly Desktop >= 768px) -->
    <div x-show="!collapsed" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 scale-95"
         class="fixed bottom-5 left-1/2 -translate-x-1/2 z-40 max-w-[94vw] pointer-events-auto hidden md:block"
         dir="rtl">
        <nav class="flex items-center gap-1.5 p-1.5 rounded-2xl glass-obsidian border border-cyber-cyan/40 shadow-[0_10px_35px_rgba(0,0,0,0.85),0_0_20px_rgba(0,245,212,0.15)] backdrop-blur-xl overflow-x-auto scrollbar-none"
             aria-label="شريط الانتقال السريع لقطاعات سولي">
            
            <template x-for="sector in sectors" :key="sector.id">
                <button type="button" 
                        @click="scrollToSector(sector.id)"
                        class="group relative flex items-center gap-1.5 px-3 py-1.5 rounded-xl transition-all duration-200 shrink-0 text-xs font-cairo font-bold cursor-pointer"
                        :class="activeSector === sector.id 
                            ? 'bg-white/15 text-white shadow-sm' 
                            : 'text-slate-300 hover:text-white hover:bg-white/5'"
                        :style="activeSector === sector.id 
                            ? `border: 1px solid ${sector.color}; background-color: ${sector.color}22; box-shadow: 0 0 14px ${sector.color}44;` 
                            : 'border: 1px solid transparent;'">
                    
                    <span class="text-sm shrink-0 transition-transform group-hover:scale-110" x-text="sector.icon"></span>
                    
                    <span class="whitespace-nowrap transition-colors"
                          :style="activeSector === sector.id ? `color: ${sector.color}; font-weight: 800;` : ''"
                          x-text="sector.label"></span>

                    <span x-show="activeSector === sector.id" 
                          class="w-1.5 h-1.5 rounded-full animate-ping shrink-0"
                          :style="`background-color: ${sector.color}`"></span>
                </button>
            </template>

            <div class="h-5 w-[1px] bg-obsidian-border/80 mx-1 shrink-0"></div>

            <button type="button" 
                    @click="collapsed = true" 
                    title="تصغير شريط التنقل"
                    class="p-1.5 rounded-xl hover:bg-white/10 text-slate-400 hover:text-cyber-cyan border border-transparent hover:border-cyber-cyan/30 transition-all text-xs font-mono font-bold flex items-center justify-center shrink-0 cursor-pointer"
                    aria-label="تصغير شريط التنقل">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
        </nav>
    </div>

    <!-- 2. Minimalist Floating Badge (Collapsed State) -->
    <div x-show="collapsed" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-3 scale-90"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-3 scale-90"
         class="fixed bottom-5 left-1/2 -translate-x-1/2 z-40 pointer-events-auto hidden md:block"
         dir="rtl">
        <button type="button" 
                @click="collapsed = false" 
                title="توسيع شريط التنقل السريع"
                class="px-4 py-2 rounded-full glass-obsidian border border-cyber-cyan/50 shadow-neon-cyan hover:border-cyber-cyan text-xs font-cairo font-bold text-cyber-cyan flex items-center gap-2 hover:scale-105 active:scale-95 transition-all cursor-pointer">
            <span class="w-2 h-2 rounded-full bg-cyber-cyan animate-pulse"></span>
            <span>⚡ قطاعات سولي (توسيع)</span>
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7" />
            </svg>
        </button>
    </div>

    <!-- 3. Back-to-Top Button with Circular SVG Scroll-Progress Ring (Safely offset on mobile) -->
    <div class="fixed bottom-20 md:bottom-5 right-4 md:right-5 z-40 pointer-events-auto transition-all duration-300"
         :class="showBackToTop ? 'opacity-100 translate-y-0 scale-100 pointer-events-auto' : 'opacity-0 translate-y-4 scale-95 pointer-events-none'">
        <button type="button" 
                @click="scrollToTop()"
                title="العودة لأعلى الصفحة"
                class="group relative w-11 h-11 rounded-2xl glass-obsidian border border-cyber-cyan/40 shadow-[0_8px_32px_rgba(0,0,0,0.85),0_0_16px_rgba(0,245,212,0.25)] hover:border-cyber-cyan hover:shadow-[0_8px_36px_rgba(0,0,0,0.95),0_0_24px_rgba(0,245,212,0.45)] flex items-center justify-center transition-all active:scale-95 cursor-pointer">
            
            <svg class="absolute inset-0 w-full h-full -rotate-90 pointer-events-none" viewBox="0 0 36 36">
                <path class="text-obsidian-border stroke-current"
                      stroke-width="2.5"
                      fill="none"
                      d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                <path class="text-cyber-cyan stroke-current transition-all duration-150"
                      stroke-dasharray="100, 100"
                      :stroke-dashoffset="100 - scrollPercent"
                      stroke-linecap="round"
                      stroke-width="2.8"
                      fill="none"
                      d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
            </svg>

            <div class="relative flex flex-col items-center justify-center">
                <svg class="w-4 h-4 text-cyber-cyan group-hover:-translate-y-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                </svg>
            </div>
        </button>
    </div>

</div>

<script>
    function soulyFloatingQuickDockController() {
        return {
            collapsed: false,
            showBackToTop: false,
            scrollPercent: 0,
            activeSector: '',
            isTicking: false,
            sectors: [
                { id: 'service-brand-erp', label: 'BrandERP', icon: '🏪', color: '#00F5D4' },
                { id: 'service-ai-agents', label: 'موظفو AI', icon: '🤖', color: '#9D4EDD' },
                { id: 'service-cybersecurity', label: 'الأمن السيبراني', icon: '🛡️', color: '#FF2A55' },
                { id: 'service-maintenance', label: 'الصيانة الهندسية', icon: '🔧', color: '#FFB800' },
                { id: 'service-business-consulting', label: 'الاستشارات', icon: '📈', color: '#10B981' },
                { id: 'service-academy', label: 'الأكاديمية', icon: '🎓', color: '#3B82F6' }
            ],

            init() {
                this.updateScroll();
                window.addEventListener('scroll', () => {
                    if (!this.isTicking) {
                        window.requestAnimationFrame(() => {
                            this.updateScroll();
                            this.isTicking = false;
                        });
                        this.isTicking = true;
                    }
                }, { passive: true });
            },

            updateScroll() {
                const scrollY = window.scrollY || window.pageYOffset;
                const docHeight = document.documentElement.scrollHeight - window.innerHeight;

                this.showBackToTop = scrollY > 300;
                this.scrollPercent = docHeight > 0 ? Math.min(100, Math.max(0, Math.round((scrollY / docHeight) * 100))) : 0;

                const scrollPosition = scrollY + (window.innerHeight * 0.35);
                let currentActive = '';

                for (let i = 0; i < this.sectors.length; i++) {
                    const el = document.getElementById(this.sectors[i].id);
                    if (el) {
                        const top = el.offsetTop;
                        const height = el.offsetHeight;
                        if (scrollPosition >= top && scrollPosition < top + height) {
                            currentActive = this.sectors[i].id;
                            break;
                        }
                    }
                }

                if (currentActive) {
                    this.activeSector = currentActive;
                }
            },

            scrollToSector(id) {
                const el = document.getElementById(id);
                if (el) {
                    const navOffset = 80;
                    const bodyRect = document.body.getBoundingClientRect().top;
                    const elementRect = el.getBoundingClientRect().top;
                    const elementPosition = elementRect - bodyRect;
                    const offsetPosition = elementPosition - navOffset;

                    window.scrollTo({
                        top: offsetPosition,
                        behavior: 'smooth'
                    });
                }
            },

            scrollToTop() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }
        };
    }
</script>
@endif