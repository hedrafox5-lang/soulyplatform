@extends('admin.layout')

@section('title', 'إعدادات النظام والـ AI ومزامنة المعرفة | سولي')
@section('page-title', 'إعدادات النظام وإدارة محرك الذكاء الاصطناعي')

@section('admin-content')
<div class="space-y-8" x-data="{
    syncing: false,
    syncMessage: '',
    async triggerSync() {
        this.syncing = true;
        this.syncMessage = 'جاري فهرسة الخدمات والباقات والأيقونات ومزامنتها مع مستودع المعرفة...';
        try {
            const res = await fetch('{{ route('admin.settings.sync_pinecone') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            const data = await res.json();
            if (data.success) {
                this.syncMessage = '✓ ' + data.message;
                setTimeout(() => window.location.reload(), 1800);
            } else {
                this.syncMessage = 'تعذر إتمام المزامنة: ' + (data.message || 'خطأ غير متوقع');
            }
        } catch (e) {
            this.syncMessage = 'حدث خطأ في الاتصال بالخادم أثناء المزامنة.';
        } finally {
            this.syncing = false;
        }
    }
}">

    <!-- Top Command Strip with Sync Widget -->
    <div class="glass-obsidian rounded-3xl border border-cyber-cyan/30 p-6 lg:p-8 space-y-6 shadow-neon-cyan">
        <div class="flex flex-col md:flex-row items-center justify-between gap-6 text-center md:text-right">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyber-cyan/10 border border-cyber-cyan/30 text-cyber-cyan text-xs font-bold font-mono">
                    <span class="w-2 h-2 rounded-full bg-cyber-cyan animate-pulse"></span>
                    <span>SYSTEM SETTINGS & VECTOR CMS</span>
                </div>
                <h2 class="text-2xl font-black text-white font-cairo">إدارة منظومة سولي ومستودع المعرفة الفكتوري</h2>
                <p class="text-xs text-slate-400 font-cairo max-w-2xl">
                    تعديل بيانات الاتصال، بوابات الدفع، مفاتيح الذكاء الاصطناعي، ومزامنة كتالوج سولي بالكامل مع قاعدة Pinecone بلحظة واحدة وبدون تعديل ملفات السيرفر.
                </p>

                <div class="flex items-center gap-2 pt-2">
                    <a href="{{ route('admin.ai_copilot.index') }}" 
                       class="px-3.5 py-2 rounded-xl bg-gradient-to-r from-cyber-gold to-amber-600 text-obsidian-base font-black text-xs font-cairo flex items-center gap-1.5 shadow-neon-gold">
                        <span>👑</span>
                        <span>خزينة مفاتيح الـ AI ومبدل الموديلات ⇥</span>
                    </a>
                    <a href="{{ route('admin.system.backup_snapshot') }}" 
                       class="px-3.5 py-2 rounded-xl bg-obsidian-surface hover:bg-slate-800 text-slate-300 hover:text-white border border-obsidian-border text-xs font-bold font-cairo flex items-center gap-1.5">
                        <span>💾</span>
                        <span>تحميل نسخة احتياطية JSON</span>
                    </a>
                </div>
            </div>

            <!-- Pinecone 1-Click Sync Action Card (Rule 13) -->
            <div class="glass-obsidian rounded-2xl border border-cyber-purple/40 p-4 space-y-3 w-full md:w-auto shrink-0 shadow-lg">
                <div class="flex items-center justify-between gap-4 border-b border-obsidian-border/80 pb-2">
                    <span class="text-xs font-bold text-slate-300 font-cairo">حالة مستودع المعرفة</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-purple-500/20 text-purple-300 border border-purple-500/30">
                        {{ $totalKnowledgeCount }} معلماً معرفياً
                    </span>
                </div>
                <div class="text-[11px] text-slate-400 font-cairo flex items-center gap-2">
                    <span>آخر مزامنة:</span>
                    <span class="font-mono text-cyber-gold font-bold">
                        {{ $lastSyncedAt ?: 'لم تتم المزامنة بعد' }}
                    </span>
                </div>
                <button type="button" 
                        @click="triggerSync()" 
                        :disabled="syncing"
                        class="w-full py-2.5 px-5 rounded-xl bg-gradient-to-r from-cyber-cyan to-emerald-400 hover:from-emerald-400 hover:to-cyber-cyan text-obsidian-base font-black font-cairo text-xs shadow-md transition-all flex items-center justify-center gap-2 active:scale-95 disabled:opacity-50">
                    <svg class="w-4 h-4" :class="syncing ? 'animate-spin' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <span x-text="syncing ? 'جاري المزامنة مع Pinecone...' : 'مزامنة قاعدة معرفة سولي مع Pinecone الآن 🔄'"></span>
                </button>
                <div x-show="syncMessage" x-text="syncMessage" class="text-[10px] font-cairo text-center text-cyber-cyan font-bold transition-all"></div>
            </div>
        </div>

        <!-- Knowledge Breakdown Counters -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-2 border-t border-obsidian-border/80 text-center">
            <div class="p-3 rounded-2xl bg-obsidian-surface border border-obsidian-border">
                <div class="text-lg font-black text-cyber-cyan font-mono">{{ $servicesCount }}</div>
                <div class="text-[11px] text-slate-400 font-cairo">قطاعات وخدمات سولي</div>
            </div>
            <div class="p-3 rounded-2xl bg-obsidian-surface border border-obsidian-border">
                <div class="text-lg font-black text-cyber-purple font-mono">{{ $packagesCount }}</div>
                <div class="text-[11px] text-slate-400 font-cairo">باقة رسمية وضمانات</div>
            </div>
            <div class="p-3 rounded-2xl bg-obsidian-surface border border-obsidian-border">
                <div class="text-lg font-black text-cyber-gold font-mono">{{ $iconsCount }}</div>
                <div class="text-[11px] text-slate-400 font-cairo">أيقونة ونشاط تفاعلي</div>
            </div>
            <div class="p-3 rounded-2xl bg-obsidian-surface border border-obsidian-border">
                <div class="text-lg font-black text-emerald-400 font-mono">{{ $totalKnowledgeCount }}</div>
                <div class="text-[11px] text-slate-400 font-cairo">إجمالي أصول المعرفة</div>
            </div>
        </div>
    </div>

    <!-- Main Settings Form -->
    <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-8">
        @csrf

        <!-- SECTION 1: General & Payment Contact Instructions -->
        <div class="glass-obsidian rounded-3xl border border-obsidian-border p-6 lg:p-8 space-y-6">
            <!-- Header in a Card (Rule 13) -->
            <div class="p-4 rounded-2xl bg-obsidian-surface border border-obsidian-border flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-center sm:text-right">
                <div>
                    <h3 class="text-base font-black text-white font-cairo flex items-center justify-center sm:justify-start gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-cyber-cyan"></span>
                        <span>قنوات التواصل وبيانات الدفع والتحويل الرسمي</span>
                    </h3>
                    <p class="text-xs text-slate-400 font-cairo mt-1">تظهر هذه البيانات للعملاء في بوابات التحويل وفواتير الضمان وشحن المحفظة.</p>
                </div>
                <span class="px-3 py-1 rounded-full text-[10px] font-mono bg-cyber-cyan/10 text-cyber-cyan border border-cyber-cyan/30 self-center sm:self-auto">
                    OFFICIAL CONTACT & PAYMENT
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                <!-- Site Name -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-300 font-cairo">اسم المنظومة</label>
                    <input type="text" name="settings[site_name]" value="{{ \App\Models\Setting::get('site_name', 'سولي') }}"
                           class="w-full bg-obsidian-base border border-obsidian-border rounded-xl px-4 py-2.5 text-xs text-white font-cairo focus:border-cyber-cyan focus:outline-none">
                </div>

                <!-- Contact Phone -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-300 font-cairo">رقم الهاتف الرسمي</label>
                    <input type="text" name="settings[contact_phone]" value="{{ \App\Models\Setting::get('contact_phone', '01090623326') }}"
                           class="w-full bg-obsidian-base border border-obsidian-border rounded-xl px-4 py-2.5 text-xs text-white font-mono text-left focus:border-cyber-cyan focus:outline-none">
                </div>

                <!-- WhatsApp Phone -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-300 font-cairo">رقم واتساب المعتمد</label>
                    <input type="text" name="settings[contact_whatsapp]" value="{{ \App\Models\Setting::get('contact_whatsapp', '01090623326') }}"
                           class="w-full bg-obsidian-base border border-obsidian-border rounded-xl px-4 py-2.5 text-xs text-white font-mono text-left focus:border-cyber-cyan focus:outline-none">
                </div>

                <!-- Official Email -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-300 font-cairo">البريد الإلكتروني الرسمي</label>
                    <input type="email" name="settings[contact_email]" value="{{ \App\Models\Setting::get('contact_email', 'support@soulyeg.online') }}"
                           class="w-full bg-obsidian-base border border-obsidian-border rounded-xl px-4 py-2.5 text-xs text-white font-mono text-left focus:border-cyber-cyan focus:outline-none">
                </div>

                <!-- Vodafone Cash Number -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-rose-400 font-cairo">رقم فودافون كاش (Vodafone Cash)</label>
                    <input type="text" name="settings[vodafone_cash_number]" value="{{ \App\Models\Setting::get('vodafone_cash_number', '01090623326') }}"
                           class="w-full bg-obsidian-base border border-rose-500/40 rounded-xl px-4 py-2.5 text-xs text-white font-mono text-left focus:border-rose-400 focus:outline-none">
                </div>

                <!-- InstaPay Address -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-purple-400 font-cairo">عنوان إنستاباي (InstaPay Address)</label>
                    <input type="text" name="settings[instapay_address]" value="{{ \App\Models\Setting::get('instapay_address', 'souly@instapay') }}"
                           class="w-full bg-obsidian-base border border-purple-500/40 rounded-xl px-4 py-2.5 text-xs text-white font-mono text-left focus:border-purple-400 focus:outline-none">
                </div>

                <!-- Bank Name -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-300 font-cairo">اسم البنك المعتمد</label>
                    <input type="text" name="settings[bank_name]" value="{{ \App\Models\Setting::get('bank_name', 'البنك الأهلي المصري (NBE)') }}"
                           class="w-full bg-obsidian-base border border-obsidian-border rounded-xl px-4 py-2.5 text-xs text-white font-cairo focus:border-cyber-cyan focus:outline-none">
                </div>

                <!-- Bank IBAN -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-300 font-cairo">رقم الحساب الدولي (IBAN)</label>
                    <input type="text" name="settings[bank_iban]" value="{{ \App\Models\Setting::get('bank_iban', 'EG3800020000000109062332601') }}"
                           class="w-full bg-obsidian-base border border-obsidian-border rounded-xl px-4 py-2.5 text-xs text-white font-mono text-left focus:border-cyber-cyan focus:outline-none">
                </div>

                <!-- Bank Account Name -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-300 font-cairo">اسم صاحب الحساب البنكي</label>
                    <input type="text" name="settings[bank_account_name]" value="{{ \App\Models\Setting::get('bank_account_name', 'مؤسسة سولي للحلول الرقمية') }}"
                           class="w-full bg-obsidian-base border border-obsidian-border rounded-xl px-4 py-2.5 text-xs text-white font-cairo focus:border-cyber-cyan focus:outline-none">
                </div>
            </div>
        </div>

        <!-- SECTION 2: AI Agent & Vector Store Engine (Pinecone, Gemini, OpenAI) -->
        <div class="glass-obsidian rounded-3xl border border-obsidian-border p-6 lg:p-8 space-y-6">
            <!-- Header in a Card (Rule 13) -->
            <div class="p-4 rounded-2xl bg-obsidian-surface border border-obsidian-border flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-center sm:text-right">
                <div>
                    <h3 class="text-base font-black text-white font-cairo flex items-center justify-center sm:justify-start gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-cyber-purple"></span>
                        <span>محرك الذكاء الاصطناعي وقاعدة الفيكتور (Pinecone & LLMs)</span>
                    </h3>
                    <p class="text-xs text-slate-400 font-cairo mt-1">تحديد المفاتيح البرمجية ومستودع البحث الدلالي وموجهات وكيل سولي الذكي.</p>
                </div>
                <span class="px-3 py-1 rounded-full text-[10px] font-mono bg-cyber-purple/10 text-purple-300 border border-cyber-purple/30 self-center sm:self-auto">
                    AI AGENT CONFIG
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                <!-- Pinecone API Key -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-300 font-cairo">Pinecone API Key</label>
                    <input type="password" name="settings[pinecone_api_key]" value="{{ \App\Models\Setting::get('pinecone_api_key', '') }}"
                           placeholder="pcsk_..."
                           class="w-full bg-obsidian-base border border-obsidian-border rounded-xl px-4 py-2.5 text-xs text-white font-mono text-left focus:border-cyber-cyan focus:outline-none">
                </div>

                <!-- Pinecone Host -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-300 font-cairo">Pinecone Host URL</label>
                    <input type="text" name="settings[pinecone_host]" value="{{ \App\Models\Setting::get('pinecone_host', '') }}"
                           placeholder="souly-knowledge-v1-xxxx.svc.pinecone.io"
                           class="w-full bg-obsidian-base border border-obsidian-border rounded-xl px-4 py-2.5 text-xs text-white font-mono text-left focus:border-cyber-cyan focus:outline-none">
                </div>

                <!-- Pinecone Index -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-300 font-cairo">Pinecone Index Name</label>
                    <input type="text" name="settings[pinecone_index]" value="{{ \App\Models\Setting::get('pinecone_index', 'souly-knowledge-v1') }}"
                           class="w-full bg-obsidian-base border border-obsidian-border rounded-xl px-4 py-2.5 text-xs text-white font-mono text-left focus:border-cyber-cyan focus:outline-none">
                </div>

                <!-- Google Gemini API Key -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-300 font-cairo">Google Gemini API Key</label>
                    <input type="password" name="settings[gemini_api_key]" value="{{ \App\Models\Setting::get('gemini_api_key', '') }}"
                           placeholder="AIzaSy..."
                           class="w-full bg-obsidian-base border border-obsidian-border rounded-xl px-4 py-2.5 text-xs text-white font-mono text-left focus:border-cyber-cyan focus:outline-none">
                </div>

                <!-- OpenAI API Key -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-300 font-cairo">OpenAI API Key</label>
                    <input type="password" name="settings[openai_api_key]" value="{{ \App\Models\Setting::get('openai_api_key', '') }}"
                           placeholder="sk-proj-..."
                           class="w-full bg-obsidian-base border border-obsidian-border rounded-xl px-4 py-2.5 text-xs text-white font-mono text-left focus:border-cyber-cyan focus:outline-none">
                </div>

                <!-- AI Welcome Message (Full Row) -->
                <div class="space-y-2 md:col-span-2 lg:col-span-3">
                    <label class="block text-xs font-bold text-slate-300 font-cairo">رسالة الترحيب الافتراضية لوكيل سولي الذكي</label>
                    <textarea name="settings[ai_welcome_message]" rows="2"
                              class="w-full bg-obsidian-base border border-obsidian-border rounded-xl px-4 py-2.5 text-xs text-white font-cairo focus:border-cyber-cyan focus:outline-none">{{ \App\Models\Setting::get('ai_welcome_message', 'مرحباً بك في سولي! 🛡️ أنا مستشارك الذكي، كيف يمكنني مساعدتك في تطوير أعمالك أو حجز خدماتك اليوم؟') }}</textarea>
                </div>
            </div>
        </div>

        <!-- SECTION 3: Cinematic Experience & Sound Settings -->
        <input type="hidden" name="__submitted_cinematic" value="1">
        <div class="glass-obsidian rounded-3xl border border-obsidian-border p-6 lg:p-8 space-y-6">
            <!-- Header in a Card (Rule 13) -->
            <div class="p-4 rounded-2xl bg-obsidian-surface border border-obsidian-border flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-center sm:text-right">
                <div>
                    <h3 class="text-base font-black text-white font-cairo flex items-center justify-center sm:justify-start gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-cyber-gold"></span>
                        <span>تجربة الدخول السينمائية والمؤثر الصوتي</span>
                    </h3>
                    <p class="text-xs text-slate-400 font-cairo mt-1">التحكم في شاشة التحميل السينمائية للموقع والصوت التفاعلي المصاحب.</p>
                </div>
                <span class="px-3 py-1 rounded-full text-[10px] font-mono bg-cyber-gold/10 text-cyber-gold border border-cyber-gold/30 self-center sm:self-auto">
                    CINEMATIC ENGINE
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 items-center">
                <!-- Toggle Cinematic Loader -->
                <div class="p-4 rounded-2xl bg-obsidian-surface border border-obsidian-border flex items-center justify-between">
                    <div>
                        <div class="text-xs font-bold text-white font-cairo">تفعيل اللودر السينمائي</div>
                        <div class="text-[10px] text-slate-400 font-cairo">عرض أنيميشن النسر وحلقات الرادار عند فتح الموقع</div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="settings[enable_cinematic_loader]" value="1" 
                               {{ \App\Models\Setting::get('enable_cinematic_loader', true) ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-cyber-cyan"></div>
                    </label>
                </div>

                <!-- Duration in Seconds -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-300 font-cairo">مدة اللودر الافتراضية (بالثواني)</label>
                    <input type="number" min="1" max="30" name="settings[loader_duration_seconds]" 
                           value="{{ \App\Models\Setting::get('loader_duration_seconds', 5) }}"
                           class="w-full bg-obsidian-base border border-obsidian-border rounded-xl px-4 py-2.5 text-xs text-white font-mono text-center focus:border-cyber-cyan focus:outline-none">
                </div>

                <!-- Toggle Audio -->
                <div class="p-4 rounded-2xl bg-obsidian-surface border border-obsidian-border flex items-center justify-between">
                    <div>
                        <div class="text-xs font-bold text-white font-cairo">المؤثر الصوتي للترحيب</div>
                        <div class="text-[10px] text-slate-400 font-cairo">تشغيل صوت هادئ عند بدء تشغيل المنظومة</div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="settings[enable_cinematic_audio]" value="1" 
                               {{ \App\Models\Setting::get('enable_cinematic_audio', true) ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-cyber-cyan"></div>
                    </label>
                </div>
            </div>
        </div>

        <!-- Save Button CTA -->
        <div class="flex items-center justify-end gap-4 pt-4 border-t border-obsidian-border">
            <button type="submit" 
                    class="px-8 py-3.5 rounded-2xl bg-cyber-cyan hover:bg-emerald-400 text-obsidian-base font-black font-cairo text-sm shadow-neon-cyan transition-all flex items-center gap-2 active:scale-95">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>حفظ وتحديث الإعدادات الآن 💾</span>
            </button>
        </div>
    </form>

    <!-- Manual Notification & Broadcast Sender Console -->
    <div class="glass-obsidian rounded-3xl border border-cyber-gold/40 p-6 lg:p-8 space-y-6 shadow-[0_10px_40px_rgba(255,184,0,0.08)]">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 border-b border-obsidian-border pb-4">
            <div class="space-y-1">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyber-gold/10 border border-cyber-gold/30 text-cyber-gold text-xs font-bold font-mono">
                    <span class="w-2 h-2 rounded-full bg-cyber-gold animate-ping"></span>
                    <span>MANUAL BROADCAST & NOTIFICATIONS</span>
                </div>
                <h3 class="text-xl font-black text-white font-cairo">مركز إرسال الإشعارات اليدوية والبث المباشر 📢</h3>
                <p class="text-xs text-slate-400 font-cairo">
                    إرسال تنبيهات لحظية تظهر مباشرة في درج إشعارات العميل والهيدر، مع إمكانية البث العام لكافة المستخدمين أو التوجيه لعميل محدد.
                </p>
            </div>
        </div>

        <form action="{{ route('admin.notifications.send_manual') }}" method="POST" class="space-y-5">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                <!-- Recipient Selector -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-300 font-cairo">
                        المستلم المستهدف <span class="text-rose-400">*</span>
                    </label>
                    <select name="recipient" required class="w-full bg-obsidian-base border border-obsidian-border rounded-xl px-4 py-2.5 text-xs text-white font-cairo focus:border-cyber-gold focus:outline-none">
                        <option value="all">🌐 بث عام لكافة المستخدمين والعملاء (Broadcast)</option>
                        <option value="clients">👥 جميع العملاء المسجلين فقط (Clients Only)</option>
                        <option value="admins">🛡️ فريق الإدارة والمهندسين فقط (Staff & Admins)</option>
                        @if(!empty($clientsList) && $clientsList->count() > 0)
                            <optgroup label="── تحديد عميل معين ──">
                                @foreach($clientsList as $userItem)
                                    <option value="{{ $userItem->id }}">{{ $userItem->name }} ({{ $userItem->phone ?: $userItem->email }}) [{{ $userItem->role }}]</option>
                                @endforeach
                            </optgroup>
                        @endif
                    </select>
                </div>

                <!-- Priority / Type -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-300 font-cairo">
                        نوع وتصنيف الإشعار <span class="text-rose-400">*</span>
                    </label>
                    <select name="type" required class="w-full bg-obsidian-base border border-obsidian-border rounded-xl px-4 py-2.5 text-xs text-white font-cairo focus:border-cyber-gold focus:outline-none">
                        <option value="info">💡 معلومة / إعلان وتحديث عام</option>
                        <option value="wallet">💰 تنبيه مالي / عروض شحن المحفظة</option>
                        <option value="system">⚙️ تحديث نظام / صيانة وتشغيل</option>
                        <option value="booking">📅 حجوزات ومواعيد هندسية</option>
                        <option value="crm">🔥 مبيعات واستشارات فنية</option>
                    </select>
                </div>

                <!-- Action URL -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-300 font-cairo">
                        رابط التوجيه (اختياري)
                    </label>
                    <input type="text" 
                           name="action_url" 
                           placeholder="/portal/wallet أو https://..." 
                           class="w-full bg-obsidian-base border border-obsidian-border rounded-xl px-4 py-2.5 text-xs text-white font-mono placeholder:text-slate-600 focus:border-cyber-gold focus:outline-none">
                </div>
            </div>

            <!-- Title -->
            <div class="space-y-2">
                <label class="block text-xs font-bold text-slate-300 font-cairo">
                    عنوان الإشعار <span class="text-rose-400">*</span>
                </label>
                <input type="text" 
                       name="title" 
                       required 
                       placeholder="مثال: خصم 20% على شحن المحفظة السنوي لنظام BrandERP" 
                       class="w-full bg-obsidian-base border border-obsidian-border rounded-xl px-4 py-2.5 text-xs text-white font-cairo placeholder:text-slate-600 focus:border-cyber-gold focus:outline-none">
            </div>

            <!-- Message -->
            <div class="space-y-2">
                <label class="block text-xs font-bold text-slate-300 font-cairo">
                    نص الإشعار والتفاصيل <span class="text-rose-400">*</span>
                </label>
                <textarea name="message" 
                          rows="3" 
                          required 
                          placeholder="اكتب رسالة الإشعار بوضوح ودقة لتظهر في درج التنبيهات الخاص بالعميل..." 
                          class="w-full bg-obsidian-base border border-obsidian-border rounded-xl p-4 text-xs text-white font-cairo placeholder:text-slate-600 focus:border-cyber-gold focus:outline-none"></textarea>
            </div>

            <!-- Submit -->
            <div class="flex items-center justify-end pt-2">
                <button type="submit" 
                        class="px-8 py-3 rounded-2xl bg-gradient-to-r from-cyber-gold to-amber-500 hover:from-amber-500 hover:to-cyber-gold text-obsidian-base font-black font-cairo text-xs shadow-md transition-all flex items-center gap-2 active:scale-95">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                    </svg>
                    <span>إرسال وبث الإشعار فورياً 🚀</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
