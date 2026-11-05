@extends('admin.layout')

@section('title', 'مركز القيادة والعمليات | سولي')
@section('page-title', 'لوحة المؤشرات والعمليات المركزية')

@section('admin-content')
<div class="space-y-8">

    <!-- ===================================================================== -->
    <!-- 1. LIVE KPIS & EXECUTIVE TELEMETRY                                    -->
    <!-- ===================================================================== -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        
        <!-- KPI 1: Total Revenue -->
        <div class="p-6 rounded-3xl glass-obsidian border border-cyber-cyan/40 relative overflow-hidden flex flex-col justify-between space-y-4">
            <div class="absolute -top-10 -right-10 w-28 h-28 bg-cyber-cyan/15 rounded-full blur-2xl pointer-events-none"></div>
            <div>
                <div class="flex items-center justify-between text-xs font-cairo text-slate-400">
                    <span>إجمالي الإيرادات المعتمدة</span>
                    <span class="w-2 h-2 rounded-full bg-cyber-cyan animate-pulse"></span>
                </div>
                <div class="flex items-baseline gap-2 mt-2">
                    <span class="text-2xl lg:text-3xl font-black font-mono text-white">
                        {{ number_format($totalApprovedRevenue, 2) }}
                    </span>
                    <span class="text-xs font-mono font-bold text-cyber-cyan">EGP</span>
                </div>
            </div>
            <div class="text-[11px] text-slate-400 font-cairo">
                عبر محفظة سولي الرقمية
            </div>
        </div>

        <!-- KPI 2: Pending Wallet Top-ups -->
        <div class="p-6 rounded-3xl glass-obsidian border border-amber-500/40 relative overflow-hidden flex flex-col justify-between space-y-4">
            <div>
                <div class="flex items-center justify-between text-xs font-cairo text-slate-400">
                    <span>إيداعات بانتظار الاعتماد</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-amber-500/20 text-amber-400 border border-amber-500/40 animate-pulse">
                        PENDING
                    </span>
                </div>
                <div class="flex items-baseline gap-2 mt-2">
                    <span class="text-2xl lg:text-3xl font-black font-mono text-amber-400">
                        {{ $pendingDepositsCount }}
                    </span>
                    <span class="text-xs font-cairo text-slate-400">طلب شحن</span>
                </div>
            </div>
            <a href="{{ route('admin.wallets.index') }}?status=pending" class="text-xs text-amber-400 font-bold hover:underline font-cairo">
                مراجعة واعتماد المبالغ ⇥
            </a>
        </div>

        <!-- KPI 3: New Bookings -->
        <div class="p-6 rounded-3xl glass-obsidian border border-obsidian-border flex flex-col justify-between space-y-4">
            <div>
                <div class="flex items-center justify-between text-xs font-cairo text-slate-400">
                    <span>طلبات الحجز الجديدة</span>
                    <div class="w-7 h-7 rounded-lg bg-cyber-gold/15 text-cyber-gold flex items-center justify-center">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="flex items-baseline gap-2 mt-2">
                    <span class="text-2xl lg:text-3xl font-black font-mono text-cyber-gold">
                        {{ $newBookingsCount }}
                    </span>
                    <span class="text-xs font-cairo text-slate-400">حجز جديد</span>
                </div>
            </div>
            <a href="{{ route('admin.bookings.index') }}?status=new" class="text-xs text-cyber-gold font-bold hover:underline font-cairo">
                جدولة الحجوزات ⇥
            </a>
        </div>

        <!-- KPI 4: Active Leads CRM -->
        <div class="p-6 rounded-3xl glass-obsidian border border-obsidian-border flex flex-col justify-between space-y-4">
            <div>
                <div class="flex items-center justify-between text-xs font-cairo text-slate-400">
                    <span>العملاء المحتملين CRM</span>
                    <div class="w-7 h-7 rounded-lg bg-cyber-purple/20 text-purple-300 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
                <div class="flex items-baseline gap-2 mt-2">
                    <span class="text-2xl lg:text-3xl font-black font-mono text-purple-300">
                        {{ $activeLeadsCount }}
                    </span>
                    <span class="text-xs font-cairo text-slate-400">عميل نشط</span>
                </div>
            </div>
            <a href="{{ route('admin.leads.index') }}" class="text-xs text-purple-300 font-bold hover:underline font-cairo">
                إدارة مسار المبيعات ⇥
            </a>
        </div>

        <!-- KPI 5: Open Tickets -->
        <div class="p-6 rounded-3xl glass-obsidian border border-obsidian-border flex flex-col justify-between space-y-4">
            <div>
                <div class="flex items-center justify-between text-xs font-cairo text-slate-400">
                    <span>تذاكر الدعم المفتوحة</span>
                    <div class="w-7 h-7 rounded-lg bg-emerald-500/15 text-emerald-400 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                        </svg>
                    </div>
                </div>
                <div class="flex items-baseline gap-2 mt-2">
                    <span class="text-2xl lg:text-3xl font-black font-mono text-emerald-400">
                        {{ $openTicketsCount }}
                    </span>
                    <span class="text-xs font-cairo text-slate-400">تذكرة مفتوحة</span>
                </div>
            </div>
            <a href="{{ route('portal.tickets') }}" class="text-xs text-emerald-400 font-bold hover:underline font-cairo">
                غرفة الردود الهندسية ⇥
            </a>
        </div>

    </div>

    <!-- ===================================================================== -->
    <!-- 2. PENDING WALLET DEPOSITS (1-CLICK APPROVE / REJECT)                 -->
    <!-- ===================================================================== -->
    <div class="rounded-3xl glass-obsidian border border-amber-500/30 p-6 sm:p-8 space-y-6">
        <div class="flex items-center justify-between border-b border-obsidian-border pb-4">
            <div>
                <h3 class="text-lg font-bold text-white font-cairo flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-400 animate-pulse"></span>
                    <span>طلبات شحن المحفظة المعلقة (تتطلب مراجعة واعتماد فوري)</span>
                </h3>
                <p class="text-xs text-slate-400 font-cairo mt-0.5">
                    الاعتماد يضيف الرصيد تلقائياً إلى محفظة العميل داخل معاملة مصرفية ذرية (Atomic DB Transaction)
                </p>
            </div>

            <a href="{{ route('admin.wallets.index') }}" class="text-xs font-bold text-amber-400 hover:underline font-cairo">
                عرض كل الحسابات ⇥
            </a>
        </div>

        @if($recentPendingDeposits->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs">
                    <thead>
                        <tr class="border-b border-obsidian-border text-slate-400 font-cairo">
                            <th class="py-3 px-3">الرقم المرجعي</th>
                            <th class="py-3 px-3">العميل</th>
                            <th class="py-3 px-3">المبلغ المطلوب</th>
                            <th class="py-3 px-3">وسيلة التحويل</th>
                            <th class="py-3 px-3">رقم المحفظة / الحساب</th>
                            <th class="py-3 px-3">الإيصال</th>
                            <th class="py-3 px-3 text-center">الإجراء الفوري</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-obsidian-border/50">
                        @foreach($recentPendingDeposits as $tx)
                            <tr class="hover:bg-obsidian-surface/60 transition-colors">
                                <td class="py-3.5 px-3 text-cyber-cyan font-bold font-mono">{{ $tx->reference_code }}</td>
                                <td class="py-3.5 px-3 font-cairo">
                                    <div class="font-bold text-white">{{ $tx->user->name }}</div>
                                    <div class="text-[10px] text-slate-400 font-mono">{{ $tx->user->email }}</div>
                                </td>
                                <td class="py-3.5 px-3 font-bold text-emerald-400 text-sm font-mono">
                                    {{ number_format($tx->amount, 2) }} EGP
                                </td>
                                <td class="py-3.5 px-3 font-cairo text-slate-300">
                                    {{ $tx->payment_method }}
                                </td>
                                <td class="py-3.5 px-3 text-slate-300 font-mono">{{ $tx->sender_number_or_account }}</td>
                                <td class="py-3.5 px-3 font-cairo">
                                    @if($tx->receipt_path)
                                        <a href="{{ asset('storage/' . $tx->receipt_path) }}" target="_blank" class="text-cyber-cyan hover:underline text-[11px]">
                                            معاينة الإيصال ↗
                                        </a>
                                    @else
                                        <span class="text-slate-500 text-[10px]">بدون ملف</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-3">
                                    <div class="flex items-center justify-center gap-2">
                                        <!-- Approve Button Form -->
                                        <form action="{{ route('admin.wallets.approve', $tx) }}" method="POST">
                                            @csrf
                                            <button type="submit" 
                                                    onclick="return confirm('هل أنت متأكد من اعتماد إضافة {{ $tx->amount }} EGP إلى محفظة العميل؟')"
                                                    class="px-3 py-1.5 rounded-lg bg-emerald-500/20 hover:bg-emerald-500 border border-emerald-500/40 hover:border-emerald-500 text-emerald-400 hover:text-obsidian-base font-bold text-xs font-cairo transition-all">
                                                اعتماد وإضافة الرصيد ✓
                                            </button>
                                        </form>

                                        <!-- Reject Button Form -->
                                        <form action="{{ route('admin.wallets.reject', $tx) }}" method="POST">
                                            @csrf
                                            <button type="submit" 
                                                    onclick="return confirm('هل أنت متأكد من رفض هذا الطلب؟')"
                                                    class="px-2.5 py-1.5 rounded-lg bg-rose-500/20 hover:bg-rose-500 border border-rose-500/40 text-rose-400 hover:text-white font-bold text-xs font-cairo transition-all">
                                                رفض ✕
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-8 text-center text-xs text-slate-400 font-cairo">
                ✓ لا توجد أي طلبات شحن معلقة حالياً. كافة المحافظ محدثة بنجاح.
            </div>
        @endif
    </div>

    <!-- ===================================================================== -->
    <!-- 3. SPLIT HUD: RECENT BOOKINGS & RECENT LEADS                          -->
    <!-- ===================================================================== -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Box 1: Recent Bookings -->
        <div class="p-6 rounded-3xl glass-obsidian border border-obsidian-border space-y-5">
            <div class="flex items-center justify-between border-b border-obsidian-border pb-3">
                <h4 class="text-sm font-bold text-white font-cairo flex items-center gap-2">
                    <svg class="w-4 h-4 text-cyber-cyan" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <span>أحدث الحجوزات الهندسية</span>
                </h4>
                <a href="{{ route('admin.bookings.index') }}" class="text-xs text-cyber-cyan hover:underline font-cairo">
                    إدارة الحجوزات ⇥
                </a>
            </div>

            <div class="space-y-3">
                @foreach($recentBookings as $b)
                    <div class="p-3.5 rounded-xl bg-obsidian-surface border border-obsidian-border/60 flex items-center justify-between gap-3 text-xs">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-mono font-bold text-cyber-cyan">{{ $b->booking_number }}</span>
                                <span class="text-white font-bold font-cairo">{{ $b->service->name }}</span>
                            </div>
                            <div class="text-[10px] text-slate-400 font-cairo mt-0.5">
                                العميل: <strong class="text-slate-300">{{ $b->user->name }}</strong>
                                · {{ number_format($b->total_amount, 2) }} EGP
                            </div>
                        </div>

                        <span class="px-2.5 py-0.5 rounded text-[10px] font-bold font-cairo
                            @if($b->status === 'confirmed') bg-cyber-cyan/15 text-cyber-cyan
                            @elseif($b->status === 'in_progress') bg-cyber-gold/20 text-cyber-gold
                            @elseif($b->status === 'completed') bg-emerald-500/15 text-emerald-400
                            @else bg-slate-700 text-slate-300
                            @endif">
                            {{ $b->status }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Box 2: Recent CRM Leads -->
        <div class="p-6 rounded-3xl glass-obsidian border border-obsidian-border space-y-5">
            <div class="flex items-center justify-between border-b border-obsidian-border pb-3">
                <h4 class="text-sm font-bold text-white font-cairo flex items-center gap-2">
                    <svg class="w-4 h-4 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span>أحدث العملاء المحتملين (Leads)</span>
                </h4>
                <a href="{{ route('admin.leads.index') }}" class="text-xs text-purple-300 hover:underline font-cairo">
                    متابعة الـ CRM ⇥
                </a>
            </div>

            <div class="space-y-3">
                @foreach($recentLeads as $lead)
                    <a href="{{ route('admin.leads.show', $lead) }}" 
                       class="p-3.5 rounded-xl bg-obsidian-surface border border-obsidian-border/60 hover:border-slate-600 flex items-center justify-between gap-3 text-xs transition-all block">
                        <div>
                            <div class="font-bold text-white font-cairo">{{ $lead->name }}</div>
                            <div class="text-[10px] text-slate-400 font-mono mt-0.5">
                                {{ $lead->phone }} · {{ $lead->source }}
                            </div>
                        </div>

                        <span class="px-2 py-0.5 rounded text-[10px] font-bold font-cairo
                            @if($lead->status === 'new') bg-cyber-cyan/15 text-cyber-cyan
                            @elseif($lead->status === 'qualified') bg-emerald-500/15 text-emerald-400
                            @elseif($lead->status === 'contacted') bg-amber-500/15 text-amber-400
                            @else bg-slate-700 text-slate-300
                            @endif">
                            {{ $lead->status }}
                        </span>
                    </a>
                @endforeach
            </div>
        </div>

    </div>

    <!-- ===================================================================== -->
    <!-- 4. RECENT SUPPORT & HARDWARE WARRANTY TICKETS HUD                     -->
    <!-- ===================================================================== -->
    <div class="p-6 rounded-3xl glass-obsidian border border-obsidian-border space-y-5">
        <div class="flex items-center justify-between border-b border-obsidian-border pb-3">
            <h4 class="text-sm font-bold text-white font-cairo flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <span>أحدث تذاكر الدعم الفني، المتاجر والضمان</span>
            </h4>
            <a href="{{ route('portal.tickets') }}" class="text-xs text-emerald-400 hover:underline font-cairo">
                كافة التذاكر ⇥
            </a>
        </div>

        @if($recentTickets->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs font-cairo">
                    <thead>
                        <tr class="border-b border-obsidian-border text-slate-400 font-bold">
                            <th class="py-2.5 px-3">رقم التذكرة</th>
                            <th class="py-2.5 px-3">العميل</th>
                            <th class="py-2.5 px-3">القسم / المتجر</th>
                            <th class="py-2.5 px-3">المرجع</th>
                            <th class="py-2.5 px-3">الموضوع</th>
                            <th class="py-2.5 px-3">الحالة</th>
                            <th class="py-2.5 px-3 text-center">إجراء</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-obsidian-border/50 text-slate-200">
                        @foreach($recentTickets as $ticket)
                            <tr class="hover:bg-obsidian-surface/60 transition-colors">
                                <td class="py-3 px-3 font-mono font-bold text-cyber-purple">{{ $ticket->ticket_number }}</td>
                                <td class="py-3 px-3 font-bold text-white">{{ $ticket->user->name ?? 'غير معروف' }}</td>
                                <td class="py-3 px-3">
                                    @php
                                        $scopeClass = match ($ticket->department_scope) {
                                            'branderp_store'    => 'bg-cyan-500/15 text-cyan-300 border-cyan-500/30',
                                            'hardware_store'    => 'bg-amber-500/15 text-amber-300 border-amber-500/30',
                                            'services_bookings' => 'bg-purple-500/15 text-purple-300 border-purple-500/30',
                                            'academy_courses'   => 'bg-emerald-500/15 text-emerald-300 border-emerald-500/30',
                                            'wallet_billing'    => 'bg-yellow-500/15 text-yellow-300 border-yellow-500/30',
                                            default             => 'bg-slate-700/50 text-slate-300 border-slate-600',
                                        };
                                    @endphp
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-cairo font-bold border {{ $scopeClass }}">
                                        {{ $ticket->scope_label_ar }}
                                    </span>
                                </td>
                                <td class="py-3 px-3 font-mono text-slate-400 text-[11px]">
                                    {{ $ticket->reference_code ?: '-' }}
                                </td>
                                <td class="py-3 px-3 max-w-xs truncate">{{ $ticket->subject }}</td>
                                <td class="py-3 px-3">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold
                                        @if($ticket->status === 'open') bg-cyber-cyan/15 text-cyber-cyan
                                        @elseif($ticket->status === 'staff_replied') bg-cyber-gold/20 text-cyber-gold
                                        @elseif($ticket->status === 'resolved') bg-emerald-500/15 text-emerald-400
                                        @else bg-slate-700 text-slate-300
                                        @endif">
                                        {{ $ticket->status }}
                                    </span>
                                </td>
                                <td class="py-3 px-3 text-center">
                                    <a href="{{ route('portal.tickets.show', $ticket) }}" class="px-3 py-1 rounded-lg bg-cyber-cyan/15 hover:bg-cyber-cyan border border-cyber-cyan/30 text-cyber-cyan hover:text-obsidian-base font-bold text-[11px] transition-all">
                                        عرض والرد ⇥
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-6 text-center text-xs text-slate-400 font-cairo">
                ✓ لا توجد تذاكر دعم مسجلة حالياً.
            </div>
        @endif
    </div>

</div>
@endsection
