@extends('portal.layout')

@section('title', 'فواتيري والضمانات المعتمدة | بوابة سولي')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between bg-slate-900 border border-slate-800 p-6 rounded-3xl">
        <div>
            <h1 class="text-xl font-black text-white flex items-center gap-2">
                <span>🧾 فواتيري وسجل الضمانات</span>
            </h1>
            <p class="text-xs text-slate-400 mt-1">استعراض كافة الفواتير الإلكترونية المعتمدة وشهادات الضمان السارية لأجهزتك.</p>
        </div>
    </div>

    <!-- Active Warranties Strip -->
    @if($warranties->isNotEmpty())
    <div class="space-y-3">
        <h2 class="text-xs font-bold text-amber-400 uppercase tracking-wider">🛡️ شهادات الضمان السارية لك:</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
            @foreach($warranties as $w)
            <div class="bg-slate-900 border border-slate-800 p-4 rounded-2xl relative overflow-hidden">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-white">{{ $w->product_or_service_name }}</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-950 border border-amber-800 text-amber-300">ساري</span>
                </div>
                <div class="text-[11px] text-slate-400 font-mono space-y-0.5">
                    <div>رقم الشهادة: <span class="text-cyan-400">#{{ $w->certificate_number }}</span></div>
                    <div>ينتهي في: <span class="text-slate-300">{{ $w->expires_at->format('Y-m-d') }}</span></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Invoices Table -->
    <div class="bg-slate-900 border border-slate-800 rounded-3xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead>
                    <tr class="bg-slate-950/70 border-b border-slate-800 text-slate-400">
                        <th class="py-3.5 px-4 font-bold">رقم الفاتورة</th>
                        <th class="py-3.5 px-4 font-bold">النوع / المصدر</th>
                        <th class="py-3.5 px-4 font-bold">المبلغ</th>
                        <th class="py-3.5 px-4 font-bold">حالة السداد</th>
                        <th class="py-3.5 px-4 font-bold">التاريخ</th>
                        <th class="py-3.5 px-4 font-bold text-center">عرض / طباعة</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($invoices as $inv)
                    <tr class="hover:bg-slate-800/30 transition-colors">
                        <td class="py-3 px-4 font-mono font-bold text-cyan-400">#{{ $inv->invoice_number }}</td>
                        <td class="py-3 px-4 text-slate-300 capitalize">{{ $inv->source_type }}</td>
                        <td class="py-3 px-4 font-mono font-bold text-white">{{ number_format($inv->total_amount, 2) }} ج.م</td>
                        <td class="py-3 px-4">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $inv->payment_status === 'paid' ? 'bg-emerald-950 border border-emerald-800 text-emerald-400' : 'bg-amber-950 border border-amber-800 text-amber-400' }}">
                                {{ $inv->payment_status === 'paid' ? 'مسددة' : 'معلقة' }}
                            </span>
                        </td>
                        <td class="py-3 px-4 font-mono text-slate-500">{{ $inv->created_at->format('Y-m-d') }}</td>
                        <td class="py-3 px-4 text-center">
                            <a href="{{ route('portal.invoices.show', $inv->id) }}" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-cyan-400 font-bold transition-all">
                                عرض الفاتورة ↗
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-slate-500">لا توجد فواتير مسجلة في حسابك حتى الآن.</td>
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