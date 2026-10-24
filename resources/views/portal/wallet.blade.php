@extends('portal.layout')

@section('title', 'المحفظة الرقمية وسجل العمليات | سولي ديدجيتال سيتادل')
@section('page-title', 'المحفظة الرقمية وسجل العمليات')

@section('portal-content')
<div class="space-y-8" x-data="{
    paymentMethod: 'vodafone_cash',
    copyNotice: false,
    copy(text) {
        navigator.clipboard.writeText(text);
        this.copyNotice = true;
        setTimeout(() => this.copyNotice = false, 2500);
    }
}">

    <!-- ===================================================================== -->
    <!-- 1. WALLET SUMMARY & BALANCE HEADER                                    -->
    <!-- ===================================================================== -->
    <div class="rounded-3xl p-6 sm:p-8 lg:p-10 glass-obsidian border border-cyber-cyan/30 shadow-neon-cyan relative overflow-hidden">
        <div class="absolute -top-24 -left-24 w-80 h-80 rounded-full bg-cyber-cyan/15 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -right-24 w-80 h-80 rounded-full bg-cyber-gold/10 blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-cyber-cyan animate-pulse"></span>
                    <span class="text-xs font-mono text-cyber-cyan font-bold uppercase tracking-wider">SOULY VAULT · SECURE BALANCE</span>
                </div>
                <div class="text-xs text-slate-400 font-readex">الرصيد المالي المتاح للحجز المباشر الفوري:</div>
                <div class="flex items-baseline gap-3">
                    <span class="text-4xl sm:text-5xl lg:text-6xl font-black font-mono text-white tracking-tight">
                        {{ number_format($wallet->balance, 2) }}
                    </span>
                    <span class="text-lg font-mono font-bold text-cyber-cyan">{{ $wallet->currency }}</span>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3">
                <a href="#deposit-section" 
                   class="px-6 py-3 rounded-2xl bg-gradient-to-r from-cyber-cyan to-cyber-gold text-obsidian-base font-extrabold text-xs sm:text-sm font-cairo shadow-neon-cyan text-center hover:scale-105 active:scale-95 transition-all">
                    + شحن المحفظة الآن
                </a>

                <a href="{{ route('portal.bookings') }}" 
                   class="px-6 py-3 rounded-2xl glass-obsidian border border-obsidian-border hover:border-slate-600 text-white font-bold text-xs sm:text-sm font-cairo text-center transition-all">
                    حجز خدمة بالرصيد
                </a>
            </div>
        </div>
    </div>

    <!-- Copy Feedback Toast -->
    <div x-show="copyNotice" x-transition 
         class="fixed bottom-6 left-6 z-50 px-4 py-2.5 rounded-xl bg-cyber-cyan text-obsidian-base font-bold text-xs font-cairo shadow-neon-cyan flex items-center gap-2"
         style="display: none;">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
        <span>تم نسخ البيانات إلى الحافظة بنجاح!</span>
    </div>

    <!-- ===================================================================== -->
    <!-- 2. TOP-UP / DEPOSIT FORM & PAYMENT CHANNELS                           -->
    <!-- ===================================================================== -->
    <div id="deposit-section" class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start scroll-mt-24">
        
        <!-- Left: Payment Destination Details & Instructions -->
        <div class="lg:col-span-5 space-y-4">
            <div class="p-6 rounded-3xl glass-obsidian border border-obsidian-border space-y-5">
                <h3 class="text-base font-bold text-white font-cairo flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-cyber-gold"></span>
                    <span>قنوات وحسابات التحويل المعتمدة</span>
                </h3>

                <!-- Channel 1: Vodafone Cash -->
                <div class="p-4 rounded-2xl bg-obsidian-surface border transition-all cursor-pointer"
                     :class="paymentMethod === 'vodafone_cash' ? 'border-cyber-cyan bg-cyber-cyan/5 shadow-sm' : 'border-obsidian-border'"
                     @click="paymentMethod = 'vodafone_cash'">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-rose-600 text-white font-black text-xs flex items-center justify-center font-mono">
                                VF
                            </div>
                            <div>
                                <span class="text-xs font-bold text-white font-cairo block">فودافون كاش (Vodafone Cash)</span>
                                <span class="text-[10px] text-slate-400 font-mono">01090623326</span>
                            </div>
                        </div>

                        <button type="button" @click.stop="copy('01090623326')" 
                                class="px-2.5 py-1 rounded-lg bg-obsidian-base hover:bg-slate-700 text-slate-300 text-[10px] font-mono border border-obsidian-border transition-colors">
                            نسخ الرقم
                        </button>
                    </div>
                </div>

                <!-- Channel 2: InstaPay -->
                <div class="p-4 rounded-2xl bg-obsidian-surface border transition-all cursor-pointer"
                     :class="paymentMethod === 'instapay' ? 'border-cyber-cyan bg-cyber-cyan/5 shadow-sm' : 'border-obsidian-border'"
                     @click="paymentMethod = 'instapay'">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-purple-600 text-white font-black text-xs flex items-center justify-center font-mono">
                                IP
                            </div>
                            <div>
                                <span class="text-xs font-bold text-white font-cairo block">إنستاباي اللحظي (InstaPay)</span>
                                <span class="text-[10px] text-slate-400 font-mono">souly@instapay</span>
                            </div>
                        </div>

                        <button type="button" @click.stop="copy('souly@instapay')" 
                                class="px-2.5 py-1 rounded-lg bg-obsidian-base hover:bg-slate-700 text-slate-300 text-[10px] font-mono border border-obsidian-border transition-colors">
                            نسخ المعرف
                        </button>
                    </div>
                </div>

                <!-- Channel 3: Bank Transfer -->
                <div class="p-4 rounded-2xl bg-obsidian-surface border transition-all cursor-pointer"
                     :class="paymentMethod === 'bank_transfer' ? 'border-cyber-cyan bg-cyber-cyan/5 shadow-sm' : 'border-obsidian-border'"
                     @click="paymentMethod = 'bank_transfer'">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-emerald-600 text-white font-black text-xs flex items-center justify-center font-mono">
                                NBE
                            </div>
                            <div>
                                <span class="text-xs font-bold text-white font-cairo block">التحويل البنكي (NBE / CIB)</span>
                                <span class="text-[10px] text-slate-400 font-mono">IBAN: EG410002000...</span>
                            </div>
                        </div>

                        <button type="button" @click.stop="copy('EG41000200000001090623326')" 
                                class="px-2.5 py-1 rounded-lg bg-obsidian-base hover:bg-slate-700 text-slate-300 text-[10px] font-mono border border-obsidian-border transition-colors">
                            نسخ الآيبان
                        </button>
                    </div>
                </div>

                <!-- Security Assurance -->
                <div class="p-3.5 rounded-xl bg-obsidian-surface/60 border border-obsidian-border text-[11px] text-slate-400 font-readex space-y-1">
                    <div class="flex items-center gap-1.5 text-cyber-cyan font-bold">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <span>إيداع رسمي معتمد وموثق</span>
                    </div>
                    <p>يتم تدقيق كل طلب شحن وإضافة الرصيد في محفظتك خلال 5-15 دقيقة طوال أيام الأسبوع.</p>
                </div>
            </div>
        </div>

        <!-- Right: Submit Deposit Form -->
        <div class="lg:col-span-7">
            <div class="p-6 sm:p-8 rounded-3xl glass-obsidian border border-obsidian-border space-y-5">
                <div class="border-b border-obsidian-border/80 pb-3">
                    <h3 class="text-base sm:text-lg font-bold text-white font-cairo">
                        تأكيد وإرسال إشعار الشحن
                    </h3>
                    <p class="text-xs text-slate-400 font-readex mt-1">
                        بعد تحويل المبلغ عبر أي من القنوات الموضحة، املأ البيانات التالية لربط التحويل بمحفظتك فورياً.
                    </p>
                </div>

                @if ($errors->any())
                    <div class="p-3.5 rounded-xl bg-rose-500/10 border border-rose-500/40 text-xs text-rose-300 font-readex space-y-1">
                        @foreach ($errors->all() as $error)
                            <div class="flex items-center gap-1.5">
                                <span class="text-rose-400 font-bold">⚠</span>
                                <span>{{ $error }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('portal.wallet.deposit') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <!-- Hidden or Synced Payment Method -->
                    <input type="hidden" name="payment_method" :value="paymentMethod">

                    <!-- Amount -->
                    <div class="space-y-1.5">
                        <label for="amount" class="block text-xs font-bold text-slate-300 font-readex">
                            المبلغ المحول (بالجنيه المصري EGP)
                        </label>
                        <div class="relative">
                            <input type="number" step="0.01" min="50" max="1000000" id="amount" name="amount" value="{{ old('amount', 500) }}" required
                                   placeholder="500.00"
                                   class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-4 py-3 text-sm text-white font-mono placeholder-slate-500 focus:outline-none focus:border-cyber-cyan transition-colors">
                            <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-xs font-mono text-slate-400">
                                EGP
                            </div>
                        </div>
                        <span class="text-[10px] text-slate-500 font-mono">* الحد الأدنى للإيداع هو 50 ج.م</span>
                    </div>

                    <!-- Sender Number or Account -->
                    <div class="space-y-1.5">
                        <label for="sender_number_or_account" class="block text-xs font-bold text-slate-300 font-readex">
                            رقم المحفظة / رقم الحساب المحول منه (أو كود التحويل)
                        </label>
                        <input type="text" id="sender_number_or_account" name="sender_number_or_account" value="{{ old('sender_number_or_account') }}" required
                               placeholder="مثال: 01012345678 أو حساب InstaPay الخاص بك"
                               class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-4 py-3 text-xs sm:text-sm text-white placeholder-slate-500 focus:outline-none focus:border-cyber-cyan transition-colors font-mono">
                    </div>

                    <!-- Receipt Upload -->
                    <div class="space-y-1.5">
                        <label for="receipt" class="block text-xs font-bold text-slate-300 font-readex">
                            صورة إيصال التحويل أو لقطة الشاشة (اختياري لتسريع التأكيد)
                        </label>
                        <input type="file" id="receipt" name="receipt" accept="image/*,.pdf"
                               class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-4 py-2 text-xs text-slate-300 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-cairo file:bg-cyber-cyan file:text-obsidian-base file:font-bold hover:file:opacity-90">
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                            class="w-full py-3.5 px-4 rounded-xl bg-gradient-to-r from-cyber-cyan via-cyber-gold to-cyber-purple text-obsidian-base font-extrabold text-sm font-cairo shadow-neon-cyan hover:scale-[1.01] active:scale-95 transition-all flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                        <span>إرسال إشعار الإيداع لاعتماده</span>
                    </button>
                </form>
            </div>
        </div>

    </div>

    <!-- ===================================================================== -->
    <!-- 3. COMPREHENSIVE WALLET TRANSACTIONS LEDGER                            -->
    <!-- ===================================================================== -->
    <div class="rounded-3xl glass-obsidian border border-obsidian-border p-6 sm:p-8 space-y-6">
        <div class="flex items-center justify-between border-b border-obsidian-border/80 pb-4">
            <div>
                <h3 class="text-lg font-bold text-white font-cairo flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-cyber-cyan"></span>
                    <span>سجل القيود المالية والمعاملات (Ledger)</span>
                </h3>
                <p class="text-xs text-slate-400 font-readex mt-0.5">
                    سجل مشفر وكامل لجميع عمليات الشحن، المشتريات، والاستردادات الخاصة بحسابك
                </p>
            </div>
        </div>

        @if($transactions->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs">
                    <thead>
                        <tr class="border-b border-obsidian-border/80 text-slate-400 font-readex">
                            <th class="py-3 px-3">الرقم المرجعي</th>
                            <th class="py-3 px-3">النوع</th>
                            <th class="py-3 px-3">المبلغ</th>
                            <th class="py-3 px-3">وسيلة الدفع</th>
                            <th class="py-3 px-3">الحالة</th>
                            <th class="py-3 px-3">التاريخ والوقت</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-obsidian-border/50 font-mono">
                        @foreach($transactions as $tx)
                            <tr class="hover:bg-obsidian-surface/40 transition-colors">
                                <td class="py-3.5 px-3 text-cyber-cyan font-bold">
                                    {{ $tx->reference_code }}
                                </td>
                                <td class="py-3.5 px-3 font-cairo">
                                    @if($tx->type === 'deposit')
                                        <span class="text-emerald-400">إيداع وشحن رصيد</span>
                                    @elseif($tx->type === 'purchase')
                                        <span class="text-slate-200">حجز وشراء خدمة</span>
                                    @elseif($tx->type === 'refund')
                                        <span class="text-cyan-400">استرداد مالي</span>
                                    @else
                                        <span class="text-slate-400">{{ $tx->type }}</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-3 font-bold {{ $tx->type === 'deposit' ? 'text-emerald-400' : 'text-rose-400' }}">
                                    {{ $tx->type === 'deposit' ? '+' : '-' }}{{ number_format($tx->amount, 2) }} EGP
                                </td>
                                <td class="py-3.5 px-3 text-slate-300 font-cairo">
                                    {{ $tx->payment_method }}
                                </td>
                                <td class="py-3.5 px-3">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold font-cairo
                                        @if($tx->status === 'approved') bg-emerald-500/15 text-emerald-400 border border-emerald-500/30
                                        @elseif($tx->status === 'pending') bg-amber-500/15 text-amber-400 border border-amber-500/30
                                        @else bg-rose-500/15 text-rose-400 border border-rose-500/30
                                        @endif">
                                        {{ $tx->status === 'approved' ? 'معتمد ومضاف' : ($tx->status === 'pending' ? 'قيد المراجعة' : 'مرفوض') }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-3 text-slate-400 text-[11px]">
                                    {{ $tx->created_at->format('Y-m-d H:i') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="pt-4 border-t border-obsidian-border/60">
                {{ $transactions->links() }}
            </div>
        @else
            <div class="p-8 text-center text-xs text-slate-500 font-readex">
                لا توجد معاملات مالية مسجلة في محفظتك حتى الآن.
            </div>
        @endif
    </div>

</div>
@endsection
