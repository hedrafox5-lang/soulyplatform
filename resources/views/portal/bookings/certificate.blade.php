@extends('portal.layout')

@section('title', 'فاتورة وشهادة ضمان ' . $booking->booking_number . ' | سولي')
@section('page-title', 'فاتورة وشهادة ضمان هندسي · ' . $booking->booking_number)

@section('portal-content')
<div class="space-y-6 max-w-4xl mx-auto">

    <!-- Top Action Bar (Hidden on Print) -->
    <div class="flex flex-wrap items-center justify-between gap-3 p-4 rounded-2xl glass-obsidian border border-obsidian-border print:hidden shadow-sm">
        <a href="{{ route('portal.bookings') }}" 
           class="px-4 py-2 rounded-xl btn-citadel-secondary text-xs flex items-center gap-2">
            <span>← العودة لقائمة الحجوزات</span>
        </a>

        <div class="flex items-center gap-2 flex-wrap">
            <!-- 1-Tap Mobile Native Share Button -->
            <button onclick="shareCertificate()"
                    type="button"
                    class="px-3.5 py-2 rounded-xl bg-obsidian-surface hover:bg-slate-800 border border-cyber-cyan/40 text-cyber-cyan text-xs font-cairo font-bold flex items-center gap-1.5 active:scale-95 transition-all cursor-pointer">
                <span>📲 مشاركة</span>
            </button>

            <!-- Download PDF Button -->
            <button onclick="downloadPdf()" 
                    id="btn-download-pdf"
                    type="button" 
                    class="px-4 sm:px-5 py-2 sm:py-2.5 rounded-xl btn-citadel-primary text-xs font-cairo font-bold flex items-center gap-2 shadow-neon-cyan active:scale-95 transition-all cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                <span id="btn-download-text">تنزيل PDF</span>
            </button>

            <!-- Print Button -->
            <button onclick="window.print()" 
                    type="button" 
                    class="px-3.5 sm:px-4 py-2 sm:py-2.5 rounded-xl btn-citadel-gold text-xs font-cairo font-bold flex items-center gap-2 shadow-neon-gold active:scale-95 transition-all cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                <span>طباعة (A4)</span>
            </button>
        </div>
    </div>

    <!-- The Printable Luxury Certificate & Invoice Card -->
    <div id="printable-certificate" 
         class="rounded-3xl glass-obsidian border-2 border-cyber-cyan/40 p-5 sm:p-10 lg:p-12 space-y-6 sm:space-y-8 relative overflow-hidden shadow-[0_12px_50px_rgba(0,0,0,0.8)] print:bg-white print:text-black print:border-slate-300 print:shadow-none print:p-6 print:rounded-none">
        
        <div class="absolute -top-32 -left-32 w-80 h-80 rounded-full bg-cyber-cyan/10 blur-3xl pointer-events-none print:hidden"></div>
        <div class="absolute -bottom-32 -right-32 w-80 h-80 rounded-full bg-cyber-gold/10 blur-3xl pointer-events-none print:hidden"></div>

        <!-- 1. HEADER: BRAND EMBLEM & INVOICE METADATA -->
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-start justify-between gap-5 border-b border-obsidian-border/80 print:border-slate-300 pb-5 sm:pb-6">
            
            <div class="space-y-2.5">
                <div class="flex items-center gap-3">
                    <x-souly-logo size="md" :showText="true" />
                </div>
                <div class="text-xs text-slate-400 font-cairo print:text-slate-600 space-y-0.5">
                    <p class="font-bold text-slate-200 print:text-slate-900">سولي لأنظمة الأعمال والذكاء الاصطناعي</p>
                    <p>منظومة الخدمات السيادية والحلول المؤسسية المعتمدة</p>
                    <p class="font-mono text-[11px] text-cyber-cyan print:text-slate-700">support@soulyeg.online · soulyeg.online</p>
                </div>
            </div>

            <div class="space-y-1.5 text-right">
                <span class="inline-block px-3 py-1 rounded-full text-[10px] font-mono font-bold uppercase tracking-wider {{ $booking->payment_status === 'paid' ? 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30 print:border-emerald-600 print:text-emerald-700' : 'bg-cyber-gold/15 text-cyber-gold border border-cyber-gold/30 print:border-amber-600 print:text-amber-700' }}">
                    {{ $booking->payment_status === 'paid' ? 'فاتورة مسددة بالكامل ✓' : 'بانتظار تأكيد السداد ⏳' }}
                </span>

                <h1 class="text-lg sm:text-2xl font-black font-cairo text-white print:text-slate-900">
                    فاتورة رسمية وشهادة ضمان هندسي
                </h1>

                <div class="text-xs font-mono text-slate-400 print:text-slate-700 space-y-1">
                    <div>رقم الحجز: <strong class="text-cyber-cyan print:text-slate-900 font-bold">{{ $booking->booking_number }}</strong></div>
                    <div>تاريخ الإصدار: <span>{{ $booking->created_at->format('Y-m-d · H:i') }}</span></div>
                    <div>طريقة الدفع: 
                        <strong class="font-cairo text-slate-200 print:text-slate-800">
                            @if($booking->payment_method === 'wallet')
                                محفظة سولي الرقمية ⚡
                            @elseif($booking->payment_method === 'vodafone_cash')
                                فودافون كاش
                            @elseif($booking->payment_method === 'instapay')
                                إنستاباي
                            @elseif($booking->payment_method === 'on_site')
                                دفع ميداني عند التنفيذ
                            @else
                                تحويل بنكي مباشر
                            @endif
                        </strong>
                    </div>
                </div>
            </div>

        </div>

        <!-- 2. CLIENT & DOSSIER DETAILS -->
        <div class="relative z-10 grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 sm:p-5 rounded-2xl bg-obsidian-surface/90 print:bg-slate-50 border border-obsidian-border print:border-slate-300">
            <div class="space-y-1 text-right">
                <span class="text-[10px] font-cairo font-bold text-slate-500 uppercase">بيانات العميل / المؤسسة:</span>
                <h4 class="text-sm sm:text-base font-extrabold text-white print:text-slate-900 font-cairo">{{ $booking->user->name }}</h4>
                <p class="text-xs font-mono text-slate-300 print:text-slate-700">{{ $booking->user->phone ?? 'الهاتف مسجل بالنظام' }}</p>
                <p class="text-xs font-mono text-slate-400 print:text-slate-600">{{ $booking->user->email }}</p>
            </div>

            <div class="space-y-1 text-right sm:text-left sm:border-r sm:border-obsidian-border sm:pr-4 print:border-slate-300">
                <span class="text-[10px] font-cairo font-bold text-slate-500 uppercase">القطاع والمنظومة المستهدفة:</span>
                <h4 class="text-sm sm:text-base font-extrabold text-cyber-cyan print:text-cyan-800 font-cairo">{{ $booking->service->name }}</h4>
                <p class="text-xs font-cairo text-slate-300 print:text-slate-700">الباقة: <strong>{{ $booking->package?->name ?? 'حجز خدمة هندسية' }}</strong></p>
                <p class="text-[11px] font-cairo text-slate-400 print:text-slate-600">موعد التنفيذ: {{ $booking->scheduled_at ? $booking->scheduled_at->format('Y-m-d') : 'فوري ومجدول' }}</p>
            </div>
        </div>

        <!-- 3. SERVICES SPECIFICATIONS TABLE -->
        <div class="relative z-10 space-y-3">
            <h3 class="text-xs font-bold text-slate-400 font-cairo uppercase tracking-wider text-right">
                تفاصيل البند الهندسي والمواصفات:
            </h3>

            <div class="overflow-x-auto rounded-xl border border-obsidian-border/80 print:border-slate-300">
                <table class="w-full text-right text-xs font-cairo min-w-[500px]">
                    <thead class="bg-obsidian-surface print:bg-slate-100 border-b border-obsidian-border print:border-slate-300 text-slate-400 print:text-slate-600">
                        <tr>
                            <th class="py-2.5 px-3">الخدمة / الباقة المعتمدة</th>
                            <th class="py-2.5 px-3 text-center">الساعات / الوحدات</th>
                            <th class="py-2.5 px-3 text-center">سعر الوحدة</th>
                            <th class="py-2.5 px-3 text-left">الإجمالي</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-obsidian-border/60 print:divide-slate-200">
                        <tr>
                            <td class="py-3 px-3 space-y-1">
                                <span class="font-bold text-white print:text-slate-900 block text-xs sm:text-sm">{{ $booking->package?->name ?? $booking->service->name }}</span>
                                <span class="text-[11px] text-slate-400 print:text-slate-600 block">{{ $booking->package?->subtitle ?? $booking->service->tagline }}</span>
                            </td>
                            <td class="py-3 px-3 text-center font-mono font-bold text-slate-200 print:text-slate-800">
                                {{ $booking->quantity_or_hours }} {{ $booking->package?->unit_label ?? 'وحدة' }}
                            </td>
                            <td class="py-3 px-3 text-center font-mono text-slate-300 print:text-slate-800">
                                @if($booking->package)
                                    {{ number_format($booking->package->price, 2) }} EGP
                                @else
                                    {{ number_format($booking->total_amount / max(1, $booking->quantity_or_hours), 2) }} EGP
                                @endif
                            </td>
                            <td class="py-3 px-3 text-left font-mono font-extrabold text-cyber-cyan print:text-slate-900 text-sm sm:text-base">
                                {{ number_format($booking->total_amount, 2) }} EGP
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="border-t-2 border-obsidian-border print:border-slate-400 bg-obsidian-surface/60 print:bg-slate-50">
                            <td colspan="3" class="py-3 px-3 font-bold text-white print:text-slate-900 text-xs sm:text-sm">
                                إجمالي الفاتورة الصافي المستحق:
                            </td>
                            <td class="py-3 px-3 text-left font-mono font-black text-lg sm:text-xl text-transparent bg-clip-text bg-gradient-to-r from-cyber-cyan to-cyber-gold print:text-slate-900">
                                {{ number_format($booking->total_amount, 2) }} EGP
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- 4. OFFICIAL ENGINEERING WARRANTY CERTIFICATE PROTOCOL -->
        <div class="relative z-10 p-5 sm:p-6 rounded-2xl bg-gradient-to-br from-[#0c1020] via-obsidian-base to-[#121828] print:bg-slate-50 border-2 border-cyber-gold/50 print:border-amber-600 shadow-lg space-y-4 text-center sm:text-right">
            
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-cyber-gold/30 print:border-amber-400 pb-3">
                <div class="flex items-center justify-center sm:justify-start gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-cyber-gold/20 text-cyber-gold flex items-center justify-center font-bold text-base shrink-0">
                        🛡️
                    </div>
                    <div>
                        <h3 class="text-sm sm:text-base font-extrabold text-white print:text-slate-900 font-cairo">
                            شهادة الضمان الهندسي المعتمدة (Engineering Warranty Protocol)
                        </h3>
                        <span class="text-[10px] text-cyber-gold font-mono block">SOULY-WARRANTY-SECURE-STAMP</span>
                    </div>
                </div>

                @php
                    $warrantyDays = $booking->package?->warranty_days ?? 30;
                    $expiryDate = $booking->created_at->addDays($warrantyDays);
                @endphp
                <span class="px-3 py-1 rounded-full text-xs font-cairo font-bold bg-cyber-gold/15 text-cyber-gold border border-cyber-gold/30 print:border-amber-600 print:text-amber-800 self-center sm:self-auto shrink-0">
                    ضمان ساري لمدة {{ $warrantyDays }} يوماً
                </span>
            </div>

            <p class="text-xs text-slate-300 print:text-slate-700 font-cairo leading-relaxed">
                تشهد الإدارة الهندسية بمؤسسة <strong>سولي</strong> بأن هذا الحجز والخدمات المرفقة خاضعة لبروتوكول التدريع والجودة الهندسية المعتمد. يلتزم فريقنا بالدعم الفني الفوري، استقرار الأداء، وسد أي ثغرة أو معالجة أي خلل تشغيلي طوال فترة الضمان حتى تاريخ:
                <strong class="font-mono text-cyber-cyan print:text-slate-900">{{ $expiryDate->format('Y-m-d') }}</strong>.
            </p>

            <!-- Verification Badges, Signatures & QR Code -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2 items-center border-t border-cyber-gold/20 print:border-amber-300">
                <div class="flex items-center justify-center sm:justify-start gap-2.5">
                    <div class="w-12 h-12 rounded-xl bg-white p-1 flex items-center justify-center shadow-md shrink-0">
                        <div class="w-full h-full bg-slate-950 rounded-lg flex flex-col items-center justify-center text-[7px] text-cyber-cyan font-mono font-bold leading-none">
                            <span>SOULY</span>
                            <span>VERIF</span>
                            <span>VALID</span>
                        </div>
                    </div>
                    <div class="text-[10px] text-slate-400 print:text-slate-600 font-cairo leading-tight text-right">
                        <span class="text-emerald-400 font-bold block">رمز QR رسمي وموثق</span>
                        <span>فحص رقمي لحظي للشهادة</span>
                    </div>
                </div>

                <div class="text-center text-[10px] font-mono text-slate-400 print:text-slate-600 space-y-0.5">
                    <span class="text-slate-200 print:text-slate-800 font-bold block font-cairo">بروتوكول الاعتماد الهندسي</span>
                    <span>CODE: {{ $booking->booking_number }}-VERIFIED</span>
                </div>

                <div class="text-center sm:text-left text-xs font-cairo text-slate-300 print:text-slate-700 space-y-1">
                    <span class="text-[10px] text-slate-500 uppercase block font-bold">الختم الهندسي:</span>
                    <span class="font-bold text-cyber-gold print:text-amber-800 block text-xs">إدارة العمليات والسيادة التقنية</span>
                    <span class="text-[10px] font-mono text-slate-400">SOULY ENGINEERING</span>
                </div>
            </div>

        </div>

        <!-- 5. FOOTER PROTOCOL NOTICE -->
        <div class="relative z-10 pt-4 border-t border-obsidian-border print:border-slate-300 text-center text-[10px] text-slate-500 print:text-slate-600 font-cairo space-y-1">
            <p>هذه الفاتورة والشهادة معتمدة إلكترونياً وصادرة آلياً عبر منصة سولي وموثقة برقم الحجز المرجعي.</p>
            <p class="font-mono">سولي · القاهرة، مصر · support@soulyeg.online · soulyeg.online</p>
        </div>

    </div>

