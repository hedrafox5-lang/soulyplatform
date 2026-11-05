@extends('admin.layout')

@section('title', 'إدارة الفواتير والضمانات الذكية | سولي')

@section('admin-content')
<div class="space-y-6">
    <!-- Top Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-900 border border-slate-800 p-6 rounded-3xl">
        <div>
            <h1 class="text-xl font-black text-white flex items-center gap-2">
                <span>🧾 الفواتير الإلكترونية والضمانات</span>
            </h1>
            <p class="text-xs text-slate-400 mt-1">سجل الفواتير الضريبية والرقمية، متابعة السداد، وشهادات الضمان المعتمدة.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.invoices.warranty_claims') }}" class="px-4 py-2.5 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-300 font-bold text-xs hover:bg-amber-500/20 transition-all">
                مطالبات الضمان 🛡️
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-4">
            <span class="text-xs text-slate-500 block mb-1">إجمالي الإيرادات المسددة</span>
            <span class="text-2xl font-black font-mono text-emerald-400">{{ number_format($stats['total_revenue'] ?? 0, 2) }} ج.م</span>
        </div>
        <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-4">
            <span class="text-xs text-slate-500 block mb-1">فواتير قيد التحصيل</span>
            <span class="text-2xl font-black font-mono text-amber-400">{{ $stats['unpaid_count'] ?? 0 }}</span>
        </div>
        <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-4">
            <span class="text-xs text-slate-500 block mb-1">شهادات الضمان السارية</span>
            <span class="text-2xl font-black font-mono text-cyan-400">{{ $stats['warranties_count'] ?? 0 }}</span>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-slate-900 border border-slate-800 rounded-3xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead>
                    <tr class="bg-slate-950/70 border-b border-slate-800 text-slate-400">
                        <th class="py-3.5 px-4 font-bold">رقم الفاتورة</th>
                        <th class="py-3.5 px-4 font-bold">العميل</th>
                        <th class="py-3.5 px-4 font-bold">المصدر</th>
                        <th class="py-3.5 px-4 font-bold">المبلغ</th>
                        <th class="py-3.5 px-4 font-bold">الحالة</th>
                        <th class="py-3.5 px-4 font-bold">التاريخ</th>
                        <th class="py-3.5 px-4 font-bold text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($invoices as $inv)
                    <tr class="hover:bg-slate-800/30 transition-colors">
                        <td class="py-3 px-4 font-mono font-bold text-cyan-400">
                            #{{ $inv->invoice_number }}
                        </td>
                        <td class="py-3 px-4">
                            <span class="font-bold text-white block">{{ $inv->client_name }}</span>
                            <span class="text-[11px] text-slate-500 font-mono">{{ $inv->client_phone }}</span>
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-0.5 rounded-lg bg-slate-800 text-slate-300 text-[10px] uppercase">{{ $inv->source_type }}</span>
                        </td>
                        <td class="py-3 px-4 font-mono font-bold text-white">
                            {{ number_format($inv->total_amount, 2) }} ج.م
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $inv->payment_status === 'paid' ? 'bg-emerald-950 border border-emerald-800 text-emerald-400' : 'bg-amber-950 border border-amber-800 text-amber-400' }}">
                                {{ $inv->payment_status === 'paid' ? 'مسددة' : 'معلقة' }}
                            </span>
                        </td>
                        <td class="py-3 px-4 font-mono text-slate-500">
                            {{ $inv->created_at->format('Y-m-d') }}
                        </td>
                        <td class="py-3 px-4 text-center">
                            <a href="{{ route('admin.invoices.show', $inv->id) }}" class="px-3 py-1 rounded-xl bg-slate-800 hover:bg-slate-700 text-cyan-400 font-bold transition-all">
                                عرض التفاصيل
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-slate-500">لا توجد فواتير مسجلة حالياً.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($invoices->hasPages())
        <div class="p-4 border-t border-slate-800">
            {{ $invoices->links() }}
        </div>
        @endif
    </div>
</div>
@endsection