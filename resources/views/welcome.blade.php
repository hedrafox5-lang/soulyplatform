@extends(request()->header('sec-ch-ua-mobile') === '?1' || request()->query('view') === 'mobile' ? 'layouts.mobile' : 'layouts.desktop')

@section('title', 'سولي ديدجيتال سيتادل | Souly Digital Citadel')
@section('page-title', 'مركز العمليات والاستخبارات الرقمية')

@section('content')
<div class="space-y-8" x-data="citadelCommandHUD()">
    
    <!-- Top Telemetry & Hero Banner -->
    <div class="relative overflow-hidden rounded-3xl glass-obsidian border border-obsidian-border p-6 lg:p-8">
        <!-- Ambient Neon Glow Effect -->
        <div class="absolute -top-24 -left-24 w-80 h-80 rounded-full bg-cyber-purple/20 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -right-24 w-80 h-80 rounded-full bg-cyber-cyan/15 blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="space-y-3 max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyber-cyan/10 border border-cyber-cyan/30 text-cyber-cyan text-xs font-mono">
                    <span class="w-2 h-2 rounded-full bg-cyber-cyan animate-pulse"></span>
                    <span>PHASE 1 CORE ARCHITECTURE: DEPLOYED</span>
                </div>
                <h1 class="text-2xl lg:text-4xl font-extrabold text-white font-cairo leading-tight">
                    منظومة <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyber-cyan via-cyber-gold to-cyber-purple">سولي ديدجيتال سيتادل</span> الرقمية
                </h1>
                <p class="text-sm lg:text-base text-slate-300 font-readex leading-relaxed">
                    منصة الجيل القادم لسيادة العمليات والتجارة الذكية والتحكم الميداني. مدعومة بمعمارية Native-First فائقة السرعة على خادم Laravel 13 و Alpine.js و GSAP ومحرك التخزين الفولاذي SoulyDB.
                </p>
            </div>

            <!-- Quick Telemetry HUD Card -->
            <div class="grid grid-cols-2 sm:grid-cols-2 gap-3 shrink-0">
                <div class="p-4 rounded-2xl bg-obsidian-surface border border-obsidian-border/80">
                    <span class="text-[11px] font-mono text-slate-400 block">إصدار الإطار</span>
                    <span class="text-lg font-bold font-mono text-cyber-cyan">Laravel 13.38</span>
                </div>
                <div class="p-4 rounded-2xl bg-obsidian-surface border border-obsidian-border/80">
                    <span class="text-[11px] font-mono text-slate-400 block">بيئة التشغيل</span>
                    <span class="text-lg font-bold font-mono text-cyber-gold">PHP 8.3.36</span>
                </div>
                <div class="p-4 rounded-2xl bg-obsidian-surface border border-obsidian-border/80">
                    <span class="text-[11px] font-mono text-slate-400 block">قاعدة البيانات</span>
                    <span class="text-lg font-bold font-mono text-cyber-purple">MySQL Active</span>
                </div>
                <div class="p-4 rounded-2xl bg-obsidian-surface border border-obsidian-border/80">
                    <span class="text-[11px] font-mono text-slate-400 block">محرك SoulyDB</span>
                    <span class="text-lg font-bold font-mono text-cyber-emerald uppercase" x-text="$store.citadel.storageEngine">Ready</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Interactive Citadel Telemetry Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Node 1: High Availability Storage -->
        <div class="p-5 rounded-2xl glass-obsidian border border-obsidian-border hover:border-cyber-cyan/50 transition-all duration-300 group">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-cyber-cyan/10 border border-cyber-cyan/30 flex items-center justify-center text-cyber-cyan group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2 1.5 3 3.5 3h9c2 0 3.5-1 3.5-3V7c0-2-1.5-3-3.5-3h-9C5.5 4 4 5 4 7z" />
                    </svg>
                </div>
                <span class="text-[11px] font-mono text-cyber-cyan uppercase" x-text="`TIER: ${$store.citadel.storageEngine}`">TIER: IDB</span>
            </div>
            <h3 class="font-bold text-base text-slate-100 font-cairo">محرك SoulyDB التراكمي</h3>
            <p class="text-xs text-slate-400 mt-1">تخزين IndexedDB مرن ومصفح ببدائل التخزين اللحظي والذاكرة لتفادي قيود التصفح الخفي.</p>
        </div>

        <!-- Node 2: Native-First Performance -->
        <div class="p-5 rounded-2xl glass-obsidian border border-obsidian-border hover:border-cyber-gold/50 transition-all duration-300 group">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-cyber-gold/10 border border-cyber-gold/30 flex items-center justify-center text-cyber-gold group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <span class="text-[11px] font-mono text-cyber-gold">ZERO-SPA LAG</span>
            </div>
            <h3 class="font-bold text-base text-slate-100 font-cairo">بنية Native-First الصافية</h3>
            <p class="text-xs text-slate-400 mt-1">استغناء كامل عن Livewire و React لصالح Blade و Alpine.js خفيف الوزن واستجابة فورية.</p>
        </div>

        <!-- Node 3: RTL & Localization -->
        <div class="p-5 rounded-2xl glass-obsidian border border-obsidian-border hover:border-cyber-purple/50 transition-all duration-300 group">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-cyber-purple/10 border border-cyber-purple/30 flex items-center justify-center text-cyber-purple group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
                    </svg>
                </div>
                <span class="text-[11px] font-mono text-cyber-purple">AR-EG RTL</span>
            </div>
            <h3 class="font-bold text-base text-slate-100 font-cairo">تعريب ونمط RTL أصيل</h3>
            <p class="text-xs text-slate-400 mt-1">توافق قياسي للغة العربية، خطوط Cairo & Readex Pro، وتوقيت القاهرة المحلي (Africa/Cairo).</p>
        </div>

        <!-- Node 4: Dynamic Responsive Shell -->
        <div class="p-5 rounded-2xl glass-obsidian border border-obsidian-border hover:border-emerald-500/50 transition-all duration-300 group">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-400 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                </div>
                <span class="text-[11px] font-mono text-emerald-400">DESKTOP / MOBILE</span>
            </div>
            <h3 class="font-bold text-base text-slate-100 font-cairo">هيكل تكيفي مزدوج</h3>
            <p class="text-xs text-slate-400 mt-1">تخطيط Wide Command Center للأجهزة المكتبية وهيكل Native Shell مع Bottom Bar للهواتف.</p>
        </div>

    </div>

    <!-- Live Demonstration & Storage Test Console -->
    <div class="glass-obsidian rounded-3xl border border-obsidian-border p-6 lg:p-8 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-obsidian-border pb-4">
            <div>
                <h2 class="text-xl font-bold text-white font-cairo flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-cyber-cyan"></span>
                    <span>منصة اختبار تخزين SoulyDB الفولاذية (Interactive Probe)</span>
                </h2>
                <p class="text-xs text-slate-400 mt-1">اختبار حي للقراءة والكتابة في IndexedDB مع التحقق من عدم الانهيار في الأوضاع المجهولة.</p>
            </div>
            <div class="flex items-center gap-3">
                <button @click="testStorageWrite()" class="px-4 py-2 rounded-xl bg-cyber-cyan/10 hover:bg-cyber-cyan/20 border border-cyber-cyan/40 text-cyber-cyan text-xs font-bold font-cairo transition-all">
                    + كتابة إشارة تجريبية
                </button>
                <button @click="testStorageClear()" class="px-4 py-2 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 border border-rose-500/30 text-rose-400 text-xs font-bold font-cairo transition-all">
                    مسح السجلات
                </button>
            </div>
        </div>

        <!-- Terminal Output Screen -->
        <div class="bg-obsidian-base rounded-2xl border border-obsidian-border/90 p-4 font-mono text-xs space-y-2">
            <div class="flex items-center justify-between text-slate-500 pb-2 border-b border-obsidian-border text-[10px]">
                <span>سجل التيليميتري الحي // SOULY-STORAGE-STREAM</span>
                <span class="text-cyber-cyan" x-text="`DRIVER: ${$store.citadel.storageEngine}`">DRIVER: ACTIVE</span>
            </div>
            <div class="space-y-1.5 max-h-48 overflow-y-auto">
                <template x-for="(log, idx) in storageLogs" :key="idx">
                    <div class="flex items-center gap-3">
                        <span class="text-slate-600 font-mono" x-text="log.time"></span>
                        <span :class="{
                            'text-cyber-cyan': log.type === 'write',
                            'text-cyber-gold': log.type === 'read',
                            'text-rose-400': log.type === 'clear',
                            'text-cyber-purple': log.type === 'info'
                        }" x-text="log.message"></span>
                    </div>
                </template>
            </div>
        </div>

        <!-- Stored Items Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead>
                    <tr class="text-slate-400 border-b border-obsidian-border font-cairo">
                        <th class="py-2.5 px-3">مفتاح السجل (Key)</th>
                        <th class="py-2.5 px-3">القيمة المشفرة / المحتوى</th>
                        <th class="py-2.5 px-3">المحرك المنفذ</th>
                        <th class="py-2.5 px-3">الإجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-obsidian-border/50 font-mono">
                    <template x-for="(item, key) in storedItems" :key="key">
                        <tr class="hover:bg-obsidian-surface/40 transition-colors">
                            <td class="py-2.5 px-3 text-cyber-gold" x-text="key"></td>
                            <td class="py-2.5 px-3 text-slate-200" x-text="JSON.stringify(item)"></td>
                            <td class="py-2.5 px-3 text-cyber-cyan" x-text="$store.citadel.storageEngine"></td>
                            <td class="py-2.5 px-3">
                                <button @click="deleteItem(key)" class="text-rose-400 hover:text-rose-300 font-cairo text-[11px]">حذف</button>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="Object.keys(storedItems).length === 0">
                        <td colspan="4" class="py-6 text-center text-slate-500 font-cairo">لا توجد بيانات مسجلة حالياً في الذاكرة المؤقتة. اضغط "كتابة إشارة تجريبية".</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

