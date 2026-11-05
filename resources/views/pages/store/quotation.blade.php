<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>عرض سعر رسمي معتمد — منظومة BrandERP | سولي للحلول التكنولوجية</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&family=JetBrains+Mono:wght@500;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Cairo', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; color: black !important; padding: 0 !important; }
            .print-border { border: 1px solid #ddd !important; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 p-4 sm:p-8 min-h-screen flex items-center justify-center">

    <div class="max-w-4xl w-full bg-white rounded-3xl shadow-xl border border-slate-200 p-6 sm:p-10 space-y-8 relative overflow-hidden">
        
        {{-- Floating Print / Close Toolbar --}}
        <div class="no-print flex items-center justify-between pb-4 border-b border-slate-200">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-xs font-bold text-slate-600">وثيقة عرض سعر رقمية معتمدة ومجهزة للطباعة والحفظ PDF</span>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="window.print()" class="px-5 py-2 rounded-xl bg-slate-900 text-white font-bold text-xs flex items-center gap-2 hover:bg-slate-800 transition-all shadow-md">
                    <span>🖨️ طباعة / حفظ كـ PDF</span>
                </button>
                <button onclick="window.close()" class="px-4 py-2 rounded-xl bg-slate-200 text-slate-700 font-bold text-xs hover:bg-slate-300 transition-all">
                    إغلاق النافذة
                </button>
            </div>
        </div>

        {{-- Official Header --}}
        <div class="flex flex-col sm:flex-row items-center justify-between gap-6 pb-6 border-b-2 border-slate-900">
            <div class="space-y-1 text-center sm:text-right">
                <div class="inline-flex items-center gap-2">
                    <span class="text-2xl font-black text-slate-900">سولي</span>
                    <span class="px-2.5 py-0.5 rounded text-[11px] font-mono font-bold bg-slate-900 text-white">SOULY TECH</span>
                </div>
                <h1 class="text-xl font-black text-slate-900">عرض سعر منظومة BrandERP الموديلار المعتمد</h1>
                <p class="text-xs text-slate-500">القلعة الرقمية لإدارة وتشغيل الأعمال السحابية في مصر</p>
                <p class="text-[11px] text-slate-400 font-mono">سجل تجاري: 402910 | بطاقة ضريبية: 712-449-301</p>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-center sm:text-left space-y-1 w-full sm:w-auto">
                <div class="text-xs text-slate-500 font-bold">رقم العرض المرجعي:</div>
                <div class="text-base font-black font-mono text-slate-900">Q-{{ date('Ymd') }}-{{ rand(1000, 9999) }}</div>
                <div class="text-[11px] text-slate-500">تاريخ الإصدار: <span class="font-mono font-bold">{{ date('Y-m-d') }}</span></div>
                <div class="text-[11px] text-emerald-600 font-bold">صالح لمدة: 15 يوماً من تاريخه</div>
            </div>
        </div>

        {{-- Client Details --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs">
            <div>
                <span class="text-slate-400 block font-bold mb-1">بيانات العميل / المؤسسة الموجه إليها العرض:</span>
                <span class="font-bold text-slate-900 text-sm block">{{ $clientName }}</span>
                <span class="text-slate-600 block">{{ $companyName }}</span>
            </div>
            <div>
                <span class="text-slate-400 block font-bold mb-1">المنظومة المطلوبة:</span>
                <span class="font-bold text-slate-900 text-sm block">نظام BrandERP السحابي الموديلار المتكامل</span>
                <span class="text-slate-600 block">شامل الدعم الفني، التدريب الهندسي، والضمان</span>
            </div>
        </div>

        {{-- Line Items Table --}}
        <div class="space-y-2">
            <h3 class="text-sm font-black text-slate-900">المكونات والموديولات المختارة:</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs">
                    <thead>
                        <tr class="bg-slate-900 text-white font-bold">
                            <th class="p-3 rounded-r-xl">م</th>
                            <th class="p-3">كود البند</th>
                            <th class="p-3">المكون / الموديول / الميزة</th>
                            <th class="p-3">التصنيف</th>
                            <th class="p-3 text-left rounded-l-xl">القيمة (EGP)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($items as $i => $item)
                            <tr class="hover:bg-slate-50">
                                <td class="p-3 font-mono text-slate-500">{{ $i + 1 }}</td>
                                <td class="p-3 font-mono font-bold text-slate-700">{{ $item->code ?? 'ERP-ITEM' }}</td>
                                <td class="p-3">
                                    <span class="font-bold text-slate-900 block">{{ $item->name_ar }}</span>
                                    <span class="text-[11px] text-slate-500">{{ $item->short_desc_ar }}</span>
                                </td>
                                <td class="p-3">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ $item->department_ar ?? $item->tier }}
                                    </span>
                                </td>
                                <td class="p-3 text-left font-mono font-bold text-slate-900">{{ number_format($item->price, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Totals Breakdown --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 p-5 rounded-2xl bg-slate-900 text-white">
            <div class="space-y-1">
                <div class="text-xs text-slate-300">طريقة السداد المعتمدة:</div>
                <div class="text-xs font-bold text-emerald-400">دفعة واحدة شاملة التركيب والتدريب الميداني والضمان</div>
                <div class="text-[11px] text-slate-400">الأسعار تشمل التحديثات السحابية الدورية الأولى مجاناً</div>
            </div>

            <div class="space-y-2 text-left w-full sm:w-64 border-t sm:border-t-0 sm:border-r border-slate-700 sm:pr-6 pt-3 sm:pt-0">
                <div class="flex justify-between text-xs text-slate-300">
                    <span>إجمالي البنود:</span>
                    <span class="font-mono font-bold">{{ number_format($subtotal, 2) }} EGP</span>
                </div>
                @if($discount > 0)
                    <div class="flex justify-between text-xs text-emerald-400 font-bold">
                        <span>خصم الباقة المجمعة:</span>
                        <span class="font-mono">- {{ number_format($discount, 2) }} EGP</span>
                    </div>
                @endif
                <div class="flex justify-between text-base font-black text-white pt-2 border-t border-slate-700">
                    <span>الإجمالي النهائي:</span>
                    <span class="font-mono text-emerald-400">{{ number_format($total, 2) }} EGP</span>
                </div>
            </div>
        </div>

        {{-- Terms & Conditions --}}
        <div class="space-y-2 text-[11px] text-slate-500 pt-2 border-t border-slate-200">
            <div class="font-bold text-slate-700">الشروط والالتزامات الهندسية المعتمدة:</div>
            <ul class="list-disc pr-4 space-y-1">
                <li>يبدأ العمل وتثبيت المنظومة خلال 24 ساعة من تاريخ اعتماد العرض وسداد الدفعة.</li>
                <li>يشمل العرض تدريباً شاملاً لفريق الكاشير والمحاسبين والإدارة حتى الإتقان التام.</li>
                <li>ضمان فني ودعم مستمر عبر بوابة الدعم الفني وواتساب الدعم السريع لمدة 12 شهراً.</li>
            </ul>
        </div>

        {{-- Official Signatures & Seal --}}
        <div class="grid grid-cols-2 gap-8 pt-6 border-t border-slate-200 items-end">
            <div class="text-center sm:text-right space-y-1 text-xs">
                <div class="text-slate-400 font-bold">اعتماد وقبول العميل:</div>
                <div class="text-slate-800 font-bold">{{ $clientName }}</div>
                <div class="h-12 border-b border-dashed border-slate-300 mt-2"></div>
                <div class="text-[10px] text-slate-400">التوقيع والختم</div>
            </div>

            <div class="text-center sm:text-left space-y-1 text-xs">
                <div class="text-slate-400 font-bold">إدارة المبيعات والحلول الهندسية — سولي:</div>
                <div class="text-slate-800 font-bold">م/ أسامة سولي — رئيس القطاع التقني</div>
                <div class="inline-block px-4 py-2 mt-2 rounded-xl border-2 border-emerald-600 text-emerald-700 font-bold text-xs uppercase tracking-wider font-mono">
                    ✓ APPROVED & VERIFIED SOULY CITADEL
                </div>
            </div>
        </div>

    </div>

</body>
</html>