</div>

<!-- html2pdf.js CDN for 1-Click High-Res PDF Generation -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
function shareCertificate() {
    if (navigator.share) {
        navigator.share({
            title: 'فاتورة وشهادة ضمان سولي - {{ $booking->booking_number }}',
            text: 'فاتورة وشهادة ضمان معتمدة رسمياً من منصة سولي لأنظمة الأعمال والذكاء الاصطناعي.',
            url: window.location.href
        }).catch(() => {});
    } else {
        navigator.clipboard.writeText(window.location.href).then(() => {
            if (window.Alpine && window.Alpine.store('citadel')) {
                window.Alpine.store('citadel').showToast('تم نسخ رابط الشهادة للمشاركة بنجاح ✓');
            }
        });
    }
}

function downloadPdf() {
    const btn = document.getElementById('btn-download-pdf');
    const btnText = document.getElementById('btn-download-text');
    const originalText = btnText.innerText;
    
    btn.disabled = true;
    btnText.innerText = 'جاري إعداد الـ PDF... ⏳';

    const element = document.getElementById('printable-certificate');
    const opt = {
        margin: [6, 6, 6, 6],
        filename: 'Souly-Invoice-{{ $booking->booking_number }}.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2, useCORS: true, backgroundColor: '#05050A' },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
    };

    html2pdf().set(opt).from(element).save().then(() => {
        btn.disabled = false;
        btnText.innerText = 'تم التنزيل بنجاح ✓';
        setTimeout(() => {
            btnText.innerText = originalText;
        }, 3000);
    }).catch(err => {
        btn.disabled = false;
        btnText.innerText = originalText;
        window.print();
    });
}
</script>

<!-- Print Stylesheet -->
<style>
@media print {
    body {
        background-color: #ffffff !important;
        color: #0f172a !important;
    }
    #printable-certificate {
        background: #ffffff !important;
        color: #0f172a !important;
        border: 1px solid #cbd5e1 !important;
        box-shadow: none !important;
    }
    .print\:hidden {
        display: none !important;
    }
}
</style>
@endsection