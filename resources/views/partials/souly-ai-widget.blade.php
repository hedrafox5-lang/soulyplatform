<!-- ===================================================================== -->
<!-- SOULY AI INTERACTIVE AGENT WIDGET (DESKTOP FLOATING & MOBILE DOCK)    -->
<!-- ===================================================================== -->
<div x-data="soulyAiWidgetController()" 
     @open-souly-ai.window="openWidget()"
     class="relative z-50">

    <!-- Desktop Floating Orb Trigger -->
    <div class="hidden lg:block fixed bottom-6 left-6 z-40">
        <button type="button" @click="toggleWidget()" 
                class="group relative flex items-center gap-3 p-3.5 rounded-2xl bg-gradient-to-r from-obsidian-surface to-obsidian-card border border-cyber-cyan/50 shadow-neon-cyan hover:scale-105 active:scale-95 transition-all">
            <!-- Pulsing AI Core -->
            <div class="relative w-8 h-8 rounded-xl bg-gradient-to-tr from-cyber-purple via-cyber-cyan to-cyber-gold p-[1.5px] flex items-center justify-center">
                <div class="w-full h-full bg-obsidian-surface rounded-xl flex items-center justify-center">
                    <svg class="w-4 h-4 text-cyber-cyan group-hover:rotate-12 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <span class="absolute -top-1 -right-1 w-2.5 h-2.5 bg-cyber-cyan rounded-full animate-ping"></span>
            </div>

            <div class="text-right">
                <div class="flex items-center gap-1.5">
                    <span class="text-xs font-bold text-white font-cairo">سولي AI</span>
                    <span class="px-1.5 py-0.2 rounded text-[9px] font-mono bg-cyber-cyan/20 text-cyber-cyan">ONLINE</span>
                </div>
                <div class="text-[10px] text-slate-400 font-readex">مستشارك الهندسي الذكي</div>
            </div>
        </button>
    </div>

    <!-- Main AI Chat Window Overlay (Desktop Floating / Mobile Bottom Sheet) -->
    <div x-show="isOpen || $store.citadel.aiSheetOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-8 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-8 scale-95"
         class="fixed inset-x-4 bottom-20 sm:bottom-6 sm:left-6 sm:right-auto sm:w-[420px] max-h-[85vh] h-[600px] z-50 glass-obsidian border border-cyber-cyan/40 rounded-3xl shadow-neon-cyan flex flex-col justify-between overflow-hidden"
         style="display: none;">
        
        <!-- Chat Header Strip -->
        <div class="p-4 border-b border-obsidian-border bg-obsidian-surface/90 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="relative w-8 h-8 rounded-xl bg-gradient-to-tr from-cyber-purple to-cyber-cyan p-[1px]">
                    <div class="w-full h-full bg-obsidian-surface rounded-xl flex items-center justify-center font-bold text-cyber-cyan text-xs">
                        AI
                    </div>
                    <span class="absolute -bottom-0.5 -right-0.5 w-2 h-2 rounded-full bg-emerald-400"></span>
                </div>
                <div>
                    <div class="flex items-center gap-1.5">
                        <span class="text-xs font-bold text-white font-cairo">مستشار سولي AI</span>
                        <span class="text-[9px] font-mono text-cyber-cyan px-1.5 py-0.5 rounded bg-cyber-cyan/10">Hybrid Engine</span>
                    </div>
                    <span class="text-[10px] text-slate-400 font-readex">مدرب على كافة أنظمة وأسعار قلعة سولي</span>
                </div>
            </div>

            <button type="button" @click="closeWidget()" class="p-1.5 text-slate-400 hover:text-white rounded-lg hover:bg-obsidian-border transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Messages Viewport -->
        <div class="flex-1 p-4 overflow-y-auto space-y-3 font-readex text-xs" x-ref="chatMessages">
            
            <!-- Welcome Bot Greeting -->
            <div class="flex items-start gap-2.5">
                <div class="w-6 h-6 rounded-lg bg-cyber-cyan/20 text-cyber-cyan flex items-center justify-center shrink-0 text-[10px] font-bold">
                    AI
                </div>
                <div class="p-3.5 rounded-2xl rounded-tr-none bg-obsidian-surface border border-cyber-cyan/30 text-slate-200 leading-relaxed space-y-2">
                    <p>مرحباً بك في قلعة سولي الرقمية! 🛡️ أنا مستشارك الذكي، كيف يمكنني مساعدتك في تطوير أعمالك أو حجز خدماتك اليوم؟</p>
                    
                    <!-- Quick Prompt Chips -->
                    <div class="flex flex-wrap gap-1.5 pt-1">
                        <button type="button" @click="sendPrompt('أسعار باقات نظام BrandERP المتكامل')" class="px-2.5 py-1 rounded-lg bg-obsidian-base hover:bg-slate-800 text-cyber-cyan border border-obsidian-border text-[10px] font-cairo transition-all">
                            أسعار باقات BrandERP
                        </button>
                        <button type="button" @click="sendPrompt('ما هي مميزات وتكلفة موظف الذكاء الاصطناعي؟')" class="px-2.5 py-1 rounded-lg bg-obsidian-base hover:bg-slate-800 text-cyber-cyan border border-obsidian-border text-[10px] font-cairo transition-all">
                            موظفو الـ AI
                        </button>
                        <button type="button" @click="sendPrompt('تفاصيل عرض خصم 50% على صيانة الأجهزة والشبكات')" class="px-2.5 py-1 rounded-lg bg-obsidian-base hover:bg-slate-800 text-cyber-gold border border-obsidian-border text-[10px] font-cairo transition-all">
                            صيانة بخصم 50%
                        </button>
                        <button type="button" @click="sendPrompt('كيف أقوم بشحن رصيد محفظتي في المنصة؟')" class="px-2.5 py-1 rounded-lg bg-obsidian-base hover:bg-slate-800 text-emerald-400 border border-obsidian-border text-[10px] font-cairo transition-all">
                            شحن المحفظة
                        </button>
                    </div>
                </div>
            </div>

            <!-- Dynamic Messages Loop -->
            <template x-for="(msg, idx) in messages" :key="idx">
                <div class="flex items-start gap-2.5" :class="msg.sender === 'visitor' ? 'flex-row-reverse' : ''">
                    <div class="w-6 h-6 rounded-lg flex items-center justify-center shrink-0 text-[10px] font-bold"
                         :class="msg.sender === 'visitor' ? 'bg-cyber-purple/30 text-purple-300' : 'bg-cyber-cyan/20 text-cyber-cyan'">
                        <span x-text="msg.sender === 'visitor' ? 'أنت' : 'AI'"></span>
                    </div>

                    <div class="max-w-[85%] p-3.5 rounded-2xl text-slate-200 leading-relaxed whitespace-pre-line space-y-2.5"
                         :class="msg.sender === 'visitor' 
                            ? 'bg-cyber-purple/20 border border-cyber-purple/40 rounded-tl-none text-right' 
                            : 'bg-obsidian-surface border border-obsidian-border rounded-tr-none text-right'">
                        
                        <div x-text="msg.content"></div>

                        <!-- Actionable Package Recommendation Cards -->
                        <template x-if="msg.suggested_links && msg.suggested_links.length > 0">
                            <div class="space-y-1.5 pt-2 border-t border-obsidian-border/50">
                                <span class="text-[10px] font-mono text-cyber-gold block font-bold">باقات مقترحة ومباشرة:</span>
                                <div class="grid grid-cols-1 gap-1.5">
                                    <template x-for="sug in msg.suggested_links" :key="sug.name">
                                        <a :href="sug.link" target="_blank" 
                                           class="p-2 rounded-xl bg-obsidian-base border border-cyber-cyan/30 hover:border-cyber-cyan flex items-center justify-between text-[11px] font-cairo transition-all group">
                                            <span class="font-bold text-white group-hover:text-cyber-cyan" x-text="sug.name"></span>
                                            <span class="font-mono text-cyber-gold" x-text="sug.price"></span>
                                        </a>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </template>

            <!-- Typing Indicator Animation -->
            <div x-show="isTyping" class="flex items-center gap-2 text-slate-400 text-xs py-1" style="display: none;">
                <div class="w-6 h-6 rounded-lg bg-cyber-cyan/10 text-cyber-cyan flex items-center justify-center text-[10px]">AI</div>
                <div class="flex items-center gap-1 p-2 rounded-xl bg-obsidian-surface border border-obsidian-border">
                    <span class="w-1.5 h-1.5 rounded-full bg-cyber-cyan animate-pulse"></span>
                    <span class="w-1.5 h-1.5 rounded-full bg-cyber-gold animate-pulse" style="animation-delay: 150ms;"></span>
                    <span class="w-1.5 h-1.5 rounded-full bg-cyber-purple animate-pulse" style="animation-delay: 300ms;"></span>
                    <span class="text-[10px] text-slate-400 font-mono mr-1">سولي AI يكتب...</span>
                </div>
            </div>

            <!-- In-Chat Mini Phone Capture Widget -->
            <div x-show="!phoneCaptured && messages.length >= 2" class="p-3 rounded-2xl bg-gradient-to-r from-cyber-cyan/10 to-obsidian-surface border border-cyber-cyan/30 space-y-2">
                <span class="text-[11px] font-bold text-white font-cairo block">هل تود أن يتواصل معك مهندس حسابات مختص؟</span>
                <div class="flex items-center gap-1.5">
                    <input type="text" x-model="quickPhone" placeholder="أدخل رقم هاتفك (010XXXXXXXX)" 
                           class="flex-1 bg-obsidian-base border border-obsidian-border text-white text-[11px] rounded-xl px-2.5 py-1.5 font-mono focus:outline-none focus:border-cyber-cyan">
                    <button type="button" @click="submitQuickPhone()" class="px-3 py-1.5 rounded-xl bg-cyber-cyan text-obsidian-base font-bold text-[10px] font-cairo hover:opacity-90">
                        إرسال
                    </button>
                </div>
            </div>

        </div>

        <!-- Chat Input Form -->
        <div class="p-3 border-t border-obsidian-border bg-obsidian-surface/90">
            <form @submit.prevent="sendMessage()" class="flex items-center gap-2">
                <input type="text" 
                       x-model="inputMessage" 
                       placeholder="اكتب استفسارك لسولي AI..." 
                       class="flex-1 bg-obsidian-base border border-obsidian-border text-xs sm:text-sm text-white placeholder-slate-500 rounded-xl px-3.5 py-2.5 focus:outline-none focus:border-cyber-cyan font-readex transition-colors">
                
                <button type="submit" 
                        :disabled="!inputMessage.trim() || isTyping"
                        class="p-2.5 rounded-xl bg-gradient-to-r from-cyber-cyan to-cyber-purple text-obsidian-base hover:scale-105 active:scale-95 disabled:opacity-40 transition-all">
                    <svg class="w-4 h-4 rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                </button>
            </form>
        </div>

    </div>

