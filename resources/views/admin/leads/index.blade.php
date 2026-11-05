@extends('admin.layout')

@section('title', 'منظومة إدارة العملاء المحتملين CRM | سولي')
@section('page-title', 'العملاء المحتملين ومسار المبيعات CRM')

@section('admin-content')
<div class="space-y-8" x-data="{ openNewLeadModal: false }">

    <!-- Top Bar -->
    <div class="glass-obsidian rounded-3xl border border-obsidian-border p-6 sm:p-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="space-y-1">
            <h2 class="text-xl sm:text-2xl font-black text-white font-cairo flex items-center gap-2.5">
                <span class="w-3 h-3 rounded-full bg-cyber-purple animate-pulse"></span>
                <span>مسار مبيعات ومتابعة العملاء (Mini-CRM Pipeline)</span>
            </h2>
            <p class="text-xs text-slate-400 font-cairo">
                متابعة كافة العملاء القادمين من شات الذكاء الاصطناعي، الحجوزات، والإعلانات، وتسجيل سجل المكالمات والواتساب.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <button type="button" @click="openNewLeadModal = true"
                    class="px-5 py-2.5 rounded-2xl bg-gradient-to-r from-cyber-purple via-cyber-cyan to-cyber-gold text-obsidian-base font-extrabold text-xs font-cairo shadow-neon-purple hover:scale-105 active:scale-95 transition-all">
                + إضافة عميل محتمل جديد
            </button>
        </div>
    </div>

    <!-- Pipeline Stages Tabs -->
    <div class="flex items-center gap-2 overflow-x-auto pb-2 text-xs font-cairo font-bold">
        <a href="{{ route('admin.leads.index') }}" 
           class="px-4 py-2 rounded-xl transition-all shrink-0
           {{ !request()->filled('status') ? 'bg-cyber-cyan text-obsidian-base shadow-neon-cyan' : 'bg-obsidian-surface text-slate-400 border border-obsidian-border hover:text-white' }}">
            الكل ({{ $pipelineCounts['all'] }})
        </a>
        <a href="{{ route('admin.leads.index') }}?status=new" 
           class="px-4 py-2 rounded-xl transition-all shrink-0
           {{ request()->query('status') === 'new' ? 'bg-cyber-cyan text-obsidian-base shadow-neon-cyan' : 'bg-obsidian-surface text-slate-400 border border-obsidian-border hover:text-white' }}">
            جديد ({{ $pipelineCounts['new'] }})
        </a>
        <a href="{{ route('admin.leads.index') }}?status=contacted" 
           class="px-4 py-2 rounded-xl transition-all shrink-0
           {{ request()->query('status') === 'contacted' ? 'bg-amber-500 text-obsidian-base' : 'bg-obsidian-surface text-slate-400 border border-obsidian-border hover:text-white' }}">
            تم التواصل ({{ $pipelineCounts['contacted'] }})
        </a>
        <a href="{{ route('admin.leads.index') }}?status=qualified" 
           class="px-4 py-2 rounded-xl transition-all shrink-0
           {{ request()->query('status') === 'qualified' ? 'bg-cyber-gold text-obsidian-base' : 'bg-obsidian-surface text-slate-400 border border-obsidian-border hover:text-white' }}">
            مؤهل ومطابق ({{ $pipelineCounts['qualified'] }})
        </a>
        <a href="{{ route('admin.leads.index') }}?status=proposal_sent" 
           class="px-4 py-2 rounded-xl transition-all shrink-0
           {{ request()->query('status') === 'proposal_sent' ? 'bg-cyber-purple text-white' : 'bg-obsidian-surface text-slate-400 border border-obsidian-border hover:text-white' }}">
            أُرسل العرض ({{ $pipelineCounts['proposal_sent'] }})
        </a>
        <a href="{{ route('admin.leads.index') }}?status=won" 
           class="px-4 py-2 rounded-xl transition-all shrink-0
           {{ request()->query('status') === 'won' ? 'bg-emerald-500 text-obsidian-base' : 'bg-obsidian-surface text-slate-400 border border-obsidian-border hover:text-white' }}">
            صفقة ناجحة ✓ ({{ $pipelineCounts['won'] }})
        </a>
    </div>

    <!-- Leads Table & Tactical Mobile Action Cards -->
    <div class="rounded-3xl glass-obsidian border border-obsidian-border p-4 sm:p-8 space-y-4">
        
        <!-- Desktop Table (Hidden on Mobile) -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead>
                    <tr class="border-b border-obsidian-border/80 text-slate-400 font-cairo">
                        <th class="py-3 px-3">الاسم والشركة</th>
                        <th class="py-3 px-3">الهاتف والواتساب</th>
                        <th class="py-3 px-3">الخدمة المهتم بها</th>
                        <th class="py-3 px-3">المصدر</th>
                        <th class="py-3 px-3">المرحلة</th>
                        <th class="py-3 px-3">المسؤول</th>
                        <th class="py-3 px-3">التفاعلات</th>
                        <th class="py-3 px-3 text-center">الإجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-obsidian-border/50">
                    @forelse($leads as $lead)
                        <tr class="hover:bg-obsidian-surface/60 transition-colors">
                            <td class="py-3.5 px-3 font-cairo">
                                <div class="font-bold text-white text-sm">{{ $lead->name }}</div>
                                <div class="text-[10px] text-slate-400">{{ $lead->company_name ?? 'شركة ناشئة / فرد' }}</div>
                            </td>
                            <td class="py-3.5 px-3 text-cyber-cyan font-bold font-mono">
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $lead->phone) }}" target="_blank" class="hover:underline flex items-center gap-1">
                                    <span>{{ $lead->phone }}</span>
                                    <span class="text-[10px] text-emerald-400 font-cairo">واتساب ↗</span>
                                </a>
                            </td>
                            <td class="py-3.5 px-3 font-cairo text-slate-300">
                                {{ $lead->interestedService?->name ?? 'غير محدد' }}
                            </td>
                            <td class="py-3.5 px-3">
                                <span class="px-2 py-0.5 rounded text-[10px] uppercase font-mono
                                    {{ $lead->source === 'ai_agent' ? 'bg-cyber-cyan/15 text-cyber-cyan border border-cyber-cyan/30' : 'bg-obsidian-surface text-slate-400' }}">
                                    {{ $lead->source }}
                                </span>
                            </td>
                            <td class="py-3.5 px-3">
                                <span class="px-2.5 py-0.5 rounded text-[10px] font-bold font-cairo
                                    @if($lead->status === 'new') bg-cyber-cyan/15 text-cyber-cyan
                                    @elseif($lead->status === 'won') bg-emerald-500/20 text-emerald-400
                                    @elseif($lead->status === 'contacted') bg-amber-500/15 text-amber-400
                                    @elseif($lead->status === 'proposal_sent') bg-purple-500/20 text-purple-300
                                    @else bg-slate-700 text-slate-300
                                    @endif">
                                    {{ $lead->status }}
                                </span>
                            </td>
                            <td class="py-3.5 px-3 font-cairo text-slate-300">
                                {{ $lead->assignedStaff?->name ?? 'غير محدد' }}
                            </td>
                            <td class="py-3.5 px-3 text-slate-400 font-cairo text-xs">
                                <span class="font-mono font-bold text-white">{{ $lead->activities->count() }}</span> نشاط
                            </td>
                            <td class="py-3.5 px-3 text-center">
                                <a href="{{ route('admin.leads.show', $lead) }}" 
                                   class="btn-citadel-secondary text-xs py-1.5 px-3 inline-block">
                                    ملف العميل Dossier ↗
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-400 font-cairo">
                                لا يوجد عملاء في هذه المرحلة من المسار.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Tactical Action Cards (Visible on < 768px only) -->
        <div class="md:hidden space-y-3">
            @forelse($leads as $lead)
                <div class="p-4 rounded-2xl bg-obsidian-surface border border-obsidian-border space-y-3 shadow-md">
                    <div class="flex items-center justify-between border-b border-obsidian-border/70 pb-2">
                        <div>
                            <span class="font-bold text-white text-sm font-cairo block">{{ $lead->name }}</span>
                            <span class="text-[10px] text-slate-400 font-cairo">{{ $lead->company_name ?? 'عميل محتمل' }}</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="px-2 py-0.5 rounded text-[9px] uppercase font-mono
                                {{ $lead->source === 'ai_agent' ? 'bg-cyber-cyan/15 text-cyber-cyan border border-cyber-cyan/30' : 'bg-obsidian-surface text-slate-400' }}">
                                {{ $lead->source }}
                            </span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold font-cairo
                                @if($lead->status === 'new') bg-cyber-cyan/15 text-cyber-cyan
                                @elseif($lead->status === 'won') bg-emerald-500/20 text-emerald-400
                                @elseif($lead->status === 'contacted') bg-amber-500/15 text-amber-400
                                @elseif($lead->status === 'proposal_sent') bg-purple-500/20 text-purple-300
                                @else bg-slate-700 text-slate-300
                                @endif">
                                {{ $lead->status }}
                            </span>
                        </div>
                    </div>

                    <div class="p-2.5 rounded-xl bg-obsidian-card border border-obsidian-border flex items-center justify-between text-[11px] font-cairo">
                        <div>
                            <span class="text-slate-400 text-[10px] block">الخدمة المستهدفة:</span>
                            <span class="text-cyber-gold font-bold">{{ $lead->interestedService?->name ?? 'غير محدد' }}</span>
                        </div>
                        <div class="text-left">
                            <span class="text-slate-400 text-[10px] block">المسؤول:</span>
                            <span class="text-slate-200">{{ $lead->assignedStaff?->name ?? 'غير معين' }}</span>
                        </div>
                    </div>

                    <!-- 1-Tap Lead Call Confirmation Button -->
                    <button type="button" 
                            onclick="toggleLeadCall(this, {{ $lead->id }})" 
                            data-confirmed="{{ $lead->call_confirmed ? '1' : '0' }}"
                            class="w-full py-1.5 px-3 rounded-xl border text-[11px] font-cairo font-bold flex items-center justify-between transition-colors {{ $lead->call_confirmed ? 'bg-emerald-500/15 text-emerald-400 border-emerald-500/30' : 'bg-rose-500/15 text-rose-300 border-rose-500/40' }}">
                        <span class="flex items-center gap-1.5">
                            <span>📞</span>
                            <span class="lead-call-text">{{ $lead->call_confirmed ? 'تم الاتصال بالعميل هاتفياً' : 'لم يتم الاتصال بالعميل (اضغط للتأكيد)' }}</span>
                        </span>
                        <span class="text-[9px] font-mono font-bold lead-call-badge">{{ $lead->call_confirmed ? 'CONFIRMED' : 'CALL NOW' }}</span>
                    </button>

                    <div class="flex items-center gap-2 pt-1">
                        <a href="{{ route('admin.leads.show', $lead) }}" 
                           class="flex-1 py-2 rounded-xl btn-citadel-primary text-xs font-bold font-cairo text-center">
                            الملف والتواصل ({{ $lead->activities->count() }} تفاعل) ⇥
                        </a>
                        @if($lead->phone)
                            <a href="tel:{{ $lead->phone }}" class="p-2 rounded-xl bg-obsidian-card border border-obsidian-border text-slate-300 text-xs">
                                📞
                            </a>
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $lead->phone) }}" target="_blank" class="p-2 rounded-xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-400 text-xs font-bold">
                                💬
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-6 text-center text-slate-400 font-cairo text-xs bg-obsidian-surface rounded-2xl border border-obsidian-border">
                    لا يوجد عملاء في هذه المرحلة من المسار.
                </div>
            @endforelse
        </div>

        <div class="pt-4 border-t border-obsidian-border/60">
            {{ $leads->links() }}
        </div>
    </div>

    <!-- Add New Lead Modal -->
    <div x-show="openNewLeadModal" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 bg-black/80 backdrop-blur-md flex items-center justify-center p-4 overflow-y-auto"
         style="display: none;">
        
        <div @click.away="openNewLeadModal = false"
             class="relative w-full max-w-lg glass-obsidian border border-cyber-purple/40 rounded-3xl p-6 sm:p-8 space-y-6 shadow-neon-purple max-h-[90vh] overflow-y-auto">
            
            <div class="flex items-center justify-between border-b border-obsidian-border pb-4">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-cyber-purple animate-pulse"></span>
                    <h3 class="text-xl font-black text-white font-cairo">تسجيل عميل محتمل جديد في الـ CRM</h3>
                </div>
                <button type="button" @click="openNewLeadModal = false" class="p-2 text-slate-400 hover:text-white rounded-lg">✕</button>
            </div>

            <form action="{{ route('admin.leads.store') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Name & Phone -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-300 font-cairo">اسم العميل / المسؤول</label>
                        <input type="text" name="name" required placeholder="م. حسام الدين"
                               class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-4 py-2.5 text-xs text-white font-cairo focus:outline-none focus:border-cyber-purple">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-300 font-cairo">رقم الهاتف / الواتساب</label>
                        <input type="text" name="phone" required placeholder="010XXXXXXXX"
                               class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-4 py-2.5 text-xs text-white font-mono focus:outline-none focus:border-cyber-purple">
                    </div>
                </div>

                <!-- Company & Email -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-300 font-cairo">الشركة / المنشأة</label>
                        <input type="text" name="company_name" placeholder="شركة الفرسان للتوزيع"
                               class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-4 py-2.5 text-xs text-white font-cairo focus:outline-none focus:border-cyber-purple">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-300 font-cairo">البريد الإلكتروني</label>
                        <input type="email" name="email" placeholder="client@company.com"
                               class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-4 py-2.5 text-xs text-white font-mono focus:outline-none focus:border-cyber-purple">
                    </div>
                </div>

                <!-- Service & Stage -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-300 font-cairo">الخدمة المهتم بها</label>
                        <select name="interested_service_id"
                                class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-4 py-2.5 text-xs text-white font-cairo focus:outline-none focus:border-cyber-purple">
                            <option value="">-- غير محدد --</option>
                            @foreach($services as $srv)
                                <option value="{{ $srv->id }}">{{ $srv->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-300 font-cairo">المرحلة الحالية</label>
                        <select name="status" required
                                class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-4 py-2.5 text-xs text-white font-cairo focus:outline-none focus:border-cyber-purple">
                            <option value="new">جديد (new)</option>
                            <option value="contacted">تم التواصل (contacted)</option>
                            <option value="qualified">مؤهل للشراء (qualified)</option>
                            <option value="proposal_sent">أرسل العرض المالي (proposal_sent)</option>
                            <option value="won">صفقة ناجحة (won)</option>
                        </select>
                    </div>
                </div>

                <!-- Notes -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-300 font-cairo">ملاحظات العميل الأولية</label>
                    <textarea name="notes" rows="3" placeholder="تفاصيل النشاط وعدد الفروع المطلوبة..."
                              class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl p-3 text-xs text-white font-cairo placeholder-slate-500 focus:outline-none focus:border-cyber-purple"></textarea>
                </div>

                <button type="submit"
                        class="btn-citadel-primary w-full py-3.5 text-xs sm:text-sm">
                    حفظ العميل في الـ CRM
                </button>
            </form>
        </div>
    </div>

</div>

<script>
function toggleLeadCall(btn, leadId) {
    fetch(`{{ url('/admin/leads') }}/${leadId}/toggle-call`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
    }).then(r => r.json()).then(d => {
        if(d.success) {
            btn.classList.toggle('bg-emerald-500/15', d.call_confirmed);
            btn.classList.toggle('text-emerald-400', d.call_confirmed);
            btn.classList.toggle('border-emerald-500/30', d.call_confirmed);
            btn.classList.toggle('bg-rose-500/15', !d.call_confirmed);
            btn.classList.toggle('text-rose-300', !d.call_confirmed);
            btn.classList.toggle('border-rose-500/40', !d.call_confirmed);
            btn.querySelector('.lead-call-text').textContent = d.call_confirmed ? 'تم الاتصال بالعميل هاتفياً' : 'لم يتم الاتصال بالعميل (اضغط للتأكيد)';
            btn.querySelector('.lead-call-badge').textContent = d.call_confirmed ? 'CONFIRMED' : 'CALL NOW';
        }
    }).catch(e => console.error(e));
}
</script>
@endsection
