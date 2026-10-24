@extends(request()->header('sec-ch-ua-mobile') === '?1' || request()->query('view') === 'mobile' ? 'layouts.mobile' : 'layouts.desktop')

@section('content')
<div class="space-y-8">
    
    <!-- Admin & Staff High-Command Sub-Header -->
    <div class="glass-obsidian rounded-3xl border border-cyber-purple/40 p-5 sm:p-6 lg:p-8 space-y-6 shadow-neon-purple">
        
        <!-- Top Command Strip -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-obsidian-border/80 pb-5">
            <div class="flex items-center gap-4">
                <a href="{{ url('/') }}" class="shrink-0" title="قلعة سولي الرقمية">
                    <x-souly-logo size="xs" :showText="false" />
                </a>

                <div class="space-y-0.5">
                    <div class="flex items-center gap-2.5">
                        <h1 class="text-xl sm:text-2xl font-black text-white font-cairo">مركز القيادة وإدارة العمليات</h1>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold uppercase bg-cyber-purple/20 text-purple-300 border border-cyber-purple/40">
                            {{ auth()->user()->role === 'admin' ? 'الإدارة العليا · ROOT' : 'طاقم العمل والمهندسين · STAFF' }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-400 font-readex">
                        منظومة التحكم المركزية بالعمليات، الحسابات، CRM، وخزينة الردود الذكية في قلعة سولي
                    </p>
                </div>
            </div>

            <!-- Quick Switches & Controls -->
            <div class="flex items-center gap-3">
                <a href="{{ route('portal.index') }}" 
                   class="px-3.5 py-2 rounded-xl bg-obsidian-surface hover:bg-slate-800 text-slate-300 hover:text-cyber-cyan border border-obsidian-border transition-all text-xs font-cairo flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-cyber-cyan" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <span>معاينة بوابة العميل</span>
                </a>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" 
                            class="p-2.5 rounded-xl bg-obsidian-surface hover:bg-rose-500/20 text-slate-400 hover:text-rose-400 border border-obsidian-border transition-all text-xs font-cairo">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>

        <!-- High-Command Navigation Tabs -->
        <nav class="flex items-center gap-2 overflow-x-auto pb-1 text-xs font-cairo font-bold">
            <!-- 1. Overview -->
            <a href="{{ route('admin.index') }}" 
               class="px-4 py-2.5 rounded-xl transition-all flex items-center gap-2 shrink-0
               {{ request()->routeIs('admin.index') ? 'bg-cyber-cyan text-obsidian-base shadow-neon-cyan' : 'bg-obsidian-surface text-slate-300 hover:text-white border border-obsidian-border' }}">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <span>لوحة المؤشرات العامة</span>
            </a>

            <!-- 2. Wallet Top-Ups & Ledger -->
            <a href="{{ route('admin.wallets.index') }}" 
               class="px-4 py-2.5 rounded-xl transition-all flex items-center gap-2 shrink-0
               {{ request()->routeIs('admin.wallets*') ? 'bg-cyber-cyan text-obsidian-base shadow-neon-cyan' : 'bg-obsidian-surface text-slate-300 hover:text-white border border-obsidian-border' }}">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
                <span>شحن المحفظة والمالية</span>
            </a>

            <!-- 3. Bookings Operations -->
            <a href="{{ route('admin.bookings.index') }}" 
               class="px-4 py-2.5 rounded-xl transition-all flex items-center gap-2 shrink-0
               {{ request()->routeIs('admin.bookings*') ? 'bg-cyber-cyan text-obsidian-base shadow-neon-cyan' : 'bg-obsidian-surface text-slate-300 hover:text-white border border-obsidian-border' }}">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <span>الحجوزات والعمليات</span>
            </a>

            <!-- 4. Mini-CRM Leads Pipeline -->
            <a href="{{ route('admin.leads.index') }}" 
               class="px-4 py-2.5 rounded-xl transition-all flex items-center gap-2 shrink-0
               {{ request()->routeIs('admin.leads*') ? 'bg-cyber-cyan text-obsidian-base shadow-neon-cyan' : 'bg-obsidian-surface text-slate-300 hover:text-white border border-obsidian-border' }}">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span>العملاء المحتملين CRM</span>
            </a>

            <!-- 5. Smart Canned Responses Vault (CRITICAL) -->
            <a href="{{ route('admin.canned-responses.index') }}" 
               class="px-4 py-2.5 rounded-xl transition-all flex items-center gap-2 shrink-0
               {{ request()->routeIs('admin.canned-responses*') ? 'bg-cyber-gold text-obsidian-base shadow-neon-gold' : 'bg-obsidian-surface text-cyber-gold hover:bg-cyber-gold/10 border border-cyber-gold/40' }}">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
                <span>خزينة الردود الذكية ⚡</span>
            </a>

            <!-- 6. Services & Packages CMS -->
            <a href="{{ route('admin.services.index') }}" 
               class="px-4 py-2.5 rounded-xl transition-all flex items-center gap-2 shrink-0
               {{ request()->routeIs('admin.services*') ? 'bg-cyber-cyan text-obsidian-base shadow-neon-cyan' : 'bg-obsidian-surface text-slate-300 hover:text-white border border-obsidian-border' }}">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <span>إدارة الخدمات والباقات</span>
            </a>
        </nav>

    </div>

    <!-- Admin Operational Content Viewport -->
    @yield('admin-content')

</div>
@endsection
