@extends('admin.layout')

@section('title', 'وكيل القيادة التنفيذي وخزينة الـ AI — سولي')

@section('content')
<div class="space-y-6" x-data="{
    activeTab: 'copilot',
    currentAdminModel: '{{ $adminModel }}',
    customModelInput: '',
    async setModel(modelName) {
        if (!modelName) return;
        try {
            const res = await fetch('{{ route('admin.ai_copilot.change_model') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ model: modelName, scope: 'both' })
            });
            const data = await res.json();
            if (data.success) {
                this.currentAdminModel = modelName;
                alert(data.message);
            }
        } catch (e) {
            alert('فشل تغيير الموديل: ' + e.message);
        }
    },
    async pingKey(keyId) {
        try {
            const res = await fetch(`{{ url('/admin/ai-copilot/keys/ping') }}/${keyId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });
            const data = await res.json();
            alert(`نتيجة فحص المفتاح: ${data.message} (${data.latency_ms}ms)`);
            window.location.reload();
        } catch (e) {
            alert('فشل الفحص: ' + e.message);
        }
    },
    async toggleKey(keyId) {
        try {
            const res = await fetch(`{{ url('/admin/ai-copilot/keys/toggle') }}/${keyId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });
            const data = await res.json();
            alert(data.message);
            window.location.reload();
        } catch (e) {
            alert('حدث خطأ: ' + e.message);
        }
    }
}">

    <!-- Top High-Command Header with Active Dynamic Model Banner -->
    <div class="glass-obsidian rounded-3xl border border-cyber-gold/40 p-5 sm:p-6 lg:p-7 space-y-5 shadow-neon-gold">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <span class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-cyber-gold via-amber-500 to-rose-600 text-obsidian-base font-black flex items-center justify-center text-2xl shadow-neon-gold shrink-0">
                    👑
                </span>
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-lg sm:text-xl font-black text-white font-cairo">منظومة القائد المنفرد ومساعد الذكاء الاصطناعي السيادي</h2>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-cyber-gold/20 text-cyber-gold border border-cyber-gold/50 shadow-neon-gold">
                            ROOT LEVEL ⚡
                        </span>
                    </div>
                    <p class="text-xs text-slate-400 font-cairo">
                        التحكم المباشر بقاعدة البيانات، مبدل الموديلات، وخزينة مفاتيح الـ API ذات التناوب التلقائي اللحظي
                    </p>
                </div>
            </div>

            <!-- 1-Tap Model Selector Bar -->
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-xs text-slate-400 font-cairo font-bold">الموديل النشط:</span>
                <template x-for="m in ['gemini-3.8-flash', 'gemini-1-argon', 'gemini-2.5-pro', 'gemini-2.5-flash', 'gemini-2.0-flash']" :key="m">
                    <button type="button" 
                            @click="setModel(m)"
                            :class="currentAdminModel === m ? 'bg-cyber-gold text-obsidian-base font-black shadow-neon-gold' : 'bg-obsidian-surface text-slate-300 hover:text-white border border-obsidian-border'"
                            class="px-2.5 py-1.5 rounded-xl text-xs font-mono transition-all">
                        <span x-text="m"></span>
                    </button>
                </template>
            </div>
        </div>

        <!-- Custom Model Input Strip -->
        <div class="pt-3 border-t border-obsidian-border/60 flex items-center gap-3">
            <input type="text" 
                   x-model="customModelInput" 
                   placeholder="أدخل أي معرف موديل مخصص من Google (Custom Model ID)..."
                   class="bg-obsidian-surface border border-obsidian-border focus:border-cyber-gold rounded-xl px-3 py-1.5 text-xs font-mono text-white focus:outline-none w-72">
            <button type="button" 
                    @click="setModel(customModelInput)"
                    :disabled="!customModelInput.trim()"
                    class="px-3 py-1.5 rounded-xl bg-obsidian-surface hover:bg-cyber-gold hover:text-obsidian-base text-cyber-gold border border-cyber-gold/40 text-xs font-cairo font-bold transition-all disabled:opacity-50">
                تفعيل الموديل المخصص ⚡
            </button>
        </div>
    </div>

    <!-- Morning Briefing Card -->
    <div class="glass-obsidian rounded-2xl border border-cyber-cyan/30 p-5 space-y-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <span class="text-xl">🌅</span>
                <h3 class="text-sm font-black text-white font-cairo">الإحاطة الصباحية التنفيذية ({{ $briefing['date_arabic'] }})</h3>
            </div>
            <span class="px-2.5 py-0.5 rounded-full text-xs font-mono font-bold bg-cyber-cyan/20 text-cyber-cyan border border-cyber-cyan/40">
                {{ $briefing['workload_percentage'] }}% حمولة اليوم
            </span>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div class="p-3 rounded-xl bg-obsidian-surface border border-obsidian-border">
                <span class="text-[10px] text-slate-400 font-cairo block">حجوزات اليوم</span>
                <span class="text-base font-black text-white font-mono">{{ $briefing['today_bookings_count'] }} زيارة</span>
            </div>
            <div class="p-3 rounded-xl bg-obsidian-surface border border-obsidian-border">
                <span class="text-[10px] text-rose-400 font-cairo block">مكالمات لم تؤكد</span>
                <span class="text-base font-black text-rose-300 font-mono">{{ $briefing['unconfirmed_calls_count'] }} عميل</span>
            </div>
            <div class="p-3 rounded-xl bg-obsidian-surface border border-obsidian-border">
                <span class="text-[10px] text-amber-400 font-cairo block">إيداعات معلقة</span>
                <span class="text-base font-black text-amber-300 font-mono">{{ $briefing['pending_wallets_count'] }} طلب</span>
            </div>
            <div class="p-3 rounded-xl bg-obsidian-surface border border-obsidian-border">
                <span class="text-[10px] text-emerald-400 font-cairo block">إيرادات اليوم</span>
                <span class="text-base font-black text-emerald-300 font-mono">{{ number_format($briefing['today_revenue'], 0) }} EGP</span>
            </div>
        </div>
    </div>

    <!-- Multi-API Key Failover Vault Section -->
    <div class="glass-obsidian rounded-2xl border border-purple-500/30 p-5 space-y-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <span class="text-xl">🔑</span>
                <div>
                    <h3 class="text-sm font-black text-white font-cairo">خزينة مفاتيح الـ API المتعددة (Multi-Key Pool Vault)</h3>
                    <p class="text-[11px] text-slate-400 font-cairo">تبديل لحظي في أقل من 50ms عند نفاذ الكوتا أو بطء الاستجابة</p>
                </div>
            </div>

            <!-- Trigger Add Key Modal -->
            <button type="button" 
                    @click="$dispatch('open-add-key-modal')"
                    class="px-3 py-1.5 rounded-xl bg-purple-500/20 hover:bg-purple-500/30 text-purple-300 border border-purple-500/40 text-xs font-cairo font-bold">
                ➕ إضافة مفتاح جديد
            </button>
        </div>

        <!-- Key Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($keys as $k)
                <div class="p-4 rounded-xl bg-obsidian-surface border {{ $k->status === 'active' ? 'border-emerald-500/40' : ($k->status === 'cooldown' ? 'border-amber-500/40' : 'border-rose-500/40') }} space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-white font-cairo">{{ $k->label }}</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold font-cairo
                            {{ $k->status === 'active' ? 'bg-emerald-500/20 text-emerald-400' : ($k->status === 'cooldown' ? 'bg-amber-500/20 text-amber-300' : 'bg-rose-500/20 text-rose-300') }}">
                            {{ $k->status === 'active' ? '🟢 نشط' : ($k->status === 'cooldown' ? '🟡 كوتا ممتلئة' : '🔴 معطل') }}
                        </span>
                    </div>

                    <div class="text-[11px] font-mono text-slate-400 space-y-1">
                        <div>📧 {{ $k->account_email ?: 'غير محدد' }}</div>
                        <div>⚡ الأولوية: #{{ $k->priority }} | المزود: {{ strtoupper($k->provider) }}</div>
                        <div>📊 طلبات اليوم: {{ $k->requests_today }} | الإجمالي: {{ $k->total_requests }}</div>
                        <div>⏱️ زمن الاستجابة: {{ $k->last_latency_ms ? $k->last_latency_ms . 'ms' : 'لم يختبر' }}</div>
                    </div>

                    <!-- Actions -->
                    <div class="pt-2 border-t border-obsidian-border/50 flex items-center justify-between gap-2">
                        <button type="button" 
                                @click="pingKey({{ $k->id }})"
                                class="px-2.5 py-1 rounded-lg bg-obsidian-base hover:bg-slate-800 text-cyber-cyan border border-cyber-cyan/30 text-[10px] font-cairo font-bold">
                            📡 اختبار البينج
                        </button>

                        <button type="button" 
                                @click="toggleKey({{ $k->id }})"
                                class="px-2.5 py-1 rounded-lg bg-obsidian-base hover:bg-slate-800 text-slate-300 border border-obsidian-border text-[10px] font-cairo">
                            {{ $k->status === 'disabled' ? 'تفعيل' : 'تعطيل' }}
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Recent Snapshots & 1-Tap Undo History -->
    <div class="glass-obsidian rounded-2xl border border-obsidian-border p-5 space-y-4">
        <div class="flex items-center gap-2">
            <span class="text-xl">↩️</span>
            <h3 class="text-sm font-black text-white font-cairo">سجل عمليات الذكاء الاصطناعي والتراجع الفوري (Action Snapshots)</h3>
        </div>

        <div class="overflow-x-auto rounded-xl bg-obsidian-surface border border-obsidian-border">
            <table class="w-full text-right text-xs font-cairo">
                <thead class="bg-black/30 text-slate-400 border-b border-obsidian-border font-bold">
                    <tr>
                        <th class="p-3">#</th>
                        <th class="p-3">نوع العملية</th>
                        <th class="p-3">الجدول الهدف</th>
                        <th class="p-3">الوصف بالعربية</th>
                        <th class="p-3">الحالة</th>
                        <th class="p-3 text-left">إجراء التراجع</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-obsidian-border/50">
                    @forelse($recentSnapshots as $snap)
                        <tr>
                            <td class="p-3 font-mono text-slate-400">#{{ $snap->id }}</td>
                            <td class="p-3 font-mono font-bold text-cyber-cyan">{{ strtoupper($snap->action_type) }}</td>
                            <td class="p-3 font-mono text-slate-300">{{ $snap->target_table }}</td>
                            <td class="p-3 text-white font-bold">{{ $snap->description_ar }}</td>
                            <td class="p-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $snap->is_reverted ? 'bg-slate-800 text-slate-500' : 'bg-emerald-500/20 text-emerald-400' }}">
                                    {{ $snap->is_reverted ? 'تم التراجع عنه' : 'ساري وفعال' }}
                                </span>
                            </td>
                            <td class="p-3 text-left">
                                @if(!$snap->is_reverted)
                                    <form action="{{ route('admin.ai_copilot.undo', $snap->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1 rounded-lg bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 border border-rose-500/40 text-[10px] font-bold">
                                            ↩️ تراجع فوري
                                        </button>
                                    </form>
                                @else
                                    <span class="text-[10px] text-slate-500 font-mono">REVERTED</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-4 text-center text-slate-500">لا توجد عمليات مسجلة حتى الآن.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
