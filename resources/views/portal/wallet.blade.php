@extends('portal.layout')

@section('title', 'المحفظة الرقمية وسجل العمليات | سولي')
@section('page-title', 'المحفظة الرقمية وسجل العمليات')

@section('portal-content')
<div class="space-y-6 sm:space-y-8" x-data="{
    paymentMethod: 'vodafone_cash',
    depositAmount: {{ old('amount', 500) }},
    currentBalance: {{ (float) $wallet->balance }},
    quickAmounts: [500, 1200, 2250, 3000, 5000],
    copy(text) {
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text);
            if (window.Alpine && window.Alpine.store('citadel')) {
                window.Alpine.store('citadel').showToast(`تم نسخ (${text}) إلى الحافظة بنجاح ✓`);
            }
        }
    },
    get projectedBalance() {
        return (this.currentBalance + (Number(this.depositAmount) || 0)).toFixed(2);
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
                <div class="text-xs text-slate-400 font-cairo">الرصيد المالي المتاح للحجز المباشر الفوري:</div>
                <div class="flex items-baseline gap-3">
                    <span class="text-4xl sm:text-5xl lg:text-6xl font-black font-mono text-white tracking-tight">
                        {{ number_format($wallet->balance, 2) }}
                    </span>
                    <span class="text-lg font-mono font-bold text-cyber-cyan">{{ $wallet->currency }}</span>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3">
                <a href="#deposit-section" 
                   class="px-6 py-3 rounded-2xl btn-citadel-primary font-extrabold text-xs sm:text-sm font-cairo shadow-neon-cyan text-center active:scale-95 transition-all">
                    + شحن المحفظة الآن
                </a>

                <a href="{{ route('portal.bookings') }}" 
                   class="px-6 py-3 rounded-2xl btn-citadel-secondary text-white font-bold text-xs sm:text-sm font-cairo text-center transition-all">
                    حجز خدمة بالرصيد
                </a>
            </div>
        </div>
    </div>

    <!-- ===================================================================== -->
    <!-- 2. TOP-UP / DEPOSIT FORM & PAYMENT CHANNELS                           -->
    <!-- ===================================================================== -->
    <div id="deposit-section" class="grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-8 items-start scroll-mt-24">
        
        <!-- Left: Payment Destination Details & Instructions -->
        <div class="lg:col-span-5 space-y-4">
            <div class="p-5 sm:p-6 rounded-3xl glass-obsidian border border-obsidian-border space-y-4">
                <h3 class="text-sm sm:text-base font-bold text-white font-cairo flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-cyber-gold"></span>
                    <span>قنوات التحويل المعتمدة رسمياً</span>
                </h3>

                <!-- Channel: Paymob -->
                <div class="p-3.5 sm:p-4 rounded-2xl bg-obsidian-surface border transition-all cursor-pointer relative overflow-hidden"
                     :class="paymentMethod === 'paymob' ? 'border-cyber-cyan bg-cyber-cyan/10 shadow-neon-cyan' : 'border-obsidian-border hover:border-slate-600'"
                     @click="paymentMethod = 'paymob'">
                    <span class="absolute top-2 left-2 px-2 py-0.5 rounded-full bg-cyber-cyan/20 text-cyber-cyan text-[9px] font-cairo font-bold border border-cyber-cyan/40">
                        فوري ⚡
                    </span>
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-cyber-cyan to-blue-600 text-obsidian-base font-black text-xs flex items-center justify-center font-mono shadow-sm">
                            💳
                        </div>
                        <div>
                            <span class="text-xs font-bold text-white font-cairo block">دفع إلكتروني فوري (Paymob)</span>
                            <span class="text-[10px] text-slate-300 font-cairo">فيزا، ماستركارد، ميزة، المحافظ الذكية</span>
                        </div>
                    </div>
                </div>

                <!-- Channel: Vodafone Cash -->
                <div class="p-3.5 sm:p-4 rounded-2xl bg-obsidian-surface border transition-all cursor-pointer"
                     :class="paymentMethod === 'vodafone_cash' ? 'border-cyber-cyan bg-cyber-cyan/5 shadow-sm' : 'border-obsidian-border'"
                     @click="paymentMethod = 'vodafone_cash'">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-rose-600 text-white font-black text-xs flex items-center justify-center font-mono">
                                VF
                            </div>
                            <div>
                                <span class="text-xs font-bold text-white font-cairo block">فودافون كاش (Vodafone Cash)</span>
                                <span class="text-[11px] text-slate-300 font-mono">01090623326</span>
                            </div>
                        </div>

                        <button type="button" @click.stop="copy('01090623326')" 
                                class="px-2.5 py-1 rounded-lg bg-obsidian-base hover:bg-slate-700 text-cyber-cyan text-[10px] font-mono border border-obsidian-border transition-colors cursor-pointer">
                            نسخ الرقم
                        </button>
                    </div>
                </div>

                <!-- Channel: InstaPay -->
                <div class="p-3.5 sm:p-4 rounded-2xl bg-obsidian-surface border transition-all cursor-pointer"
                     :class="paymentMethod === 'instapay' ? 'border-cyber-cyan bg-cyber-cyan/5 shadow-sm' : 'border-obsidian-border'"
                     @click="paymentMethod = 'instapay'">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-purple-600 text-white font-black text-xs flex items-center justify-center font-mono">
                                IP
                            </div>
                            <div>
                                <span class="text-xs font-bold text-white font-cairo block">إنستاباي اللحظي (InstaPay)</span>
                                <span class="text-[11px] text-slate-300 font-mono">{{ $gateways['instapay'] ?? 'souly@instapay' }}</span>
                            </div>
                        </div>

                        <button type="button" @click.stop="copy('{{ $gateways['instapay'] ?? 'souly@instapay' }}')" 
                                class="px-2.5 py-1 rounded-lg bg-obsidian-base hover:bg-slate-700 text-cyber-cyan text-[10px] font-mono border border-obsidian-border transition-colors cursor-pointer">
                            نسخ المعرف
                        </button>
                    </div>
                </div>

                <!-- Live Dynamic Payment QR Card -->
                <div class="p-4 rounded-2xl bg-gradient-to-br from-obsidian-surface to-obsidian-card border border-cyber-cyan/30 space-y-3">
                    <div class="flex items-center justify-between border-b border-obsidian-border/80 pb-2">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-cyber-cyan animate-pulse"></span>
                            <span class="text-xs font-cairo font-bold text-white">رمز الـ QR المباشر للتحويل:</span>
                        </div>
                        <span class="text-[10px] font-mono text-cyber-cyan font-bold" x-text="paymentMethod.toUpperCase()"></span>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="w-16 h-16 rounded-xl bg-white p-1 flex items-center justify-center shadow-md shrink-0">
                            <div class="w-full h-full bg-slate-950 rounded-lg flex flex-col items-center justify-center text-[8px] text-cyber-cyan font-mono font-bold leading-tight text-center">
                                <span>SOULY</span>
                                <span class="text-cyber-gold text-[7px]" x-text="paymentMethod === 'instapay' ? 'INSTAPAY' : (paymentMethod === 'vodafone_cash' ? 'VF-CASH' : 'ONLINE')"></span>
                                <span>PAY</span>
                            </div>
                        </div>

                        <div class="space-y-1 text-right text-xs font-cairo">
                            <span class="text-slate-200 font-bold block" x-text="paymentMethod === 'instapay' ? 'معرف إنستاباي: souly@instapay' : (paymentMethod === 'vodafone_cash' ? 'فودافون كاش: 01090623326' : 'بوابة Paymob الإلكترونية')"></span>
                            <p class="text-[11px] text-slate-400">امسح الكود عبر تطبيق بنكك أو المحفظة الذكية أو انسخ الرقم بنقرة واحدة.</p>
                        </div>
                    </div>
                </div>

                <div class="p-3.5 rounded-xl bg-obsidian-surface/60 border border-obsidian-border text-[11px] text-slate-400 font-cairo space-y-1">
                    <div class="flex items-center gap-1.5 text-cyber-cyan font-bold">
                        <span>🛡️ إيداع فوري معتمد وموثق</span>
                    </div>
                    <p>يتم تدقيق كل طلب شحن وإضافة الرصيد في محفظتك خلال 5-15 دقيقة طوال أيام الأسبوع.</p>
                </div>
            </div>
        </div>

        <!-- Right: Submit Deposit Form -->
        <div class="lg:col-span-7">
            <div class="p-5 sm:p-8 rounded-3xl glass-obsidian border border-obsidian-border space-y-5">
                <div class="border-b border-obsidian-border/80 pb-3">
                    <h3 class="text-base sm:text-lg font-bold text-white font-cairo">
                        تأكيد وإرسال إشعار الشحن
                    </h3>
                    <p class="text-xs text-slate-400 font-cairo mt-1">
                        بعد تحويل المبلغ عبر أي من القنوات الموضحة، املأ البيانات التالية لربط التحويل بمحفظتك فورياً.
                    </p>
                </div>

                @if ($errors->any())
                    <div class="p-3.5 rounded-xl bg-rose-500/10 border border-rose-500/40 text-xs text-rose-300 font-cairo space-y-1">
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
                    <input type="hidden" name="payment_method" :value="paymentMethod">

                    <!-- Quick Amount Chips -->
                    <div class="space-y-1.5 text-right">
                        <label class="block text-[11px] font-bold text-slate-300 font-cairo">
                            مبالغ شحن سريعة بنقرة واحدة (1-Tap Amount):
                        </label>
                        <div class="flex flex-wrap items-center gap-2">
                            <template x-for="amt in quickAmounts" :key="amt">
                                <button type="button" 
                                        @click="depositAmount = amt" 
                                        class="px-3 py-1.5 rounded-xl border text-xs font-mono font-bold transition-all active:scale-95 cursor-pointer"
                                        :class="depositAmount == amt ? 'bg-cyber-cyan text-obsidian-base font-black border-cyber-cyan shadow-neon-cyan' : 'bg-obsidian-surface text-slate-300 border-obsidian-border hover:border-slate-600'">
                                    <span x-text="`${amt.toLocaleString()} ج.م`"></span>
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- Amount Input -->
                    <div class="space-y-1.5 text-right">
                        <label for="amount" class="block text-xs font-bold text-slate-300 font-cairo">
                            المبلغ المراد شحنه (بالجنيه المصري EGP)
                        </label>
                        <div class="relative">
                            <input type="number" step="0.01" min="50" max="1000000" id="amount" name="amount" x-model.number="depositAmount" required
                                   placeholder="500.00"
                                   class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-4 py-3 text-sm text-white font-mono placeholder-slate-500 focus:outline-none focus:border-cyber-cyan transition-colors">
                            <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-xs font-mono text-slate-400">
                                EGP
                            </div>
                        </div>
                    </div>

                    <!-- Live Projected Balance Preview (UX Super-Feature) -->
                    <div class="p-3.5 rounded-2xl bg-gradient-to-r from-obsidian-surface to-cyber-cyan/10 border border-cyber-cyan/30 flex items-center justify-between text-xs font-cairo">
                        <span class="text-slate-300">الرصيد الإجمالي المتوقع بعد الإيداع:</span>
                        <div class="flex items-baseline gap-1 font-mono">
                            <span class="text-lg font-black text-cyber-cyan" x-text="projectedBalance"></span>
                            <span class="text-[10px] text-slate-400">EGP</span>
                        </div>
                    </div>

                    <!-- Paymob Notice -->
                    <div x-show="paymentMethod === 'paymob'" class="p-3.5 rounded-xl bg-cyber-cyan/10 border border-cyber-cyan/30 text-xs text-cyber-cyan font-cairo space-y-1">
                        <div class="flex items-center gap-2 font-bold">
                            <span class="w-2 h-2 rounded-full bg-cyber-cyan animate-pulse"></span>
                            <span>دفع إلكتروني آمن ولحظي 100%</span>
                        </div>
                        <p class="text-[11px] text-slate-300">
                            سيتم توجيهك لبوابة الدفع لإدخال بيانات بطاقتك وتُشحن المحفظة آلياً وفورياً دون انتظار مراجعة يدوية.
                        </p>
                    </div>

                    <!-- Manual Deposit Inputs -->
                    <div x-show="paymentMethod !== 'paymob'" class="space-y-3">
                        <div class="space-y-1.5 text-right">
                            <label for="sender_number_or_account" class="block text-xs font-bold text-slate-300 font-cairo">
                                رقم المحفظة / الحساب المحول منه (أو كود العملية)
                            </label>
                            <input type="text" id="sender_number_or_account" name="sender_number_or_account" value="{{ old('sender_number_or_account') }}"
                                   placeholder="مثال: 01012345678 أو حساب InstaPay"
                                   :required="paymentMethod !== 'paymob'"
                                   class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-4 py-3 text-xs sm:text-sm text-white placeholder-slate-500 focus:outline-none focus:border-cyber-cyan font-mono">
                        </div>

                        <div class="space-y-1.5 text-right">
                            <label for="receipt" class="block text-xs font-bold text-slate-300 font-cairo">
                                صورة إيصال التحويل (اختياري لتسريع الاعتماد)
                            </label>
                            <input type="file" id="receipt" name="receipt" accept="image/*,.pdf"
                                   class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-4 py-2 text-xs text-slate-300 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-cairo file:btn-citadel-primary file:text-obsidian-base file:font-bold">
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                            :class="paymentMethod === 'paymob' ? 'btn-citadel-primary shadow-neon-cyan' : 'btn-citadel-gold shadow-neon-gold'"
                            class="w-full py-3.5 px-4 rounded-xl text-xs sm:text-sm flex items-center justify-center gap-2 font-cairo font-bold transition-all cursor-pointer active:scale-95">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                        <span x-text="paymentMethod === 'paymob' ? 'الانتقال للدفع الفوري (Paymob Checkout ⚡)' : 'إرسال إشعار الإيداع للاعتماد'"></span>
                    </button>
                </form>
            </div>
        </div>

    </div>

    <!-- ===================================================================== -->
    <!-- 3. COMPREHENSIVE WALLET TRANSACTIONS LEDGER                            -->
    <!-- ===================================================================== -->
    <div class="rounded-3xl glass-obsidian border border-obsidian-border p-5 sm:p-7 lg:p-8 space-y-6">
        <div class="flex items-center justify-between border-b border-obsidian-border/80 pb-4">
            <div>
                <h3 class="text-base sm:text-lg font-bold text-white font-cairo flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-cyber-cyan"></span>
                    <span>سجل القيود المالية والمعاملات (Ledger)</span>
                </h3>
                <p class="text-[11px] sm:text-xs text-slate-400 font-cairo mt-0.5">
                    سجل مشفر وكامل لجميع عمليات الشحن والمشتريات الخاصة بحسابك
                </p>
            </div>
        </div>

        @if($transactions->count() > 0)
            <div class="overflow-x-auto rounded-2xl border border-obsidian-border/80 overscroll-contain">
                <table class="w-full text-right text-xs min-w-[580px]">
                    <thead class="bg-obsidian-surface text-slate-400 border-b border-obsidian-border font-cairo">
                        <tr>
                            <th class="py-3 px-3">الرقم المرجعي</th>
                            <th class="py-3 px-3">النوع</th>
                            <th class="py-3 px-3">المبلغ</th>
                            <th class="py-3 px-3">وسيلة الدفع</th>
                            <th class="py-3 px-3">الحالة</th>
                            <th class="py-3 px-3">التاريخ</th>
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
                                        {{ $tx->status === 'approved' ? 'معتمد' : ($tx->status === 'pending' ? 'قيد المراجعة' : 'مرفوض') }}
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
            <div class="p-8 text-center text-xs text-slate-500 font-cairo">
                لا توجد معاملات مالية مسجلة في محفظتك حتى الآن.
            </div>
        @endif
    </div>

</div>
@endsection