@push('scripts')
<script>
    function citadelCommandHUD() {
        return {
            storedItems: {},
            storageLogs: [
                { time: new Date().toLocaleTimeString(), type: 'info', message: 'نظام التخزين الفولاذي SoulyDB متصل وجاهز للعمليات.' }
            ],

            async init() {
                await this.refreshItems();
            },

            async testStorageWrite() {
                const probeId = 'node_' + Math.floor(Math.random() * 9000 + 1000);
                const payload = {
                    node: probeId,
                    timestamp: new Date().toISOString(),
                    status: 'OPTIMAL',
                    telemetry: {
                        ping: Math.floor(Math.random() * 20 + 8) + 'ms',
                        cpu: Math.floor(Math.random() * 30 + 10) + '%'
                    }
                };

                await window.SoulyDB.set(probeId, payload);
                this.storageLogs.unshift({
                    time: new Date().toLocaleTimeString(),
                    type: 'write',
                    message: `تم حفظ الإشارة بنجاح [${probeId}] عبر محرك ${window.SoulyDB.getEngine()}`
                });
                await this.refreshItems();
            },

            async deleteItem(key) {
                await window.SoulyDB.delete(key);
                this.storageLogs.unshift({
                    time: new Date().toLocaleTimeString(),
                    type: 'read',
                    message: `تم حذف المفتاح [${key}] من المخزن.`
                });
                await this.refreshItems();
            },

            async testStorageClear() {
                await window.SoulyDB.clear();
                this.storageLogs.unshift({
                    time: new Date().toLocaleTimeString(),
                    type: 'clear',
                    message: 'تم تفريغ كافة سجلات التخزين المؤقتة.'
                });
                await this.refreshItems();
            },

            async refreshItems() {
                if (!window.SoulyDB) return;
                const keys = await window.SoulyDB.keys();
                const map = {};
                for (const k of keys) {
                    map[k] = await window.SoulyDB.get(k);
                }
                this.storedItems = map;
            }
        };
    }
</script>
@endpush
@endsection
