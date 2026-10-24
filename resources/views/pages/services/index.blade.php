@extends(request()->header('sec-ch-ua-mobile') === '?1' || request()->query('view') === 'mobile' ? 'layouts.mobile' : 'layouts.desktop')

@section('title', 'بوابة الخدمات والحلول السيادية | سولي ديدجيتال سيتادل')
@section('page-title', 'كتالوج الخدمات والحلول التكنولوجية المتكاملة')

@section('content')
<div class="space-y-12" x-data="servicesHubController()">

    <!-- Hub Header Banner -->
    <div class="relative overflow-hidden rounded-3xl glass-obsidian border border-obsidian-border p-6 sm:p-10 lg:p-12">
        <div class="absolute -top-24 -left-24 w-80 h-80 rounded-full bg-cyber-purple/20 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -right-24 w-80 h-80 rounded-full bg-cyber-cyan/15 blur-3xl pointer-events-none"></div>

        <div class="relative z-10 max-w-3xl space-y-4">
            <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full text-xs font-mono font-bold bg-cyber-cyan/10 border border-cyber-cyan/30 text-cyber-cyan">
                <span class="w-2 h-2 rounded-full bg-cyber-cyan animate-pulse"></span>
                <span>SOULY CITADEL · 6 CORE SECTORS</span>
            </div>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white font-cairo leading-tight">
                منظومة الخدمات <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyber-cyan via-cyber-gold to-cyber-purple">السيادية الست</span>
            </h1>
            <p class="text-sm sm:text-base text-slate-300 font-readex leading-relaxed">
                منظومات تقنية متقدمة تم بناؤها لتغطي كافة متطلبات التحول الرقمي، الإدارة السحابية، الذكاء الاصطناعي، الأمن السيبراني، الصيانة الهندسية الميدانية، والتعليم التطبيقي.
            </p>
        </div>
    </div>

    <!-- Filter & Search Controls -->
    <div class="glass-obsidian rounded-2xl border border-obsidian-border p-3 sm:p-4 flex flex-col md:flex-row items-center justify-between gap-4">
        
        <!-- Category Filter Pills -->
        <div class="flex items-center gap-2 overflow-x-auto w-full md:w-auto pb-2 md:pb-0">
            <button @click="activeFilter = 'all'" 
                    class="px-4 py-2 rounded-xl text-xs font-cairo font-bold transition-all shrink-0"
                    :class="activeFilter === 'all' ? 'bg-cyber-cyan text-obsidian-base shadow-neon-cyan' : 'bg-obsidian-surface text-slate-300 hover:text-white'">
                كافة الخدمات ({{ $services->count() }})
            </button>
            <button @click="activeFilter = 'software'" 
                    class="px-4 py-2 rounded-xl text-xs font-cairo font-bold transition-all shrink-0"
                    :class="activeFilter === 'software' ? 'bg-cyber-cyan text-obsidian-base shadow-neon-cyan' : 'bg-obsidian-surface text-slate-300 hover:text-white'">
                الأنظمة والـ AI (2)
            </button>
            <button @click="activeFilter = 'security_hardware'" 
                    class="px-4 py-2 rounded-xl text-xs font-cairo font-bold transition-all shrink-0"
                    :class="activeFilter === 'security_hardware' ? 'bg-cyber-cyan text-obsidian-base shadow-neon-cyan' : 'bg-obsidian-surface text-slate-300 hover:text-white'">
                الأمن والصيانة (2)
            </button>
            <button @click="activeFilter = 'business_edu'" 
                    class="px-4 py-2 rounded-xl text-xs font-cairo font-bold transition-all shrink-0"
                    :class="activeFilter === 'business_edu' ? 'bg-cyber-cyan text-obsidian-base shadow-neon-cyan' : 'bg-obsidian-surface text-slate-300 hover:text-white'">
                الاستشارات والأكاديمية (2)
            </button>
        </div>

        <!-- Search Input -->
        <div class="relative w-full md:w-72">
            <input type="text" 
                   x-model="searchQuery" 
                   placeholder="ابحث عن خدمة أو ميزة..." 
                   class="w-full bg-obsidian-surface text-xs text-slate-200 placeholder-slate-500 rounded-xl px-9 py-2.5 border border-obsidian-border focus:border-cyber-cyan focus:outline-none transition-all font-cairo">
            <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-slate-400">
                <svg class="w-4 h-4 text-cyber-cyan" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </div>

    </div>

    <!-- Services Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($services as $index => $service)
            <div x-show="matchesFilter('{{ $service->slug }}')" 
                 x-transition
                 class="relative rounded-3xl glass-obsidian border border-obsidian-border hover:border-cyber-cyan/50 p-6 sm:p-7 flex flex-col justify-between group transition-all duration-300">
                
                <div class="space-y-4">
                    <!-- Top Ribbon -->
                    <div class="flex items-center justify-between">
                        <span class="px-3 py-1 rounded-full text-[10px] font-mono font-bold uppercase"
                              style="background-color: {{ $service->accent_color }}15; border: 1px solid {{ $service->accent_color }}40; color: {{ $service->accent_color }};">
                            {{ $service->hero_badge ?? 'قطاع سيادي' }}
                        </span>
                        <span class="text-xs font-mono text-slate-500">SECTOR #0{{ $index + 1 }}</span>
                    </div>

                    <!-- Title & Tagline -->
                    <div class="space-y-1.5">
                        <h3 class="text-xl font-extrabold text-white font-cairo group-hover:text-cyber-cyan transition-colors">
                            {{ $service->name }}
                        </h3>
                        <p class="text-xs text-slate-400 font-readex leading-relaxed line-clamp-3">
                            {{ $service->tagline ?? $service->short_description }}
                        </p>
                    </div>

                    <!-- Metrics Badges -->
                    <div class="grid grid-cols-2 gap-2 pt-2">
                        <div class="p-2.5 rounded-xl bg-obsidian-surface border border-obsidian-border text-center">
                            <span class="text-[10px] font-mono text-slate-400 block">المزايا والأنشطة</span>
                            <span class="text-xs font-bold font-mono text-cyber-cyan">{{ $service->icons->count() }} ميزة</span>
                        </div>
                        <div class="p-2.5 rounded-xl bg-obsidian-surface border border-obsidian-border text-center">
                            <span class="text-[10px] font-mono text-slate-400 block">الباقات المتاحة</span>
                            <span class="text-xs font-bold font-mono text-cyber-gold">{{ $service->packages->count() }} باقات</span>
                        </div>
                    </div>
                </div>

                <!-- Footer CTAs -->
                <div class="pt-6 border-t border-obsidian-border mt-6 flex items-center justify-between gap-3">
                    <a href="{{ route('services.show', $service->slug) }}" 
                       class="flex-1 py-2.5 rounded-xl bg-gradient-to-r from-cyber-cyan to-cyber-purple text-obsidian-base font-extrabold font-cairo text-xs text-center shadow-neon-cyan hover:scale-[1.02] active:scale-95 transition-all">
                        دخول صفحة القطاع ⇥
                    </a>
                    <button type="button" 
                            @click="$dispatch('open-icon-popup', {
                                id: 'hub_{{ $service->id }}',
                                title: '{{ $service->name }}',
                                badge_text: '{{ $service->hero_badge }}',
                                popup_title: '{{ $service->name }}',
                                popup_summary: '{{ addslashes($service->short_description) }}',
                                popup_bullets: {{ json_encode($service->packages->pluck('name')->toArray()) }},
                                service_name: 'سولي سيتادل',
                                popup_cta_text: 'احجز هذه المنظومة الآن',
                                cta_link: '{{ route('services.show', $service->slug) }}'
                            })"
                            class="p-2.5 rounded-xl bg-obsidian-surface hover:bg-obsidian-card border border-obsidian-border text-slate-300 hover:text-white transition-colors"
                            title="نظرة سريعة">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                </div>

            </div>
        @endforeach
    </div>

</div>

@push('scripts')
<script>
    function servicesHubController() {
        return {
            activeFilter: 'all',
            searchQuery: '',

            matchesFilter(slug) {
                if (this.searchQuery.trim() !== '') {
                    return slug.toLowerCase().includes(this.searchQuery.toLowerCase());
                }

                if (this.activeFilter === 'all') return true;
                if (this.activeFilter === 'software') return ['brand-erp', 'ai-agents'].includes(slug);
                if (this.activeFilter === 'security_hardware') return ['cybersecurity', 'maintenance'].includes(slug);
                if (this.activeFilter === 'business_edu') return ['business-consulting', 'academy'].includes(slug);
                return true;
            }
        };
    }
</script>
@endpush
@endsection
