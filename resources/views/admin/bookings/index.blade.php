@extends('admin.layout')

@section('title', 'إدارة الحجوزات والعمليات الهندسية | سولي')
@section('page-title', 'إدارة الحجوزات والعمليات')

@section('admin-content')
<div class="space-y-8" x-data="{
    editingBooking: null,
    openEditModal: false,
    openEditor(booking) {
        this.editingBooking = booking;
        this.openEditModal = true;
    },
    callStates: {
        @foreach($bookings as $b)
            {{ $b->id }}: {{ $b->call_confirmed ? 'true' : 'false' }},
        @endforeach
    },
    async toggleCall(id) {
        try {
            const res = await fetch(`{{ url('/admin/bookings') }}/${id}/toggle-call`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });
            const data = await res.json();
            if (data.success) {
                this.callStates[id] = data.call_confirmed;
            }
        } catch (e) {
            alert('حدث خطأ: ' + e.message);
        }
    }
}">

    <!-- Top Bar -->
    <div class="glass-obsidian rounded-3xl border border-obsidian-border p-6 sm:p-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="space-y-1">
            <h2 class="text-xl sm:text-2xl font-black text-white font-cairo flex items-center gap-2.5">
                <span class="w-3 h-3 rounded-full bg-cyber-gold animate-pulse"></span>
                <span>إدارة وتنفيذ الحجوزات الهندسية</span>
            </h2>
            <p class="text-xs text-slate-400 font-cairo">
                توزيع مهام الصيانة والـ ERP على طاقم المهندسين، وتحديث تقارير التنفيذ والضمان.
            </p>
        </div>

        <form action="{{ route('admin.bookings.index') }}" method="GET" class="flex items-center gap-2 w-full md:w-auto">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث برقم الحجز أو اسم العميل..."
                   class="bg-obsidian-surface border border-obsidian-border text-xs text-white placeholder-slate-500 rounded-xl px-4 py-2.5 focus:outline-none focus:border-cyber-cyan w-full md:w-64 font-cairo">
            <button type="submit" class="px-4 py-2.5 bg-obsidian-surface border border-obsidian-border hover:border-cyber-cyan text-cyber-cyan font-bold text-xs rounded-xl transition-all">
                بحث
            </button>
        </form>
    </div>

    <!-- Status Filter Tabs -->
    <div class="flex items-center gap-2 overflow-x-auto pb-2 text-xs font-cairo font-bold">
        <a href="{{ route('admin.bookings.index') }}" 
           class="px-4 py-2 rounded-xl transition-all shrink-0
           {{ !request()->filled('status') && !request()->boolean('urgent_calls') ? 'bg-cyber-cyan text-obsidian-base shadow-neon-cyan' : 'bg-obsidian-surface text-slate-400 border border-obsidian-border hover:text-white' }}">
            كافة الحجوزات ({{ $bookings->total() }})
        </a>
        <a href="{{ route('admin.bookings.index', ['urgent_calls' => 1]) }}" 
           class="px-4 py-2 rounded-xl transition-all shrink-0
           {{ request()->boolean('urgent_calls') ? 'bg-rose-500 text-white shadow-lg' : 'bg-rose-500/10 text-rose-300 border border-rose-500/30' }}">
            📞 مكالمات مطلوبة الآن ({{ $unconfirmedCallsCount ?? 0 }})
        </a>
        <a href="{{ route('admin.bookings.index') }}?status=new" 
           class="px-4 py-2 rounded-xl transition-all shrink-0
           {{ request()->query('status') === 'new' ? 'bg-amber-500 text-obsidian-base shadow-neon-gold' : 'bg-obsidian-surface text-amber-400 border border-obsidian-border' }}">
            طلبات جديدة
        </a>
        <a href="{{ route('admin.bookings.index') }}?status=confirmed" 
           class="px-4 py-2 rounded-xl transition-all shrink-0
           {{ request()->query('status') === 'confirmed' ? 'bg-cyber-cyan text-obsidian-base shadow-neon-cyan' : 'bg-obsidian-surface text-slate-400 border border-obsidian-border hover:text-white' }}">
            مؤكدة
        </a>
        <a href="{{ route('admin.bookings.index') }}?status=in_progress" 
           class="px-4 py-2 rounded-xl transition-all shrink-0
           {{ request()->query('status') === 'in_progress' ? 'bg-cyber-gold text-obsidian-base shadow-neon-gold' : 'bg-obsidian-surface text-cyber-gold border border-obsidian-border' }}">
            قيد التنفيذ الميداني
        </a>
        <a href="{{ route('admin.bookings.index') }}?status=completed" 
           class="px-4 py-2 rounded-xl transition-all shrink-0
           {{ request()->query('status') === 'completed' ? 'bg-emerald-500 text-obsidian-base' : 'bg-obsidian-surface text-slate-400 border border-obsidian-border hover:text-white' }}">
            مكتملة ومضمونة
        </a>
    </div>

    <!-- Bookings Table & Mobile Action Cards -->
    <div class="rounded-3xl glass-obsidian border border-obsidian-border p-4 sm:p-8 space-y-4">
        
        <!-- Desktop Table (Hidden on Mobile) -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead>
                    <tr class="border-b border-obsidian-border/80 text-slate-400 font-cairo">
                        <th class="py-3 px-3">رقم الحجز</th>
                        <th class="py-3 px-3">العميل</th>
                        <th class="py-3 px-3">الخدمة والباقة</th>
                        <th class="py-3 px-3">القيمة والدفع</th>
                        <th class="py-3 px-3">الموعد المحدد</th>
                        <th class="py-3 px-3">الحالة التنفيذية</th>
                        <th class="py-3 px-3">المهندس المسؤول</th>
                        <th class="py-3 px-3 text-center">الإجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-obsidian-border/50">
                    @forelse($bookings as $b)
                        <tr class="hover:bg-obsidian-surface/60 transition-colors">
                            <td class="py-3.5 px-3 text-cyber-cyan font-bold font-mono">{{ $b->booking_number }}</td>
                            <td class="py-3.5 px-3 font-cairo">
                                <div class="font-bold text-white">{{ $b->user->name }}</div>
                                <div class="text-[10px] text-slate-400 font-mono">{{ $b->user->phone ?? $b->user->email }}</div>
                            </td>
                            <td class="py-3.5 px-3 font-cairo">
                                <div class="font-bold text-white">{{ $b->service->name }}</div>
                                <div class="text-[10px] text-cyber-gold font-cairo font-bold">{{ $b->package?->name ?? 'بدون باقة مسبقة' }}</div>
                            </td>
                            <td class="py-3.5 px-3">
                                <div class="font-bold text-emerald-400 text-sm font-mono">{{ number_format($b->total_amount, 2) }} EGP</div>
                                <div class="text-[10px] font-cairo font-bold {{ $b->payment_status === 'paid' ? 'text-emerald-400' : 'text-amber-400' }}">
                                    {{ $b->payment_status === 'paid' ? 'مدفوع بالكامل' : 'قيد التأكيد' }} ({{ $b->payment_method }})
                                </div>
                            </td>
                            <td class="py-3.5 px-3 text-slate-300 font-mono">
                                {{ $b->scheduled_at ? $b->scheduled_at->format('Y-m-d') : 'مفتوح' }}
                            </td>
                            <td class="py-3.5 px-3">
                                <span class="px-2.5 py-0.5 rounded text-[10px] font-bold font-cairo
                                    @if($b->status === 'confirmed') bg-cyber-cyan/15 text-cyber-cyan border border-cyber-cyan/30
                                    @elseif($b->status === 'in_progress') bg-cyber-gold/15 text-cyber-gold border border-cyber-gold/30 animate-pulse
                                    @elseif($b->status === 'completed') bg-emerald-500/15 text-emerald-400 border border-emerald-500/30
                                    @elseif($b->status === 'cancelled') bg-rose-500/15 text-rose-400 border border-rose-500/30
                                    @else bg-slate-700/50 text-slate-300 border border-slate-600
                                    @endif">
                                    {{ $b->status }}
                                </span>
                            </td>
                            <td class="py-3.5 px-3 font-cairo">
                                <span class="text-slate-300">{{ $b->assignedStaff?->name ?? 'غير معين' }}</span>
                            </td>
                            <td class="py-3.5 px-3 text-center">
                                <button type="button" @click="openEditor({{ Js::from($b) }})" 
                                        class="btn-citadel-secondary text-xs py-1.5 px-3">
                                    تحديث وإسناد ⚙
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-400 font-cairo">
                                لا توجد حجوزات مطابقة للبحث أو التصفية.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Tactical Action Cards (Visible on < 768px only) -->
        <div class="md:hidden space-y-3">
            @forelse($bookings as $b)
                <div class="p-4 rounded-2xl bg-obsidian-surface border border-obsidian-border space-y-3 shadow-md">
                    <div class="flex items-center justify-between border-b border-obsidian-border/70 pb-2">
                        <span class="font-mono font-black text-cyber-cyan text-xs">{{ $b->booking_number }}</span>
                        <span class="px-2.5 py-0.5 rounded text-[10px] font-bold font-cairo
                            @if($b->status === 'confirmed') bg-cyber-cyan/15 text-cyber-cyan border border-cyber-cyan/30
                            @elseif($b->status === 'in_progress') bg-cyber-gold/15 text-cyber-gold border border-cyber-gold/30 animate-pulse
                            @elseif($b->status === 'completed') bg-emerald-500/15 text-emerald-400 border border-emerald-500/30
                            @elseif($b->status === 'cancelled') bg-rose-500/15 text-rose-400 border border-rose-500/30
                            @else bg-slate-700/50 text-slate-300 border border-slate-600
                            @endif">
                            {{ $b->status }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold text-white font-cairo block">{{ $b->user->name }}</span>
                            <span class="text-[10px] text-slate-400 font-mono">{{ $b->user->phone ?? $b->user->email }}</span>
                        </div>
                        <div class="text-left font-mono">
                            <span class="text-xs font-black text-emerald-400">{{ number_format($b->total_amount, 2) }} EGP</span>
                            <span class="text-[9px] font-cairo block {{ $b->payment_status === 'paid' ? 'text-emerald-400' : 'text-amber-400' }}">
                                {{ $b->payment_status === 'paid' ? 'مدفوع' : 'غير مدفوع' }}
                            </span>
                        </div>
                    </div>

                    <div class="p-2.5 rounded-xl bg-obsidian-card border border-obsidian-border flex items-center justify-between text-[11px] font-cairo">
                        <div>
                            <span class="text-white font-bold block">{{ $b->service->name }}</span>
                            <span class="text-[10px] text-cyber-gold">{{ $b->package?->name ?? 'بدون باقة مسبقة' }}</span>
                        </div>
                        <div class="text-left">
                            <span class="text-slate-400 text-[10px] block">المهندس:</span>
                            <span class="text-slate-200 font-bold text-[10px]">{{ $b->assignedStaff?->name ?? 'غير معين' }}</span>
                        </div>
                    </div>

                    <!-- 1-Tap Call Confirmation Button -->
                    <button type="button" 
                            @click="toggleCall({{ $b->id }})" 
                            :class="callStates[{{ $b->id }}] ? 'bg-emerald-500/15 text-emerald-400 border-emerald-500/30' : 'bg-rose-500/15 text-rose-300 border-rose-500/40 animate-pulse'"
                            class="w-full py-1.5 px-3 rounded-xl border text-[11px] font-cairo font-bold flex items-center justify-between transition-colors">
                        <span class="flex items-center gap-1.5">
                            <span>📞</span>
                            <span x-text="callStates[{{ $b->id }}] ? 'تم الاتصال وتأكيد الحجز' : 'لم يتم الاتصال للتأكيد (اضغط للتأكيد)'"></span>
                        </span>
                        <span class="text-[9px] font-mono font-bold" x-text="callStates[{{ $b->id }}] ? 'CONFIRMED' : 'CALL NOW'"></span>
                    </button>

                    <div class="flex items-center gap-2 pt-1">
                        <button type="button" 
                                @click="openEditor({{ Js::from($b) }})" 
                                class="flex-1 py-2 rounded-xl btn-citadel-primary text-xs font-bold font-cairo text-center">
                            تحديث وإسناد ⚙
                        </button>
                        @if($b->user->phone)
                            <a href="tel:{{ $b->user->phone }}" class="p-2 rounded-xl bg-obsidian-card border border-obsidian-border text-slate-300 hover:text-cyber-cyan text-xs">
                                📞
                            </a>
                            <a href="https://wa.me/2{{ preg_replace('/^0/', '', $b->user->phone) }}" target="_blank" class="p-2 rounded-xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-400 text-xs font-bold">
                                💬
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-6 text-center text-slate-400 font-cairo text-xs bg-obsidian-surface rounded-2xl border border-obsidian-border">
                    لا توجد حجوزات مطابقة.
                </div>
            @endforelse
        </div>

        <div class="pt-4 border-t border-obsidian-border/60">
            {{ $bookings->links() }}
        </div>
    </div>

    <!-- Edit & Staff Assignment Modal (Swipeable Bottom Sheet on Mobile) -->
    <div x-show="openEditModal" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 bg-black/80 backdrop-blur-md flex items-end md:items-center justify-center max-md:p-0 p-4 overflow-y-auto mobile-bottom-sheet-modal"
         style="display: none;">
        
        <div @click.away="openEditModal = false"
             class="relative w-full max-w-xl glass-obsidian border max-md:border-b-0 border-cyber-cyan/40 max-md:rounded-t-3xl max-md:rounded-b-none rounded-3xl p-6 sm:p-8 space-y-6 shadow-neon-cyan max-h-[88dvh] md:max-h-[90vh] overflow-y-auto mobile-bottom-sheet-panel">
            
            <!-- Mobile Drag Handle -->
            <div class="w-12 h-1.5 bg-slate-600 rounded-full mx-auto mb-2 md:hidden"></div>

            <div class="flex items-center justify-between border-b border-obsidian-border pb-4">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-cyber-gold animate-pulse"></span>
                    <h3 class="text-xl font-black text-white font-cairo">
                        تحديث الحجز: <span class="text-cyber-cyan font-mono" x-text="editingBooking?.booking_number"></span>
                    </h3>
                </div>
                <button type="button" @click="openEditModal = false" class="p-2 text-slate-400 hover:text-white rounded-lg">✕</button>
            </div>

            <form :action="`/admin/bookings/${editingBooking?.id}/update`" method="POST" class="space-y-4">
                @csrf

                <!-- Status & Payment Status -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-300 font-cairo">الحالة التنفيذية للطلب</label>
                        <select name="status" :value="editingBooking?.status" required
                                class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-4 py-2.5 text-xs text-white font-cairo focus:outline-none focus:border-cyber-cyan">
                            <option value="new">جديد (new)</option>
                            <option value="confirmed">مؤكد ومجدول (confirmed)</option>
                            <option value="in_progress">قيد التنفيذ الهندسي (in_progress)</option>
                            <option value="completed">مكتمل ومضمون (completed)</option>
                            <option value="cancelled">ملغي (cancelled)</option>
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-300 font-cairo">حالة السداد والدفع</label>
                        <select name="payment_status" :value="editingBooking?.payment_status" required
                                class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-4 py-2.5 text-xs text-white font-cairo focus:outline-none focus:border-cyber-cyan">
                            <option value="paid">مدفوع بالكامل (paid)</option>
                            <option value="pending_verification">بانتظار التأكيد (pending_verification)</option>
                            <option value="unpaid">غير مدفوع (unpaid)</option>
                            <option value="refunded">مسترد (refunded)</option>
                        </select>
                    </div>
                </div>

                <!-- Assigned Staff & Date -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-300 font-cairo">إسناد إلى مهندس / موظف</label>
                        <select name="assigned_staff_id" :value="editingBooking?.assigned_staff_id"
                                class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-4 py-2.5 text-xs text-white font-cairo focus:outline-none focus:border-cyber-cyan">
                            <option value="">-- بدون إسناد --</option>
                            @foreach($staffMembers as $staff)
                                <option value="{{ $staff->id }}">{{ $staff->name }} ({{ $staff->role }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-300 font-cairo">تاريخ الزيارة / التنفيذ</label>
                        <input type="date" name="scheduled_at" :value="editingBooking?.scheduled_at ? editingBooking.scheduled_at.substring(0, 10) : ''"
                               class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-4 py-2.5 text-xs text-white font-mono focus:outline-none focus:border-cyber-cyan">
                    </div>
                </div>

                <!-- Engineering Staff Report -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-300 font-cairo">تقرير المهندس وتفاصيل الإنجاز / التوصيات</label>
                    <textarea name="staff_report" rows="4" :value="editingBooking?.staff_report" placeholder="اكتب تقرير الفحص الفني، قطع الغيار، وشهادة الضمان الصادرة للعميل..."
                              class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl p-3 text-xs text-white font-cairo placeholder-slate-500 focus:outline-none focus:border-cyber-cyan"></textarea>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                        class="btn-citadel-primary w-full py-3.5 text-xs sm:text-sm">
                    حفظ وتحديث بيانات الحجز
                </button>
            </form>
        </div>
    </div>

</div>
@endsection
