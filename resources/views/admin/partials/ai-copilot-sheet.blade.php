<!-- Sovereign Admin AI Sheet (Universal Across All Admin Views) -->
<div x-data="{
    open: false,
    mode: 'chat', // 'chat' or 'paste'
    input: '',
    rawChat: '',
    loading: false,
    listening: false,
    messages: [
        {
            role: 'assistant',
            text: 'أهلاً بك يا باشمهندس هدرا! أنا وكيل القيادة التنفيذي الخاص بحضرتك. تحت أمرك لتنفيذ أي تعديل لحظي، اعتماد إيداعات المحفظة، قراءة تقرير الصباح، أو بناء ملفات العملاء من الشات.',
            telemetry: { model: '{{ \App\Models\Setting::get('ai_admin_model') ?: 'gemini-3.8-flash' }}', latency_ms: 15 },
            snapshot_id: null
        }
    ],
    init() {
        window.addEventListener('open-admin-ai-sheet', () => { this.open = true; this.mode = 'chat'; });
        window.addEventListener('open-paste-chat-modal', () => { this.open = true; this.mode = 'paste'; });
    },
    async sendMessage() {
        if (!this.input.trim() || this.loading) return;
        const msg = this.input.trim();
        this.input = '';
        this.messages.push({ role: 'user', text: msg });
        this.loading = true;
        this.scrollToBottom();

        try {
            const res = await fetch('{{ route('admin.ai_copilot.chat') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ message: msg })
            });
            const data = await res.json();
            this.messages.push({
                role: 'assistant',
                text: data.reply || 'تم تنفيذ الأمر بنجاح.',
                telemetry: data.telemetry || null,
                snapshot_id: data.snapshot_id || null,
                action_taken: data.action_taken || null
            });
        } catch (e) {
            this.messages.push({
                role: 'assistant',
                text: 'عذراً يا باشمهندس، حدث خطأ أثناء الاتصال: ' + e.message,
                telemetry: null,
                snapshot_id: null
            });
        } finally {
            this.loading = false;
            this.scrollToBottom();
        }
    },
    async submitChatParser() {
        if (!this.rawChat.trim() || this.loading) return;
        this.loading = true;
        try {
            const res = await fetch('{{ route('admin.ai_copilot.parse_chat') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ chat_transcript: this.rawChat })
            });
            const data = await res.json();
            if (data.success) {
                this.messages.push({
                    role: 'assistant',
                    text: data.message + '\n\n' + 'تم إدراج الحجز في مسار الصيانة وتجهيز ملف العميل 360.',
                    telemetry: data.telemetry,
                    snapshot_id: data.snapshot_id
                });
                this.rawChat = '';
                this.mode = 'chat';
            } else {
                alert(data.message || 'فشل استخراج ملف العميل.');
            }
        } catch (e) {
            alert('حدث خطأ أثناء تحليل الشات: ' + e.message);
        } finally {
            this.loading = false;
            this.scrollToBottom();
        }
    },
    async executeUndo(snapshotId, index) {
        if (!snapshotId) return;
        try {
            const res = await fetch(`{{ url('/admin/ai-copilot/undo') }}/${snapshotId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });
            const data = await res.json();
            if (data.success) {
                this.messages[index].snapshot_id = null;
                this.messages.push({
                    role: 'assistant',
                    text: '↩️ ' + data.message,
                    telemetry: { model: 'system', latency_ms: 5 },
                    snapshot_id: null
                });
            } else {
                alert(data.message);
            }
        } catch (e) {
            alert('فشل التراجع: ' + e.message);
        }
    },
    startVoiceInput() {
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        if (!SpeechRecognition) {
            alert('المتصفح لا يدعم ميزة الإملاء الصوتي المباشر.');
            return;
        }
        const recognition = new SpeechRecognition();
        recognition.lang = 'ar-EG';
        recognition.interimResults = false;
        this.listening = true;
        recognition.onresult = (e) => {
            const transcript = e.results[0][0].transcript;
            this.input = transcript;
            this.listening = false;
            this.sendMessage();
        };
        recognition.onerror = () => { this.listening = false; };
        recognition.onend = () => { this.listening = false; };
        recognition.start();
    },
    scrollToBottom() {
        this.$nextTick(() => {
            const el = this.$refs.chatBox;
            if (el) el.scrollTop = el.scrollHeight;
        });
    }
}" 
     x-show="open" 
     @keydown.escape.window="open = false"
     class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 bg-black/80 backdrop-blur-md"
     style="display: none;"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 translate-y-full sm:translate-y-0 sm:scale-95"
     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
     x-transition:leave-end="opacity-0 translate-y-full sm:translate-y-0 sm:scale-95">

    <!-- Modal Panel -->
    <div @click.away="open = false" 
         class="w-full sm:max-w-2xl bg-obsidian-base border border-cyber-gold/40 rounded-t-3xl sm:rounded-3xl shadow-2xl flex flex-col max-h-[92dvh] sm:max-h-[85vh] overflow-hidden">
        
        <!-- Mobile Drag Handle -->
        <div class="sm:hidden w-12 h-1.5 bg-slate-700 rounded-full mx-auto mt-2.5 mb-1 cursor-grab"></div>

        <!-- Header -->
        <div class="px-5 py-3.5 border-b border-obsidian-border/80 flex items-center justify-between bg-black/40">
            <div class="flex items-center gap-3">
                <span class="w-9 h-9 rounded-2xl bg-gradient-to-tr from-cyber-gold to-amber-500 text-obsidian-base font-black flex items-center justify-center text-lg shadow-neon-gold">
                    🤖
                </span>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-sm font-black text-white font-cairo">وكيل القيادة التنفيذي (Hedra AI)</h3>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-amber-500/20 text-cyber-gold border border-amber-500/40">
                            ROOT ACCESS
                        </span>
                    </div>
                    <p class="text-[10px] text-slate-400 font-cairo">
                        صلاحيات قراءة وتنفيذ أوامر مباشرة على قاعدة البيانات مع التراجع الفوري
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <!-- Mode Toggle Button -->
                <button type="button" 
                        @click="mode = mode === 'chat' ? 'paste' : 'chat'"
                        :class="mode === 'paste' ? 'bg-cyber-cyan text-obsidian-base' : 'bg-obsidian-surface text-slate-300 border border-obsidian-border'"
                        class="px-2.5 py-1.5 rounded-xl text-[11px] font-cairo font-bold flex items-center gap-1 transition-colors">
                    <span x-text="mode === 'paste' ? '💬 وضع الشات' : '📋 لصق شات'"></span>
                </button>

                <!-- Close Button -->
                <button type="button" 
                        @click="open = false" 
                        class="w-8 h-8 rounded-xl bg-obsidian-surface hover:bg-slate-800 text-slate-400 hover:text-white border border-obsidian-border flex items-center justify-center font-bold text-lg">
                    ×
                </button>
            </div>
        </div>

        <!-- Mode 1: Active Chat Window -->
        <div x-show="mode === 'chat'" class="flex-1 flex flex-col overflow-hidden">
            <!-- Messages Stream -->
            <div x-ref="chatBox" class="flex-1 overflow-y-auto p-4 sm:p-5 space-y-4">
                <template x-for="(msg, index) in messages" :key="index">
                    <div class="flex flex-col" :class="msg.role === 'user' ? 'items-end' : 'items-start'">
                        <div class="max-w-[88%] sm:max-w-[82%] rounded-2xl p-3.5 text-xs font-cairo leading-relaxed"
                             :class="msg.role === 'user' ? 'bg-gradient-to-r from-cyber-cyan to-blue-600 text-obsidian-base font-bold' : 'bg-obsidian-surface border border-obsidian-border text-slate-200'">
                            <p class="whitespace-pre-line" x-text="msg.text"></p>

                            <!-- Interactive 1-Tap Undo Action Card -->
                            <template x-if="msg.snapshot_id">
                                <div class="mt-3 pt-2.5 border-t border-slate-700/60 flex items-center justify-between gap-3">
                                    <span class="text-[10px] text-amber-300 font-mono font-bold flex items-center gap-1">
                                        <span>⚡</span>
                                        <span>تم حفظ لقطة التراجع #<span x-text="msg.snapshot_id"></span></span>
                                    </span>
                                    <button type="button" 
                                            @click="executeUndo(msg.snapshot_id, index)"
                                            class="px-2.5 py-1 rounded-lg bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 border border-rose-500/40 text-[10px] font-bold font-cairo flex items-center gap-1 transition-colors">
                                        <span>↩️</span>
                                        <span>تراجع فوري (Undo)</span>
                                    </button>
                                </div>
                            </template>

                            <!-- Telemetry Badge -->
                            <template x-if="msg.telemetry">
                                <div class="mt-2 text-[9px] font-mono text-slate-500 flex items-center gap-2">
                                    <span x-text="'الموديل: ' + (msg.telemetry.model || 'gemini')"></span>
                                    <span>•</span>
                                    <span x-text="(msg.telemetry.latency_ms || 20) + 'ms'"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

                <div x-show="loading" class="flex items-center gap-2 text-cyber-gold text-xs font-cairo">
                    <span class="animate-spin text-base">⚙️</span>
                    <span>وكيل القيادة ينفذ الأمر ويفحص قاعدة البيانات...</span>
                </div>
            </div>

            <!-- Fast Tactical Command Chips -->
            <div class="px-4 py-2 border-t border-obsidian-border/50 bg-black/20 flex items-center gap-2 overflow-x-auto text-[11px] font-cairo">
                <button type="button" @click="input = 'تقرير الصباح'; sendMessage()" class="px-2.5 py-1 rounded-lg bg-obsidian-surface hover:bg-slate-800 text-slate-300 border border-obsidian-border shrink-0">
                    🌅 تقرير الصباح
                </button>
                <button type="button" @click="input = 'مكالمات لم يتم تأكيدها'; sendMessage()" class="px-2.5 py-1 rounded-lg bg-obsidian-surface hover:bg-slate-800 text-slate-300 border border-obsidian-border shrink-0">
                    📞 المكالمات المطلوبة
                </button>
                <button type="button" @click="input = 'إيداعات المحافظ المعلقة'; sendMessage()" class="px-2.5 py-1 rounded-lg bg-obsidian-surface hover:bg-slate-800 text-slate-300 border border-obsidian-border shrink-0">
                    💳 شحن المحافظ
                </button>
                <button type="button" @click="input = 'تراجع'; sendMessage()" class="px-2.5 py-1 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-300 border border-rose-500/30 shrink-0">
                    ↩️ تراجع عن آخر عملية
                </button>
            </div>

            <!-- Input Bar with Voice Recognition -->
            <div class="p-3 sm:p-4 border-t border-obsidian-border bg-black/40 flex items-center gap-2">
                <!-- Microphone Voice Button -->
                <button type="button" 
                        @click="startVoiceInput()"
                        :class="listening ? 'bg-rose-500 text-white animate-pulse' : 'bg-obsidian-surface text-slate-300 border border-obsidian-border hover:text-cyber-gold'"
                        class="w-10 h-10 rounded-2xl flex items-center justify-center text-lg shrink-0 transition-colors"
                        title="أوامر صوتية باللغة العربية">
                    🎤
                </button>

                <!-- Text Input -->
                <input type="text" 
                       x-model="input" 
                       @keydown.enter="sendMessage()"
                       placeholder="اكتب أمرك للمدير (مثال: أكد مكالمة الحجز 12، تقرير الصباح، عدل سعر...)"
                       class="flex-1 bg-obsidian-surface border border-obsidian-border focus:border-cyber-gold rounded-2xl px-4 py-2.5 text-xs font-cairo text-white focus:outline-none placeholder-slate-500">

                <!-- Send Button -->
                <button type="button" 
                        @click="sendMessage()"
                        :disabled="loading || !input.trim()"
                        class="px-4 py-2.5 rounded-2xl bg-gradient-to-r from-cyber-gold to-amber-600 text-obsidian-base font-black text-xs font-cairo shrink-0 shadow-neon-gold disabled:opacity-50">
                    إرسال ⚡
                </button>
            </div>
        </div>

        <!-- Mode 2: Chat-to-Dossier Parser -->
        <div x-show="mode === 'paste'" class="flex-1 p-5 overflow-y-auto space-y-4">
            <div class="p-4 rounded-2xl bg-cyber-cyan/10 border border-cyber-cyan/30 text-cyber-cyan text-xs font-cairo">
                <span class="font-bold block mb-1">📋 محلل شات العملاء الذكي (AI Chat-to-Dossier Parser):</span>
                <span>انسخ نص المحادثة من واتساب أو فيسبوك والصقه هنا. سيقوم وكيل الذكاء الاصطناعي باستخراج اسم العميل، الهاتف، المنطقة، نوع العطل، والقطع المطلوبة وإنشاء الحجز ومرحلة الصيانة فوراً!</span>
            </div>

            <textarea x-model="rawChat" 
                      rows="8"
                      placeholder="الصق نص المحادثة هنا بالكامل (مثال:
العميل: مساء الخير، عندي مشكلة في الكاشير التاتش مش بيقرا الطابعة، وعايز مهندس يجيلي فرع مصر الجديدة بكرة.. رقمي 01012345678..)"
                      class="w-full bg-obsidian-surface border border-obsidian-border focus:border-cyber-cyan rounded-2xl p-4 text-xs font-cairo text-white focus:outline-none placeholder-slate-500 resize-none"></textarea>

            <div class="flex items-center justify-end gap-3">
                <button type="button" 
                        @click="mode = 'chat'"
                        class="px-4 py-2.5 rounded-xl bg-obsidian-surface text-slate-300 font-cairo text-xs font-bold border border-obsidian-border">
                    إلغاء
                </button>

                <button type="button" 
                        @click="submitChatParser()"
                        :disabled="loading || !rawChat.trim()"
                        class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-cyber-cyan to-blue-600 text-obsidian-base font-black text-xs font-cairo shadow-neon-cyan disabled:opacity-50">
                    <span x-show="!loading">🚀 استخراج الملف وإنشاء الحجز فوراً</span>
                    <span x-show="loading">جاري التحليل واستخراج البيانات...</span>
                </button>
            </div>
        </div>
    </div>
</div>
