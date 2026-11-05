@extends('portal.layout')

@section('title', 'متابعة أوردرات الهاردوير وحالة الشحن والضمان | بوابة العميل')
@section('page-title', 'متابعة أوردرات الهاردوير والضمان الرسمي')

@section('portal-content')
<div class="space-y-6 sm:space-y-8" x-data="{ activeOrderSerial: null }">

    {{-- Top Overview Card --}}
    <div class="relative overflow-hidden rounded-3xl glass-obsidian border border-cyber-cyan/40 p-5 sm:p-7 lg:p-8 shadow-[0_4px_30px_rgba(0,245,212,0.1)]">
        <div class="absolute -top-16 -left-16 w-64 h-64 bg-cyber-cyan/15 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-5 sm:gap-6">
            <div class="space-y-2 text-right">
                <span class="px-3 py-1 rounded-full text-xs font-mono font-bold bg-cyber-cyan/15 text-cyber-cyan border border-cyber-cyan/30">
                    HARDWARE ORDERS & WARRANTY TRACKER
                </span>
                <h2 class="text-lg sm:text-2xl font-black text-white font-cairo">
                    متابعة شحنات الأجهزة ومستندات الضمان المعتمدة 🚚
                </h2>
                <p class="text-xs text-slate-300 font-cairo max-w-xl leading-relaxed">
                    تتبع مراحل فحص وتوصيل أجهزة الكاشير والهاردوير الخاصة بمؤسستك خطوة بخطوة، مع إمكانية الوصول الفوري للأرقام التسلسلية (Serials) وفترات الضمان.
                </p>
            </div>

            <div class="flex items-center gap-3 shrink-0 self-end md:self-auto">
                <a href="{{ route('store.hardware') }}" 
                   class="px-5 py-2.5 rounded-xl btn-citadel-primary text-xs font-cairo font-bold shadow-neon-cyan flex items-center gap-2">
                    <span>🛒 تسوق المزيد من الأجهزة</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Orders List --}}
    @if($orders->count() > 0)
        <div class="space-y-5 sm:space-y-6">
            @foreach($orders as $order)
           @php
                    $stepIndex = $order->step_index ?? match($order->status) {
                        'pending_review' => 1,
                        'confirmed_preparing' => 2,
                        'out_for_delivery' => 3,
                        'delivered_warranty_active' => 4,
                        default => 1,
                    };
                    $statusLabelAr = $order->status_label_ar ?? match($order->status) {
                        'pending_review' => 'قيد المراجعة وتأكيد المخزون',
                        'confirmed_preparing' => 'تم التأكيد وجاري الفحص والتجهيز',
                        'out_for_delivery' => 'خرج للتوصيل مع المندوب',
                        'delivered_warranty_active' => 'تم التسليم والضمان مفعل',
                        'cancelled' => 'طلب ملغي',
                        default => $order->status,
                    };
                    $isDelivered = ($order->status === 'delivered_warranty_active');
                    $isCancelled = ($order->status === 'cancelled');
                @endphp
                
                <div class="rounded-3xl glass-obsidian border border-obsidian-border p-5 sm:p-7 lg:p-8 space-y-5 sm:space-y-6 transition-all hover:border-cyber-cyan/40">
                    
                    {{-- Order Header --}}
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-obsidian-border pb-4 sm:pb-5">
                        <div class="space-y-1">
                            <div class="flex items-center gap-3 flex-wrap">
                                <span class="text-sm sm:text-base font-black font-mono text-cyber-cyan tracking-wider">{{ $order->order_number }}</span>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold font-cairo
                                    {{ match($order->status) {
                                        'pending_review' => 'bg-amber-500/20 text-amber-300 border border-amber-500/30',
                                        'confirmed_preparing' => 'bg-purple-500/20 text-purple-300 border border-purple-500/30',
                                        'out_for_delivery' => 'bg-cyan-500/20 text-cyan-300 border border-cyan-500/30',
                                        'delivered_warranty_active' => 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30',
                                        'cancelled' => 'bg-rose-500/20 text-rose-300 border border-rose-500/30',
                                        default => 'bg-slate-800 text-slate-400'
                                    } }}">
                                    {{ $order->status_label_ar }}
                                </span>
                            </div>
                            <span class="text-xs text-slate-400 font-mono">تاريخ الطلب: {{ $order->created_at->format('Y-m-d · h:i A') }}</span>
                        </div>

                        {{-- Total & Fast Action --}}
                        <div class="flex items-center gap-3 w-full sm:w-auto justify-between sm:justify-end flex-wrap">
                            <div class="text-right sm:text-left">
                                <span class="text-[10px] text-slate-400 block font-cairo">الإجمالي النهائي:</span>
                                <span class="text-base sm:text-lg font-black font-mono text-cyber-gold">{{ number_format($order->total_amount, 2) }} EGP</span>
                            </div>

                            <a href="{{ route('portal.tickets', ['scope' => 'hardware_store', 'ref' => $order->order_number]) }}"
                               class="px-3.5 py-2 rounded-xl bg-obsidian-surface hover:bg-obsidian-card border border-rose-500/40 text-rose-300 hover:text-white text-xs font-cairo font-bold transition-all flex items-center gap-1.5 shrink-0">
                                <span>🎫</span>
                                <span>فتح تذكرة ضمان</span>
                            </a>
                        </div>
                    </div>

                    {{-- 4-STEP ORDER TRACKING BAR --}}
                    @if(!$isCancelled)
                        <div class="py-2">
                            <div class="text-xs font-bold text-slate-300 font-cairo mb-3 flex items-center justify-between">
                                <span>مراحل تجهيز وتوصيل الشحنة والضمان:</span>
                                <span class="font-mono text-cyber-cyan text-xs">المرحلة {{ $stepIndex }} من 4</span>
                            </div>

                            {{-- Desktop Stepper --}}
                            <div class="relative hidden sm:block">
                                <div class="absolute top-4 right-0 left-0 h-1 bg-slate-800 rounded-full z-0">
                                    <div class="h-full bg-gradient-to-r from-cyber-cyan via-purple-500 to-emerald-400 rounded-full transition-all duration-700"
                                         style="width: {{ (($stepIndex - 1) / 3) * 100 }}%;"></div>
                                </div>

                                <div class="grid grid-cols-4 gap-4 relative z-10">
                                    <div class="flex flex-col items-center text-center gap-2">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center font-mono font-bold text-xs shrink-0
                                            {{ $stepIndex >= 1 ? 'bg-cyber-cyan text-obsidian-base shadow-neon-cyan' : 'bg-slate-800 text-slate-500 border border-slate-700' }}">
                                            {{ $stepIndex > 1 ? '✓' : '1' }}
                                        </div>
                                        <div>
                                            <span class="text-xs font-bold font-cairo block {{ $stepIndex >= 1 ? 'text-white' : 'text-slate-500' }}">المراجعة</span>
                                            <span class="text-[10px] text-slate-400 font-cairo">تأكيد توفر المخزون</span>
                                        </div>
                                    </div>

                                    <div class="flex flex-col items-center text-center gap-2">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center font-mono font-bold text-xs shrink-0
                                            {{ $stepIndex >= 2 ? 'bg-purple-500 text-white shadow-sm' : 'bg-slate-800 text-slate-500 border border-slate-700' }}">
                                            {{ $stepIndex > 2 ? '✓' : '2' }}
                                        </div>
                                        <div>
                                            <span class="text-xs font-bold font-cairo block {{ $stepIndex >= 2 ? 'text-white' : 'text-slate-500' }}">الفحص والتجهيز</span>
                                            <span class="text-[10px] text-slate-400 font-cairo">اختبار العتاد والتغليف</span>
                                        </div>
                                    </div>

                                    <div class="flex flex-col items-center text-center gap-2">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center font-mono font-bold text-xs shrink-0
                                            {{ $stepIndex >= 3 ? 'bg-cyan-400 text-obsidian-base shadow-sm' : 'bg-slate-800 text-slate-500 border border-slate-700' }}">
                                            {{ $stepIndex > 3 ? '✓' : '3' }}
                                        </div>
                                        <div>
                                            <span class="text-xs font-bold font-cairo block {{ $stepIndex >= 3 ? 'text-white' : 'text-slate-500' }}">خرج للتوصيل</span>
                                            <span class="text-[10px] text-slate-400 font-cairo">مع مندوب سولي أو المهندس</span>
                                        </div>
                                    </div>

                                    <div class="flex flex-col items-center text-center gap-2">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center font-mono font-bold text-xs shrink-0
                                            {{ $stepIndex >= 4 ? 'bg-emerald-500 text-white shadow-sm' : 'bg-slate-800 text-slate-500 border border-slate-700' }}">
                                            {{ $stepIndex >= 4 ? '✓' : '4' }}
                                        </div>
                                        <div>
                                            <span class="text-xs font-bold font-cairo block {{ $stepIndex >= 4 ? 'text-white' : 'text-slate-500' }}">الاستلام والضمان</span>
                                            <span class="text-[10px] text-slate-400 font-cairo">تفعيل السيريال رسمياً</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Mobile Vertical Timeline --}}
                            <div class="relative sm:hidden space-y-4 pr-4 border-r-2 border-slate-800 mr-2 my-2">
                                <div class="relative flex items-start gap-2.5">
                                    <div class="absolute -right-[1.55rem] top-0 w-5 h-5 rounded-full flex items-center justify-center font-mono font-bold text-[9px] shrink-0
                                        {{ $stepIndex >= 1 ? 'bg-cyber-cyan text-obsidian-base ring-2 ring-cyber-cyan/30' : 'bg-slate-800 text-slate-500' }}">
                                        {{ $stepIndex > 1 ? '✓' : '1' }}
                                    </div>
                                    <div>
                                        <span class="text-xs font-bold font-cairo block {{ $stepIndex >= 1 ? 'text-white' : 'text-slate-500' }}">قيد المراجعة</span>
                                        <span class="text-[10px] text-slate-400 font-cairo">تأكيد توفر المخزون بالمستودع</span>
                                    </div>
                                </div>

                                <div class="relative flex items-start gap-2.5">
                                    <div class="absolute -right-[1.55rem] top-0 w-5 h-5 rounded-full flex items-center justify-center font-mono font-bold text-[9px] shrink-0
                                        {{ $stepIndex >= 2 ? 'bg-purple-500 text-white ring-2 ring-purple-500/30' : 'bg-slate-800 text-slate-500' }}">
                                        {{ $stepIndex > 2 ? '✓' : '2' }}
                                    </div>
                                    <div>
                                        <span class="text-xs font-bold font-cairo block {{ $stepIndex >= 2 ? 'text-white' : 'text-slate-500' }}">جاري التجهيز والفحص</span>
                                        <span class="text-[10px] text-slate-400 font-cairo">اختبار العتاد والتغليف الفني</span>
                                    </div>
                                </div>

                                <div class="relative flex items-start gap-2.5">
                                    <div class="absolute -right-[1.55rem] top-0 w-5 h-5 rounded-full flex items-center justify-center font-mono font-bold text-[9px] shrink-0
                                        {{ $stepIndex >= 3 ? 'bg-cyan-400 text-obsidian-base ring-2 ring-cyan-400/30' : 'bg-slate-800 text-slate-500' }}">
                                        {{ $stepIndex > 3 ? '✓' : '3' }}
                                    </div>
                                    <div>
                                        <span class="text-xs font-bold font-cairo block {{ $stepIndex >= 3 ? 'text-white' : 'text-slate-500' }}">خرج للتوصيل</span>
                                        <span class="text-[10px] text-slate-400 font-cairo">مع مندوب سولي أو المهندس</span>
                                    </div>
                                </div>

                                <div class="relative flex items-start gap-2.5">
                                    <div class="absolute -right-[1.55rem] top-0 w-5 h-5 rounded-full flex items-center justify-center font-mono font-bold text-[9px] shrink-0
                                        {{ $stepIndex >= 4 ? 'bg-emerald-500 text-white ring-2 ring-emerald-500/30' : 'bg-slate-800 text-slate-500' }}">
                                        {{ $stepIndex >= 4 ? '✓' : '4' }}
                                    </div>
                                    <div>
                                        <span class="text-xs font-bold font-cairo block {{ $stepIndex >= 4 ? 'text-white' : 'text-slate-500' }}">تم التسليم وتفعيل الضمان</span>
                                        <span class="text-[10px] text-slate-400 font-cairo">سريال مفعل ومحضر استلام رسمي</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Itemized Invoice & Serials Row --}}
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 pt-2">
                        <div class="lg:col-span-8 space-y-3">
                            <span class="text-xs font-bold text-slate-300 font-cairo block">الأجهزة المشمولة في الطلب:</span>
                            <div class="overflow-x-auto rounded-2xl bg-obsidian-surface border border-obsidian-border overscroll-contain">
                                <table class="w-full text-right text-xs font-cairo min-w-[460px]">
                                    <thead class="bg-black/30 text-slate-400 font-bold border-b border-obsidian-border">
                                        <tr>
                                            <th class="p-3">الكود SKU</th>
                                            <th class="p-3">اسم الجهاز</th>
                                            <th class="p-3 text-center">الكمية</th>
                                            <th class="p-3">سعر الوحدة</th>
                                            <th class="p-3 text-left">الإجمالي</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-obsidian-border/50">
                                        @foreach($order->items as $item)
                                            <tr>
                                                <td class="p-3 font-mono font-bold text-cyber-cyan">{{ $item->product_sku }}</td>
                                                <td class="p-3 text-white font-bold">{{ $item->product_name }}</td>
                                                <td class="p-3 font-mono text-center font-bold text-slate-300">{{ $item->quantity }}</td>
                                                <td class="p-3 font-mono text-slate-400">{{ number_format($item->unit_price, 0) }} EGP</td>
                                                <td class="p-3 font-mono font-bold text-cyber-gold text-left">{{ number_format($item->subtotal, 0) }} EGP</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Warranty & Serial Numbers Box --}}
                        <div class="lg:col-span-4 p-4 sm:p-5 rounded-2xl bg-obsidian-surface border border-obsidian-border space-y-3 flex flex-col justify-between">
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-black text-white font-cairo flex items-center gap-1.5">
                                        <span>🛡️</span>
                                        <span>وثيقة الضمان والسيريال</span>
                                    </span>
                                    <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-400 font-bold">SOULY CARE</span>
                                </div>

                                <div class="space-y-1 text-xs font-cairo">
                                    <span class="text-slate-400 block text-[10px]">الأرقام التسلسلية (Serials):</span>
                                    <div class="p-2.5 rounded-xl bg-black/60 border border-slate-700 font-mono text-[11px] text-cyber-cyan select-all break-all">
                                        {{ $order->serial_numbers_notes ?? 'جاري الفحص الهندسي وتسجيل السيريال فور خروج الشحنة.' }}
                                    </div>
                                </div>

                                <div class="text-[11px] font-cairo text-slate-400 leading-relaxed">
                                    @if($isDelivered)
                                        <span class="text-emerald-400 font-bold">✓ الضمان مفعل وساري لمدة 12 إلى 36 شهراً بحسب القطع.</span>
                                    @else
                                        <span>يتم تفعيل الضمان رسمياً فور استلامك للأجهزة وتوقيع محضر الفحص الفني.</span>
                                    @endif
                                </div>
                            </div>

                            <div class="pt-2 border-t border-obsidian-border/70 text-[11px] text-slate-400 font-cairo flex items-center justify-between gap-2">
                                <span>العنوان:</span>
                                <span class="font-bold text-slate-200 truncate">{{ $order->shipping_address }}</span>
                            </div>
                        </div>
                    </div>

                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-12 sm:py-16 rounded-3xl glass-obsidian border border-obsidian-border space-y-4">
            <span class="text-4xl sm:text-5xl block">🖥️</span>
            <h3 class="text-base sm:text-lg font-bold text-white font-cairo">لم تقم بشراء أي أجهزة هاردوير حتى الآن</h3>
            <p class="text-xs text-slate-400 font-cairo max-w-md mx-auto">
                تصفح متجر قطع الهاردوير وأجهزة الـ POS لشراء طابعات فواتير، أجهزة كاشير، وهاردات متوافقة مع منظومتك.
            </p>
            <a href="{{ route('store.hardware') }}" class="inline-block px-6 py-2.5 rounded-xl btn-citadel-primary text-xs font-cairo font-bold shadow-neon-cyan">
                تصفح متجر الهاردوير ⇥
            </a>
        </div>
    @endif

</div>
@endsection