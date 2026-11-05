<!-- Souly Digital Citadel: Universal Interactive Icon Popup & Mobile Bottom-Sheet Engine -->
<div x-data="soulyIconPopupEngine()" 
     x-cloak
     @open-icon-popup.window="open($event.detail)"
     @keydown.escape.window="close()"
     class="relative z-[99998]">

    <!-- Modal Backdrop -->
    <div x-show="isOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="close()"
         class="fixed inset-0 bg-black/85 backdrop-blur-md z-[99998] cursor-pointer"></div>

    <!-- Presentation Layer: Responsive Desktop Centered Modal & Mobile Bottom-Sheet -->
    <div x-show="isOpen" 
         @click.self="close()"
         class="fixed inset-0 z-[99999] overflow-hidden flex items-end sm:items-center justify-center p-0 sm:p-4">

        <!-- The Modal Container -->
        <div id="souly-icon-sheet-container"
             @click.stop
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-full sm:translate-y-6 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-full sm:translate-y-6 sm:scale-95"
             class="w-full max-w-2xl rounded-t-3xl sm:rounded-3xl glass-obsidian border border-b-0 sm:border-b border-cyber-cyan/40 shadow-[0_10px_50px_rgba(0,0,0,0.9)] max-h-[88dvh] sm:max-h-[85vh] flex flex-col overflow-hidden text-right font-sans">

            <!-- Mobile Drag Handle Bar -->
            <div class="w-12 h-1.5 rounded-full bg-slate-600 mx-auto my-2.5 sm:hidden shrink-0"></div>

            <!-- Header -->
            <div class="p-4 sm:p-6 border-b border-obsidian-border/80 flex items-start justify-between gap-4 shrink-0 bg-obsidian-surface/90">
                <div class="flex items-center gap-3 sm:gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-cyber-purple/30 via-obsidian-surface to-cyber-cyan/20 border border-cyber-cyan/50 p-2.5 flex items-center justify-center shrink-0 shadow-sm">
                        <svg class="w-7 h-7 text-cyber-cyan" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>

                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-cairo font-bold bg-cyber-cyan/15 text-cyber-cyan border border-cyber-cyan/40 shadow-sm" 
                                  x-text="currentIcon.badge_text || 'ميزة سيادية معتمدة'"></span>
                            <span class="text-xs text-slate-400 font-cairo" x-text="currentIcon.service_name || 'سولي'"></span>
                        </div>
                        <h3 class="text-base sm:text-xl font-extrabold text-white font-cairo leading-snug" 
                            x-text="currentIcon.popup_title || currentIcon.title"></h3>
                    </div>
                </div>

                <button @click="close()" 
                        type="button" 
                        class="p-2 rounded-xl bg-obsidian-surface border border-obsidian-border text-slate-400 hover:text-white hover:border-cyber-cyan/40 transition-colors shrink-0 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Scrollable Body -->
            <div class="p-4 sm:p-6 space-y-4 flex-1 overflow-y-auto overscroll-contain">
                <div>
                    <h4 class="text-xs font-cairo text-slate-400 font-bold mb-1">نظرة عامة على الخدمة</h4>
                    <p class="text-xs sm:text-sm text-slate-200 leading-relaxed font-cairo" 
                       x-text="currentIcon.popup_summary"></p>
                </div>

                <div x-show="currentIcon.popup_bullets && currentIcon.popup_bullets.length > 0">
                    <h4 class="text-xs font-cairo text-cyber-gold font-bold mb-2">أبرز المزايا والقدرات التشغيلية:</h4>
                    <ul class="space-y-2">
                        <template x-for="(bullet, idx) in currentIcon.popup_bullets" :key="idx">
                            <li class="flex items-start gap-2.5 text-xs sm:text-sm text-slate-300 font-cairo">
                                <span class="w-1.5 h-1.5 rounded-full bg-cyber-cyan mt-1.5 shrink-0"></span>
                                <span x-text="bullet"></span>
                            </li>
                        </template>
                    </ul>
                </div>
            </div>

            <!-- Sticky Bottom Footer with CTAs -->
            <div class="sticky bottom-0 inset-x-0 p-4 border-t border-obsidian-border bg-obsidian-base/95 backdrop-blur-xl flex items-center justify-between gap-3 shrink-0 safe-bottom">
                <button @click="close()" 
                        type="button" 
                        class="px-4 py-2.5 rounded-xl btn-citadel-secondary text-xs cursor-pointer">
                    إغلاق
                </button>

                <a :href="currentIcon.cta_link && !currentIcon.cta_link.includes('#booking') ? currentIcon.cta_link : '{{ route('portal.bookings') }}'" 
                   @click.prevent="$dispatch('open-express-booking', {
                       serviceSlug: 'brand-erp',
                       serviceName: currentIcon.service_name || currentIcon.title,
                       packageName: currentIcon.title
                   }); close()"
                   class="px-6 py-2.5 rounded-xl btn-citadel-primary text-xs text-center flex items-center justify-center gap-2 cursor-pointer shadow-neon-cyan">
                    <span x-text="currentIcon.popup_cta_text || 'احجز هذه الميزة الآن'"></span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
            </div>

        </div>
    </div>
</div>

<script>
    function soulyIconPopupEngine() {
        return {
            isOpen: false,
            currentIcon: {
                id: null,
                title: '',
                badge_text: '',
                popup_title: '',
                popup_summary: '',
                popup_bullets: [],
                popup_cta_text: 'احجز هذه الخدمة الآن',
                service_name: 'سولي',
                cta_link: '{{ route('portal.bookings') }}'
            },

            async open(payload) {
                let iconData = payload || {};
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
                    popup_summary: iconData.popup_summary || iconData.summary || 'تفاصيل الخدمة والميزة متوفرة عبر منظومة سولي.',
                    popup_bullets: Array.isArray(iconData.popup_bullets_json) ? iconData.popup_bullets_json : (iconData.popup_bullets || []),
                    popup_cta_text: iconData.popup_cta_text || 'احجز هذه الخدمة الآن',
                    service_name: iconData.service_name || 'سولي',
                    cta_link: iconData.cta_link || '{{ route('portal.bookings') }}'
                };

                this.isOpen = true;
                if (window.SoulyUI) window.SoulyUI.lockScroll('icon-popup');
            },

            close() {
                this.isOpen = false;
                if (window.SoulyUI) window.SoulyUI.unlockScroll('icon-popup');
                window.dispatchEvent(new CustomEvent('close-icon-popup'));
            }
        };
    }
</script>