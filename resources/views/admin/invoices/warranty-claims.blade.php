@extends('admin.layout')

@section('title', 'مطالبات الضمان والصيانة المجانية | سولي')

@section('admin-content')
<div class="space-y-6">
    <div class="flex items-center justify-between bg-slate-900 border border-slate-800 p-6 rounded-3xl">
        <div>
            <h1 class="text-xl font-black text-white flex items-center gap-2">
                <span>🛡️ مطالبات الضمان والصيانة</span>
            </h1>
            <p class="text-xs text-slate-400 mt-1">طلبات الصيانة المجانية المقدمة من العملاء بموجب شهادات الضمان المعتمدة.</p>
        </div>
        <a href="{{ route('admin.invoices.index') }}" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold transition-all">
            ← قائمة الفواتير
        </a>
    </div>

    <div class="bg-slate-900 border border-slate-800 rounded-3xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead>
                    <tr class="bg-slate-950/70 border-b border-slate-800 text-slate-400">
                        <th class="py-3.5 px-4 font-bold">رقم المطالبة</th>
                        <th class="py-3.5 px-4 font-bold">شهادة الضمان</th>
                        <th class="py-3.5 px-4 font-bold">العميل</th>
                        <th class="py-3.5 px-4 font-bold">وصف العطل</th>
                        <th class="py-3.5 px-4 font-bold">الحالة</th>
                        <th class="py-3.5 px-4 font-bold text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($claims as $claim)
                    <tr class="hover:bg-slate-800/30 transition-colors">
                        <td class="py-3 px-4 font-mono font-bold text-cyan-400">#{{ $claim->claim_number }}</td>
                        <td class="py-3 px-4 font-mono text-white">{{ $claim->warranty?->certificate_number ?? '-' }}</td>
                        <td class="py-3 px-4 text-white font-bold">{{ $claim->user?->name ?? 'عميل' }}</td>
                        <td class="py-3 px-4 text-slate-300 max-w-xs truncate">{{ $claim->issue_description }}</td>
                        <td class="py-3 px-4">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-800 text-amber-300 border border-slate-700 uppercase">
                                {{ $claim->claim_status }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <form method="POST" action="{{ route('admin.invoices.resolve_warranty', $claim->id) }}" class="inline-flex gap-1.5">
                                @csrf
                                <select name="claim_status" onchange="this.form.submit()" class="bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-1 text-xs text-white">
                                    <option value="pending" {{ $claim->claim_status === 'pending' ? 'selected' : '' }}>قيد المراجعة</option>
                                    <option value="approved" {{ $claim->claim_status === 'approved' ? 'selected' : '' }}>قبول الضمان</option>
                                    <option value="in_progress" {{ $claim->claim_status === 'in_progress' ? 'selected' : '' }}>جاري الصيانة</option>
                                    <option value="resolved" {{ $claim->claim_status === 'resolved' ? 'selected' : '' }}>تم الإصلاح</option>
                                    <option value="rejected" {{ $claim->claim_status === 'rejected' ? 'selected' : '' }}>مرفوض</option>
                                </select>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-slate-500">لا توجد مطالبات ضمان مسجلة حالياً.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection