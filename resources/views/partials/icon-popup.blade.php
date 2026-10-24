<!-- Souly Digital Citadel: Universal Interactive Icon Popup & Mobile Bottom-Sheet Engine -->
<div x-data="soulyIconPopupEngine()" 
     x-cloak
     @open-icon-popup.window="open($event.detail)"
     class="relative z-50">

    <!-- Modal Backdrop -->
    <div x-show="isOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="close()"
         @keydown.escape.window="close()"
         class="fixed inset-0 bg-black/75 backdrop-blur-md transition-opacity"></div>

    <!-- Presentation Layer: Responsive Desktop 3D Modal & Mobile Bottom-Sheet -->
    <div x-show="isOpen" 
         class="fixed inset-0 z-50 overflow-hidden pointer-events-none flex"
         :class="isMobile ? 'items-end justify-center' : 'items-center justify-center p-4'">

        <!-- The Modal / Bottom-Sheet Container -->
        <div id="souly-icon-sheet-container"
             @click.away="close()"
             class="pointer-events-auto w-full transition-all duration-300 overflow-hidden text-right font-sans"
             :class="isMobile ? 
                'max-w-lg rounded-t-3xl glass-obsidian border-t border-x border-obsidian-border/90 p-6 safe-bottom shadow-[0_-15px_40px_rgba(0,0,0,0.8)] max-h-[85vh] overflow-y-auto' : 
                'max-w-2xl rounded-3xl glass-obsidian border border-cyber-cyan/40 p-8 shadow-neon-cyan relative transform'">

            <!-- Ambient Glow Effect -->
            <div class="absolute -top-16 -left-16 w-48 h-48 rounded-full bg-cyber-purple/20 blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-16 -right-16 w-48 h-48 rounded-full bg-cyber-cyan/15 blur-3xl pointer-events-none"></div>

            <!-- Mobile Drag Handle Bar -->
            <template x-if="isMobile">
                <div class="w-12 h-1.5 rounded-full bg-obsidian-border mx-auto mb-5"></div>
            </template>

            <!-- Header: Icon, Badge & Close Button -->
            <div class="relative z-10 flex items-start justify-between gap-4 pb-4 border-b border-obsidian-border/70">
                <div class="flex items-center gap-4">
                    <!-- Icon Glow Box -->
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-cyber-purple/30 via-obsidian-surface to-cyber-cyan/20 border border-cyber-cyan/50 p-3 flex items-center justify-center shrink-0 shadow-neon-cyan">
                        <svg class="w-8 h-8 text-cyber-cyan" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>

                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-cyber-cyan/10 border border-cyber-cyan/40 text-cyber-cyan uppercase" 
                                  x-text="currentIcon.badge_text || 'ميزة سيادية معتمدة'"></span>
                            <span class="text-xs text-slate-400 font-cairo" x-text="currentIcon.service_name || 'سولي سيتادل'"></span>
                        </div>
                        <h3 class="text-xl sm:text-2xl font-extrabold text-white font-cairo leading-snug" 
                            x-text="currentIcon.popup_title || currentIcon.title"></h3>
                    </div>
                </div>

                <!-- Close Button -->
                <button @click="close()" 
                        type="button" 
                        class="p-2 rounded-xl bg-obsidian-surface/90 border border-obsidian-border text-slate-400 hover:text-white hover:border-cyber-cyan/40 transition-colors shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Body: Summary & Bullet List -->
            <div class="relative z-10 py-5 space-y-4">
                <div>
                    <h4 class="text-xs font-mono uppercase tracking-wider text-slate-400 font-bold mb-1">نظرة عامة على الخدمة</h4>
                    <p class="text-sm text-slate-200 leading-relaxed font-readex" 
                       x-text="currentIcon.popup_summary"></p>
                </div>

                <!-- Bullets List -->
                <div x-show="currentIcon.popup_bullets && currentIcon.popup_bullets.length > 0">
                    <h4 class="text-xs font-mono uppercase tracking-wider text-cyber-gold font-bold mb-2">أبرز المزايا والقدرات التشغيلية:</h4>
                    <ul class="space-y-2">
                        <template x-for="(bullet, idx) in currentIcon.popup_bullets" :key="idx">
                            <li class="flex items-start gap-2.5 text-xs sm:text-sm text-slate-300">
                                <span class="w-1.5 h-1.5 rounded-full bg-cyber-cyan mt-2 shrink-0"></span>
                                <span x-text="bullet"></span>
                            </li>
                        </template>
                    </ul>
                </div>
            </div>

            <!-- Footer: CTA & Direct Booking Trigger -->
            <div class="relative z-10 pt-4 border-t border-obsidian-border/70 flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="flex items-center gap-2 text-xs font-mono text-slate-400">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    <span>سرعة تفعيل فورية · ضمان معتمد</span>
                </div>

                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <button @click="close()" 
                            type="button" 
                            class="flex-1 sm:flex-none px-4 py-2.5 rounded-xl bg-obsidian-surface border border-obsidian-border text-slate-300 hover:text-white text-xs font-cairo font-semibold transition-all">
                        إغلاق النافذة
                    </button>
                    <a :href="currentIcon.cta_link || '{{ url('/portal#booking') }}'" 
                       class="flex-1 sm:flex-none px-6 py-2.5 rounded-xl bg-gradient-to-r from-cyber-cyan via-cyber-gold to-cyber-purple text-obsidian-base font-extrabold font-cairo text-xs shadow-neon-cyan hover:scale-[1.02] active:scale-95 transition-all text-center flex items-center justify-center gap-2">
                        <span x-text="currentIcon.popup_cta_text || 'احجز هذه الميزة الآن'"></span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    function soulyIconPopupEngine() {
        return {
            isOpen: false,
            isMobile: window.innerWidth < 1024,
            currentIcon: {
                id: null,
                title: '',
                badge_text: '',
                popup_title: '',
                popup_summary: '',
                popup_bullets: [],
                popup_cta_text: 'احجز هذه الخدمة الآن',
                service_name: 'سولي سيتادل',
                cta_link: '{{ url('/portal#booking') }}'
            },

            init() {
                window.addEventListener('resize', () => {
                    this.isMobile = window.innerWidth < 1024;
                });
            },

            async open(payload) {
                // Check SoulyDB for 0ms cached record if payload only has an ID or slug
                let iconData = payload;
                if (payload && payload.id && (!payload.popup_summary || !payload.popup_bullets)) {
                    if (window.SoulyDB) {
                        try {
                            const cached = await window.SoulyDB.get(`icon_${payload.id}`);
                            if (cached) iconData = cached;
                        } catch (e) {}
                    }
                }

                this.currentIcon = {
                    id: iconData.id || null,
                    title: iconData.title || '',
                    badge_text: iconData.badge_text || '',
                    popup_title: iconData.popup_title || iconData.title || '',
                    popup_summary: iconData.popup_summary || iconData.summary || 'تفاصيل الخدمة والميزة متوفرة عبر منظومة سولي ديدجيتال سيتادل.',
                    popup_bullets: Array.isArray(iconData.popup_bullets_json) ? iconData.popup_bullets_json : (iconData.popup_bullets || []),
                    popup_cta_text: iconData.popup_cta_text || 'احجز هذه الخدمة الآن',
                    service_name: iconData.service_name || 'سولي سيتادل',
                    cta_link: iconData.cta_link || '{{ url('/portal#booking') }}'
                };

                this.isOpen = true;

                // Animate entry via GSAP
                this.$nextTick(() => {
                    const el = document.getElementById('souly-icon-sheet-container');
                    if (el && typeof gsap !== 'undefined') {
                        if (this.isMobile) {
                            gsap.fromTo(el, 
                                { y: '100%', opacity: 0 }, 
                                { y: '0%', opacity: 1, duration: 0.45, ease: 'power3.out' }
                            );
                        } else {
                            gsap.fromTo(el, 
                                { scale: 0.9, opacity: 0, y: 20 }, 
                                { scale: 1, opacity: 1, y: 0, duration: 0.35, ease: 'back.out(1.4)' }
                            );
                        }
                    }
                });
            },

            close() {
                const el = document.getElementById('souly-icon-sheet-container');
                if (el && typeof gsap !== 'undefined') {
                    if (this.isMobile) {
                        gsap.to(el, {
                            y: '100%',
                            opacity: 0,
                            duration: 0.25,
                            ease: 'power2.in',
                            onComplete: () => { this.isOpen = false; }
                        });
                    } else {
                        gsap.to(el, {
                            scale: 0.95,
                            opacity: 0,
                            duration: 0.2,
                            ease: 'power2.in',
                            onComplete: () => { this.isOpen = false; }
                        });
                    }
                } else {
                    this.isOpen = false;
                }
            }
        };
    }
</script>