</div>

<!-- ===================================================================== -->
<!-- ALPINE CONTROLLER FOR SOULY AI WIDGET                                 -->
<!-- ===================================================================== -->
<script>
function soulyAiWidgetController() {
    return {
        isOpen: false,
        isTyping: false,
        inputMessage: '',
        quickPhone: '',
        phoneCaptured: false,
        conversationId: localStorage.getItem('souly_ai_conversation_id') || '',
        messages: [],

        init() {
            if (this.conversationId) {
                fetch(`/api/ai/history/${this.conversationId}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.messages && data.messages.length > 0) {
                            this.messages = data.messages;
                            this.scrollToBottom();
                        }
                    }).catch(() => {});
            }
        },

        openWidget() {
            this.isOpen = true;
            this.scrollToBottom();
        },

        closeWidget() {
            this.isOpen = false;
            if (Alpine.store('citadel')) {
                Alpine.store('citadel').aiSheetOpen = false;
            }
        },

        toggleWidget() {
            this.isOpen = !this.isOpen;
            if (this.isOpen) {
                this.scrollToBottom();
            }
        },

        sendPrompt(promptText) {
            this.inputMessage = promptText;
            this.sendMessage();
        },

        submitQuickPhone() {
            if (!this.quickPhone.trim()) return;
            const text = `رقم هاتفي للتواصل والمتابعة هو: ${this.quickPhone.trim()}`;
            this.inputMessage = text;
            this.phoneCaptured = true;
            this.sendMessage();
            this.quickPhone = '';
        },

        sendMessage() {
            const text = this.inputMessage.trim();
            if (!text || this.isTyping) return;

            // Add visitor message
            this.messages.push({
                sender: 'visitor',
                content: text,
                suggested_links: []
            });

            this.inputMessage = '';
            this.isTyping = true;
            this.scrollToBottom();

            fetch('/api/ai/chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    message: text,
                    conversation_id: this.conversationId
                })
            })
            .then(res => res.json())
            .then(data => {
                this.isTyping = false;
                if (data.conversation_id) {
                    this.conversationId = data.conversation_id;
                    localStorage.setItem('souly_ai_conversation_id', data.conversation_id);
                }

                this.messages.push({
                    sender: 'souly_ai',
                    content: data.reply,
                    suggested_links: data.package_suggestions || []
                });

                if (data.lead_captured) {
                    this.phoneCaptured = true;
                }

                this.scrollToBottom();
            })
            .catch(err => {
                this.isTyping = false;
                this.messages.push({
                    sender: 'souly_ai',
                    content: 'عذراً، حدث خطأ مؤقت في الاتصال. يمكنك إعادة المحاولة أو التواصل معنا مباشرة عبر واتساب: 01090623326.',
                    suggested_links: []
                });
                this.scrollToBottom();
            });
        },

        scrollToBottom() {
            this.$nextTick(() => {
                const el = this.$refs.chatMessages;
                if (el) el.scrollTop = el.scrollHeight;
            });
        }
    };
}
</script>
