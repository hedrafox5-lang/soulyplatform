@extends('layouts.master')

@section('layout-body')
<div class="min-h-screen flex flex-col w-full bg-obsidian-base">
    <!-- Wide Command Center Top Navigation Bar -->
    <header class="sticky top-0 z-40 w-full glass-obsidian border-b border-obsidian-border/80 px-6 py-3">
        <div class="max-w-[1920px] mx-auto flex items-center justify-between gap-4">
            
            <!-- Left: Brand / Citadel Node Identifier -->
            <div class="flex items-center gap-4">
                <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-cyber-purple via-obsidian-surface to-cyber-cyan p-[1px] shadow-neon-cyan">
                        <div class="w-full h-full bg-obsidian-surface rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-cyber-cyan group-hover:scale-110 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-extrabold text-lg tracking-wider text-slate-100 font-cairo">سولي ديدجيتال سيتادل</span>
                            <span class="px-2 py-0.5 text-[10px] font-mono rounded-full bg-cyber-cyan/10 text-cyber-cyan border border-cyber-cyan/30">L13 · CORE</span>
                        </div>
                        <p class="text-[11px] text-slate-400 font-mono tracking-tight">SOULYEG.ONLINE // COMMAND-CENTER</p>
                    </div>
                </a>

                <!-- Breadcrumbs / Context Display -->
                <div class="hidden xl:flex items-center gap-2 pr-6 border-r border-obsidian-border text-xs text-slate-400">
                    <span>المنصة الرئيسية</span>
                    <span class="text-cyber-cyan">/</span>
                    <span class="text-slate-200">@yield('page-title', 'لوحة التحكم والعمليات المركزية')</span>
                </div>
            </div>

            <!-- Center: Command Omni-Search HUD -->
            <div class="flex-1 max-w-xl mx-4">
                <div class="relative">
                    <input type="text" 
                           placeholder="بحث قيادي ذكي (العمليات، العقد، السجلات)... [Ctrl + K]" 
                           class="w-full bg-obsidian-surface/90 text-sm text-slate-200 placeholder-slate-500 rounded-xl px-10 py-2 border border-obsidian-border focus:outline-none focus:border-cyber-cyan focus:ring-1 focus:ring-cyber-cyan/40 transition-all font-cairo">
                    <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4 text-cyber-cyan" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <div class="absolute inset-y-0 left-3 flex items-center">
                        <kbd class="px-1.5 py-0.5 text-[10px] font-mono bg-obsidian-base text-slate-400 rounded border border-obsidian-border">⌘K</kbd>
                    </div>
                </div>
            </div>

            <!-- Right: Real-time Telemetry, Wallet Pill & Controls -->
            <div class="flex items-center gap-3">
                <!-- Replay Cinematic Story Loader Button -->
                <button onclick="SoulyLoader.replay()" 
                        title="إعادة العرض السينمائي (25 ثانية)"
                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-obsidian-surface border border-obsidian-border text-slate-300 hover:text-cyber-cyan hover:border-cyber-cyan/40 text-xs font-cairo transition-all">
                    <svg class="w-3.5 h-3.5 text-cyber-cyan animate-spin" style="animation-duration: 10s;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="hidden xl:inline">العرض السينمائي</span>
                </button>

                <!-- Quick Wallet Balance Pill -->
                <a href="{{ url('/portal') }}" 
                   class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-gradient-to-r from-obsidian-surface to-obsidian-card border border-cyber-cyan/40 hover:border-cyber-cyan text-xs font-mono shadow-sm hover:shadow-neon-cyan transition-all">
                    <div class="w-2 h-2 rounded-full bg-cyber-cyan animate-pulse"></div>
                    <span class="text-slate-400 text-[11px]">محفظتي:</span>
                    <span class="text-cyber-cyan font-bold" x-text="`${$store.citadel.walletBalance} EGP`">{{ $citadelWalletBalance ?? '3,500.00' }} EGP</span>
                </a>

                <!-- Cairo Time Status -->
                <div class="hidden 2xl:flex flex-col items-end border-l border-obsidian-border pl-3">
                    <span class="text-[10px] text-slate-400 font-mono">القاهرة (UTC+3)</span>
                    <span class="text-xs font-mono font-bold text-cyber-gold" x-text="$store.citadel.cairoTime">--:--:--</span>
                </div>

                <!-- Live Node Telemetry Pulse -->
                <div class="hidden md:flex items-center gap-2 px-3 py-1.5 rounded-lg bg-obsidian-surface border border-obsidian-border">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-cyber-cyan opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-cyber-cyan"></span>
                    </span>
                    <span class="text-[11px] font-mono text-slate-300">PING: <strong class="text-cyber-cyan" x-text="$store.citadel.nodePing">12ms</strong></span>
                </div>

                <!-- Storage Driver Indicator -->
                <div class="hidden lg:flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-obsidian-surface border border-cyber-purple/40 text-[11px] font-mono text-cyber-purple">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2 1.5 3 3.5 3h9c2 0 3.5-1 3.5-3V7c0-2-1.5-3-3.5-3h-9C5.5 4 4 5 4 7z" />
                    </svg>
                    <span class="uppercase" x-text="$store.citadel.storageEngine">IDB</span>
                </div>

                <!-- Operator / Auth Profile -->
                @auth
                    <div class="flex items-center gap-3">
                        <a href="{{ route('portal.index') }}" class="flex items-center gap-2 group">
                            <div class="w-9 h-9 rounded-lg bg-gradient-to-tr from-cyber-purple to-cyber-cyan p-[1px]">
                                <div class="w-full h-full bg-obsidian-surface rounded-lg flex items-center justify-center font-bold text-xs text-cyber-cyan">
                                    {{ mb_substr(auth()->user()->name, 0, 2) }}
                                </div>
                            </div>
                            <div class="hidden xl:block text-right">
                                <div class="text-xs font-bold text-white font-cairo leading-none">{{ auth()->user()->name }}</div>
                                <div class="text-[10px] font-mono text-cyber-cyan mt-0.5 uppercase">{{ auth()->user()->role }}</div>
                            </div>
                        </a>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" title="تسجيل الخروج" class="p-2 text-slate-400 hover:text-rose-400 rounded-lg hover:bg-obsidian-surface transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                            </button>
                        </form>
                    </div>
                @else
                    <div class="flex items-center gap-2">
                        <a href="{{ route('login') }}" class="px-3.5 py-1.5 rounded-xl bg-obsidian-surface border border-obsidian-border text-xs font-cairo text-slate-200 hover:text-cyber-cyan hover:border-cyber-cyan transition-all">
                            دخول
                        </a>
                        <a href="{{ route('register') }}" class="px-3.5 py-1.5 rounded-xl bg-cyber-cyan text-obsidian-base font-bold text-xs font-cairo shadow-neon-cyan hover:scale-105 active:scale-95 transition-all">
                            تسجيل
                        </a>
                    </div>
                @endauth
            </div>

        </div>
    </header>

    <!-- Main Command Center Shell Layout -->
    <div class="flex-1 max-w-[1920px] w-full mx-auto flex">
        
        <!-- Command Center Left Navigation Dock (Wide Sidebar) -->
        <aside class="w-72 hidden lg:flex flex-col justify-between glass-obsidian border-l border-obsidian-border/70 p-4 shrink-0 min-h-[calc(100vh-65px)]">
            <div class="space-y-6">
                <!-- Operations Section -->
                <div>
                    <span class="px-3 text-[10px] font-mono uppercase tracking-wider text-slate-500 font-bold">منظومة القيادة والعمليات</span>
                    <nav class="mt-2 space-y-1">
                        <a href="{{ url('/') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->is('/') ? 'bg-cyber-cyan/10 text-cyber-cyan border border-cyber-cyan/30' : 'text-slate-300 hover:text-cyber-cyan hover:bg-obsidian-surface/60' }} transition-all">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-cyber-cyan" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                                <span>لوحة القيادة المركزية</span>
                            </div>
                            <span class="w-2 h-2 rounded-full bg-cyber-cyan animate-pulse"></span>
                        </a>

                        <a href="{{ route('services.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->is('services*') ? 'bg-cyber-cyan/10 text-cyber-cyan border border-cyber-cyan/30' : 'text-slate-300 hover:text-cyber-cyan hover:bg-obsidian-surface/60' }} transition-all">
                            <svg class="w-5 h-5 text-slate-400 group-hover:text-cyber-cyan" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                            <span>كتالوج الخدمات السيادية</span>
                        </a>

                        <a href="{{ route('portal.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->is('portal') ? 'bg-cyber-cyan/10 text-cyber-cyan border border-cyber-cyan/30' : 'text-slate-300 hover:text-cyber-cyan hover:bg-obsidian-surface/60' }} transition-all">
                            <svg class="w-5 h-5 text-slate-400 group-hover:text-cyber-cyan" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                            <span>بوابة العملاء والمحفظة</span>
                        </a>

                        <a href="{{ route('portal.bookings') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->is('portal/bookings*') ? 'bg-cyber-cyan/10 text-cyber-cyan border border-cyber-cyan/30' : 'text-slate-300 hover:text-cyber-cyan hover:bg-obsidian-surface/60' }} transition-all">
                            <svg class="w-5 h-5 text-slate-400 group-hover:text-cyber-cyan" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <span>حجز ومتابعة الخدمات</span>
                        </a>

                        <a href="{{ route('portal.tickets') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->is('portal/tickets*') ? 'bg-cyber-cyan/10 text-cyber-cyan border border-cyber-cyan/30' : 'text-slate-300 hover:text-cyber-purple hover:bg-obsidian-surface/60' }} transition-all">
                            <svg class="w-5 h-5 text-cyber-purple" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <span>تذاكر الدعم والاستشارات</span>
                        </a>

                        @if(auth()->check() && in_array(auth()->user()->role, ['admin', 'staff']))
                            <div class="pt-3 border-t border-obsidian-border/60">
                                <span class="px-3 text-[10px] font-mono uppercase tracking-wider text-cyber-gold font-bold">إدارة العمليات والـ HQ</span>
                                <a href="{{ route('admin.index') }}" class="mt-1 flex items-center justify-between px-3 py-2 rounded-xl text-sm font-bold bg-cyber-gold/15 text-cyber-gold border border-cyber-gold/40 shadow-neon-gold transition-all">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-cyber-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <span>مركز القيادة Admin HQ</span>
                                    </div>
                                    <span class="w-2 h-2 rounded-full bg-cyber-gold animate-pulse"></span>
                                </a>
                            </div>
                        @endif
                    </nav>
                </div>
            </div>

            <!-- Sidebar Diagnostic Card -->
            <div class="p-3 rounded-2xl bg-obsidian-surface border border-obsidian-border space-y-2">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-slate-400 font-mono">حالة العقدة:</span>
                    <span class="text-cyber-cyan font-bold font-mono">نشط ومستقر</span>
                </div>
                <div class="w-full bg-obsidian-base rounded-full h-1.5 overflow-hidden">
                    <div class="bg-gradient-to-r from-cyber-purple to-cyber-cyan h-1.5 rounded-full w-[94%]"></div>
                </div>
                <div class="flex items-center justify-between text-[10px] text-slate-500 font-mono">
                    <span>حمولة الخادم: 18%</span>
                    <span>استجابة: 99.98%</span>
                </div>
            </div>
        </aside>

        <!-- Main Operational Content Viewport -->
        <main class="flex-1 p-6 lg:p-8 overflow-y-auto">
            @yield('content')
        </main>

    </div>

    <!-- Wide Command Center Status Ribbon (Footer) -->
    <footer class="w-full glass-obsidian border-t border-obsidian-border/80 px-6 py-2.5 text-xs text-slate-400">
        <div class="max-w-[1920px] mx-auto flex flex-col sm:flex-row items-center justify-between gap-3 font-mono">
            <div class="flex items-center gap-4">
                <span class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-cyber-cyan"></span>
                    <span>SOULY DIGITAL CITADEL &copy; {{ date('Y') }}</span>
                </span>
                <span class="hidden md:inline text-obsidian-border">|</span>
                <span class="hidden md:inline text-slate-400">إصدار الهيكل: Laravel 13.38 Native (Blade + Alpine + GSAP)</span>
            </div>

            <div class="flex items-center gap-4">
                <span class="text-cyber-gold">المنطقة الزمنية: Africa/Cairo</span>
                <span class="text-obsidian-border">|</span>
                <span class="text-cyber-purple">محرك التخزين: <span x-text="$store.citadel.storageEngine">SoulyDB</span></span>
                <span class="text-obsidian-border">|</span>
                <span class="text-slate-300">الاتجاه: RTL (العربية)</span>
            </div>
        </div>
    </footer>
</div>
@endsection
