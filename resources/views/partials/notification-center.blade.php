<!-- Souly Digital Citadel: Glassmorphic Notification Center Sheet -->
<div id="souly-notification-center" 
     x-show="$store.citadel.notificationCenterOpen" 
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0 translate-y-2"
     x-transition:enter-end="opacity-100 translate-y-0"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100 translate-y-0"
     x-transition:leave-end="opacity-0 translate-y-2"
     class="fixed inset-0 z-[99999] flex items-start justify-end p-4 sm:p-6 bg-black/60 backdrop-blur-md"
     style="display: none;"
     @click.self="$store.citadel.notificationCenterOpen = false"
     dir="rtl">

    <div class="max-w-md w-full glass-obsidian border border-cyber-cyan/40 rounded-3xl p-5 shadow-neon-cyan space-y-4 overflow-hidden mt-12 sm:mt-16">
        
        <!-- Header -->
        <div class="flex items-center justify-between border-b border-obsidian-border/80 pb-3">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-cyber-gold/15 text-cyber-gold border border-cyber-gold/30 flex items-center justify-center">
                    <svg class="w-4 h-4 animate-bounce" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-extrabold text-sm text-white font-cairo">مركز الإشعارات والتنبيهات السيادية</h3>
                    <span class="text-[10px] font-mono text-slate-400">TELEMETRY & STATUS FEED</span>
                </div>
            </div>

            <button @click="$store.citadel.notificationCenterOpen = false" class="p-1.5 rounded-lg text-slate-400 hover:text-white">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Notification Cards Feed -->
        <div class="space-y-2.5 max-h-96 overflow-y-auto pr-1 custom-scroll">
            
            <!-- Beacon 1: Security -->
            <div class="p-3.5 rounded-2xl bg-obsidian-surface border border-cyber-cyan/30 flex items-start gap-3">
                <div class="w-2 h-2 rounded-full bg-cyber-cyan animate-ping mt-1.5 shrink-0"></div>
                <div class="space-y-1">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-white font-cairo">التدريع السيبراني 256-bit نشط</span>
                        <span class="text-[9px] font-mono text-slate-500">منذ دقيقة</span>
                    </div>
                    <p class="text-[11px] text-slate-300 font-readex leading-relaxed">
                        عقد الحماية وجدار الحماية السحابي يعملان بكفاءة 100%، وتم التحقق من سلامة الجلسة الحالية.
                    </p>
                </div>
            </div>

            <!-- Beacon 2: Wallet / Balance -->
            <div class="p-3.5 rounded-2xl bg-obsidian-surface border border-cyber-gold/30 flex items-start gap-3">
                <div class="w-2 h-2 rounded-full bg-cyber-gold animate-pulse mt-1.5 shrink-0"></div>
                <div class="space-y-1">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-white font-cairo">محفظة سولي الرقمية جاهزة</span>
                        <span class="text-[9px] font-mono text-slate-500">لحظي</span>
                    </div>
                    <p class="text-[11px] text-slate-300 font-readex leading-relaxed">
                        رصيدك متاح لإجراء الحجوزات والاشتراكات الفورية بضغطة زر وبدون رسوم تحويل إضافية.
                    </p>
                </div>
            </div>

            <!-- Beacon 3: Engineering Team Standby -->
            <div class="p-3.5 rounded-2xl bg-obsidian-surface border border-cyber-purple/30 flex items-start gap-3">
                <div class="w-2 h-2 rounded-full bg-cyber-purple animate-pulse mt-1.5 shrink-0"></div>
                <div class="space-y-1">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-white font-cairo">طاقم المهندسين في وضع الجاهزية</span>
                        <span class="text-[9px] font-mono text-slate-500">24/7</span>
                    </div>
                    <p class="text-[11px] text-slate-300 font-readex leading-relaxed">
                        فريق صيانة الأجهزة والشبكات مستعد لتغطية زيارات المقرات الفورية في القاهرة الكبرى.
                    </p>
                </div>
            </div>

            <!-- Beacon 4: AI Agent Node -->
            <div class="p-3.5 rounded-2xl bg-obsidian-surface border border-emerald-500/30 flex items-start gap-3">
                <div class="w-2 h-2 rounded-full bg-emerald-400 mt-1.5 shrink-0"></div>
                <div class="space-y-1">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-white font-cairo">سولي AI مستعد للاستشارات</span>
                        <span class="text-[9px] font-mono text-slate-500">متصل</span>
                    </div>
                    <p class="text-[11px] text-slate-300 font-readex leading-relaxed">
                        يمكنك فتح ويدجت المحادثة الذكية لطرح أي استفسار تسعيري أو تقني والحصول على إجابة هندسية فورية.
                    </p>
                </div>
            </div>

        </div>

        <!-- Footer -->
        <div class="pt-2 border-t border-obsidian-border/80 flex items-center justify-between text-xs text-slate-400 font-mono">
            <span class="text-cyber-cyan">4 تنبيهات مفعلة</span>
            <button @click="$store.citadel.notificationCenterOpen = false" class="text-slate-400 hover:text-white font-cairo">
                إغلاق النافذة ✕
            </button>
        </div>

    </div>
</div>
