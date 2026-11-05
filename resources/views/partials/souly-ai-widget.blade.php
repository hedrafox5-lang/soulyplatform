<!-- ===================================================================== -->
<!-- SOULY AI INTERACTIVE AGENT WIDGET (ROCK-SOLID ISOLATED EDITION)       -->
<!-- ===================================================================== -->
<script>
if (typeof window.soulyAiWidgetController !== 'function') {
    window.soulyAiWidgetController = function soulyAiWidgetController() {
        return {
            isOpen: false,
            isTyping: false,
            inputMessage: '',
            conversationId: localStorage.getItem('souly_ai_conversation_id') || '',
            visitorUuid: '',
            visitorName: '',
            welcomeMessage: 'أهلاً بيك في منظومة سولي! أنا سولي، مستشارك التقني الذكي.. أتشرف باسم حضرتك الأول؟',
            messages: [],

            init() {
                // Force strict initial closed state
                this.isOpen = false;
                if (this.$store && this.$store.citadel) {
                    this.$store.citadel.aiSheetOpen = false;
                }

                this.visitorUuid = window.SoulyIdentity ? window.SoulyIdentity.getUuid() : (localStorage.getItem('souly_visitor_uuid') || '');
                this.visitorName = window.SoulyIdentity ? window.SoulyIdentity.getName() : (localStorage.getItem('souly_visitor_name') || '');
                this.updateGreeting();

                // Clean store watch (only triggers on intentional boolean change)
                if (this.$store && this.$store.citadel) {
                    this.$watch('$store.citadel.aiSheetOpen', (val) => {
                        const nextVal = Boolean(val);
                        if (this.isOpen !== nextVal) {
                            if (nextVal) {
                                this.openWidget();
                            } else {
                                this.closeWidget();
                            }
                        }
                    });
                }
            },

            updateGreeting() {
                if (this.visitorName) {
                    this.welcomeMessage = `أهلاً بك يا ${this.visitorName}، نورت منصة سولي مجدداً! تحب نكمل استفسارنا السابق ولا نناقش موضوع جديد؟`;
                }
            },

            editVisitorName() {
                const currentName = this.visitorName || '';
                const newName = prompt('أدخل أو عدّل اسمك الكريم:', currentName);
                if (newName !== null && newName.trim() !== '') {
                    const cleaned = newName.trim();
                    this.visitorName = cleaned;
                    if (window.SoulyIdentity) window.SoulyIdentity.setName(cleaned);
                    localStorage.setItem('souly_visitor_name', cleaned);
                    this.updateGreeting();
                    this.inputMessage = `اسمي ${cleaned}`;
                    this.sendMessage();
                }
            },

            loadHistory() {
                if (this.messages.length > 0) return;
                const vUuid = this.visitorUuid || (window.SoulyIdentity ? window.SoulyIdentity.getUuid() : '');
                let url = '/api/ai/history';
                if (this.conversationId) {
                    url = `/api/ai/history/${this.conversationId}?visitor_uuid=${encodeURIComponent(vUuid)}`;
                } else if (vUuid) {
                    url = `/api/ai/history?visitor_uuid=${encodeURIComponent(vUuid)}`;
                }

                fetch(url)
                    .then(res => res.ok ? res.json() : {})
                    .then(data => {
                        if (data.session_uuid) {
                            this.conversationId = data.session_uuid;
                            localStorage.setItem('souly_ai_conversation_id', data.session_uuid);
                        }
                        if (data.visitor_name) {
                            this.visitorName = data.visitor_name;
                            if (window.SoulyIdentity) window.SoulyIdentity.setName(data.visitor_name);
                        }
                        if (data.messages && data.messages.length > 0) {
                            this.messages = data.messages;
                            this.scrollToBottom();
                        }
                    }).catch(() => {});
            },

            openWidget() {
                this.isOpen = true;
                if (this.$store && this.$store.citadel) {
                    this.$store.citadel.aiSheetOpen = true;
                }
                if (window.SoulyUI && typeof window.SoulyUI.lockScroll === 'function') {
                    window.SoulyUI.lockScroll('ai-widget');
                }
                this.$nextTick(() => {
                    if (this.$refs.chatContainer) {
                        this.$refs.chatContainer.style.removeProperty('display');
                    }
                });
                this.loadHistory();
                this.scrollToBottom();
            },

            closeWidget() {
                this.isOpen = false;
                if (this.$store && this.$store.citadel) {
                    this.$store.citadel.aiSheetOpen = false;
                }
                if (window.SoulyUI && typeof window.SoulyUI.unlockScroll === 'function') {
                    window.SoulyUI.unlockScroll('ai-widget');
                }

                // Force DOM safeguard to override any GSAP animation interference
                this.$nextTick(() => {
                    if (this.$refs.chatContainer) {
                        this.$refs.chatContainer.style.setProperty('display', 'none', 'important');
                    }
                });
            },

            toggleWidget(forceState = null) {
                if (forceState && typeof forceState === 'object' && forceState.detail) {
                    forceState = forceState.detail.open;
                }
                const next = forceState !== null && forceState !== undefined ? Boolean(forceState) : !this.isOpen;
                if (next) {
                    this.openWidget();
                } else {
                    this.closeWidget();
                }
            },

            sendPrompt(promptText) {
                this.inputMessage = promptText;
                this.sendMessage();
            },

            sendMessage() {
                const text = this.inputMessage.trim();
                if (!text || this.isTyping) return;

                this.messages.push({
                    sender: 'visitor',
                    content: text
                });

                this.inputMessage = '';
                this.isTyping = true;
                this.scrollToBottom();

                const vUuid = this.visitorUuid || (window.SoulyIdentity ? window.SoulyIdentity.getUuid() : '');

                fetch('/api/ai/chat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify({
                        message: text,
                        conversation_id: this.conversationId,
                        visitor_uuid: vUuid,
                        visitor_name: this.visitorName,
                        device_fingerprint: navigator.userAgent
                    })
                })
                .then(res => res.json())
                .then(data => {
                    this.isTyping = false;
                    if (data.conversation_id) {
                        this.conversationId = data.conversation_id;
                        localStorage.setItem('souly_ai_conversation_id', data.conversation_id);
                    }

                    if (data.visitor_name) {
                        this.visitorName = data.visitor_name;
                        if (window.SoulyIdentity) window.SoulyIdentity.setName(data.visitor_name);
                        this.updateGreeting();
                    }

                    const replyText = (data.reply && data.reply.trim() !== '')
                        ? data.reply
                        : 'أهلاً بحضرتك في سولي 🌟 أنا سولي، مستشارك التقني الذكي.. كيف أقدر أساعدك اليوم؟';

                    this.messages.push({
                        sender: 'souly_ai',
                        content: replyText
                    });

                    this.scrollToBottom();
                })
                .catch(() => {
                    this.isTyping = false;
                    this.messages.push({
                        sender: 'souly_ai',
                        content: 'عذراً، حدث خطأ مؤقت في الاتصال. يمكنك إعادة المحاولة أو التواصل معنا مباشرة عبر واتساب: 01090623326.'
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
    };
}
if (window.Alpine) {
    window.Alpine.data('soulyAiWidgetController', window.soulyAiWidgetController);
} else {
    document.addEventListener('alpine:init', () => {
        if (window.Alpine) {
            window.Alpine.data('soulyAiWidgetController', window.soulyAiWidgetController);
        }
    });
}
</script>
<div x-data="soulyAiWidgetController()" 
     x-cloak
     @open-souly-ai.window="openWidget()"
     @close-souly-ai.window="closeWidget()"
     @toggle-souly-ai.window="toggleWidget()"
     @open-ai-chat-sheet.window="openWidget()"
     @close-ai-chat-sheet.window="closeWidget()"
     @keydown.escape.window="closeWidget()"
     class="relative z-[99990]">

    <!-- Desktop Floating Trigger Core -->
    <div class="hidden lg:block fixed bottom-6 left-6 z-[99991]">
        <button type="button" 
                @click.stop="toggleWidget()" 
                class="group relative flex items-center gap-3.5 px-4 py-3 rounded-2xl bg-gradient-to-r from-[#0B0C16] via-[#121422] to-[#04060E] border border-cyber-cyan/50 shadow-[0_8px_32px_rgba(0,0,0,0.85),0_0_20px_rgba(0,245,212,0.25)] hover:border-cyber-cyan hover:shadow-[0_8px_36px_rgba(0,0,0,0.95),0_0_28px_rgba(0,245,212,0.45)] hover:scale-105 active:scale-95 transition-all cursor-pointer">
            
            <div class="relative w-10 h-10 flex items-center justify-center shrink-0">
                <div class="absolute inset-0 rounded-full border border-cyber-gold/40 border-dashed animate-spin pointer-events-none" style="animation-duration: 12s; animation-direction: reverse;"></div>
                <div class="absolute inset-0.5 rounded-full border border-cyber-cyan/60 border-t-transparent animate-spin pointer-events-none" style="animation-duration: 6s;"></div>

                <div class="relative w-7 h-7 rounded-full bg-[#05050A] border border-cyber-cyan/70 shadow-[0_0_12px_rgba(0,245,212,0.45)] flex items-center justify-center overflow-hidden">
                    <div class="flex items-center gap-[1.5px] pointer-events-none">
                        <span class="w-[2px] h-2 bg-cyber-cyan rounded-full animate-pulse" style="animation-duration: 0.8s;"></span>
                        <span class="w-[2px] h-3.5 bg-cyber-gold rounded-full animate-pulse" style="animation-duration: 0.6s;"></span>
                        <span class="w-[2px] h-4.5 bg-white rounded-full animate-pulse" style="animation-duration: 0.5s;"></span>
                        <span class="w-[2px] h-3 bg-cyber-gold rounded-full animate-pulse" style="animation-duration: 0.7s;"></span>
                        <span class="w-[2px] h-1.5 bg-cyber-cyan rounded-full animate-pulse" style="animation-duration: 0.9s;"></span>
                    </div>
                </div>

                <span class="absolute top-0 right-0 w-2.5 h-2.5 bg-cyber-cyan rounded-full animate-ping pointer-events-none opacity-80"></span>
                <span class="absolute top-0 right-0 w-2 h-2 bg-cyber-cyan rounded-full shadow-[0_0_8px_#00F5D4] pointer-events-none"></span>
            </div>

            <div class="text-right">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-black text-white font-cairo drop-shadow-[0_0_8px_rgba(0,245,212,0.5)]">سولي AI</span>
                    <span class="px-2 py-0.5 rounded-full text-[9px] font-mono font-bold bg-cyber-cyan/15 text-cyber-cyan border border-cyber-cyan/30">ONLINE</span>
                </div>
                <div class="text-[10px] text-slate-300 font-cairo">مستشارك الهندسي الذكي</div>
            </div>
        </button>
    </div>

    <!-- AI Chat Backdrop -->
    <div x-show="isOpen" 
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click.stop="closeWidget()" 
         class="fixed inset-0 z-[99994] bg-black/80 backdrop-blur-sm cursor-pointer" 
         style="display: none;"></div>

    <!-- Main AI Chat Window Overlay (NO .glass-obsidian to prevent GSAP overrides) -->
    <div x-show="isOpen" 
         x-cloak
         x-ref="chatContainer"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-8 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-8 scale-95"
         class="fixed inset-x-3 bottom-[76px] sm:bottom-6 sm:left-6 sm:right-auto sm:w-[430px] max-h-[82vh] h-[580px] z-[99995] bg-[#0B0C16]/98 backdrop-blur-2xl border border-cyber-cyan/50 rounded-3xl shadow-[0_16px_50px_rgba(0,0,0,0.95)] flex flex-col justify-between overflow-hidden"
         style="display: none;">
        
        <!-- Header -->
        <div class="p-4 border-b border-white/10 bg-[#070812] flex items-center justify-between shrink-0">
            <div class="flex items-center gap-3">
                <div class="relative w-8 h-8 rounded-xl bg-gradient-to-tr from-cyber-cyan to-cyber-purple p-[1px]">
                    <div class="w-full h-full bg-[#0B0C16] rounded-xl flex items-center justify-center font-bold text-cyber-cyan text-xs">
                        AI
                    </div>
                    <span class="absolute -bottom-0.5 -right-0.5 w-2 h-2 rounded-full bg-emerald-400"></span>
                </div>
                <div>
                    <div class="flex items-center gap-1.5">
                        <span class="text-xs font-bold text-white font-cairo">سولي AI</span>
                        <span class="text-[9px] font-mono text-cyber-cyan px-1.5 py-0.5 rounded bg-cyber-cyan/10">مساعد فوري</span>
                    </div>
                    <span class="text-[10px] text-slate-400 font-cairo">مدرب على كافة أنظمة وباقات سولي</span>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <template x-if="visitorName">
                    <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-cyber-purple/20 border border-cyber-purple/40 text-[10px] text-purple-200">
                        <span class="text-slate-400">أهلاً</span>
                        <strong x-text="visitorName" class="text-cyber-cyan font-bold max-w-[80px] truncate"></strong>
                        <button type="button" @click="editVisitorName()" title="تعديل الاسم" class="text-slate-400 hover:text-white transition-colors cursor-pointer">
                            ✏️
                        </button>
                    </div>
                </template>

                <button type="button" 
                        @click.stop="closeWidget()" 
                        class="p-2 text-slate-400 hover:text-rose-400 rounded-xl hover:bg-white/10 active:scale-95 transition-all cursor-pointer" 
                        title="إغلاق الشات (Esc)">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Messages Viewport -->
        <div class="flex-1 p-4 overflow-y-auto space-y-3 font-cairo text-xs overscroll-contain" x-ref="chatMessages">
            
            <!-- Welcome Greeting -->
            <div class="flex items-start gap-2.5">
                <div class="w-6 h-6 rounded-lg bg-cyber-cyan/20 text-cyber-cyan flex items-center justify-center shrink-0 text-[10px] font-bold">
                    AI
                </div>
                <div class="p-3.5 rounded-2xl rounded-tr-none bg-[#121422] border border-cyber-cyan/30 text-slate-200 leading-relaxed space-y-2">
                    <p x-text="welcomeMessage">أهلاً بيك في منظومة سولي! أنا سولي، مستشارك التقني الذكي.. أتشرف باسم حضرتك الأول؟</p>
                    
                    <!-- Quick Prompt Chips -->
                    <div class="flex flex-wrap gap-1.5 pt-1">
                        <button type="button" @click="sendPrompt('🎯 أسعار BrandERP والموديولات')" class="px-2.5 py-1 rounded-lg bg-[#05050A] hover:bg-slate-800 text-cyber-cyan border border-cyber-cyan/30 text-[10px] font-cairo transition-all cursor-pointer">
                            🎯 أسعار BrandERP والموديولات
                        </button>
                        <button type="button" @click="sendPrompt('🤖 باقات موظفي الذكاء الاصطناعي')" class="px-2.5 py-1 rounded-lg bg-[#05050A] hover:bg-slate-800 text-purple-300 border border-purple-500/30 text-[10px] font-cairo transition-all cursor-pointer">
                            🤖 موظفو الذكاء الاصطناعي
                        </button>
                        <button type="button" @click="sendPrompt('🛡️ حماية الأسرة والأمن السيبراني')" class="px-2.5 py-1 rounded-lg bg-[#05050A] hover:bg-slate-800 text-rose-400 border border-rose-500/30 text-[10px] font-cairo transition-all cursor-pointer">
                            🛡️ حماية الأسرة والأمن السيبراني
                        </button>
                        <button type="button" @click="sendPrompt('🔧 حجز زيارة صيانة وهاردوير')" class="px-2.5 py-1 rounded-lg bg-[#05050A] hover:bg-slate-800 text-cyber-gold border border-cyber-gold/30 text-[10px] font-cairo transition-all cursor-pointer">
                            🔧 حجز زيارة صيانة وهاردوير
                        </button>
                    </div>
                </div>
            </div>

            <!-- Dynamic Messages -->
            <template x-for="(msg, idx) in messages" :key="idx">
                <div class="flex items-start gap-2.5" :class="msg.sender === 'visitor' ? 'flex-row-reverse' : ''">
                    <div class="w-6 h-6 rounded-lg flex items-center justify-center shrink-0 text-[10px] font-bold"
                         :class="msg.sender === 'visitor' ? 'bg-cyber-purple/30 text-purple-300' : 'bg-cyber-cyan/20 text-cyber-cyan'">
                        <span x-text="msg.sender === 'visitor' ? 'أنت' : 'AI'"></span>
                    </div>

                    <div class="max-w-[85%] p-3.5 rounded-2xl text-slate-200 leading-relaxed whitespace-pre-line space-y-2.5"
                         :class="msg.sender === 'visitor' 
                            ? 'bg-cyber-purple/20 border border-cyber-purple/40 rounded-tl-none text-right' 
                            : 'bg-[#121422] border border-white/10 rounded-tr-none text-right'">
                        
                        <div x-text="msg.content"></div>

                        <!-- Direct CTAs -->
                        <div class="flex flex-wrap items-center gap-1.5 pt-2 border-t border-white/10">
                            <button type="button" 
                                    @click.prevent="closeWidget(); $dispatch('branderp-store-open');"
                                    class="px-2 py-1 rounded-lg bg-cyber-cyan/15 hover:bg-cyber-cyan/25 text-cyber-cyan border border-cyber-cyan/30 text-[10px] font-cairo transition-all flex items-center gap-1 cursor-pointer">
                                <span>🏪 فتح متجر BrandERP</span>
                            </button>
                            <button type="button" 
                                    @click.prevent="closeWidget(); $dispatch('branderp-roi-open');"
                                    class="px-2 py-1 rounded-lg bg-purple-500/15 hover:bg-purple-500/25 text-purple-300 border border-purple-500/30 text-[10px] font-cairo transition-all flex items-center gap-1 cursor-pointer">
                                <span>📊 حاسبة ROI</span>
                            </button>
                            <button type="button" 
                                    @click.prevent="closeWidget(); $dispatch('open-express-booking', { serviceSlug: 'brand-erp', serviceName: 'نظام BrandERP المتكامل', packageSlug: 'starter', packageName: 'باقة الانطلاق السحابي', price: 2000 });"
                                    class="px-2 py-1 rounded-lg bg-cyber-gold/15 hover:bg-cyber-gold/25 text-cyber-gold border border-cyber-gold/30 text-[10px] font-cairo transition-all flex items-center gap-1 cursor-pointer">
                                <span>⚡ حجز استشارة/خدمة</span>
                            </button>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Typing Animation -->
            <div x-show="isTyping" class="flex items-center gap-2 text-slate-400 text-xs py-1" style="display: none;">
                <div class="w-6 h-6 rounded-lg bg-cyber-cyan/10 text-cyber-cyan flex items-center justify-center text-[10px]">AI</div>
                <div class="flex items-center gap-1 p-2 rounded-xl bg-[#121422] border border-white/10">
                    <span class="w-1.5 h-1.5 rounded-full bg-cyber-cyan animate-pulse"></span>
                    <span class="w-1.5 h-1.5 rounded-full bg-cyber-gold animate-pulse" style="animation-delay: 150ms;"></span>
                    <span class="w-1.5 h-1.5 rounded-full bg-cyber-purple animate-pulse" style="animation-delay: 300ms;"></span>
                    <span class="text-[10px] text-cyber-cyan font-cairo font-bold mr-1">سولي يكتب الآن...</span>
                </div>
            </div>
        </div>

        <!-- Input Bar & Quick-Reply Chips -->
        <div class="border-t border-white/10 bg-[#070812] shrink-0">
            <div class="px-3 pt-2 pb-1 overflow-x-auto flex items-center gap-1.5 scrollbar-none">
                <button type="button" @click="sendPrompt('🎯 أسعار BrandERP والموديولات')" class="whitespace-nowrap px-2.5 py-1 rounded-full bg-[#121422] hover:bg-slate-800 text-cyber-cyan border border-cyber-cyan/30 text-[9px] font-cairo transition-all cursor-pointer">
                    🎯 أسعار BrandERP والموديولات
                </button>
                <button type="button" @click="sendPrompt('🤖 باقات موظفي الذكاء الاصطناعي')" class="whitespace-nowrap px-2.5 py-1 rounded-full bg-[#121422] hover:bg-slate-800 text-purple-300 border border-purple-500/30 text-[9px] font-cairo transition-all cursor-pointer">
                    🤖 موظفو الذكاء الاصطناعي
                </button>
                <button type="button" @click="sendPrompt('🛡️ حماية الأسرة والأمن السيبراني')" class="whitespace-nowrap px-2.5 py-1 rounded-full bg-[#121422] hover:bg-slate-800 text-rose-400 border border-rose-500/30 text-[9px] font-cairo transition-all cursor-pointer">
                    🛡️ حماية الأسرة والأمن السيبراني
                </button>
                <button type="button" @click="sendPrompt('🔧 حجز زيارة صيانة وهاردوير')" class="whitespace-nowrap px-2.5 py-1 rounded-full bg-[#121422] hover:bg-slate-800 text-cyber-gold border border-cyber-gold/30 text-[9px] font-cairo transition-all cursor-pointer">
                    🔧 حجز زيارة صيانة وهاردوير
                </button>
            </div>

            <div class="p-3 pt-1">
                <form @submit.prevent="sendMessage()" class="flex items-center gap-2">
                    <input type="text" 
                           x-model="inputMessage" 
                           @keydown.enter.exact.prevent="sendMessage()"
                           placeholder="اكتب استفسارك لسولي AI..." 
                           class="flex-1 bg-[#121422] border border-white/10 text-xs sm:text-sm text-white placeholder-slate-500 rounded-xl px-3.5 py-2.5 focus:outline-none focus:border-cyber-cyan font-cairo transition-colors">
                    
                    <button type="submit" 
                            :disabled="!inputMessage.trim() || isTyping"
                            class="p-2.5 rounded-xl btn-citadel-primary disabled:opacity-40 transition-all shrink-0 cursor-pointer">
                        <svg class="w-4 h-4 rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>

    </div>

</div>