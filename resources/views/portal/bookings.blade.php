@extends('portal.layout')

@section('title', 'إدارة الحجوزات والخدمات الهندسية | سولي')
@section('page-title', 'إدارة الحجوزات وجلسات الصيانة')

@section('portal-content')
<div class="space-y-8" x-data="bookingManagerController({
    services: {{ Js::from($services) }},
    walletBalance: {{ (float) ($wallet->balance ?? 0) }},
    preselectedService: {{ Js::from(request()->query('service', '')) }},
    preselectedPackage: {{ Js::from(request()->query('package', '')) }}
})">

    <!-- ===================================================================== -->
    <!-- 1. TOP CONTROLS & NEW BOOKING ACTION                                  -->
    <!-- ===================================================================== -->
    <div class="glass-obsidian rounded-3xl border border-obsidian-border p-6 sm:p-8 flex flex-col md:flex-row md:items-center justify-between gap-6 shadow-sm">
        <div class="space-y-1 text-right">
            <h2 class="text-xl sm:text-2xl font-black text-white font-cairo flex items-center gap-2.5">
                <span class="w-3 h-3 rounded-full bg-cyber-cyan animate-pulse"></span>
                <span>الحجوزات والخدمات الهندسية المعتمدة</span>
            </h2>
            <p class="text-xs text-slate-400 font-cairo">
                استعرض حالة حجوزاتك، تحكم في جلسات الصيانة الميدانية الحية، أو قم بجدولة خدمة جديدة فودياً.
            </p>
        </div>

        <button type="button" @click="openBookingModal = true"
                class="px-6 py-3 rounded-2xl btn-citadel-primary text-xs sm:text-sm font-cairo font-bold flex items-center justify-center gap-2 shadow-neon-cyan cursor-pointer">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            <span>+ حجز خدمة هندسية جديدة</span>
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

    <!-- ===================================================================== -->
    <!-- 2. BOOKINGS CARDS LIST & STATUS FILTERS                               -->
    <!-- ===================================================================== -->
    <div class="space-y-6">
        
        <!-- Status Filter Tabs -->
        <div class="flex items-center gap-2 overflow-x-auto pb-2 text-xs font-cairo font-bold scrollbar-none">
            <a href="{{ route('portal.bookings') }}" 
               class="px-4 py-2 rounded-xl transition-all shrink-0
               {{ !request()->filled('status') ? 'bg-cyber-cyan text-obsidian-base shadow-neon-cyan' : 'bg-obsidian-surface text-slate-400 border border-obsidian-border hover:text-white' }}">
                كافة الطلبات ({{ $bookings->total() }})
            </a>
            <a href="{{ route('portal.bookings') }}?status=new" 
               class="px-4 py-2 rounded-xl transition-all shrink-0
               {{ request()->query('status') === 'new' ? 'bg-cyber-cyan text-obsidian-base shadow-neon-cyan' : 'bg-obsidian-surface text-slate-400 border border-obsidian-border hover:text-white' }}">
                طلبات جديدة
            </a>
            <a href="{{ route('portal.bookings') }}?status=confirmed" 
               class="px-4 py-2 rounded-xl transition-all shrink-0
               {{ request()->query('status') === 'confirmed' ? 'bg-cyber-cyan text-obsidian-base shadow-neon-cyan' : 'bg-obsidian-surface text-slate-400 border border-obsidian-border hover:text-white' }}">
                مؤكدة
            </a>
            <a href="{{ route('portal.bookings') }}?status=in_progress" 
               class="px-4 py-2 rounded-xl transition-all shrink-0
               {{ request()->query('status') === 'in_progress' ? 'bg-cyber-cyan text-obsidian-base shadow-neon-cyan' : 'bg-obsidian-surface text-slate-400 border border-obsidian-border hover:text-white' }}">
                قيد التنفيذ المباشر
            </a>
            <a href="{{ route('portal.bookings') }}?status=completed" 
               class="px-4 py-2 rounded-xl transition-all shrink-0
               {{ request()->query('status') === 'completed' ? 'bg-cyber-cyan text-obsidian-base shadow-neon-cyan' : 'bg-obsidian-surface text-slate-400 border border-obsidian-border hover:text-white' }}">
                مكتملة ومضمونة
            </a>
        </div>

        @if($bookings->count() > 0)
            <div class="grid grid-cols-1 gap-5">
                @foreach($bookings as $b)
                    <div class="p-6 rounded-3xl glass-obsidian border border-obsidian-border hover:border-slate-700 transition-all space-y-5">
                        
                        <!-- Top Metadata -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="space-y-1">
                                <div class="flex items-center gap-3">
                                    <span class="font-mono text-sm font-bold text-cyber-cyan">{{ $b->booking_number }}</span>
                                    <span class="text-slate-600">|</span>
                                    <h3 class="text-base font-bold text-white font-cairo">{{ $b->service->name }}</h3>
                                    @if($b->package)
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-mono bg-obsidian-surface border border-obsidian-border text-cyber-gold">
                                            {{ $b->package->name }}
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="text-xs text-slate-400 font-cairo flex items-center gap-4 flex-wrap">
                                    <span>الموعد: <strong class="text-slate-200 font-mono">{{ $b->scheduled_at ? $b->scheduled_at->format('Y-m-d') : 'مفتوح للتنسيق' }}</strong></span>
                                    <span>الساعات / الوحدات: <strong class="text-slate-200 font-mono">{{ $b->quantity_or_hours }}</strong></span>
                                    <span>طريقة الدفع: <strong class="text-slate-200 font-cairo">{{ $b->payment_method === 'wallet' ? 'محفظة سولي' : ($b->payment_method === 'direct_transfer' ? 'تحويل مباشر' : 'دفع ميداني') }}</strong></span>
                                </div>
                            </div>

                            <!-- Badges -->
                            <div class="flex items-center gap-2">
                                <span class="px-3 py-1 rounded-full text-xs font-mono font-bold
                                    {{ $b->payment_status === 'paid' ? 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30' : 'bg-amber-500/15 text-amber-400 border border-amber-500/30' }}">
                                    {{ $b->payment_status === 'paid' ? 'مدفوع بالكامل ✓' : 'قيد التأكيد والسداد' }}
                                </span>

                                <span class="px-3 py-1 rounded-full text-xs font-cairo font-bold
                                    @if($b->status === 'confirmed') bg-cyber-cyan/15 text-cyber-cyan border border-cyber-cyan/30
                                    @elseif($b->status === 'in_progress') bg-cyber-gold/15 text-cyber-gold border border-cyber-gold/30 animate-pulse
                                    @elseif($b->status === 'completed') bg-emerald-500/15 text-emerald-400 border border-emerald-500/30
                                    @elseif($b->status === 'cancelled') bg-rose-500/15 text-rose-400 border border-rose-500/30
                                    @else bg-slate-700/50 text-slate-300 border border-slate-600
                                    @endif">
                                    @if($b->status === 'new') بانتظار الاعتماد
                                    @elseif($b->status === 'confirmed') مؤكد ومجدول
                                    @elseif($b->status === 'in_progress') قيد التنفيذ المباشر
                                    @elseif($b->status === 'completed') مكتمل وتحت الضمان
                                    @else ملغي
                                    @endif
                                </span>
                            </div>
                        </div>

                        <!-- Client Requirements -->
                        @if($b->client_requirements)
                            <div class="p-3.5 rounded-2xl bg-obsidian-surface border border-obsidian-border text-xs text-slate-300 font-cairo">
                                <span class="text-slate-500 block text-[10px] font-mono">تفاصيل ومتطلبات العميل:</span>
                                {{ $b->client_requirements }}
                            </div>
                        @endif

                        <!-- ========================================================= -->
                        <!-- ON-SITE VISIT STOPWATCH & LIVE CONTROLS (بعد الموافقة)      -->
                        <!-- ========================================================= -->
                        @if($b->status === 'confirmed' || $b->status === 'in_progress')
                            <div class="p-5 rounded-2xl border space-y-4 transition-all"
                                 :class="{{ $b->status === 'in_progress' ? 'true' : 'false' }} 
                                    ? 'bg-gradient-to-r from-cyber-cyan/15 via-obsidian-surface to-cyber-gold/10 border-cyber-cyan shadow-[0_0_25px_rgba(0,245,212,0.2)]' 
                                    : 'bg-obsidian-surface border-cyber-gold/40'"
                                 x-data="{
                                     visitStarted: {{ $b->visit_started_at ? 'true' : 'false' }},
                                     startTimestamp: {{ $b->visit_started_at ? $b->visit_started_at->timestamp * 1000 : 'null' }},
                                     secondsElapsed: 0,
                                     timerInterval: null,
                                     completeModalOpen: false,

                                     init() {
                                         if (this.visitStarted && this.startTimestamp) {
                                             this.calculateElapsed();
                                             this.timerInterval = setInterval(() => { this.calculateElapsed(); }, 1000);
                                         }
                                     },

                                     calculateElapsed() {
                                         const now = Date.now();
                                         this.secondsElapsed = Math.max(0, Math.floor((now - this.startTimestamp) / 1000));
                                     },

                                     get formattedTimer() {
                                         const hrs = Math.floor(this.secondsElapsed / 3600);
                                         const mins = Math.floor((this.secondsElapsed % 3600) / 60);
                                         const secs = this.secondsElapsed % 60;
                                         return `${String(hrs).padStart(2, '0')}:${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
                                     },

                                     get currentBilledHours() {
                                         const actual = this.secondsElapsed / 3600;
                                         return Math.max(3.0, Math.ceil(actual * 2) / 2);
                                     },

                                     get currentLaborCost() {
                                         return this.currentBilledHours * 400;
                                     }
                                 }">

                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-white/10 pb-3">
                                    <div class="flex items-center gap-2.5">
                                        <span class="text-2xl animate-bounce">🚐</span>
                                        <div>
                                            <h4 class="text-sm font-black text-white font-cairo">
                                                {{ $b->status === 'in_progress' ? 'جلسة الزيارة الميدانية جارية الآن' : 'تمت الموافقة وتأكيد موعد الزيارة' }}
                                            </h4>
                                            <span class="text-[11px] text-slate-300 font-cairo">
                                                تسعير معتمد: <strong>400 ج.م/ساعة</strong> (الحد الأدنى للجلسة 3 ساعات = 1,200 ج.م)
                                            </span>
                                        </div>
                                    </div>

                                    @if($b->status === 'confirmed')
                                        <form action="{{ route('portal.bookings.start_visit', $b->id) }}" method="POST">
                                            @csrf
                                            <button type="submit"
                                                    class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-cyber-cyan to-[#00BB9B] text-obsidian-base font-black text-xs font-cairo shadow-neon-cyan active:scale-95 transition-all flex items-center gap-2 cursor-pointer">
                                                <span>▶ بدء الزيارة وتشغيل العداد</span>
                                            </button>
                                        </form>
                                    @endif
                                </div>

                                @if($b->status === 'in_progress')
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-center">
                                        <div class="p-3 rounded-xl bg-obsidian-card border border-white/10">
                                            <span class="text-[10px] text-slate-400 font-cairo block">مدة الزيارة المنقضية</span>
                                            <span class="text-2xl font-black font-mono text-cyber-cyan" x-text="formattedTimer">00:00:00</span>
                                        </div>
                                        <div class="p-3 rounded-xl bg-obsidian-card border border-white/10">
                                            <span class="text-[10px] text-slate-400 font-cairo block">الساعات المحتسبة (حد أدنى 3س)</span>
                                            <span class="text-2xl font-black font-mono text-cyber-gold" x-text="`${currentBilledHours} ساعة`">3 ساعات</span>
                                        </div>
                                        <div class="p-3 rounded-xl bg-obsidian-card border border-white/10">
                                            <span class="text-[10px] text-slate-400 font-cairo block">القيمة التقديرية الحالية</span>
                                            <span class="text-2xl font-black font-mono text-emerald-400" x-text="`${currentLaborCost.toLocaleString()} ج.م`">1,200 ج.م</span>
                                        </div>
                                    </div>

                                    <div class="pt-2 flex justify-end">
                                        <button type="button"
                                                @click="completeModalOpen = true"
                                                class="px-6 py-2.5 rounded-xl bg-rose-500 hover:bg-rose-600 text-white font-black text-xs font-cairo shadow-lg active:scale-95 transition-all flex items-center gap-2 cursor-pointer">
                                            <span>⏹ إنهاء الزيارة وتسديد الحساب</span>
                                        </button>
                                    </div>
                                @endif

                                <!-- Completion, Hardware & Rating Modal -->
                                <div x-show="completeModalOpen"
                                     x-cloak
                                     style="display: none;"
                                     @keydown.escape.window="completeModalOpen = false"
                                     class="fixed inset-0 z-[99999] bg-black/85 backdrop-blur-md flex items-end sm:items-center justify-center p-0 sm:p-4 overflow-y-auto">
                                    
                                    <div @click.away="completeModalOpen = false"
                                         class="relative w-full max-w-xl bg-[#0B0C16] border border-cyber-cyan/50 rounded-t-3xl sm:rounded-3xl p-5 sm:p-7 space-y-5 text-right font-cairo max-h-[92dvh] overflow-y-auto shadow-2xl safe-bottom">
                                        
                                        <div class="flex items-center justify-between border-b border-white/10 pb-3">
                                            <h3 class="text-base font-black text-white">إنهاء الزيارة وتسديد الحساب النهائي</h3>
                                            <button type="button" @click="completeModalOpen = false" class="text-slate-400 hover:text-white">✕</button>
                                        </div>

                                        <form action="{{ route('portal.bookings.complete_visit', $b->id) }}" method="POST" class="space-y-4"
                                              x-data="{
                                                  rating: 5,
                                                  parts: [],
                                                  paymentSource: 'wallet',
                                                  addPart() {
                                                      this.parts.push({ name: '', price: 0 });
                                                  },
                                                  removePart(index) {
                                                      this.parts.splice(index, 1);
                                                  },
                                                  get hardwareSum() {
                                                      return this.parts.reduce((acc, p) => acc + (parseFloat(p.price) || 0), 0);
                                                  },
                                                  get grandFinalTotal() {
                                                      return currentLaborCost + this.hardwareSum;
                                                  }
                                              }">
                                            @csrf

                                            <!-- 1. Labor Cost Summary -->
                                            <div class="p-3.5 rounded-xl bg-[#121422] border border-white/10 space-y-1.5 text-xs">
                                                <div class="flex justify-between text-slate-300">
                                                    <span>الساعات الهندسية المعتمدة:</span>
                                                    <span class="font-mono text-cyber-cyan font-bold" x-text="`${currentBilledHours} ساعات (بحد أدنى 3س)`"></span>
                                                </div>
                                                <div class="flex justify-between text-slate-300">
                                                    <span>تكلفة الصيانة واليد العاملة:</span>
                                                    <span class="font-mono text-emerald-400 font-bold" x-text="`${currentLaborCost.toLocaleString()} ج.م`"></span>
                                                </div>
                                            </div>

                                            <!-- 2. Hardware Replacement Parts -->
                                            <div class="space-y-2 border-t border-white/10 pt-3">
                                                <div class="flex items-center justify-between">
                                                    <span class="text-xs font-bold text-white">قطع الغيار والهاردوير المستبدلة (إن وجدت):</span>
                                                    <button type="button" @click="addPart()" class="text-[11px] text-cyber-cyan font-bold hover:underline">+ إضافة قطعة</button>
                                                </div>

                                                <template x-for="(part, idx) in parts" :key="idx">
                                                    <div class="flex items-center gap-2">
                                                        <input type="text" name="hardware_names[]" x-model="part.name" placeholder="اسم القطعة (مثل: SSD 512GB)" required class="flex-1 bg-obsidian-surface border border-white/10 rounded-xl px-3 py-2 text-xs text-white">
                                                        <input type="number" name="hardware_prices[]" x-model.number="part.price" placeholder="السعر" required min="1" class="w-24 bg-obsidian-surface border border-white/10 rounded-xl px-3 py-2 text-xs text-white font-mono">
                                                        <button type="button" @click="removePart(idx)" class="text-rose-400 hover:text-rose-300 p-2">✕</button>
                                                    </div>
                                                </template>

                                                <div x-show="parts.length > 0" class="text-xs text-slate-400 font-mono flex justify-between pt-1">
                                                    <span>إجمالي قطع الهاردوير (فاتورة منفصلة):</span>
                                                    <span class="text-cyber-gold font-bold" x-text="`${hardwareSum.toLocaleString()} ج.م`"></span>
                                                </div>
                                            </div>

                                            <!-- 3. Final Total -->
                                            <div class="p-3.5 rounded-xl bg-cyber-cyan/10 border border-cyber-cyan/30 flex items-center justify-between">
                                                <span class="text-xs font-black text-white">المبلغ النهائي المستحق:</span>
                                                <span class="text-lg font-black font-mono text-cyber-cyan" x-text="`${grandFinalTotal.toLocaleString()} ج.م`"></span>
                                            </div>

                                            <!-- 4. Payment Source -->
                                            <div class="space-y-1.5 border-t border-white/10 pt-3">
                                                <span class="text-xs font-bold text-slate-200 block">طريقة تسديد الحساب:</span>
                                                <div class="grid grid-cols-2 gap-2 text-xs">
                                                    <label class="p-3 rounded-xl border cursor-pointer flex items-center gap-2"
                                                           :class="paymentSource === 'wallet' ? 'border-cyber-cyan bg-cyber-cyan/15 text-white' : 'border-white/10 text-slate-400'">
                                                        <input type="radio" name="payment_source" value="wallet" x-model="paymentSource" class="hidden">
                                                        <span>⚡ محفظة سولي</span>
                                                    </label>
                                                    <label class="p-3 rounded-xl border cursor-pointer flex items-center gap-2"
                                                           :class="paymentSource === 'on_site_cash' ? 'border-cyber-gold bg-cyber-gold/15 text-white' : 'border-white/10 text-slate-400'">
                                                        <input type="radio" name="payment_source" value="on_site_cash" x-model="paymentSource" class="hidden">
                                                        <span>💵 نقداً للمهندس</span>
                                                    </label>
                                                </div>
                                            </div>

                                            <!-- 5. Rating & Review -->
                                            <div class="space-y-2 border-t border-white/10 pt-3">
                                                <span class="text-xs font-bold text-slate-200 block">تقييم المهندس وجودة الزيارة:</span>
                                                <div class="flex items-center gap-2 text-2xl cursor-pointer">
                                                    <template x-for="star in [1, 2, 3, 4, 5]" :key="star">
                                                        <span @click="rating = star" :class="star <= rating ? 'text-cyber-gold' : 'text-slate-600'">★</span>
                                                    </template>
                                                </div>
                                                <input type="hidden" name="technician_rating" :value="rating">
                                                
                                                <textarea name="client_review" rows="2" placeholder="ملاحظاتك على أداء المهندس أو الخدمة..." class="w-full bg-obsidian-surface border border-white/10 rounded-xl p-3 text-xs text-white"></textarea>
                                            </div>

                                            <button type="submit" class="w-full py-3.5 rounded-xl btn-citadel-primary text-xs font-black shadow-neon-cyan active:scale-95">
                                                تأكيد وإنهاء الجلسة وإصدار الفاتورة والضمان ✓
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Bottom Strip: Price & Actions -->
                        <div class="pt-3 border-t border-obsidian-border/60 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-baseline gap-2">
                                <span class="text-xs text-slate-400 font-cairo">الإجمالي:</span>
                                <span class="text-xl font-black font-mono text-white">{{ number_format($b->total_amount, 2) }}</span>
                                <span class="text-xs font-mono text-cyber-cyan">EGP</span>
                            </div>

                            <div class="flex items-center gap-3">
                                <a href="{{ route('portal.bookings.invoice', $b->id) }}" 
                                   class="text-xs text-cyber-gold hover:text-white font-cairo flex items-center gap-1.5 transition-colors border border-cyber-gold/30 px-3 py-1.5 rounded-lg bg-cyber-gold/10">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span>الفاتورة وشهادة الضمان 📜</span>
                                </a>

                                <a href="{{ route('portal.tickets') }}?booking_id={{ $b->id }}&department={{ $b->service->slug }}" 
                                   class="text-xs text-slate-400 hover:text-cyber-cyan font-cairo flex items-center gap-1.5 transition-colors">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                    </svg>
                                    <span>استفسار أو تذكرة متابعة</span>
                                </a>
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>

            <div class="pt-4">
                {{ $bookings->links() }}
            </div>
        @else
            <div class="p-12 text-center rounded-3xl glass-obsidian border border-obsidian-border space-y-3">
                <div class="text-sm font-bold text-white font-cairo">لا توجد حجوزات مسجلة في هذا القسم</div>
                <p class="text-xs text-slate-400 font-cairo">
                    ابدأ الآن بحجز منظومة جديدة واستمتع بأعلى معايير الدعم الهندسي والضمان المعتمد.
                </p>
            </div>
        @endif

    </div>

    <!-- ===================================================================== -->
    <!-- 3. INTERACTIVE BOOKING ENGINE MODAL                                   -->
    <!-- ===================================================================== -->
    <div x-show="openBookingModal" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 bg-black/80 backdrop-blur-md flex items-center justify-center p-4 overflow-y-auto"
         style="display: none;">
        
        <div @click.away="openBookingModal = false"
             class="relative w-full max-w-2xl glass-obsidian border border-cyber-cyan/40 rounded-3xl p-6 sm:p-8 space-y-6 shadow-neon-cyan max-h-[90vh] overflow-y-auto">
            
            <div class="flex items-center justify-between border-b border-obsidian-border pb-4">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-cyber-cyan animate-pulse"></span>
                    <h3 class="text-xl font-black text-white font-cairo">حجز خدمة جديدة في قلعة سولي</h3>
                </div>
                <button type="button" @click="openBookingModal = false" class="p-2 text-slate-400 hover:text-white rounded-lg">
                    ✕
                </button>
            </div>

            <form action="{{ route('portal.bookings.store') }}" method="POST" class="space-y-5">
                @csrf

                <!-- Service Selector -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-300 font-cairo">1. اختر القطاع أو الخدمة المطلوبة</label>
                    <select name="service_id" x-model="selectedServiceId" @change="onServiceChange()" required
                            class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-4 py-3 text-xs sm:text-sm text-white focus:outline-none focus:border-cyber-cyan font-cairo">
                        <template x-for="srv in services" :key="srv.id">
                            <option :value="srv.id" x-text="srv.name"></option>
                        </template>
                    </select>
                </div>

                <!-- Package Selector -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-300 font-cairo">2. اختر باقة الخدمة المحددة</label>
                    <select name="service_package_id" x-model="selectedPackageId" @change="onPackageChange()"
                            class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-4 py-3 text-xs sm:text-sm text-white focus:outline-none focus:border-cyber-cyan font-cairo">
                        <option value="">بدون باقة مسبقة (استشارة عامة / حسب الاتفاق)</option>
                        <template x-for="pkg in availablePackages" :key="pkg.id">
                            <option :value="pkg.id" x-text="`${pkg.name} — ${Math.floor(pkg.price)} EGP`"></option>
                        </template>
                    </select>
                </div>

                <!-- Quantity / Units Adjuster -->
                <div class="p-4 rounded-2xl bg-obsidian-surface border border-obsidian-border space-y-3">
                    <div class="flex items-center justify-between text-xs font-cairo">
                        <span class="text-slate-300 font-bold">3. عدد الوحدات / الساعات المطلوبة:</span>
                        <span class="font-mono text-cyber-cyan font-bold" x-text="`${quantity} ${unitLabel}`"></span>
                    </div>

                    <div class="flex items-center justify-between gap-3">
                        <button type="button" @click="decrementQuantity()" 
                                class="w-10 h-10 rounded-xl bg-obsidian-border text-white font-mono font-bold flex items-center justify-center hover:bg-slate-700 active:scale-95 text-lg">
                            -
                        </button>
                        <input type="number" name="quantity_or_hours" x-model="quantity" min="1" max="1000"
                               class="flex-1 text-center font-mono font-bold text-xl text-white bg-transparent border-0 focus:outline-none">
                        <button type="button" @click="incrementQuantity()" 
                                class="w-10 h-10 rounded-xl bg-cyber-cyan/20 border border-cyber-cyan/30 text-cyber-cyan font-mono font-bold flex items-center justify-center hover:bg-cyber-cyan/30 active:scale-95 text-lg">
                            +
                        </button>
                    </div>
                </div>

                <!-- Preferred Date & Requirements -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-300 font-cairo">الموعد المفضل للتنفيذ / البدء</label>
                        <input type="date" name="scheduled_at" 
                               class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-4 py-3 text-xs text-white font-mono focus:outline-none focus:border-cyber-cyan">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-300 font-cairo">طريقة الدفع وسداد التكلفة</label>
                        <select name="payment_method" x-model="paymentMethod" required
                                class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-4 py-3 text-xs text-white font-cairo focus:outline-none focus:border-cyber-cyan">
                            <option value="wallet">الخصم الفوري من محفظة سولي الرقمية</option>
                            <option value="direct_transfer">تحويل مباشر (فودافون كاش / إنستاباي)</option>
                            <option value="on_site">دفع نقدي ميداني عند بدء الزيارة</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-300 font-cairo">ملاحظاتك أو استفسارات إضافية (اختياري)</label>
                    <textarea name="client_requirements" rows="2" placeholder="اكتب أي متطلبات خاصة أو عناوين الفروع..."
                              class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl p-3 text-xs text-white font-cairo placeholder-slate-500 focus:outline-none focus:border-cyber-cyan"></textarea>
                </div>

                <!-- Price Summary & Wallet Status -->
                <div class="p-4 rounded-2xl border space-y-2"
                     :class="paymentMethod === 'wallet' && !hasSufficientBalance ? 'bg-amber-500/10 border-amber-500/40' : 'bg-cyber-cyan/5 border-cyber-cyan/30'">
                    
                    <div class="flex items-center justify-between text-xs font-cairo">
                        <span class="text-slate-300 font-bold">التكلفة التقديرية الإجمالية:</span>
                        <div class="flex items-baseline gap-1.5 font-mono">
                            <span class="text-2xl font-black text-white" x-text="computedTotalFormatted"></span>
                            <span class="text-xs text-cyber-cyan">EGP</span>
                        </div>
                    </div>

                    <template x-if="paymentMethod === 'wallet'">
                        <div class="pt-2 border-t border-obsidian-border/60 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400">رصيد محفظتك الحالي:</span>
                                <span class="font-mono font-bold text-white" x-text="`${walletBalance.toFixed(2)} EGP`"></span>
                            </div>

                            <template x-if="hasSufficientBalance">
                                <div class="text-emerald-400 font-bold font-cairo pt-1">
                                    ✓ رصيدك كافٍ لإتمام وتأكيد الحجز اللحظي فوراً.
                                </div>
                            </template>

                            <template x-if="!hasSufficientBalance">
                                <div class="text-amber-300 font-cairo pt-1 flex items-center justify-between">
                                    <span>المتبقي لشحن المحفظة:</span>
                                    <span class="font-mono font-bold" x-text="`${shortfallFormatted} EGP`"></span>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                        class="w-full py-4 px-6 rounded-2xl btn-citadel-primary text-sm sm:text-base flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>تأكيد وتسجيل الحجز</span>
                </button>
            </form>
        </div>
    </div>

</div>

<!-- ===================================================================== -->
<!-- ALPINE BOOKINGS CONTROLLER                                            -->
<!-- ===================================================================== -->
<script>
function bookingManagerController(config) {
    return {
        services: config.services || [],
        walletBalance: config.walletBalance || 0,
        openBookingModal: false,
        selectedServiceId: '',
        selectedPackageId: '',
        quantity: 1,
        paymentMethod: 'wallet',

        init() {
            if (this.services.length > 0) {
                const urlParams = new URLSearchParams(window.location.search);
                const hash = window.location.hash || '';
                let srvSlug = config.preselectedService || urlParams.get('service');
                let pkgSlug = config.preselectedPackage || urlParams.get('package');

                if (!srvSlug && hash.includes('service=')) {
                    const hashParams = new URLSearchParams(hash.split('?')[1] || hash.replace('#booking', '').replace('#', ''));
                    srvSlug = hashParams.get('service') || srvSlug;
                    pkgSlug = hashParams.get('package') || pkgSlug;
                }

                const preSrv = srvSlug ? this.services.find(s => s.slug === srvSlug) : null;
                if (preSrv) {
                    this.selectedServiceId = preSrv.id;
                    this.openBookingModal = true;
                    if (pkgSlug) {
                        const prePkg = (preSrv.packages || []).find(p => p.slug === pkgSlug);
                        if (prePkg) {
                            this.selectedPackageId = prePkg.id;
                        }
                    }
                } else if (hash.includes('booking') || urlParams.has('new')) {
                    this.selectedServiceId = this.services[0].id;
                    this.openBookingModal = true;
                } else {
                    this.selectedServiceId = this.services[0].id;
                }
            }
        },

        get currentService() {
            return this.services.find(s => s.id == this.selectedServiceId) || {};
        },

        get availablePackages() {
            return this.currentService.packages || [];
        },

        get currentPackage() {
            return this.availablePackages.find(p => p.id == this.selectedPackageId) || null;
        },

        get unitLabel() {
            return this.currentPackage?.unit_label || 'وحدة / ساعة';
        },

        onServiceChange() {
            this.selectedPackageId = '';
            this.quantity = 1;
        },

        onPackageChange() {
            if (this.currentPackage && this.currentPackage.min_units) {
                this.quantity = this.currentPackage.min_units;
            } else {
                this.quantity = 1;
            }
        },

        incrementQuantity() {
            this.quantity++;
        },

        decrementQuantity() {
            const min = this.currentPackage?.min_units || 1;
            if (this.quantity > min) {
                this.quantity--;
            }
        },

        get computedTotal() {
            if (this.currentPackage) {
                if (this.currentPackage.billing_type === 'hourly' || this.currentPackage.min_units > 1) {
                    return parseFloat(this.currentPackage.price) * this.quantity;
                }
                return parseFloat(this.currentPackage.price);
            }
            return 500.00 * this.quantity;
        },

        get computedTotalFormatted() {
            return this.computedTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },

        get hasSufficientBalance() {
            return this.walletBalance >= this.computedTotal;
        },

        get shortfallFormatted() {
            const diff = Math.max(0, this.computedTotal - this.walletBalance);
            return diff.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
    };
}
</script>
@endsection