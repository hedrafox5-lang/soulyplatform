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
                    <span>PHASE 3: DUAL-SHELL & CINEMATIC ENGINE READY</span>
                </div>
                <h1 class="text-2xl lg:text-4xl font-extrabold text-white font-cairo leading-tight">
                    منظومة <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyber-cyan via-cyber-gold to-cyber-purple">سولي ديدجيتال سيتادل</span> الرقمية
                </h1>
                <p class="text-sm lg:text-base text-slate-300 font-readex leading-relaxed">
                    منصة الجيل القادم لسيادة العمليات والتجارة الذكية والتحكم الميداني. مدعومة بمعمارية Native-First فائقة السرعة على خادم Laravel 13 و Alpine.js و GSAP، ومحرك التخزين الفولاذي SoulyDB، وعارض القصص السينمائي المدمج.
                </p>

                <!-- Action Controls: Replay Loader & Open AI -->
                <div class="flex flex-wrap items-center gap-3 pt-2">
                    <button onclick="SoulyLoader.replay()" 
                            class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-cyber-purple/40 to-obsidian-surface border border-cyber-purple hover:border-cyber-cyan text-white text-xs font-cairo font-bold shadow-neon-purple hover:scale-[1.02] active:scale-95 transition-all flex items-center gap-2">
                        <svg class="w-4 h-4 text-cyber-cyan animate-spin" style="animation-duration: 8s;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>إعادة العرض السينمائي (25 ثانية)</span>
                    </button>

                    <button @click="$dispatch('open-ai-chat-sheet')" 
                            class="px-4 py-2.5 rounded-xl bg-cyber-cyan/10 hover:bg-cyber-cyan/20 border border-cyber-cyan/40 text-cyber-cyan text-xs font-cairo font-bold hover:scale-[1.02] active:scale-95 transition-all flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-cyber-cyan animate-ping"></span>
                        <span>شات سولي AI الفوري</span>
                    </button>
                </div>
            </div>

            <!-- Quick Telemetry HUD Card -->
            <div class="grid grid-cols-2 sm:grid-cols-2 gap-3 shrink-0">
                <div class="p-4 rounded-2xl bg-obsidian-surface border border-obsidian-border/80">
                    <span class="text-[11px] font-mono text-slate-400 block">إصدار الإطار</span>
                    <span class="text-lg font-bold font-mono text-cyber-cyan">Laravel 13.38</span>
                </div>
                <div class="p-4 rounded-2xl bg-obsidian-surface border border-obsidian-border/80">
                    <span class="text-[11px] font-mono text-slate-400 block">رصيد المحفظة</span>
                    <span class="text-lg font-bold font-mono text-cyber-gold" x-text="`${$store.citadel.walletBalance} EGP`">{{ $citadelWalletBalance ?? '3,500.00' }} EGP</span>
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

    <!-- The 6 Core Services & 0ms Interactive Icon Engine Showcase -->
    <div id="services" class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl sm:text-2xl font-extrabold text-white font-cairo flex items-center gap-2.5">
                    <span class="w-3 h-3 rounded-full bg-cyber-cyan animate-pulse"></span>
                    <span>منظومة الخدمات السيادية الست (Interactive Matrix)</span>
                </h2>
                <p class="text-xs sm:text-sm text-slate-400 mt-1 font-readex">
                    اضغط على أي ميزة أو نشاط لفتح نافذة التفاصيل الفورية بمحرك الـ 0ms المنبثق (Modal على الحاسوب و Bottom Sheet على الموبايل).
                </p>
            </div>
        </div>

        <!-- Services Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @forelse($citadelServices as $service)
                <div class="glass-obsidian rounded-2xl border border-obsidian-border p-5 hover:border-cyber-cyan/40 transition-all duration-300 group flex flex-col justify-between">
                    <div>
                        <!-- Header & Badge -->
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-mono font-bold bg-obsidian-surface text-cyber-cyan border border-cyber-cyan/30">
                                {{ $service->hero_badge ?? 'خدمة سيادية' }}
                            </span>
                            <span class="text-xs font-mono text-slate-500">ORD: #{{ $service->display_order }}</span>
                        </div>

                        <h3 class="text-lg font-bold text-white font-cairo group-hover:text-cyber-cyan transition-colors">
                            {{ $service->name }}
                        </h3>
                        <p class="text-xs text-slate-400 mt-1.5 leading-relaxed font-readex line-clamp-2">
                            {{ $service->tagline ?? $service->short_description }}
                        </p>

                        <!-- Service Icons Interactive Triggers -->
                        @if($service->icons->isNotEmpty())
                            <div class="mt-4 pt-3 border-t border-obsidian-border/60">
                                <span class="text-[10px] font-mono uppercase text-slate-500 block mb-2 font-bold">المزايا والأنشطة (انقر للاستعراض):</span>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($service->icons as $icon)
                                        <button type="button" 
                                                data-icon-popup
                                                data-icon-id="{{ $icon->id }}"
                                                data-icon-title="{{ $icon->title }}"
                                                data-icon-badge="{{ $icon->badge_text }}"
                                                data-icon-summary="{{ $icon->popup_summary }}"
                                                data-icon-bullets="{{ json_encode($icon->popup_bullets_json) }}"
                                                data-service-name="{{ $service->name }}"
                                                data-cta-text="{{ $icon->popup_cta_text }}"
                                                class="flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-obsidian-surface/90 hover:bg-cyber-cyan/10 border border-obsidian-border hover:border-cyber-cyan/40 text-xs font-cairo text-slate-300 hover:text-cyber-cyan active:scale-95 transition-all">
                                            <span class="w-1.5 h-1.5 rounded-full" style="background-color: {{ $service->accent_color ?? '#00F5D4' }};"></span>
                                            <span>{{ $icon->title }}</span>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Package Pricing Preview & CTA -->
                    <div class="mt-5 pt-3 border-t border-obsidian-border flex items-center justify-between">
                        @if($service->packages->isNotEmpty())
                            @php $pkg = $service->packages->first(); @endphp
                            <div>
                                <span class="text-[10px] font-mono text-slate-500 block">تبدأ من:</span>
                                <span class="text-sm font-bold font-mono text-cyber-gold">{{ number_format($pkg->price, 0) }} EGP</span>
                                <span class="text-[10px] text-slate-400">{{ $pkg->unit_label }}</span>
                            </div>
                        @else
                            <span class="text-xs text-slate-500 font-mono">باقات مخصصة</span>
                        @endif

                        <button type="button" 
                                @click="$dispatch('open-icon-popup', {
                                    id: 'svc_{{ $service->id }}',
                                    title: '{{ $service->name }}',
                                    badge_text: '{{ $service->hero_badge ?? 'نظرة عامة' }}',
                                    popup_title: '{{ $service->name }}',
                                    popup_summary: '{{ addslashes($service->short_description) }}',
                                    popup_bullets: {{ json_encode($service->packages->pluck('name')->toArray()) }},
                                    service_name: 'سولي سيتادل',
                                    popup_cta_text: 'احجز هذه المنظومة الآن'
                                })"
                                class="px-3.5 py-1.5 rounded-xl bg-obsidian-surface hover:bg-cyber-cyan/20 border border-cyber-cyan/30 text-cyber-cyan font-cairo font-bold text-xs transition-all">
                            استكشاف وتفاصيل
                        </button>
                    </div>
                </div>
            @empty
                <div class="col-span-full p-8 text-center glass-obsidian rounded-2xl border border-obsidian-border text-slate-400">
                    جاري مزامنة بيانات الخدمات من قاعدة البيانات...
                </div>
            @endforelse
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
