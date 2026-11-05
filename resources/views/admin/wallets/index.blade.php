@extends('admin.layout')

@section('title', 'إدارة شحن المحفظة والمعاملات المالية | سولي')
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
            <p class="text-xs text-slate-400 font-cairo">
                مراجعة إيصالات فودافون كاش وإنستاباي والتحويلات البنكية واعتماد رصيد محافظ العملاء فورياً.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <span class="px-4 py-2 rounded-xl bg-amber-500/15 border border-amber-500/40 text-amber-400 text-xs font-bold font-cairo flex items-center gap-1.5">
                <span class="font-mono text-sm">{{ $pendingCount }}</span>
                <span>إيداع معلق</span>
            </span>
            <span class="px-4 py-2 rounded-xl bg-emerald-500/15 border border-emerald-500/40 text-emerald-400 text-xs font-bold font-cairo flex items-center gap-1.5">
                <span class="font-mono text-sm">{{ number_format($approvedSum, 2) }}</span>
                <span>EGP معتمد</span>
            </span>
        </div>
    </div>

    <!-- Live Payment Gateways Quick Configuration Card -->
    <div class="glass-obsidian rounded-2xl border border-obsidian-border p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs font-cairo"
         x-data="{ showGatewaysModal: false }">
        <div class="flex items-center gap-2">
            <span class="text-base">💳</span>
            <div>
                <span class="text-white font-bold block">بوابات الدفع اللحظية المعتمدة (تظهر للعملاء في شحن المحفظة):</span>
                <span class="text-slate-400 font-mono text-[11px]">
                    فودافون كاش: <span class="text-cyber-cyan">{{ $gateways['vodafone_cash'] ?? '01026042784' }}</span> | 
                    إنستاباي: <span class="text-cyber-gold">{{ $gateways['instapay'] ?? 'souly@instapay' }}</span>
                </span>
            </div>
        </div>

        <button type="button" 
                @click="showGatewaysModal = true"
                class="px-3 py-1.5 rounded-xl bg-obsidian-surface hover:bg-slate-800 text-cyber-gold border border-cyber-gold/40 text-xs font-bold font-cairo shrink-0">
            ⚙️ تعديل أرقام وحسابات الدفع
        </button>

        <!-- Edit Gateways Modal -->
        <div x-show="showGatewaysModal" 
             class="fixed inset-0 z-50 bg-black/80 backdrop-blur-md flex items-center justify-center p-4"
             style="display: none;"
             @click.away="showGatewaysModal = false">
            <div class="glass-obsidian rounded-3xl border border-cyber-gold/40 p-6 max-w-md w-full space-y-4">
                <div class="flex items-center justify-between border-b border-obsidian-border pb-3">
                    <h3 class="text-sm font-bold text-white font-cairo">تحديث بوابات الدفع اللحظية</h3>
                    <button type="button" @click="showGatewaysModal = false" class="text-slate-400 hover:text-white text-lg font-bold">×</button>
                </div>
                <form action="{{ route('admin.wallets.gateways') }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">رقم فودافون كاش (Vodafone Cash)</label>
                        <input type="text" name="payment_vodafone_cash" value="{{ $gateways['vodafone_cash'] ?? '01026042784' }}" class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-3 py-2 text-xs text-white font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">عنوان إنستاباي (InstaPay Handle)</label>
                        <input type="text" name="payment_instapay" value="{{ $gateways['instapay'] ?? 'souly@instapay' }}" class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-3 py-2 text-xs text-white font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">بيانات الحساب البنكي</label>
                        <textarea name="payment_bank_account" rows="2" class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-3 py-2 text-xs text-white font-cairo">{{ $gateways['bank_account'] ?? 'CIB Bank' }}</textarea>
                    </div>
                    <button type="submit" class="w-full py-2.5 rounded-xl btn-citadel-primary text-xs font-bold font-cairo">
                        حفظ التعديلات اللحظية
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="flex items-center gap-2 overflow-x-auto pb-2 text-xs font-cairo font-bold">
        <a href="{{ route('admin.wallets.index') }}" 
           class="px-4 py-2 rounded-xl transition-all shrink-0
           {{ !request()->filled('status') ? 'bg-cyber-cyan text-obsidian-base font-black shadow-sm' : 'bg-obsidian-surface text-slate-400 border border-obsidian-border hover:text-white' }}">
            كافة المعاملات ({{ $transactions->total() }})
        </a>
        <a href="{{ route('admin.wallets.index') }}?status=pending" 
           class="px-4 py-2 rounded-xl transition-all shrink-0
           {{ request()->query('status') === 'pending' ? 'bg-amber-500 text-obsidian-base font-black shadow-sm' : 'bg-obsidian-surface text-amber-400 border border-obsidian-border' }}">
            بانتظار الاعتماد ({{ $pendingCount }})
        </a>
        <a href="{{ route('admin.wallets.index') }}?status=approved" 
           class="px-4 py-2 rounded-xl transition-all shrink-0
           {{ request()->query('status') === 'approved' ? 'bg-emerald-500 text-obsidian-base font-black shadow-sm' : 'bg-obsidian-surface text-slate-400 border border-obsidian-border hover:text-white' }}">
            معتمدة
        </a>
        <a href="{{ route('admin.wallets.index') }}?status=rejected" 
           class="px-4 py-2 rounded-xl transition-all shrink-0
           {{ request()->query('status') === 'rejected' ? 'bg-rose-500 text-white font-black shadow-sm' : 'bg-obsidian-surface text-slate-400 border border-obsidian-border hover:text-white' }}">
            مرفوضة
        </a>
    </div>

    <!-- Transactions Table & Tactical Mobile Action Cards -->
    <div class="rounded-3xl glass-obsidian border border-obsidian-border p-4 sm:p-8 space-y-4">
        
        <!-- Desktop Table (Hidden on Mobile) -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead>
                    <tr class="border-b border-obsidian-border/80 text-slate-400 font-cairo">
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
                <tbody class="divide-y divide-obsidian-border/50">
                    @forelse($transactions as $tx)
                        <tr class="hover:bg-obsidian-surface/60 transition-colors">
                            <td class="py-3.5 px-3 text-cyber-cyan font-bold font-mono">{{ $tx->reference_code }}</td>
                            <td class="py-3.5 px-3 font-cairo">
                                <div class="font-bold text-white">{{ $tx->user->name }}</div>
                                <div class="text-[10px] text-slate-400 font-mono">{{ $tx->user->email }}</div>
                            </td>
                            <td class="py-3.5 px-3 font-cairo">
                                @if($tx->type === 'deposit')
                                    <span class="text-emerald-400 font-bold">إيداع وشحن</span>
                                @elseif($tx->type === 'purchase')
                                    <span class="text-slate-200">شراء خدمة</span>
                                @else
                                    <span>{{ $tx->type }}</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-3 font-bold text-sm font-mono {{ $tx->type === 'deposit' ? 'text-emerald-400' : 'text-slate-200' }}">
                                {{ $tx->type === 'deposit' ? '+' : '-' }}{{ number_format($tx->amount, 2) }} EGP
                            </td>
                            <td class="py-3.5 px-3 font-cairo text-slate-300">{{ $tx->payment_method }}</td>
                            <td class="py-3.5 px-3 text-slate-300 font-mono">{{ $tx->sender_number_or_account ?? '—' }}</td>
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
                            <td colspan="9" class="p-8 text-center text-slate-400 font-cairo">
                                لا توجد معاملات مالية مطابقة.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Tactical Action Cards (Visible on < 768px only) -->
        <div class="md:hidden space-y-3">
            @forelse($transactions as $tx)
                <div class="p-4 rounded-2xl bg-obsidian-surface border border-obsidian-border space-y-3 shadow-md">
                    <div class="flex items-center justify-between border-b border-obsidian-border/70 pb-2">
                        <span class="font-mono font-black text-cyber-cyan text-xs">{{ $tx->reference_code }}</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold font-cairo
                            @if($tx->status === 'approved') bg-emerald-500/15 text-emerald-400 border border-emerald-500/30
                            @elseif($tx->status === 'pending') bg-amber-500/15 text-amber-400 border border-amber-500/30
                            @else bg-rose-500/15 text-rose-400 border border-rose-500/30
                            @endif">
                            {{ $tx->status === 'approved' ? 'معتمد' : ($tx->status === 'pending' ? 'قيد المراجعة' : 'مرفوض') }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold text-white font-cairo block">{{ $tx->user->name }}</span>
                            <span class="text-[10px] text-slate-400 font-mono">{{ $tx->user->phone ?? $tx->user->email }}</span>
                        </div>
                        <div class="text-left font-mono">
                            <span class="text-base font-black {{ $tx->type === 'deposit' ? 'text-emerald-400' : 'text-slate-200' }}">
                                {{ $tx->type === 'deposit' ? '+' : '-' }}{{ number_format($tx->amount, 2) }} EGP
                            </span>
                            <span class="text-[9px] font-cairo text-slate-400 block">{{ $tx->payment_method }}</span>
                        </div>
                    </div>

                    <div class="p-2.5 rounded-xl bg-obsidian-card border border-obsidian-border flex items-center justify-between text-[11px] font-cairo">
                        <div>
                            <span class="text-slate-400 text-[10px] block">رقم المحول:</span>
                            <span class="text-white font-mono">{{ $tx->sender_number_or_account ?? '—' }}</span>
                        </div>
                        <div>
                            @if($tx->receipt_path)
                                <a href="{{ asset('storage/' . $tx->receipt_path) }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-cyber-cyan/15 text-cyber-cyan border border-cyber-cyan/30 text-[10px] font-bold">
                                    عرض الإيصال ↗
                                </a>
                            @else
                                <span class="text-slate-500 text-[10px]">بدون إيصال</span>
                            @endif
                        </div>
                    </div>

                    @if($tx->status === 'pending' && $tx->type === 'deposit')
                        <div class="grid grid-cols-2 gap-2 pt-1">
                            <form action="{{ route('admin.wallets.approve', $tx) }}" method="POST">
                                @csrf
                                <button type="submit" 
                                        onclick="return confirm('تأكيد اعتماد إضافة الرصيد إلى محفظة العميل؟')"
                                        class="w-full py-2.5 rounded-xl bg-emerald-500 text-obsidian-base font-black text-xs font-cairo shadow-sm active:scale-95 text-center">
                                    اعتماد الرصيد ✓
                                </button>
                            </form>

                            <form action="{{ route('admin.wallets.reject', $tx) }}" method="POST">
                                @csrf
                                <button type="submit" 
                                        onclick="return confirm('تأكيد رفض هذا الإيداع؟')"
                                        class="w-full py-2.5 rounded-xl bg-rose-500/20 text-rose-400 border border-rose-500/40 font-bold text-xs font-cairo active:scale-95 text-center">
                                    رفض ✕
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            @empty
                <div class="p-6 text-center text-slate-400 font-cairo text-xs bg-obsidian-surface rounded-2xl border border-obsidian-border">
                    لا توجد معاملات مالية مطابقة.
                </div>
            @endforelse
        </div>

        <div class="pt-4 border-t border-obsidian-border/60">
            {{ $transactions->links() }}
        </div>
    </div>

</div>
@endsection
