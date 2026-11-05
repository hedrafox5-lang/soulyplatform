@extends('portal.layout')

@section('title', 'تذاكر الدعم الفني والاستشارات | سولي')
@section('page-title', 'تذاكر الدعم الفني والاستشارات')

@section('portal-content')
<div class="space-y-6 sm:space-y-8" x-data="{
    openTicketModal: {{ (request()->query('booking_id') || request()->query('scope') || request()->query('ref')) ? 'true' : 'false' }},
    preselectedBooking: '{{ request()->query('booking_id') }}',
    preselectedScope: '{{ request()->query('scope') ?? 'general' }}',
    preselectedRef: '{{ request()->query('ref') ?? '' }}',
    openModal() {
        this.openTicketModal = true;
        if (window.SoulyUI) window.SoulyUI.lockScroll('ticket-modal');
    },
    closeModal() {
        this.openTicketModal = false;
        if (window.SoulyUI) window.SoulyUI.unlockScroll('ticket-modal');
    }
}">

    <!-- 1. TOP HEADER & NEW TICKET TRIGGER -->
    <div class="glass-obsidian rounded-3xl border border-obsidian-border p-5 sm:p-7 lg:p-8 flex flex-col md:flex-row md:items-center justify-between gap-5 sm:gap-6 shadow-md">
        <div class="space-y-1">
            <h2 class="text-lg sm:text-2xl font-black text-white font-cairo flex items-center gap-2.5">
                <span class="w-3 h-3 rounded-full bg-cyber-purple animate-pulse"></span>
                <span>تذاكر الدعم الهندسي والاستشارات الفنية</span>
            </h2>
            <p class="text-xs sm:text-sm text-slate-400 font-cairo">
                تواصل مباشر ومشفر مع مهندسي واستشاريي قلعة سولي لمتابعة أنظمتك وتفعيل الضمان الرسمي.
            </p>
        </div>

        <button type="button" @click="openModal()"
                class="px-6 py-3 rounded-2xl btn-citadel-primary text-xs sm:text-sm font-cairo font-extrabold shadow-neon-purple shrink-0 active:scale-95 transition-all flex items-center justify-center gap-2 cursor-pointer">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            <span>+ فتح تذكرة دعم أو ضمان</span>
        </button>
    </div>

    @if ($errors->any())
        <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/40 text-xs text-rose-300 font-cairo space-y-1">
            @foreach ($errors->all() as $error)
                <div class="flex items-center gap-2">
                    <span class="text-rose-400 font-bold">⚠</span>
                    <span>{{ $error }}</span>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Department Scope Pills -->
    <div class="flex items-center gap-1.5 sm:gap-2 overflow-x-auto scrollbar-none pb-2">
        <a href="{{ route('portal.tickets') }}" 
           class="px-4 py-2 rounded-xl text-xs font-cairo font-bold transition-all shrink-0 {{ !request('scope') ? 'bg-cyber-cyan text-obsidian-base font-black shadow-neon-cyan' : 'glass-obsidian border border-obsidian-border text-slate-300 hover:text-white' }}">
            كافة الأقسام
        </a>
        <a href="{{ route('portal.tickets', ['scope' => 'branderp_store']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-cairo font-bold transition-all shrink-0 {{ request('scope') === 'branderp_store' ? 'bg-cyber-cyan text-obsidian-base font-black shadow-neon-cyan' : 'glass-obsidian border border-obsidian-border text-slate-300 hover:text-white' }}">
            🏪 منظومة BrandERP
        </a>
        <a href="{{ route('portal.tickets', ['scope' => 'hardware_store']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-cairo font-bold transition-all shrink-0 {{ request('scope') === 'hardware_store' ? 'bg-amber-400 text-obsidian-base font-black shadow-lg shadow-amber-400/20' : 'glass-obsidian border border-obsidian-border text-slate-300 hover:text-white' }}">
            🖥️ متجر الهاردوير والضمان
        </a>
        <a href="{{ route('portal.tickets', ['scope' => 'services_bookings']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-cairo font-bold transition-all shrink-0 {{ request('scope') === 'services_bookings' ? 'bg-cyber-purple text-white font-black shadow-neon-purple' : 'glass-obsidian border border-obsidian-border text-slate-300 hover:text-white' }}">
            ⚡ الحجوزات والخدمات
        </a>
        <a href="{{ route('portal.tickets', ['scope' => 'wallet_billing']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-cairo font-bold transition-all shrink-0 {{ request('scope') === 'wallet_billing' ? 'bg-cyber-gold text-obsidian-base font-black shadow-neon-gold' : 'glass-obsidian border border-obsidian-border text-slate-300 hover:text-white' }}">
            💳 المحفظة والمدفوعات
        </a>
    </div>

    <!-- 2. TICKETS LIST GRID -->
    @if($tickets->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($tickets as $t)
                <a href="{{ route('portal.tickets.show', $t) }}" 
                   class="p-5 sm:p-6 rounded-3xl glass-obsidian border border-obsidian-border hover:border-cyber-purple/50 transition-all space-y-4 block group shadow-sm">
                    
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="font-mono text-xs font-bold text-cyber-purple">{{ $t->ticket_number }}</span>
                            
                            @php
                                $scopeClass = match ($t->department_scope) {
                                    'branderp_store'    => 'bg-cyan-500/15 text-cyan-300 border-cyan-500/30',
                                    'hardware_store'    => 'bg-amber-500/15 text-amber-300 border-amber-500/30',
                                    'services_bookings' => 'bg-purple-500/15 text-purple-300 border-purple-500/30',
                                    'academy_courses'   => 'bg-emerald-500/15 text-emerald-300 border-emerald-500/30',
                                    'wallet_billing'    => 'bg-yellow-500/15 text-yellow-300 border-yellow-500/30',
                                    default             => 'bg-slate-700/50 text-slate-300 border-slate-600',
                                };
                                $scopeLabel = $t->scope_label_ar ?? match ($t->department_scope) {
                                    'branderp_store'    => 'منظومة BrandERP',
                                    'hardware_store'    => 'متجر الهاردوير والضمان',
                                    'services_bookings' => 'الحجوزات والخدمات',
                                    'wallet_billing'    => 'المحفظة والمدفوعات',
                                    default             => 'دعم فني عام',
                                };
                                $priorityAr = match ($t->priority) {
                                    'urgent' => 'طارئة جداً',
                                    'high'   => 'أولوية مرتفعة',
                                    'low'    => 'أولوية منخفضة',
                                    default  => 'أولوية متوسطة',
                                };
                            @endphp
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-cairo font-bold border {{ $scopeClass }}">
                                {{ $scopeLabel }}
                            </span>
                        </div>

                        <span class="px-2.5 py-0.5 rounded-full text-[10px] sm:text-xs font-cairo font-bold shrink-0
                            @if($t->status === 'open') bg-cyber-cyan/15 text-cyber-cyan border border-cyber-cyan/30
                            @elseif($t->status === 'staff_replied') bg-cyber-gold/20 text-cyber-gold border border-cyber-gold/30 animate-pulse
                            @elseif($t->status === 'client_replied') bg-purple-500/20 text-purple-300 border border-purple-500/30
                            @elseif($t->status === 'resolved') bg-emerald-500/15 text-emerald-400 border border-emerald-500/30
                            @else bg-slate-700/50 text-slate-300 border border-slate-600
                            @endif">
                            @if($t->status === 'open') مفتوحة
                            @elseif($t->status === 'staff_replied') رد مهندس سولي
                            @elseif($t->status === 'client_replied') بانتظار الرد
                            @elseif($t->status === 'resolved') تم الحل ✓
                            @else مغلقة
                            @endif
                        </span>
                    </div>

                    <div>
                        <h3 class="text-sm sm:text-base font-bold text-white font-cairo group-hover:text-cyber-cyan transition-colors">
                            {{ $t->subject }}
                        </h3>
                        @if($t->booking)
                            <div class="text-[11px] text-slate-400 font-cairo mt-1">
                                حجز: <strong class="text-slate-200 font-mono">{{ $t->booking->booking_number }}</strong> ({{ $t->booking->service?->name ?? 'خدمة هندسية' }})
                            </div>
                        @endif
                    </div>

                    <div class="pt-3 border-t border-obsidian-border/60 flex items-center justify-between text-xs text-slate-400 font-cairo">
                        <div class="flex items-center gap-1.5 text-[11px]">
                            <span class="w-2 h-2 rounded-full 
                                @if($t->priority === 'urgent') bg-rose-500
                                @elseif($t->priority === 'high') bg-amber-500
                                @else bg-cyber-cyan
                                @endif"></span>
                            <span>{{ $priorityAr }}</span>
                        </div>

                        <span class="text-[10px] text-slate-500 font-mono">
                            {{ $t->created_at ? $t->created_at->diffForHumans() : '' }}
                        </span>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="pt-4">
            {{ $tickets->links() }}
        </div>
    @else
        <div class="p-10 text-center rounded-3xl glass-obsidian border border-obsidian-border space-y-3">
            <div class="text-sm font-bold text-white font-cairo">لا توجد أي تذاكر دعم فني مسجلة في هذا القسم</div>
            <p class="text-xs text-slate-400 font-cairo max-w-md mx-auto">
                إذا واجهتك أي مشكلة تقنية أو رغبت في متابعة الضمان، مهندسو سولي متاحون على مدار الساعة.
            </p>
            <div class="pt-2">
                <button type="button" @click="openModal()"
                        class="px-5 py-2.5 rounded-xl btn-citadel-primary text-xs font-cairo font-bold cursor-pointer">
                    فتح تذكرة دعم فني أو ضمان
                </button>
            </div>
        </div>
    @endif

    <!-- 3. OPEN NEW TICKET MODAL -->
    <div x-show="openTicketModal" 
         x-transition.opacity
         class="fixed inset-0 z-50 bg-black/85 backdrop-blur-md flex items-end sm:items-center justify-center p-0 sm:p-4 overflow-y-auto"
         @click.self="closeModal()"
         style="display: none;">
        
        <div @click.stop
             x-show="openTicketModal"
             x-transition
             class="relative w-full max-w-xl glass-obsidian border-t sm:border border-cyber-purple/40 rounded-t-3xl sm:rounded-3xl shadow-[0_10px_60px_rgba(0,0,0,0.95)] max-h-[90dvh] sm:max-h-[90vh] flex flex-col overflow-hidden">
            
            <div class="w-12 h-1.5 bg-slate-600 rounded-full mx-auto my-2.5 sm:hidden shrink-0"></div>

            <div class="flex items-center justify-between border-b border-obsidian-border