<!-- Souly Digital Citadel: Glassmorphic Notification Center Sheet -->
<div id="souly-notification-center" 
     x-show="$store.citadel && $store.citadel.notificationCenterOpen" 
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0 translate-y-2"
     x-transition:enter-end="opacity-100 translate-y-0"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100 translate-y-0"
     x-transition:leave-end="opacity-0 translate-y-2"
     class="fixed inset-0 z-[99999] flex items-start justify-end p-3 sm:p-6 bg-black/60 backdrop-blur-md"
     style="display: none;"
     @click.self="$store.citadel.notificationCenterOpen = false"
     dir="rtl">

    <div class="max-w-md w-full glass-obsidian border border-cyber-cyan/40 rounded-3xl p-4 sm:p-5 shadow-[0_10px_40px_rgba(0,0,0,0.85)] space-y-4 overflow-hidden mt-12 sm:mt-16 safe-bottom"
         @click.stop>
        
        <!-- Header -->
        <div class="flex items-center justify-between border-b border-obsidian-border/80 pb-3">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-cyber-gold/15 text-cyber-gold border border-cyber-gold/30 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 animate-bounce" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-extrabold text-xs sm:text-sm text-white font-cairo">مركز الإشعارات والتنبيهات السيادية</h3>
                    <span class="text-[9px] sm:text-[10px] font-mono text-slate-400">TELEMETRY & NOTIFICATION FEED</span>
                </div>
            </div>

            <div class="flex items-center gap-2">
                @auth
                <button type="button" 
                        x-show="$store.citadel && $store.citadel.unreadNotificationsCount > 0"
                        @click="$store.citadel.markAllNotificationsRead()" 
                        class="text-[10px] font-cairo text-cyber-cyan hover:text-emerald-400 font-bold px-2 py-1 rounded-lg bg-cyber-cyan/10 border border-cyber-cyan/30 hover:bg-cyber-cyan/20 transition-all cursor-pointer">
                    مقروء الكل ✓
                </button>
                @endauth

                <button @click="$store.citadel.notificationCenterOpen = false" class="p-1.5 rounded-lg text-slate-400 hover:text-white transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Notification Cards Feed Container -->
        <div class="space-y-2.5 max-h-80 sm:max-h-96 overflow-y-auto pr-1 overscroll-contain">
            @auth
                <template x-if="$store.citadel.notificationsLoading">
                    <div class="space-y-2.5">
                        <div class="p-3.5 rounded-2xl bg-obsidian-surface border border-obsidian-border animate-pulse space-y-2">
                            <div class="h-3 bg-slate-700 rounded w-1/2"></div>
                            <div class="h-2 bg-slate-800 rounded w-3/4"></div>
                        </div>
                    </div>
                </template>

                <div x-show="!$store.citadel.notificationsLoading && $store.citadel.notificationsList.length === 0" 
                     class="py-8 text-center space-y-2">
                    <div class="w-10 h-10 rounded-2xl bg-cyber-cyan/10 border border-cyber-cyan/30 text-cyber-cyan mx-auto flex items-center justify-center">
                        ✓
                    </div>
                    <h4 class="text-xs font-bold text-white font-cairo">لا توجد إشعارات جديدة حالياً</h4>
                </div>

                <template x-for="notif in $store.citadel.notificationsList" :key="notif.id">
                    <div @click="$store.citadel.clickNotification(notif)"
                         :class="{
                             'border-cyber-gold/40 hover:border-cyber-gold': notif.type === 'wallet',
                             'border-cyber-cyan/40 hover:border-cyber-cyan': notif.type === 'booking',
                             'border-cyber-purple/40 hover:border-cyber-purple': notif.type === 'ticket',
                             'border-emerald-500/40 hover:border-emerald-500': notif.type === 'crm',
                             'border-obsidian-border hover:border-slate-500': notif.type === 'info',
                             'bg-obsidian-surface/90': !notif.is_read,
                             'bg-obsidian-surface/40 opacity-75': notif.is_read
                         }"
                         class="p-3 rounded-2xl border transition-all cursor-pointer group flex items-start gap-3 relative">
                        
                        <div class="mt-1.5 shrink-0">
                            <div class="w-2 h-2 rounded-full" :class="notif.is_read ? 'bg-slate-600' : 'bg-rose-500 animate-ping'"></div>
                        </div>

                        <div class="space-y-1 flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-xs font-bold text-white font-cairo truncate" x-text="notif.title"></span>
                                <span class="text-[9px] font-cairo text-slate-400 shrink-0 font-mono" x-text="notif.created_at"></span>
                            </div>
                            <p class="text-[11px] text-slate-300 font-cairo leading-relaxed line-clamp-2" x-text="notif.message"></p>
                        </div>
                    </div>
                </template>
            @endauth

            @guest
                <div class="p-3.5 rounded-2xl bg-obsidian-surface border border-cyber-cyan/30 flex items-start gap-3">
                    <div class="w-2 h-2 rounded-full bg-cyber-cyan animate-ping mt-1.5 shrink-0"></div>
                    <div class="space-y-1">
                        <span class="text-xs font-bold text-white font-cairo block">التدريع السيبراني 256-bit نشط</span>
                        <p class="text-[11px] text-slate-300 font-cairo leading-relaxed">
                            عقد الحماية السحابية تعمل بكفاءة 100%.
                        </p>
                    </div>
                </div>

                <div class="p-3 rounded-2xl bg-gradient-to-r from-cyber-cyan/10 to-cyber-purple/10 border border-cyber-cyan/30 text-center space-y-2">
                    <p class="text-[11px] text-slate-300 font-cairo">سجل دخولك لتفعيل سجل إشعارات حسابك وتنبيهات الحجوزات.</p>
                    <a href="{{ route('login') }}" class="btn-citadel-primary py-1.5 px-4 text-xs inline-flex rounded-xl font-bold">
                        تسجيل الدخول الآن
                    </a>
                </div>
            @endguest
        </div>

        <div class="pt-2 border-t border-obsidian-border/80 flex items-center justify-between text-xs text-slate-400 font-cairo">
            <button @click="$store.citadel.notificationCenterOpen = false" class="text-slate-400 hover:text-white font-cairo transition-colors cursor-pointer mr-auto">
                إغلاق ✕
            </button>
        </div>

    </div>
</div>