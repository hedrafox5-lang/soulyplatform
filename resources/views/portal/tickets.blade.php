@extends('portal.layout')

@section('title', 'تذاكر الدعم الفني والاستشارات | سولي ديدجيتال سيتادل')
@section('page-title', 'تذاكر الدعم الفني والاستشارات')

@section('portal-content')
<div class="space-y-8" x-data="{
    openTicketModal: {{ request()->query('booking_id') ? 'true' : 'false' }},
    preselectedBooking: '{{ request()->query('booking_id') }}',
    preselectedDept: '{{ request()->query('department') ?? 'maintenance' }}'
}">

    <!-- ===================================================================== -->
    <!-- 1. TOP HEADER & NEW TICKET TRIGGER                                    -->
    <!-- ===================================================================== -->
    <div class="glass-obsidian rounded-3xl border border-obsidian-border p-6 sm:p-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="space-y-1">
            <h2 class="text-xl sm:text-2xl font-black text-white font-cairo flex items-center gap-2.5">
                <span class="w-3 h-3 rounded-full bg-cyber-purple animate-pulse"></span>
                <span>تذاكر الدعم الهندسي والاستشارات الفنية</span>
            </h2>
            <p class="text-xs text-slate-400 font-readex">
                تواصل مباشر ومشفر مع مهندسي واستشاريي قلعة سولي لمتابعة منظوماتك أو الإبلاغ عن أي استفسار طارئ.
            </p>
        </div>

        <button type="button" @click="openTicketModal = true"
                class="px-6 py-3 rounded-2xl bg-gradient-to-r from-cyber-purple via-cyber-cyan to-cyber-gold text-obsidian-base font-extrabold text-xs sm:text-sm font-cairo shadow-neon-purple hover:scale-105 active:scale-95 transition-all flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            <span>+ فتح تذكرة دعم جديدة</span>
        </button>
    </div>

    @if ($errors->any())
        <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/40 text-xs text-rose-300 font-readex space-y-1">
            @foreach ($errors->all() as $error)
                <div class="flex items-center gap-2">
                    <span class="text-rose-400 font-bold">⚠</span>
                    <span>{{ $error }}</span>
                </div>
            @endforeach
        </div>
    @endif

    <!-- ===================================================================== -->
    <!-- 2. TICKETS LIST GRID                                                  -->
    <!-- ===================================================================== -->
    @if($tickets->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($tickets as $t)
                <a href="{{ route('portal.tickets.show', $t) }}" 
                   class="p-6 rounded-3xl glass-obsidian border border-obsidian-border hover:border-cyber-purple/50 transition-all space-y-4 block group">
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="font-mono text-xs font-bold text-cyber-purple">{{ $t->ticket_number }}</span>
                            <span class="text-slate-600">|</span>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono uppercase bg-obsidian-surface border border-obsidian-border text-slate-300">
                                {{ $t->department }}
                            </span>
                        </div>

                        <span class="px-2.5 py-1 rounded-full text-xs font-cairo font-bold
                            @if($t->status === 'open') bg-cyber-cyan/15 text-cyber-cyan border border-cyber-cyan/30
                            @elseif($t->status === 'staff_replied') bg-cyber-gold/20 text-cyber-gold border border-cyber-gold/30 animate-pulse
                            @elseif($t->status === 'client_replied') bg-purple-500/20 text-purple-300 border border-purple-500/30
                            @elseif($t->status === 'resolved') bg-emerald-500/15 text-emerald-400 border border-emerald-500/30
                            @else bg-slate-700/50 text-slate-300 border border-slate-600
                            @endif">
                            @if($t->status === 'open') تذكرة مفتوحة
                            @elseif($t->status === 'staff_replied') رد جديد من مهندس سولي
                            @elseif($t->status === 'client_replied') بانتظار رد المهندس
                            @elseif($t->status === 'resolved') تم الحل بنجاح
                            @else مغلقة
                            @endif
                        </span>
                    </div>

                    <div>
                        <h3 class="text-base font-bold text-white font-cairo group-hover:text-cyber-cyan transition-colors">
                            {{ $t->subject }}
                        </h3>
                        @if($t->booking)
                            <div class="text-[11px] text-slate-400 font-readex mt-1">
                                مرتبط بحجز: <strong class="text-slate-200 font-mono">{{ $t->booking->booking_number }}</strong> ({{ $t->booking->service->name }})
                            </div>
                        @endif
                    </div>

                    <div class="pt-3 border-t border-obsidian-border/60 flex items-center justify-between text-xs text-slate-400 font-readex">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full 
                                @if($t->priority === 'urgent') bg-rose-500
                                @elseif($t->priority === 'high') bg-amber-500
                                @else bg-cyber-cyan
                                @endif"></span>
                            <span>أولوية: 
                                @if($t->priority === 'urgent') طارئة جداً
                                @elseif($t->priority === 'high') مرتفعة
                                @elseif($t->priority === 'medium') متوسطة
                                @else عادية
                                @endif
                            </span>
                        </div>

                        <span class="text-[11px] text-slate-500 font-mono">
                            {{ $t->created_at->diffForHumans() }}
                        </span>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="pt-4">
            {{ $tickets->links() }}
        </div>
    @else
        <div class="p-12 text-center rounded-3xl glass-obsidian border border-obsidian-border space-y-4">
            <div class="w-14 h-14 rounded-2xl bg-cyber-purple/20 text-purple-300 flex items-center justify-center mx-auto">
                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
            </div>
            <div class="text-base font-bold text-white font-cairo">لا توجد أي تذاكر دعم فني مسجلة حتى الآن</div>
            <p class="text-xs text-slate-400 font-readex max-w-md mx-auto">
                إذا واجهتك أي مشكلة تقنية أو أردت استشارة متخصصة بخصوص أنظمتك، فريق الدعم الهندسي في قلعة سولي متاح على مدار الساعة.
            </p>
            <div class="pt-2">
                <button type="button" @click="openTicketModal = true"
                        class="px-6 py-3 rounded-2xl bg-cyber-purple text-white font-extrabold text-xs font-cairo shadow-neon-purple">
                    فتح أول تذكرة دعم فني
                </button>
            </div>
        </div>
    @endif

    <!-- ===================================================================== -->
    <!-- 3. OPEN NEW TICKET MODAL                                              -->
    <!-- ===================================================================== -->
    <div x-show="openTicketModal" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 bg-black/80 backdrop-blur-md flex items-center justify-center p-4 overflow-y-auto"
         style="display: none;">
        
        <div @click.away="openTicketModal = false"
             class="relative w-full max-w-xl glass-obsidian border border-cyber-purple/40 rounded-3xl p-6 sm:p-8 space-y-6 shadow-neon-purple max-h-[90vh] overflow-y-auto">
            
            <div class="flex items-center justify-between border-b border-obsidian-border pb-4">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-cyber-purple animate-pulse"></span>
                    <h3 class="text-xl font-black text-white font-cairo">فتح تذكرة دعم فني أو استشارة</h3>
                </div>
                <button type="button" @click="openTicketModal = false" class="p-2 text-slate-400 hover:text-white rounded-lg">
                    ✕
                </button>
            </div>

            <form action="{{ route('portal.tickets.store') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Subject -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-300 font-readex">عنوان التذكرة أو الموضوع الرئيسي</label>
                    <input type="text" name="subject" required placeholder="مثال: استفسار حول إعدادات الفاتورة الإلكترونية في BrandERP"
                           class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-4 py-3 text-xs sm:text-sm text-white placeholder-slate-500 focus:outline-none focus:border-cyber-purple font-cairo">
                </div>

                <!-- Department & Priority -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-300 font-readex">القسم الهندسي المختص</label>
                        <select name="department" required
                                class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-4 py-3 text-xs text-white font-cairo focus:outline-none focus:border-cyber-purple">
                            <option value="erp">نظام BrandERP ومقرات البيع</option>
                            <option value="ai_agents">موظفو الذكاء الاصطناعي (AI)</option>
                            <option value="cybersecurity">التدريع والأمن السيبراني</option>
                            <option value="maintenance">الصيانة الميدانية والأجهزة</option>
                            <option value="consulting">الاستشارات والتحول الرقمي</option>
                            <option value="academy">الأكاديمية والتدريب التقني</option>
                            <option value="billing">الحسابات والمحفظة الرقمية</option>
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-300 font-readex">درجة الأولوية</label>
                        <select name="priority" required
                                class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-4 py-3 text-xs text-white font-cairo focus:outline-none focus:border-cyber-purple">
                            <option value="medium">متوسطة (استفسار عادي)</option>
                            <option value="low">منخفضة (اقتراح / تحسين)</option>
                            <option value="high">مرتفعة (خلل يؤثر على العمل)</option>
                            <option value="urgent">طارئة جداً (توقف كلي للخدمة)</option>
                        </select>
                    </div>
                </div>

                <!-- Linked Booking (Optional) -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-300 font-readex">ربط التذكرة بحجز سابق (اختياري)</label>
                    <select name="booking_id"
                            class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-4 py-3 text-xs text-white font-cairo focus:outline-none focus:border-cyber-purple">
                        <option value="">-- بدون ربط بحجز محدد --</option>
                        @foreach($userBookings as $ub)
                            <option value="{{ $ub->id }}" {{ request()->query('booking_id') == $ub->id ? 'selected' : '' }}>
                                {{ $ub->booking_number }} — {{ $ub->service->name }} ({{ number_format($ub->total_amount, 2) }} EGP)
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Message -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-300 font-readex">تفاصيل المشكلة أو الاستفسار</label>
                    <textarea name="message" rows="5" required placeholder="اشرح بالتفصيل ما ترغب في إنجازه، أو المشكلة التي واجهتك، لتتمكن هندسة سولي من معالجتها في أسرع وقت..."
                              class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl p-3.5 text-xs text-white font-readex placeholder-slate-500 focus:outline-none focus:border-cyber-purple leading-relaxed"></textarea>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                        class="w-full py-4 px-6 rounded-2xl bg-gradient-to-r from-cyber-purple via-cyber-cyan to-cyber-gold text-obsidian-base font-extrabold text-sm font-cairo shadow-neon-purple hover:scale-[1.01] active:scale-95 transition-all flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                    <span>إرسال التذكرة لفريق الدعم الهندسي</span>
                </button>
            </form>
        </div>
    </div>

</div>
@endsection
