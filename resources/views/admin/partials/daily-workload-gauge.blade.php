@php
    $todayDate = \Carbon\Carbon::now()->locale('ar')->isoFormat('dddd، D MMMM');
    $todayBookingsCount = \App\Models\Booking::whereDate('scheduled_at', \Carbon\Carbon::today())->count();
    $unconfirmedCount = \App\Models\Booking::where('call_confirmed', false)->whereIn('status', ['new', 'confirmed'])->count();
    $maxCapacity = 8;
    $loadPercent = min(100, round(($todayBookingsCount / $maxCapacity) * 100));
@endphp

<div class="glass-obsidian rounded-2xl border border-cyber-gold/30 p-3.5 sm:p-4 mb-4 shadow-[0_0_20px_rgba(255,184,0,0.08)]">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <!-- Title & Date -->
        <div class="flex items-center gap-2.5">
            <span class="w-8 h-8 rounded-xl bg-cyber-gold/20 text-cyber-gold flex items-center justify-center text-sm font-bold border border-cyber-gold/40 shrink-0">
                ⚡
            </span>
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-black text-white font-cairo">مؤشر حمولة اليوم ({{ $todayDate }})</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold {{ $loadPercent >= 80 ? 'bg-rose-500/20 text-rose-300 border border-rose-500/40' : 'bg-cyber-cyan/20 text-cyber-cyan border border-cyber-cyan/40' }}">
                        {{ $loadPercent }}% ضغط العمل
                    </span>
                </div>
                <p class="text-[11px] text-slate-400 font-cairo">
                    {{ $todayBookingsCount }} زيارات مجدولة اليوم من أصل {{ $maxCapacity }} مواعيد متاحة للمهندس
                </p>
            </div>
        </div>

        <!-- Quick Action Badges -->
        <div class="flex items-center gap-2 overflow-x-auto pb-1 sm:pb-0">
            @if($unconfirmedCount > 0)
                <a href="{{ route('admin.bookings.index', ['urgent_calls' => 1]) }}" 
                   class="px-2.5 py-1.5 rounded-xl bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 border border-rose-500/50 text-[11px] font-cairo font-bold flex items-center gap-1.5 shrink-0 animate-pulse">
                    <span>📞</span>
                    <span>{{ $unconfirmedCount }} مكالمات مطلوبة الآن</span>
                </a>
            @else
                <span class="px-2.5 py-1.5 rounded-xl bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 text-[11px] font-cairo font-bold flex items-center gap-1.5 shrink-0">
                    <span>✓</span>
                    <span>كافة المكالمات مؤكدة</span>
                </span>
            @endif

            <!-- 1-Tap Backup Download -->
            <a href="{{ route('admin.system.backup_snapshot') }}" 
               class="px-2.5 py-1.5 rounded-xl bg-obsidian-surface hover:bg-slate-800 text-slate-300 hover:text-white border border-obsidian-border text-[11px] font-cairo font-bold flex items-center gap-1.5 shrink-0 transition-colors"
               title="تحميل نسخة احتياطية فورية لبيانات المنصة كملف JSON">
                <span>💾</span>
                <span class="hidden sm:inline">نسخة احتياطية</span>
            </a>

            <!-- Quick Sovereign Agent Trigger -->
            <button type="button" 
                    @click="$dispatch('open-admin-ai-sheet')"
                    class="px-3 py-1.5 rounded-xl bg-gradient-to-r from-cyber-gold to-amber-600 text-obsidian-base font-black text-[11px] font-cairo flex items-center gap-1.5 shrink-0 shadow-neon-gold hover:opacity-90 transition-opacity">
                <span>🤖</span>
                <span>الوكيل التنفيذي</span>
            </button>
        </div>
    </div>

    <!-- Capacity Bar -->
    <div class="w-full bg-slate-800/80 rounded-full h-1.5 mt-3 overflow-hidden">
        <div class="h-full rounded-full transition-all duration-500 {{ $loadPercent >= 80 ? 'bg-gradient-to-r from-amber-500 to-rose-500' : 'bg-gradient-to-r from-cyber-cyan to-cyber-gold' }}" 
             style="width: {{ $loadPercent }}%"></div>
    </div>
</div>
