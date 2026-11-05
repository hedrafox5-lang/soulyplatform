@php
    $powerMeter = $powerMeter ?? 0;
    $hasCore = $hasCore ?? false;
    $hasActiveLicense = $hasActiveLicense ?? false;
    $purchasedModules = $purchasedModules ?? collect();
    $purchasedFeatures = $purchasedFeatures ?? collect();
    $licenseKey = $licenseKey ?? null;
@endphp
@extends('portal.layout')

@section('title', 'إدارة منظومة BrandERP والتراخيص | بوابة العميل')
@section('page-title', 'إدارة منظومة BrandERP والتراخيص')

@section('portal-content')
<div class="space-y-6 sm:space-y-8" x-data="{ copiedLicense: false }">

    {{-- ===================================================================== --}}
    {{-- 1. ERP POWER METER %                                                  --}}
    {{-- ===================================================================== --}}
    <div class="relative overflow-hidden rounded-3xl glass-obsidian border border-cyber-cyan/40 p-5 sm:p-7 lg:p-8 shadow-[0_4px_30px_rgba(0,245,212,0.12)]">
        <div class="absolute -top-20 -left-20 w-72 h-72 bg-cyber-cyan/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-20 -right-20 w-72 h-72 bg-purple-600/15 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-8 items-center">
            
            {{-- Gauge & Percentage --}}
            <div class="lg:col-span-4 flex flex-col items-center justify-center text-center p-5 sm:p-6 rounded-2xl bg-obsidian-surface/90 border border-obsidian-border space-y-3">
                <div class="relative w-32 sm:w-36 h-32 sm:h-36 flex items-center justify-center shrink-0">
                    <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                        <circle cx="50" cy="50" r="42" stroke="currentColor" stroke-width="8" class="text-slate-800" fill="transparent"/>
                        <circle cx="50" cy="50" r="42" stroke="currentColor" stroke-width="8" class="text-cyber-cyan transition-all duration-1000 ease-out" 
                                stroke-dasharray="264" 
                                stroke-dashoffset="{{ 264 - (264 * $powerMeter / 100) }}" 
                                stroke-linecap="round" 
                                fill="transparent"/>
                    </svg>
                    <div class="absolute flex flex-col items-center justify-center">
                        <span class="text-2xl sm:text-3xl font-black font-mono text-white">{{ $powerMeter }}%</span>
                        <span class="text-[10px] font-cairo text-slate-400 font-bold">اكتمال المنظومة</span>
                    </div>
                </div>

                <div class="space-y-1">
                    <h4 class="text-sm font-black text-white font-cairo">مؤشر قوة واكتمال BrandERP</h4>
                    <p class="text-[11px] text-slate-400 font-cairo">
                        {{ $hasCore ? 'النواة الأساسية مفعلة وجاهزة للربط' : 'لم يتم تفعيل النواة الأساسية بعد' }}
                    </p>
                </div>
            </div>

            {{-- Breakdown & Advice --}}
            <div class="lg:col-span-8 space-y-4 sm:space-y-5">
                <div class="space-y-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="px-3 py-0.5 rounded-full text-[10px] font-mono font-bold bg-cyber-cyan/15 text-cyber-cyan border border-cyber-cyan/30">
                            ERP HEALTH & COMPLETION
                        </span>
                        @if($hasActiveLicense)
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-cairo font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/40">
                                ترخيص ساري معتمد ✓
                            </span>
                        @endif
                    </div>
                    <h3 class="text-lg sm:text-2xl font-black text-white font-cairo">
                        حالة منظومتك السحابية ونقاط القوة
                    </h3>
                    <p class="text-xs text-slate-300 font-cairo leading-relaxed">
                        كل موديول أو ميزة دقيقة تضيفها من المتجر ترفع جاهزية شركتك التشغيلية وتغلق ثغرات الهدر المالي والمخزني.
                    </p>
                </div>

                {{-- Status Pills --}}
                <div class="grid grid-cols-3 gap-2 sm:gap-3 text-center">
                    <div class="p-2.5 sm:p-3 rounded-xl bg-obsidian-surface border border-obsidian-border">
                        <span class="text-[9px] sm:text-[10px] font-cairo text-slate-400 block font-bold">النواة (Core)</span>
                        <span class="text-[11px] sm:text-xs font-bold font-cairo {{ $hasCore ? 'text-emerald-400' : 'text-slate-500' }}">
                            {{ $hasCore ? 'مُفعّلة (2,000)' : 'غير مُفعّلة' }}
                        </span>
                    </div>
                    <div class="p-2.5 sm:p-3 rounded-xl bg-obsidian-surface border border-obsidian-border">
                        <span class="text-[9px] sm:text-[10px] font-cairo text-slate-400 block font-bold">الموديولات</span>
                        <span class="text-[11px] sm:text-xs font-bold font-mono text-purple-300">{{ $purchasedModules->count() }} / 16</span>
                    </div>
                    <div class="p-2.5 sm:p-3 rounded-xl bg-obsidian-surface border border-obsidian-border">
                        <span class="text-[9px] sm:text-[10px] font-cairo text-slate-400 block font-bold">الميزات الدقيقة</span>
                        <span class="text-[11px] sm:text-xs font-bold font-mono text-cyber-gold">{{ $purchasedFeatures->count() }} ميزة</span>
                    </div>
                </div>

                {{-- AI Upsell Advice Box --}}
                <div class="p-3.5 sm:p-4 rounded-2xl bg-gradient-to-r from-cyber-purple/20 via-obsidian-surface to-cyber-cyan/15 border border-cyber-purple/40 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 text-xs font-cairo">
                    <div class="flex items-center gap-2.5">
                        <span class="text-2xl shrink-0">🤖</span>
                        <div>
                            <strong class="text-white block">توصية الذكاء الاصطناعي:</strong>
                            <span class="text-slate-300 text-[11px] sm:text-xs">
                                @if($purchasedModules->count() < 3)
                                    ننصحك بإضافة موديول <strong>الحسابات العامة</strong> وموديول <strong>المخازن</strong> لرفع اكتمال منظومتك والحصول على تقارير الأرباح اللحظية.
                                @else
                                    منظومتك ممتازة! يمكنك إضافة ميزات الواتساب التلقائي والذكاء الاصطناعي التنبؤي لتعظيم المبيعات.
                                @endif
                            </span>
                        </div>
                    </div>
                    <a href="{{ route('store.branderp') }}" class="px-4 py-2 rounded-xl btn-citadel-primary text-xs font-bold shrink-0 shadow-neon-cyan self-end sm:self-auto">
                        ترقية المنظومة ⇥
                    </a>
                </div>

            </div>

        </div>
    </div>

    {{-- ===================================================================== --}}
    {{-- 2. LICENSE KEY & SYSTEM ACTIVATION STATUS                             --}}
    {{-- ===================================================================== --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
        
        {{-- License Key Card --}}
        <div class="p-5 sm:p-6 rounded-3xl glass-obsidian border border-obsidian-border space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-sm sm:text-base font-black text-white font-cairo flex items-center gap-2">
                    <span>🔑</span>
                    <span>مفتاح ترخيص BrandERP السحابي</span>
                </h3>
                <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-slate-800 text-slate-400">LICENSE</span>
            </div>

            @if($licenseKey)
                <div class="p-3.5 sm:p-4 rounded-2xl bg-black/60 border border-cyber-cyan/30 flex items-center justify-between gap-2">
                    <span class="font-mono text-xs sm:text-base font-black text-cyber-cyan tracking-wider select-all truncate">
                        {{ $licenseKey }}
                    </span>
                    <button type="button" 
                            @click="navigator.clipboard.writeText('{{ $licenseKey }}'); copiedLicense = true; setTimeout(() => copiedLicense = false, 2000)"
                            class="px-3 py-1.5 rounded-xl bg-obsidian-surface hover:bg-obsidian-card border border-cyber-cyan/40 text-xs font-cairo text-cyber-cyan font-bold transition-all shrink-0 cursor-pointer">
                        <span x-text="copiedLicense ? 'تم النسخ ✓' : 'نسخ المفتاح'"></span>
                    </button>
                </div>
                <p class="text-[11px] text-slate-400 font-cairo">
                    مفتاح الترخيص معتمد ومربوط بحسابك لتفعيل نقاط البيع والـ POS بدون قيود.
                </p>
            @else
                <div class="p-4 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-xs font-cairo text-amber-200 space-y-2">
                    <p>لم يتم إصدار مفتاح ترخيص نهائي بعد. يمكنك البدء الآن بطلب النواة الأساسية بـ 2,000 ج.م فقط وسيتم توليد ترخيصك فورياً.</p>
                    <a href="{{ route('store.branderp') }}" class="inline-block px-4 py-1.5 rounded-xl bg-cyber-gold text-obsidian-base font-bold text-xs">
                        شراء النواة وتفعيل الترخيص (2,000 ج.م)
                    </a>
                </div>
            @endif
        </div>

        {{-- Core & Quick Launch Link --}}
        <div class="p-5 sm:p-6 rounded-3xl glass-obsidian border border-obsidian-border space-y-4 flex flex-col justify-between">
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm sm:text-base font-black text-white font-cairo flex items-center gap-2">
                        <span>🏪</span>
                        <span>النواة الأساسية (BrandERP Core)</span>
                    </h3>
                    <span class="text-xs font-bold font-cairo {{ $hasCore ? 'text-emerald-400' : 'text-slate-500' }}">
                        {{ $hasCore ? 'نشط ويعمل ✓' : 'غير مفعل' }}
                    </span>
                </div>
                <p class="text-xs text-slate-300 font-cairo leading-relaxed">
                    تشمل كاشير نقاط البيع السريع، إدارة المخزون، طباعة الفواتير الحرارية، حركة الخزينة، وإدارة العملاء.
                </p>
            </div>

            <div class="pt-2">
                <a href="{{ route('store.branderp') }}" 
                   class="w-full py-2.5 rounded-xl btn-citadel-primary text-xs text-center font-cairo font-bold block shadow-neon-cyan">
                    🏪 تصفح متجر الميزات والموديولات
                </a>
            </div>
        </div>

    </div>

    {{-- ===================================================================== --}}
    {{-- 3. ORDER HISTORY                                                      --}}
    {{-- ===================================================================== --}}
    <div class="rounded-3xl glass-obsidian border border-obsidian-border p-5 sm:p-7 lg:p-8 space-y-5 sm:space-y-6">
        <div class="flex items-center justify-between">
            <h3 class="text-sm sm:text-base font-black text-white font-cairo flex items-center gap-2">
                <span>📑</span>
                <span>سجل ترقيات وطلبات BrandERP:</span>
            </h3>
            <span class="text-xs font-mono text-slate-400 font-bold">{{ $orders->count() }} طلبات</span>
        </div>

        @if($orders->count() > 0)
            <div class="overflow-x-auto rounded-2xl border border-obsidian-border/80 overscroll-contain">
                <table class="w-full text-right text-xs font-cairo min-w-[580px]">
                    <thead class="bg-obsidian-surface text-slate-400 border-b border-obsidian-border">
                        <tr>
                            <th class="p-3">رقم الطلب</th>
                            <th class="p-3">التاريخ</th>
                            <th class="p-3">المكونات</th>
                            <th class="p-3">طريقة السداد</th>
                            <th class="p-3">الإجمالي</th>
                            <th class="p-3">الحالة</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-obsidian-border/60">
                        @foreach($orders as $ord)
                            <tr class="hover:bg-obsidian-surface/40 transition-colors">
                                <td class="p-3 font-mono font-bold text-cyber-cyan">{{ $ord->order_number }}</td>
                                <td class="p-3 font-mono text-slate-400">{{ $ord->created_at->format('Y-m-d') }}</td>
                                <td class="p-3 text-slate-200">
                                    {{ count($ord->items_json ?? []) }} موديولات وميزات
                                </td>
                                <td class="p-3 font-bold text-slate-300">
                                    {{ $ord->payment_method === 'wallet' ? 'المحفظة الرقمية' : ($ord->payment_method === 'trial' ? 'تجربة مجانية' : 'تحويل بنكي') }}
                                </td>
                                <td class="p-3 font-mono font-bold text-white">{{ number_format($ord->total_amount, 2) }} EGP</td>
                                <td class="p-3">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold font-cairo
                                        {{ $ord->status === 'active' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-amber-500/20 text-amber-300 border border-amber-500/30' }}">
                                        {{ $ord->status === 'active' ? 'مفعل ✓' : 'قيد المراجعة' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8 space-y-3">
                <span class="text-4xl block">🏪</span>
                <p class="text-xs text-slate-400 font-cairo">لم تقم بشراء أي موديولات أو ترقيات بعد. تفضل بزيارة المتجر لبناء منظومتك.</p>
                <a href="{{ route('store.branderp') }}" class="inline-block px-5 py-2 rounded-xl btn-citadel-primary text-xs font-cairo font-bold shadow-neon-cyan">
                    فتح متجر BrandERP الموديلار ⇥
                </a>
            </div>
        @endif
    </div>

</div>
@endsection