@extends(request()->header('sec-ch-ua-mobile') === '?1' || request()->query('view') === 'mobile' ? 'layouts.mobile' : 'layouts.desktop')

@section('content')
<div class="space-y-8">
    
    <!-- Portal Sub-Header & Navigation Tabs -->
    <div class="glass-obsidian rounded-3xl border border-obsidian-border p-5 sm:p-6 lg:p-8 space-y-6">
        
        <!-- User Welcome & Profile Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-obsidian-border/80 pb-5">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-cyber-purple via-obsidian-surface to-cyber-cyan p-[1.5px] shadow-neon-cyan shrink-0">
                    <div class="w-full h-full bg-obsidian-surface rounded-2xl flex items-center justify-center font-bold font-cairo text-sm text-cyber-cyan">
                        {{ mb_substr(auth()->user()->name, 0, 2) }}
                    </div>
                </div>

                <div class="space-y-0.5">
                    <div class="flex items-center gap-2">
                        <h2 class="text-lg sm:text-xl font-bold text-white font-cairo">{{ auth()->user()->name }}</h2>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold uppercase
                            @if(auth()->user()->role === 'admin') bg-cyber-purple/20 text-purple-300 border border-cyber-purple/40
                            @elseif(auth()->user()->role === 'staff') bg-cyber-gold/20 text-cyber-gold border border-cyber-gold/40
                            @else bg-cyber-cyan/15 text-cyber-cyan border border-cyber-cyan/30
                            @endif">
                            {{ auth()->user()->role === 'admin' ? 'الإدارة العليا' : (auth()->user()->role === 'staff' ? 'مهندس سولي' : 'عميل القلعة') }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-400 font-readex">
                        {{ auth()->user()->company_name ? auth()->user()->company_name . ' · ' : '' }}{{ auth()->user()->email }}
                    </p>
                </div>
            </div>

            <!-- Header Quick Actions & Logout -->
            <div class="flex items-center gap-3">
                <a href="{{ route('portal.wallet') }}" 
                   class="flex items-center gap-2 px-3.5 py-2 rounded-xl bg-gradient-to-r from-cyber-gold/15 to-cyber-cyan/15 border border-cyber-cyan/40 hover:border-cyber-cyan transition-all text-xs font-mono">
                    <span class="text-slate-400">الرصيد:</span>
                    <strong class="text-cyber-cyan font-bold text-sm">{{ number_format(auth()->user()->walletOrCreate()->balance, 2) }}</strong>
                    <span class="text-[10px] text-slate-400">ج.م</span>
                </a>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" 
                            title="تسجيل الخروج"
                            class="p-2.5 rounded-xl bg-obsidian-surface hover:bg-rose-500/20 text-slate-400 hover:text-rose-400 border border-obsidian-border hover:border-rose-500/30 transition-all text-xs font-cairo flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span class="hidden sm:inline">خروج</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <nav class="flex items-center gap-2 overflow-x-auto pb-1 text-xs font-cairo font-bold">
            <a href="{{ route('portal.index') }}" 
               class="px-4 py-2.5 rounded-xl transition-all flex items-center gap-2 shrink-0
               {{ request()->routeIs('portal.index') ? 'bg-cyber-cyan text-obsidian-base shadow-neon-cyan' : 'bg-obsidian-surface text-slate-300 hover:text-white border border-obsidian-border' }}">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span>نظرة عامة</span>
            </a>

            <a href="{{ route('portal.wallet') }}" 
               class="px-4 py-2.5 rounded-xl transition-all flex items-center gap-2 shrink-0
               {{ request()->routeIs('portal.wallet') ? 'bg-cyber-cyan text-obsidian-base shadow-neon-cyan' : 'bg-obsidian-surface text-slate-300 hover:text-white border border-obsidian-border' }}">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
                <span>المحفظة والشحن</span>
            </a>

            <a href="{{ route('portal.bookings') }}" 
               class="px-4 py-2.5 rounded-xl transition-all flex items-center gap-2 shrink-0
               {{ request()->routeIs('portal.bookings*') ? 'bg-cyber-cyan text-obsidian-base shadow-neon-cyan' : 'bg-obsidian-surface text-slate-300 hover:text-white border border-obsidian-border' }}">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <span>الحجوزات والخدمات</span>
            </a>

            <a href="{{ route('portal.tickets') }}" 
               class="px-4 py-2.5 rounded-xl transition-all flex items-center gap-2 shrink-0
               {{ request()->routeIs('portal.tickets*') ? 'bg-cyber-cyan text-obsidian-base shadow-neon-cyan' : 'bg-obsidian-surface text-slate-300 hover:text-white border border-obsidian-border' }}">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <span>تذاكر الدعم الهندسي</span>
            </a>

            <a href="{{ route('services.index') }}" 
               class="mr-auto px-4 py-2.5 rounded-xl bg-obsidian-surface hover:bg-slate-800 text-cyber-gold border border-cyber-gold/30 transition-all flex items-center gap-2 shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                <span>استكشاف كافة الخدمات</span>
            </a>
        </nav>

    </div>

    <!-- Portal Page Dynamic Content -->
    @yield('portal-content')

</div>
@endsection
