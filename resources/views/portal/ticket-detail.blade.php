@extends('portal.layout')

@section('title', 'تذكرة #' . $ticket->ticket_number . ' - ' . $ticket->subject . ' | سولي')
@section('page-title', 'تذكرة #' . $ticket->ticket_number)

@section('portal-content')
<div class="space-y-6 max-w-4xl mx-auto" x-data="{
    replyText: '',
    setReply(text) {
        this.replyText = text;
        this.$nextTick(() => {
            const el = this.$refs.replyInput;
            if (el) {
                el.focus();
                el.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    }
}">

    <!-- ===================================================================== -->
    <!-- 1. TICKET HEADER & STATUS STRIP                                       -->
    <!-- ===================================================================== -->
    <div class="glass-obsidian rounded-3xl border border-obsidian-border p-5 sm:p-7 space-y-4 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-obsidian-border/80 pb-4">
            <div class="space-y-1">
                <div class="flex items-center gap-2 sm:gap-3 flex-wrap">
                    <span class="font-mono text-xs font-bold text-cyber-purple">{{ $ticket->ticket_number }}</span>
                    <span class="text-slate-600">|</span>
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
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-cairo font-bold border {{ $scopeClass }}">
                        {{ $ticket->scope_label_ar }}
                    </span>
                    @if($ticket->reference_code)
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-mono font-bold bg-obsidian-surface border border-obsidian-border text-slate-300">
                            المرجع: {{ $ticket->reference_code }}
                        </span>
                    @endif
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-mono font-bold
                        @if($ticket->priority === 'urgent') bg-rose-500/15 text-rose-400 border border-rose-500/30
                        @elseif($ticket->priority === 'high') bg-amber-500/15 text-amber-400 border border-amber-500/30
                        @else bg-cyber-cyan/15 text-cyber-cyan border border-cyber-cyan/30
                        @endif">
                        أولوية: {{ $ticket->priority }}
                    </span>
                </div>

                <h1 class="text-lg sm:text-2xl font-black text-white font-cairo pt-1">
                    {{ $ticket->subject }}
                </h1>
            </div>

            <div class="flex items-center gap-2.5 self-start sm:self-auto shrink-0">
                <span class="px-3 py-1 rounded-full text-xs font-cairo font-bold
                    @if($ticket->status === 'open') bg-cyber-cyan/15 text-cyber-cyan border border-cyber-cyan/30
                    @elseif($ticket->status === 'staff_replied') bg-cyber-gold/20 text-cyber-gold border border-cyber-gold/30 animate-pulse
                    @elseif($ticket->status === 'client_replied') bg-purple-500/20 text-purple-300 border border-purple-500/30
                    @elseif($ticket->status === 'resolved') bg-emerald-500/15 text-emerald-400 border border-emerald-500/30
                    @else bg-slate-700/50 text-slate-300 border border-slate-600
                    @endif">
                    @if($ticket->status === 'open') مفتوحة
                    @elseif($ticket->status === 'staff_replied') رد مهندس سولي
                    @elseif($ticket->status === 'client_replied') بانتظار رد المهندس
                    @elseif($ticket->status === 'resolved') تم الحل بنجاح ✓
                    @else مغلقة
                    @endif
                </span>

                <a href="{{ route('portal.tickets') }}" class="text-xs text-slate-400 hover:text-white px-3 py-1.5 rounded-xl bg-obsidian-surface border border-obsidian-border transition-colors">
                    العودة للتذاكر ⇥
                </a>
            </div>
        </div>

        @if($ticket->booking)
            <div class="p-3.5 rounded-2xl bg-obsidian-surface border border-obsidian-border flex items-center justify-between text-xs text-slate-300 font-cairo">
                <div>
                    مرتبطة بالحجز: 
                    <strong class="text-cyber-cyan font-mono">{{ $ticket->booking->booking_number }}</strong>
                    ({{ $ticket->booking->service->name }})
                </div>
                <div class="font-mono text-slate-400">
                    {{ number_format($ticket->booking->total_amount, 2) }} EGP
                </div>
            </div>
        @endif
    </div>

    <!-- ===================================================================== -->
    <!-- 2. MESSAGES THREAD (CHAT-LIKE ARCHITECTURE)                           -->
    <!-- ===================================================================== -->
    <div class="space-y-4">
        @foreach($ticket->messages as $msg)
            @php
                $isStaff = $msg->user->role !== 'client';
            @endphp

            <div class="flex flex-col {{ $isStaff ? 'items-start' : 'items-end' }}">
                <div class="max-w-2xl w-full p-4 sm:p-5 rounded-3xl space-y-3 transition-all
                    {{ $isStaff 
                        ? 'bg-obsidian-surface border border-cyber-gold/40 shadow-neon-gold text-right' 
                        : 'glass-obsidian border border-cyber-cyan/30 shadow-neon-cyan text-right' }}">
                    
                    <div class="flex items-center justify-between border-b border-obsidian-border/60 pb-2">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center font-bold text-xs
                                {{ $isStaff ? 'bg-cyber-gold/20 text-cyber-gold' : 'bg-cyber-cyan/20 text-cyber-cyan' }}">
                                {{ mb_substr($msg->user->name, 0, 1) }}
                            </div>
                            <div>
                                <span class="font-bold text-xs text-white font-cairo block">{{ $msg->user->name }}</span>
                                <span class="text-[10px] font-mono text-slate-400">
                                    {{ $isStaff ? 'مهندس دعم سولي (Souly Staff)' : 'صاحب التذكرة (Client)' }}
                                </span>
                            </div>
                        </div>

                        <span class="text-[10px] text-slate-500 font-mono">
                            {{ $msg->created_at->format('Y-m-d H:i') }}
                        </span>
                    </div>

                    <div class="text-xs sm:text-sm text-slate-200 font-cairo leading-relaxed whitespace-pre-line">
                        {{ $msg->message }}
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- ===================================================================== -->
    <!-- 3. REPLY FORM BOX WITH QUICK CHIPS                                    -->
    <!-- ===================================================================== -->
    <div class="glass-obsidian rounded-3xl border border-obsidian-border p-5 sm:p-7 space-y-4 shadow-sm">
        <h3 class="text-xs sm:text-sm font-bold text-white font-cairo flex items-center gap-2">
            <svg class="w-4 h-4 text-cyber-cyan" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
            </svg>
            <span>إضافة رد أو استفسار جديد على التذكرة</span>
        </h3>

        <!-- Smart Quick Reply Chips -->
        <div class="flex flex-wrap items-center gap-1.5 pt-1">
            <button type="button" @click="setReply('شكراً لكم، تم حل المشكلة بنجاح ويمكن إغلاق التذكرة ✓')"
                    class="px-2.5 py-1 rounded-lg bg-obsidian-surface hover:bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 text-[10px] font-cairo transition-all cursor-pointer">
                ✓ تم حل المشكلة بنجاح
            </button>
            <button type="button" @click="setReply('تم تحويل المبلغ المطلوب وإرفاق البيانات، يرجى المراجعة.')"
                    class="px-2.5 py-1 rounded-lg bg-obsidian-surface hover:bg-cyber-cyan/20 text-cyber-cyan border border-cyber-cyan/30 text-[10px] font-cairo transition-all cursor-pointer">
                💳 تم سداد المبلغ
            </button>
            <button type="button" @click="setReply('أرجو التواصل معي هاتفياً للتنسيق العاجل.')"
                    class="px-2.5 py-1 rounded-lg bg-obsidian-surface hover:bg-cyber-gold/20 text-cyber-gold border border-cyber-gold/30 text-[10px] font-cairo transition-all cursor-pointer">
                📞 أرجو الاتصال هاتفياً
            </button>
        </div>

        @if ($errors->any())
            <div class="p-3.5 rounded-xl bg-rose-500/10 border border-rose-500/40 text-xs text-rose-300 font-cairo">
                @foreach ($errors->all() as $error)
                    <div>⚠ {{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('portal.tickets.reply', $ticket) }}" method="POST" class="space-y-3.5">
            @csrf

            <textarea x-ref="replyInput" x-model="replyText" name="message" rows="4" required placeholder="اكتب ردك أو التحديث الفني هنا..."
                      class="w-full bg-obsidian-surface border border-obsidian-border rounded-2xl p-3.5 sm:p-4 text-xs sm:text-sm text-white font-cairo placeholder-slate-500 focus:outline-none focus:border-cyber-cyan leading-relaxed"></textarea>

            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <span class="text-[11px] text-slate-500 font-cairo">
                    * سيتم إشعار المهندس المسؤول وتحديث حالة التذكرة تلقائياً.
                </span>

                <button type="submit"
                        class="w-full sm:w-auto px-6 py-2.5 rounded-xl btn-citadel-primary text-xs font-cairo font-extrabold shadow-neon-cyan active:scale-95 transition-all flex items-center justify-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                    <span>إرسال الرد</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection