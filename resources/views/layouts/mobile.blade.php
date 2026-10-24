@extends('layouts.master')

@section('layout-body')
<div class="min-h-screen flex flex-col w-full bg-obsidian-base relative pb-20 safe-bottom">
    
    <!-- Native Mobile Top Bar -->
    <header class="sticky top-0 z-40 w-full glass-obsidian border-b border-obsidian-border/70 px-4 py-3 safe-top">
        <div class="flex items-center justify-between gap-2">
            <!-- Brand Insignia -->
            <a href="{{ url('/') }}" class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-cyber-purple to-cyber-cyan p-[1px] shadow-neon-cyan">
                    <div class="w-full h-full bg-obsidian-surface rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-cyber-cyan" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                </div>
                <div class="leading-none">
                    <span class="font-extrabold text-sm text-slate-100 font-cairo block">سولي سيتادل</span>
                    <span class="text-[9px] font-mono text-cyber-cyan tracking-wider">SOULYEG.ONLINE</span>
                </div>
            </a>

            <!-- Right Telemetry Beacon & Drawer Button -->
            <div class="flex items-center gap-2">
                <!-- Ping Beacon -->
                <div class="flex items-center gap-1.5 px-2 py-1 rounded-md bg-obsidian-surface/90 border border-obsidian-border text-[10px] font-mono text-slate-300">
                    <span class="w-1.5 h-1.5 rounded-full bg-cyber-cyan animate-pulse"></span>
                    <span x-text="$store.citadel.nodePing">12ms</span>
                </div>

                <!-- Menu Drawer Toggle -->
                <button @click="$store.citadel.mobileNavOpen = true" 
                        class="p-2 rounded-lg bg-obsidian-surface border border-obsidian-border text-slate-300 hover:text-cyber-cyan focus:outline-none transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </header>

    <!-- Mobile Slide-over Drawer -->
    <div x-show="$store.citadel.mobileNavOpen" 
         x-transition:enter="transition-opacity ease-linear duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 bg-black/70 backdrop-blur-sm"
         style="display: none;">
        
        <div @click.away="$store.citadel.mobileNavOpen = false" 
             class="fixed inset-y-0 right-0 max-w-xs w-full glass-obsidian border-l border-obsidian-border p-6 flex flex-col justify-between safe-top safe-bottom">
            <div class="space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-obsidian-border">
                    <span class="font-extrabold text-base text-slate-100 font-cairo">بوابة التحكم السريع</span>
                    <button @click="$store.citadel.mobileNavOpen = false" class="p-1.5 rounded-lg text-slate-400 hover:text-white">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <nav class="space-y-2">
                    <a href="{{ url('/') }}" @click="$store.citadel.mobileNavOpen = false" class="flex items-center gap-3 px-3 py-2.5 rounded-xl bg-cyber-cyan/10 text-cyber-cyan border border-cyber-cyan/30 text-sm font-medium">
                        <svg class="w-5 h-5 text-cyber-cyan" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span>الرئيسية</span>
                    </a>
                    <a href="#operations" @click="$store.citadel.mobileNavOpen = false" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-300 hover:text-cyber-cyan text-sm font-medium">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        <span>العمليات الميدانية</span>
                    </a>
                    <a href="#inventory" @click="$store.citadel.mobileNavOpen = false" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-300 hover:text-cyber-cyan text-sm font-medium">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        <span>خزينة المنتجات</span>
                    </a>
                    <a href="#security" @click="$store.citadel.mobileNavOpen = false" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-300 hover:text-cyber-purple text-sm font-medium">
                        <svg class="w-5 h-5 text-cyber-purple" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        <span>جدار التشفير</span>
                    </a>
                </nav>
            </div>

            <!-- Drawer Bottom Diagnostics -->
            <div class="p-3 rounded-xl bg-obsidian-surface border border-obsidian-border text-xs font-mono space-y-1">
                <div class="flex justify-between text-slate-400">
                    <span>التوقيت:</span>
                    <span class="text-cyber-gold" x-text="$store.citadel.cairoTime">--:--</span>
                </div>
                <div class="flex justify-between text-slate-400">
                    <span>المحرك:</span>
                    <span class="text-cyber-cyan uppercase" x-text="$store.citadel.storageEngine">IDB</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Mobile Content Area -->
    <main class="flex-1 p-4 overflow-y-auto">
        @yield('content')
    </main>

    <!-- Native Bottom Navigation Bar (Dock) -->
    <nav class="fixed bottom-0 inset-x-0 z-40 glass-obsidian border-t border-obsidian-border/80 px-4 py-2 safe-bottom">
        <div class="max-w-md mx-auto grid grid-cols-5 items-center">
            
            <!-- Tab 1: Home -->
            <a href="{{ url('/') }}" class="flex flex-col items-center gap-1 py-1 text-cyber-cyan transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span class="text-[10px] font-medium font-cairo">الرئيسية</span>
            </a>

            <!-- Tab 2: Operations -->
            <a href="#operations" class="flex flex-col items-center gap-1 py-1 text-slate-400 hover:text-cyber-cyan transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                <span class="text-[10px] font-medium font-cairo">العمليات</span>
            </a>

            <!-- Center Action FAB: Quick Action / Omni-Scan -->
            <div class="flex justify-center -mt-5">
                <button class="w-12 h-12 rounded-full bg-gradient-to-tr from-cyber-purple via-obsidian-surface to-cyber-cyan p-[2px] shadow-neon-cyan focus:outline-none active:scale-95 transition-transform">
                    <div class="w-full h-full rounded-full bg-obsidian-surface flex items-center justify-center">
                        <svg class="w-6 h-6 text-cyber-cyan" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                </button>
            </div>

            <!-- Tab 4: Vault / Inventory -->
            <a href="#inventory" class="flex flex-col items-center gap-1 py-1 text-slate-400 hover:text-cyber-cyan transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                <span class="text-[10px] font-medium font-cairo">الخزينة</span>
            </a>

            <!-- Tab 5: Profile / Security -->
            <a href="#security" class="flex flex-col items-center gap-1 py-1 text-slate-400 hover:text-cyber-purple transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span class="text-[10px] font-medium font-cairo">حسابي</span>
            </a>

        </div>
    </nav>

</div>
@endsection
