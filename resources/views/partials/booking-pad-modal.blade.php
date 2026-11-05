<div x-data="{ 
    sector: 'engineering_maintenance', 
    date: '{{ date('Y-m-d') }}', 
    slot: '10:00 ص - 01:00 م',
    slots: ['10:00 ص - 01:00 م', '01:30 م - 04:30 م', '05:00 م - 08:00 م', '08:30 م - 11:30 م']
}" class="w-full">
    <!-- Dual Sector Toggle -->
    <div class="flex items-center justify-center p-1 bg-slate-900/90 rounded-2xl border border-slate-800 mb-6">
        <button type="button" @click="sector = 'engineering_maintenance'" 
            :class="sector === 'engineering_maintenance' ? 'bg-amber-500 text-slate-950 font-bold shadow-lg shadow-amber-500/20' : 'text-slate-400 hover:text-white'"
            class="w-1/2 py-2.5 rounded-xl text-sm transition-all duration-300 flex items-center justify-center gap-2">
            <span>🛠️</span> صيانة هندسية
        </button>
        <button type="button" @click="sector = 'cyber_shield'" 
            :class="sector === 'cyber_shield' ? 'bg-cyan-500 text-slate-950 font-bold shadow-lg shadow-cyan-500/20' : 'text-slate-400 hover:text-white'"
            class="w-1/2 py-2.5 rounded-xl text-sm transition-all duration-300 flex items-center justify-center gap-2">
            <span>🛡️</span> أمن سيبراني
        </button>
    </div>

    <!-- Hidden Input for Form Submission -->
    <input type="hidden" name="service_sector" :value="sector">
    <input type="hidden" name="preferred_time" :value="slot">

    <!-- Interactive Time-Slot Pad -->
    <div class="space-y-3">
        <label class="block text-xs font-medium text-slate-400">اختر الفترة المناسبة للزيارة الميدانية:</label>
        <div class="grid grid-cols-2 gap-2.5">
            <template x-for="s in slots" :key="s">
                <button type="button" @click="slot = s"
                    :class="slot === s ? 'border-amber-400 bg-amber-400/10 text-amber-300 font-bold' : 'border-slate-800 bg-slate-900/50 text-slate-400 hover:border-slate-700'"
                    class="py-3 px-3 rounded-xl border text-xs text-center transition-all flex items-center justify-center gap-1.5">
                    <span class="inline-block w-1.5 h-1.5 rounded-full" :class="slot === s ? 'bg-amber-400 animate-pulse' : 'bg-slate-600'"></span>
                    <span x-text="s"></span>
                </button>
            </template>
        </div>
    </div>
</div>