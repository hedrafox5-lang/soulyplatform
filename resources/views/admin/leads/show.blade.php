@extends('admin.layout')

@section('title', 'ملف العميل: ' . $lead->name . ' | سولي CRM')
@section('page-title', 'ملف العميل Dossier: ' . $lead->name)

@section('admin-content')
<div class="space-y-8">

    <!-- Back & Top Header -->
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.leads.index') }}" class="text-xs text-slate-400 hover:text-white flex items-center gap-1.5 transition-colors">
            <svg class="w-4 h-4 rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
            </svg>
            <span>العودة لقائمة الـ CRM</span>
        </a>

        <div class="flex items-center gap-2">
            <span class="text-xs text-slate-400 font-cairo">مصدر العميل:</span>
            <span class="px-3 py-1 rounded-full text-xs font-mono font-bold uppercase bg-cyber-purple/20 text-purple-300 border border-cyber-purple/40">
                {{ $lead->source }}
            </span>
        </div>
    </div>

    <!-- Client Dossier Header Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Left Column: Client Summary & Pipeline Controls -->
        <div class="lg:col-span-5 space-y-6">
            
            <!-- Summary Card -->
            <div class="p-6 rounded-3xl glass-obsidian border border-obsidian-border space-y-5">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-cyber-purple to-cyber-cyan p-[1.5px] shrink-0">
                        <div class="w-full h-full bg-obsidian-surface rounded-2xl flex items-center justify-center font-bold text-white font-cairo">
                            {{ mb_substr($lead->name, 0, 2) }}
                        </div>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-white font-cairo">{{ $lead->name }}</h2>
                        <div class="text-xs text-slate-400 font-cairo">{{ $lead->company_name ?? 'شركة تجارية / فرد' }}</div>
                    </div>
                </div>

                <div class="space-y-2 pt-2 border-t border-obsidian-border/60 text-xs font-cairo">
                    <div class="flex justify-between items-center py-1">
                        <span class="text-slate-400">رقم الهاتف / الواتساب:</span>
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $lead->phone) }}" target="_blank" class="font-mono text-cyber-cyan font-bold hover:underline flex items-center gap-1">
                            <span>{{ $lead->phone }}</span>
                            <span class="text-[10px] text-emerald-400">WhatsApp ↗</span>
                        </a>
                    </div>

                    <div class="flex justify-between items-center py-1">
                        <span class="text-slate-400">البريد الإلكتروني:</span>
                        <span class="font-mono text-slate-200">{{ $lead->email ?? '—' }}</span>
                    </div>

                    <div class="flex justify-between items-center py-1">
                        <span class="text-slate-400">الخدمة المهتم بها:</span>
                        <span class="text-cyber-gold font-bold font-cairo">{{ $lead->interestedService?->name ?? 'غير محدد' }}</span>
                    </div>

                    <div class="flex justify-between items-center py-1">
                        <span class="text-slate-400">المسؤول عن المتابعة:</span>
                        <span class="text-purple-300 font-bold font-cairo">{{ $lead->assignedStaff?->name ?? 'غير معين' }}</span>
                    </div>
                </div>

                @if($lead->ai_summary)
                    <div class="p-3.5 rounded-2xl bg-cyber-cyan/10 border border-cyber-cyan/30 text-xs text-slate-300 font-cairo space-y-1">
                        <span class="text-cyber-cyan font-bold block text-[11px] font-cairo">ملخص الذكاء الاصطناعي (AI Summary):</span>
                        <p>{{ $lead->ai_summary }}</p>
                    </div>
                @endif
            </div>

            <!-- Pipeline Status & Assignment Updater -->
            <div class="p-6 rounded-3xl glass-obsidian border border-cyber-purple/30 space-y-4">
                <h3 class="text-sm font-bold text-white font-cairo">تحديث مرحلة العميل والمسؤول</h3>

                <form action="{{ route('admin.leads.status', $lead) }}" method="POST" class="space-y-4">
                    @csrf

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-300 font-cairo">المرحلة في المسار (Pipeline Stage)</label>
                        <select name="status" class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-4 py-2.5 text-xs text-white font-cairo focus:outline-none focus:border-cyber-purple">
                            <option value="new" {{ $lead->status === 'new' ? 'selected' : '' }}>جديد (new)</option>
                            <option value="contacted" {{ $lead->status === 'contacted' ? 'selected' : '' }}>تم التواصل (contacted)</option>
                            <option value="qualified" {{ $lead->status === 'qualified' ? 'selected' : '' }}>مؤهل ومطابق (qualified)</option>
                            <option value="proposal_sent" {{ $lead->status === 'proposal_sent' ? 'selected' : '' }}>أُرسل العرض المالي (proposal_sent)</option>
                            <option value="won" {{ $lead->status === 'won' ? 'selected' : '' }}>صفقة ناجحة ومغلقة (won)</option>
                            <option value="lost" {{ $lead->status === 'lost' ? 'selected' : '' }}>صفقة ملغية (lost)</option>
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-300 font-cairo">إسناد المتابعة إلى</label>
                        <select name="assigned_to" class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-4 py-2.5 text-xs text-white font-cairo focus:outline-none focus:border-cyber-purple">
                            <option value="">-- بدون إسناد --</option>
                            @foreach($staffMembers as $staff)
                                <option value="{{ $staff->id }}" {{ $lead->assigned_to == $staff->id ? 'selected' : '' }}>
                                    {{ $staff->name }} ({{ $staff->role }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" 
                            class="btn-citadel-primary w-full py-2.5 text-xs">
                        تحديث المسار والإسناد
                    </button>
                </form>
            </div>

        </div>

        <!-- Right Column: Chronological Activities Timeline & Interaction Logger -->
        <div class="lg:col-span-7 space-y-6">
            
            <!-- Log New Interaction Form -->
            <div class="p-6 rounded-3xl glass-obsidian border border-obsidian-border space-y-4">
                <h3 class="text-sm font-bold text-white font-cairo flex items-center gap-2">
                    <svg class="w-4 h-4 text-cyber-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <span>تسجيل نشاط أو ملاحظة تواصل جديدة</span>
                </h3>

                <form action="{{ route('admin.leads.activity', $lead) }}" method="POST" class="space-y-4">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-2">
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="call" checked class="peer sr-only">
                            <div class="p-2.5 rounded-xl bg-obsidian-surface border border-obsidian-border peer-checked:border-cyber-cyan peer-checked:bg-cyber-cyan/15 peer-checked:text-cyber-cyan text-slate-300 text-xs font-cairo font-bold text-center transition-all">
                                📞 مكالمة هاتفية
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="whatsapp" class="peer sr-only">
                            <div class="p-2.5 rounded-xl bg-obsidian-surface border border-obsidian-border peer-checked:border-emerald-400 peer-checked:bg-emerald-500/15 peer-checked:text-emerald-400 text-slate-300 text-xs font-cairo font-bold text-center transition-all">
                                💬 محادثة واتساب
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="meeting" class="peer sr-only">
                            <div class="p-2.5 rounded-xl bg-obsidian-surface border border-obsidian-border peer-checked:border-cyber-gold peer-checked:bg-cyber-gold/15 peer-checked:text-cyber-gold text-slate-300 text-xs font-cairo font-bold text-center transition-all">
                                🤝 اجتماع / ديمو
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="note" class="peer sr-only">
                            <div class="p-2.5 rounded-xl bg-obsidian-surface border border-obsidian-border peer-checked:border-purple-400 peer-checked:bg-purple-500/15 peer-checked:text-purple-300 text-slate-300 text-xs font-cairo font-bold text-center transition-all">
                                📝 ملاحظة داخلية
                            </div>
                        </label>
                    </div>

                    <textarea name="description" rows="3" required placeholder="اكتب نتائج المكالمة، متطلبات العميل، أو موعد المتابعة القادم..."
                              class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl p-3.5 text-xs text-white font-cairo placeholder-slate-500 focus:outline-none focus:border-cyber-cyan"></textarea>

                    <div class="flex justify-end">
                        <button type="submit" 
                                class="btn-citadel-primary px-5 py-2.5 text-xs">
                            إضافة النشاط للسجل
                        </button>
                    </div>
                </form>
            </div>

            <!-- Activity Log Chronological Timeline -->
            <div class="p-6 rounded-3xl glass-obsidian border border-obsidian-border space-y-5">
                <h3 class="text-sm font-bold text-white font-cairo flex items-center justify-between">
                    <span>سجل التفاعلات الزمني (Activity Timeline)</span>
                    <span class="text-xs font-cairo text-slate-400"><strong class="font-mono text-white">{{ $lead->activities->count() }}</strong> حدث موثق</span>
                </h3>

                @if($lead->activities->count() > 0)
                    <div class="relative border-r border-obsidian-border/80 mr-4 space-y-6">
                        @foreach($lead->activities as $act)
                            <div class="relative pr-6">
                                <span class="absolute -right-2 top-1.5 w-4 h-4 rounded-full border-2 border-obsidian-base
                                    @if($act->type === 'call') bg-cyber-cyan
                                    @elseif($act->type === 'whatsapp') bg-emerald-400
                                    @elseif($act->type === 'meeting') bg-cyber-gold
                                    @elseif($act->type === 'status_change') bg-purple-500
                                    @else bg-slate-400
                                    @endif"></span>

                                <div class="p-4 rounded-2xl bg-obsidian-surface border border-obsidian-border/60 space-y-2">
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="font-bold font-cairo text-white">
                                            @if($act->type === 'call') 📞 مكالمة هاتفية
                                            @elseif($act->type === 'whatsapp') 💬 واتساب
                                            @elseif($act->type === 'meeting') 🤝 اجتماع / ديمو
                                            @elseif($act->type === 'status_change') ⚙ تغيير مرحلة المسار
                                            @else 📝 ملاحظة
                                            @endif
                                            <span class="text-[10px] text-slate-400 font-normal">بواسطة {{ $act->user?->name ?? 'النظام الآلي' }}</span>
                                        </span>

                                        <span class="text-[10px] text-slate-500 font-mono">
                                            {{ $act->created_at->format('Y-m-d H:i') }}
                                        </span>
                                    </div>

                                    <p class="text-xs text-slate-300 font-cairo leading-relaxed whitespace-pre-line">
                                        {{ $act->description }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-8 text-center text-xs text-slate-400 font-cairo">
                        لا توجد أي تفاعلات مسجلة في هذا الملف بعد.
                    </div>
                @endif
            </div>

        </div>

    </div>

</div>
@endsection
