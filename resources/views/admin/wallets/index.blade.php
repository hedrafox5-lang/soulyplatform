@extends('admin.layout')

@section('title', 'إدارة شحن المحفظة والمعاملات المالية | سولي ديدجيتال سيتادل')
@section('page-title', 'إدارة شحن المحفظة والمالية')

@section('admin-content')
<div class="space-y-8">

    <!-- Top Banner -->
    <div class="glass-obsidian rounded-3xl border border-obsidian-border p-6 sm:p-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="space-y-1">
            <h2 class="text-xl sm:text-2xl font-black text-white font-cairo flex items-center gap-2.5">
                <span class="w-3 h-3 rounded-full bg-cyber-cyan animate-pulse"></span>
                <span>دفتر قيود المحفظة واعتماد الإيداعات</span>
            </h2>
            <p class="text-xs text-slate-400 font-readex">
                مراجعة إيصالات فودافون كاش وإنستاباي والتحويلات البنكية واعتماد رصيد محافظ العملاء فورياً.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <span class="px-4 py-2 rounded-xl bg-amber-500/15 border border-amber-500/40 text-amber-400 font-mono text-xs font-bold">
                {{ $pendingCount }} إيداع معلق
            </span>
            <span class="px-4 py-2 rounded-xl bg-emerald-500/15 border border-emerald-500/40 text-emerald-400 font-mono text-xs font-bold">
                {{ number_format($approvedSum, 2) }} EGP معتمد
            </span>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="flex items-center gap-2 overflow-x-auto pb-2 text-xs font-cairo font-bold">
        <a href="{{ route('admin.wallets.index') }}" 
           class="px-4 py-2 rounded-xl transition-all shrink-0
           {{ !request()->filled('status') ? 'bg-cyber-cyan text-obsidian-base shadow-neon-cyan' : 'bg-obsidian-surface text-slate-400 border border-obsidian-border hover:text-white' }}">
            كافة المعاملات ({{ $transactions->total() }})
        </a>
        <a href="{{ route('admin.wallets.index') }}?status=pending" 
           class="px-4 py-2 rounded-xl transition-all shrink-0
           {{ request()->query('status') === 'pending' ? 'bg-amber-500 text-obsidian-base shadow-neon-gold' : 'bg-obsidian-surface text-amber-400 border border-obsidian-border' }}">
            بانتظار الاعتماد ({{ $pendingCount }})
        </a>
        <a href="{{ route('admin.wallets.index') }}?status=approved" 
           class="px-4 py-2 rounded-xl transition-all shrink-0
           {{ request()->query('status') === 'approved' ? 'bg-emerald-500 text-obsidian-base' : 'bg-obsidian-surface text-slate-400 border border-obsidian-border hover:text-white' }}">
            معتمدة
        </a>
        <a href="{{ route('admin.wallets.index') }}?status=rejected" 
           class="px-4 py-2 rounded-xl transition-all shrink-0
           {{ request()->query('status') === 'rejected' ? 'bg-rose-500 text-white' : 'bg-obsidian-surface text-slate-400 border border-obsidian-border hover:text-white' }}">
            مرفوضة
        </a>
    </div>

    <!-- Transactions Table -->
    <div class="rounded-3xl glass-obsidian border border-obsidian-border p-6 sm:p-8 space-y-4">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead>
                    <tr class="border-b border-obsidian-border/80 text-slate-400 font-readex">
                        <th class="py-3 px-3">الرقم المرجعي</th>
                        <th class="py-3 px-3">العميل والمحفظة</th>
                        <th class="py-3 px-3">النوع</th>
                        <th class="py-3 px-3">المبلغ</th>
                        <th class="py-3 px-3">وسيلة الدفع</th>
                        <th class="py-3 px-3">بيانات المحول</th>
                        <th class="py-3 px-3">الإيصال</th>
                        <th class="py-3 px-3">الحالة</th>
                        <th class="py-3 px-3 text-center">الإجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-obsidian-border/50 font-mono">
                    @forelse($transactions as $tx)
                        <tr class="hover:bg-obsidian-surface/60 transition-colors">
                            <td class="py-3.5 px-3 text-cyber-cyan font-bold">{{ $tx->reference_code }}</td>
                            <td class="py-3.5 px-3 font-cairo">
                                <div class="font-bold text-white">{{ $tx->user->name }}</div>
                                <div class="text-[10px] text-slate-400">{{ $tx->user->email }}</div>
                            </td>
                            <td class="py-3.5 px-3 font-cairo">
                                @if($tx->type === 'deposit')
                                    <span class="text-emerald-400">إيداع وشحن</span>
                                @elseif($tx->type === 'purchase')
                                    <span class="text-slate-200">شراء خدمة</span>
                                @else
                                    <span>{{ $tx->type }}</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-3 font-bold text-sm {{ $tx->type === 'deposit' ? 'text-emerald-400' : 'text-slate-200' }}">
                                {{ $tx->type === 'deposit' ? '+' : '-' }}{{ number_format($tx->amount, 2) }} EGP
                            </td>
                            <td class="py-3.5 px-3 font-cairo text-slate-300">{{ $tx->payment_method }}</td>
                            <td class="py-3.5 px-3 text-slate-300">{{ $tx->sender_number_or_account ?? '—' }}</td>
                            <td class="py-3.5 px-3 font-cairo">
                                @if($tx->receipt_path)
                                    <a href="{{ asset('storage/' . $tx->receipt_path) }}" target="_blank" class="text-cyber-cyan hover:underline text-[11px] font-bold">
                                        عرض الإيصال ↗
                                    </a>
                                @else
                                    <span class="text-slate-500 text-[10px]">بدون ملف</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-3">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold font-cairo
                                    @if($tx->status === 'approved') bg-emerald-500/15 text-emerald-400 border border-emerald-500/30
                                    @elseif($tx->status === 'pending') bg-amber-500/15 text-amber-400 border border-amber-500/30
                                    @else bg-rose-500/15 text-rose-400 border border-rose-500/30
                                    @endif">
                                    {{ $tx->status === 'approved' ? 'معتمد' : ($tx->status === 'pending' ? 'قيد المراجعة' : 'مرفوض') }}
                                </span>
                            </td>
                            <td class="py-3.5 px-3">
                                @if($tx->status === 'pending' && $tx->type === 'deposit')
                                    <div class="flex items-center justify-center gap-2">
                                        <form action="{{ route('admin.wallets.approve', $tx) }}" method="POST">
                                            @csrf
                                            <button type="submit" 
                                                    onclick="return confirm('تأكيد اعتماد إضافة الرصيد إلى محفظة العميل؟')"
                                                    class="px-3 py-1 rounded-lg bg-emerald-500/20 hover:bg-emerald-500 text-emerald-400 hover:text-obsidian-base font-bold text-xs font-cairo transition-all">
                                                اعتماد ✓
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.wallets.reject', $tx) }}" method="POST">
                                            @csrf
                                            <button type="submit" 
                                                    onclick="return confirm('تأكيد رفض هذا الإيداع؟')"
                                                    class="px-2.5 py-1 rounded-lg bg-rose-500/20 hover:bg-rose-500 text-rose-400 hover:text-white font-bold text-xs font-cairo transition-all">
                                                رفض ✕
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <div class="text-center text-slate-500 text-[10px] font-cairo">
                                        {{ $tx->reviewed_by ? 'تمت المراجعة' : '—' }}
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="p-8 text-center text-slate-400 font-readex">
                                لا توجد معاملات مالية مطابقة.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pt-4 border-t border-obsidian-border/60">
            {{ $transactions->links() }}
        </div>
    </div>

</div>
@endsection
