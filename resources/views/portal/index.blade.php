@extends('portal.layout')

@section('title', 'لوحة التحكم والعمليات | بوابة عملاء قلعة سولي')
@section('page-title', 'لوحة التحكم والعمليات')

@section('portal-content')
<div class="space-y-8">

    <!-- ===================================================================== -->
    <!-- 1. STATS & WALLET HUD METRICS                                         -->
    <!-- ===================================================================== -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Metric 1: Digital Wallet Balance -->
        <div class="p-6 rounded-3xl glass-obsidian border border-cyber-cyan/40 shadow-neon-cyan relative overflow-hidden flex flex-col justify-between space-y-4">
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-cyber-cyan/15 rounded-full blur-2xl pointer-events-none"></div>
            <div>
                <div class="flex items-center justify-between text-xs font-readex text-slate-400">
                    <span>رصيد محفظة سولي</span>
                    <span class="w-2 h-2 rounded-full bg-cyber-cyan animate-pulse"></span>
                </div>
                <div class="flex items-baseline gap-2 mt-2">
                    <span class="text-3xl lg:text-4xl font-black font-mono text-white">
                        {{ number_format($wallet->balance, 2) }}
                    </span>
                    <span class="text-xs font-mono font-bold text-cyber-cyan">{{ $wallet->currency }}</span>
                </div>
            </div>

            <div class="pt-2">
                <a href="{{ route('portal.wallet') }}" 
                   class="w-full py-2.5 px-3 rounded-xl bg-cyber-cyan text-obsidian-base font-extrabold text-xs font-cairo text-center block shadow-neon-cyan hover:scale-[1.02] active:scale-95 transition-all">
                    + شحن رصيد المحفظة
                </a>
            </div>
        </div>

        <!-- Metric 2: Active Bookings -->
        <div class="p-6 rounded-3xl glass-obsidian border border-obsidian-border flex flex-col justify-between space-y-4">
            <div>
                <div class="flex items-center justify-between text-xs font-readex text-slate-400">
                    <span>الحجوزات والطلبات الجارية</span>
                    <div class="w-8 h-8 rounded-xl bg-cyber-gold/15 text-cyber-gold flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="flex items-baseline gap-2 mt-2">
                    <span class="text-3xl lg:text-4xl font-black font-mono text-cyber-gold">
                        {{ $activeBookingsCount }}
                    </span>
                    <span class="text-xs font-readex text-slate-400">طلب نشط</span>
                </div>
            </div>

            <div class="pt-2">
                <a href="{{ route('portal.bookings') }}" class="text-xs text-cyber-gold font-bold hover:underline font-cairo flex items-center gap-1">
                    <span>متابعة حالة الحجوزات</span>
                    <svg class="w-3.5 h-3.5 rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </a>
            </div>
        </div>

        <!-- Metric 3: Completed Orders -->
        <div class="p-6 rounded-3xl glass-obsidian border border-obsidian-border flex flex-col justify-between space-y-4">
            <div>
                <div class="flex items-center justify-between text-xs font-readex text-slate-400">
                    <span>المشاريع المنجزة بنجاح</span>
                    <div class="w-8 h-8 rounded-xl bg-emerald-500/15 text-emerald-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                </div>
                <div class="flex items-baseline gap-2 mt-2">
                    <span class="text-3xl lg:text-4xl font-black font-mono text-emerald-400">
                        {{ $completedBookingsCount }}
                    </span>
                    <span class="text-xs font-readex text-slate-400">مشروع منجز</span>
                </div>
            </div>

            <div class="pt-2">
                <a href="{{ route('portal.bookings') }}?status=completed" class="text-xs text-emerald-400 font-bold hover:underline font-cairo flex items-center gap-1">
                    <span>سجل الإنجاز والضمان</span>
                    <svg class="w-3.5 h-3.5 rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </a>
            </div>
        </div>

        <!-- Metric 4: Open Support Tickets -->
        <div class="p-6 rounded-3xl glass-obsidian border border-obsidian-border flex flex-col justify-between space-y-4">
            <div>
                <div class="flex items-center justify-between text-xs font-readex text-slate-400">
                    <span>تذاكر الدعم المفتوحة</span>
                    <div class="w-8 h-8 rounded-xl bg-cyber-purple/20 text-purple-300 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                        </svg>
                    </div>
                </div>
                <div class="flex items-baseline gap-2 mt-2">
                    <span class="text-3xl lg:text-4xl font-black font-mono text-purple-300">
                        {{ $openTicketsCount }}
                    </span>
                    <span class="text-xs font-readex text-slate-400">تذكرة قيد المتابعة</span>
                </div>
            </div>

            <div class="pt-2">
                <a href="{{ route('portal.tickets') }}" class="text-xs text-purple-300 font-bold hover:underline font-cairo flex items-center gap-1">
                    <span>فتح تذكرة دعم جديدة</span>
                    <svg class="w-3.5 h-3.5 rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </a>
            </div>
        </div>

    </div>

    <!-- ===================================================================== -->
    <!-- 2. ACTIVE BOOKINGS STATUS PROGRESSION TRACKER                         -->
    <!-- ===================================================================== -->
    <div class="rounded-3xl glass-obsidian border border-obsidian-border p-6 sm:p-8 space-y-6">
        <div class="flex items-center justify-between border-b border-obsidian-border/80 pb-4">
            <div>
                <h3 class="text-lg font-bold text-white font-cairo flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-cyber-cyan animate-pulse"></span>
                    <span>تتبع مراحل تنفيذ الحجوزات والخدمات</span>
                </h3>
                <p class="text-xs text-slate-400 font-readex mt-0.5">
                    متابعة هندسية حية لكافة مراحل تجهيز وتركيب أنظمتك من البداية وحتى التسليم والضمان
                </p>
            </div>

            <a href="{{ route('portal.bookings') }}" class="text-xs font-bold text-cyber-cyan hover:underline font-cairo">
                كافة الحجوزات ⇥
            </a>
        </div>

        @if($recentBookings->count() > 0)
            <div class="space-y-4">
                @foreach($recentBookings as $booking)
                    <div class="p-4 sm:p-5 rounded-2xl bg-obsidian-surface border border-obsidian-border space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-mono text-xs font-bold text-cyber-cyan">{{ $booking->booking_number }}</span>
                                    <span class="text-xs text-slate-500">|</span>
                                    <span class="font-bold text-sm text-white font-cairo">{{ $booking->service->name }}</span>
                                    @if($booking->package)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-mono bg-obsidian-base border border-obsidian-border text-slate-300">
                                            {{ $booking->package->name }}
                                        </span>
                                    @endif
                                </div>
                                <div class="text-[11px] text-slate-400 font-readex mt-1">
                                    التكلفة: <strong class="text-white font-mono">{{ number_format($booking->total_amount, 2) }} ج.م</strong>
                                    · حالة الدفع: 
                                    @if($booking->payment_status === 'paid')
                                        <span class="text-emerald-400 font-bold">مدفوع بالكامل ✓</span>
                                    @else
                                        <span class="text-amber-400 font-bold">بانتظار التأكيد</span>
                                    @endif
                                </div>
                            </div>

                            <span class="px-3 py-1 rounded-full text-xs font-cairo font-bold self-start sm:self-auto
                                @if($booking->status === 'confirmed') bg-cyber-cyan/15 text-cyber-cyan border border-cyber-cyan/30
                                @elseif($booking->status === 'in_progress') bg-cyber-gold/15 text-cyber-gold border border-cyber-gold/30 animate-pulse
                                @elseif($booking->status === 'completed') bg-emerald-500/15 text-emerald-400 border border-emerald-500/30
                                @elseif($booking->status === 'cancelled') bg-rose-500/15 text-rose-400 border border-rose-500/30
                                @else bg-slate-700/50 text-slate-300 border border-slate-600
                                @endif">
                                @if($booking->status === 'new') طلب جديد بانتظار الاعتماد
                                @elseif($booking->status === 'confirmed') تم التأكيد ومجدول للتنفيذ
                                @elseif($booking->status === 'in_progress') قيد التركيب والتنفيذ الهندسي
                                @elseif($booking->status === 'completed') مكتمل وتحت الضمان المعتمد
                                @else ملغي
                                @endif
                            </span>
                        </div>

                        <!-- Visual Progress Bar Tracker -->
                        <div class="relative pt-2">
                            @php
                                $step = 1;
                                if ($booking->status === 'confirmed') $step = 2;
                                elseif ($booking->status === 'in_progress') $step = 3;
                                elseif ($booking->status === 'completed') $step = 4;
                            @endphp

                            <div class="grid grid-cols-4 gap-2 text-center text-[10px] font-cairo font-bold">
                                <div class="space-y-1.5">
                                    <div class="h-1.5 rounded-full {{ $step >= 1 ? 'bg-cyber-cyan shadow-neon-cyan' : 'bg-obsidian-border' }}"></div>
                                    <span class="{{ $step >= 1 ? 'text-cyber-cyan' : 'text-slate-500' }}">1. تسجيل الحجز</span>
                                </div>
                                <div class="space-y-1.5">
                                    <div class="h-1.5 rounded-full {{ $step >= 2 ? 'bg-cyber-cyan shadow-neon-cyan' : 'bg-obsidian-border' }}"></div>
                                    <span class="{{ $step >= 2 ? 'text-cyber-cyan' : 'text-slate-500' }}">2. التأكيد والاعتماد</span>
                                </div>
                                <div class="space-y-1.5">
                                    <div class="h-1.5 rounded-full {{ $step >= 3 ? 'bg-cyber-gold shadow-neon-gold' : 'bg-obsidian-border' }}"></div>
                                    <span class="{{ $step >= 3 ? 'text-cyber-gold' : 'text-slate-500' }}">3. التنفيذ الميداني</span>
                                </div>
                                <div class="space-y-1.5">
                                    <div class="h-1.5 rounded-full {{ $step >= 4 ? 'bg-emerald-400' : 'bg-obsidian-border' }}"></div>
                                    <span class="{{ $step >= 4 ? 'text-emerald-400' : 'text-slate-500' }}">4. التسليم والضمان</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-8 rounded-2xl bg-obsidian-surface border border-obsidian-border/60 text-center space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-cyber-cyan/10 text-cyber-cyan flex items-center justify-center mx-auto">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="text-sm font-bold text-white font-cairo">لا توجد حجوزات نشطة حالياً</div>
                <p class="text-xs text-slate-400 font-readex max-w-sm mx-auto">
                    يمكنك استعراض خدماتنا الست وحجز باقتك مباشرة بالخصم من رصيد محفظتك الرقمية.
                </p>
                <div class="pt-2">
                    <a href="{{ route('portal.bookings') }}" class="inline-flex px-5 py-2.5 rounded-xl bg-cyber-cyan text-obsidian-base font-extrabold text-xs font-cairo shadow-neon-cyan">
                        حجز خدمة هندسية جديدة
                    </a>
                </div>
            </div>
        @endif
    </div>

    <!-- ===================================================================== -->
    <!-- 3. SPLIT HUD: RECENT TRANSACTIONS & SUPPORT TICKETS                   -->
    <!-- ===================================================================== -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Box 1: Recent Wallet Activity -->
        <div class="p-6 rounded-3xl glass-obsidian border border-obsidian-border space-y-5">
            <div class="flex items-center justify-between border-b border-obsidian-border/80 pb-3">
                <h4 class="text-sm font-bold text-white font-cairo flex items-center gap-2">
                    <svg class="w-4 h-4 text-cyber-cyan" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                    <span>آخر حركات المحفظة الرقمية</span>
                </h4>
                <a href="{{ route('portal.wallet') }}" class="text-xs text-cyber-cyan hover:underline font-cairo">
                    كافة الحركات ⇥
                </a>
            </div>

            @if($recentTransactions->count() > 0)
                <div class="space-y-3">
                    @foreach($recentTransactions as $tx)
                        <div class="p-3.5 rounded-xl bg-obsidian-surface border border-obsidian-border/60 flex items-center justify-between gap-3 text-xs">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center font-mono font-black text-xs
                                    {{ $tx->type === 'deposit' ? 'bg-emerald-500/15 text-emerald-400' : 'bg-rose-500/15 text-rose-400' }}">
                                    {{ $tx->type === 'deposit' ? '+' : '-' }}
                                </div>
                                <div>
                                    <div class="font-bold text-white font-mono">{{ $tx->reference_code }}</div>
                                    <div class="text-[10px] text-slate-400 font-readex">
                                        {{ $tx->type === 'deposit' ? 'شحن رصيد' : 'حجز خدمة' }} · {{ $tx->payment_method }}
                                    </div>
                                </div>
                            </div>

                            <div class="text-left font-mono">
                                <div class="font-extrabold {{ $tx->type === 'deposit' ? 'text-emerald-400' : 'text-slate-200' }}">
                                    {{ $tx->type === 'deposit' ? '+' : '-' }}{{ number_format($tx->amount, 2) }} EGP
                                </div>
                                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded
                                    @if($tx->status === 'approved') bg-emerald-500/10 text-emerald-400
                                    @elseif($tx->status === 'pending') bg-amber-500/10 text-amber-400
                                    @else bg-rose-500/10 text-rose-400
                                    @endif">
                                    {{ $tx->status === 'approved' ? 'معتمد' : ($tx->status === 'pending' ? 'قيد المراجعة' : 'مرفوض') }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-6 text-center text-xs text-slate-500 font-readex">
                    لا توجد حركات مالية مسجلة بعد.
                </div>
            @endif
        </div>

        <!-- Box 2: Recent Support Tickets -->
        <div class="p-6 rounded-3xl glass-obsidian border border-obsidian-border space-y-5">
            <div class="flex items-center justify-between border-b border-obsidian-border/80 pb-3">
                <h4 class="text-sm font-bold text-white font-cairo flex items-center gap-2">
                    <svg class="w-4 h-4 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span>آخر تذاكر الدعم والاستفسارات</span>
                </h4>
                <a href="{{ route('portal.tickets') }}" class="text-xs text-purple-300 hover:underline font-cairo">
                    كافة التذاكر ⇥
                </a>
            </div>

            @if($recentTickets->count() > 0)
                <div class="space-y-3">
                    @foreach($recentTickets as $ticket)
                        <a href="{{ route('portal.tickets.show', $ticket) }}" 
                           class="p-3.5 rounded-xl bg-obsidian-surface border border-obsidian-border/60 hover:border-slate-600 flex items-center justify-between gap-3 text-xs transition-all block">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-mono font-bold text-purple-300">{{ $ticket->ticket_number }}</span>
                                    <span class="text-white font-bold font-cairo">{{ $ticket->subject }}</span>
                                </div>
                                <div class="text-[10px] text-slate-400 font-readex mt-1">
                                    قسم: <strong class="text-slate-300">{{ $ticket->department }}</strong>
                                    · {{ $ticket->created_at->diffForHumans() }}
                                </div>
                            </div>

                            <span class="px-2 py-0.5 rounded text-[10px] font-bold shrink-0
                                @if($ticket->status === 'open') bg-cyber-cyan/15 text-cyber-cyan
                                @elseif($ticket->status === 'staff_replied') bg-cyber-gold/20 text-cyber-gold
                                @elseif($ticket->status === 'client_replied') bg-purple-500/20 text-purple-300
                                @else bg-slate-700 text-slate-300
                                @endif">
                                {{ $ticket->status === 'open' ? 'مفتوحة' : ($ticket->status === 'staff_replied' ? 'رد مهندس سولي' : ($ticket->status === 'client_replied' ? 'رد العميل' : 'مغلقة')) }}
                            </span>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="p-6 text-center text-xs text-slate-500 font-readex">
                    لا توجد تذاكر دعم فني مفتوحة حالياً. فريقنا في خدمتك دائماً!
                </div>
            @endif
        </div>

    </div>

</div>
@endsection
