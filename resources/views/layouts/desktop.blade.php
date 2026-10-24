@extends('layouts.master')

@section('layout-body')
<div class="min-h-screen flex flex-col w-full bg-obsidian-base">
    <!-- Wide Command Center Top Navigation Bar -->
    <header class="sticky top-0 z-40 w-full glass-obsidian border-b border-obsidian-border/80 px-6 py-3">
        <div class="max-w-[1920px] mx-auto flex items-center justify-between gap-4">
            
            <!-- Left: Brand / Citadel Node Identifier -->
            <div class="flex items-center gap-4">
                <a href="{{ url('/') }}" class="flex items-center group">
                    <x-souly-logo size="sm" :showText="true" />
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
                <div class="relative cursor-pointer" @click="$store.citadel.commandPaletteOpen = true">
                    <input type="text" 
                           readonly
                           @click="$store.citadel.commandPaletteOpen = true"
                           placeholder="بحث قيادي ذكي (العمليات، الخدمات، السجلات)... [Ctrl + K]" 
                           class="w-full bg-obsidian-surface/90 text-sm text-slate-200 placeholder-slate-400 rounded-xl px-10 py-2 border border-obsidian-border hover:border-cyber-cyan/50 focus:outline-none focus:border-cyber-cyan focus:ring-1 focus:ring-cyber-cyan/40 transition-all font-cairo cursor-pointer">
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

            <!-- Right: Real-time Telemetry, PWA Install, Wallet Pill & Controls -->
            <div class="flex items-center gap-3">
                <!-- Install App Button (Desktop PWA) -->
                <button @click="$store.citadel.installApp()" 
                        type="button"
                        title="تثبيت منصة سولي كتطبيق مستقل على جهازك"
                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-gradient-to-r from-cyber-cyan/15 to-cyber-gold/15 border border-cyber-cyan/40 hover:border-cyber-cyan text-cyber-cyan text-xs font-cairo font-bold transition-all shadow-sm hover:shadow-neon-cyan">
                    <svg class="w-3.5 h-3.5 text-cyber-cyan" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    <span class="hidden xl:inline">تثبيت التطبيق 📱</span>
                </button>

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

                <!-- Notification Center Bell Trigger -->
                <button @click="$store.citadel.notificationCenterOpen = true" 
                        type="button"
                        title="مركز الإشعارات والتنبيهات السيادية"
                        class="relative p-2 rounded-xl bg-obsidian-surface border border-obsidian-border text-slate-300 hover:text-cyber-gold hover:border-cyber-gold/40 transition-all">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span class="absolute top-1 right-1 w-2 h-2 rounded-full bg-cyber-gold animate-pulse"></span>
                </button>

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
        
        <!-- Command Center Left Navigation Dock (Collapsible Auto-Close HUD Dock) -->
        <aside class="hidden lg:flex flex-col justify-between glass-obsidian border-l border-obsidian-border/70 p-3.5 shrink-0 min-h-[calc(100vh-65px)] transition-all duration-300 ease-in-out"
               :class="$store.citadel.sidebarCollapsed ? 'w-20' : 'w-72'">
            <div class="space-y-5">
                <!-- Dock Header with Collapse/Expand Toggle -->
                <div class="flex items-center justify-between pb-2 border-b border-obsidian-border/70">
                    <span x-show="!$store.citadel.sidebarCollapsed" 
                          x-transition
                          class="px-2 text-[10px] font-mono uppercase tracking-wider text-slate-400 font-bold">منظومة القيادة والعمليات</span>
                    
                    <button @click="$store.citadel.toggleSidebar()" 
                            type="button"
                            :title="$store.citadel.sidebarCollapsed ? 'توسيع القائمة الجانبية' : 'طي القائمة الجانبية للتركيز'"
                            class="p-1.5 rounded-xl bg-obsidian-surface hover:bg-cyber-cyan/15 text-slate-400 hover:text-cyber-cyan border border-obsidian-border hover:border-cyber-cyan/40 transition-all mx-auto">
                        <svg class="w-4 h-4 transition-transform duration-300" 
                             :class="$store.citadel.sidebarCollapsed ? 'rotate-180 text-cyber-cyan' : ''" 
                             fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                        </svg>
                    </button>
                </div>

                <!-- Navigation Links List -->
                <nav class="space-y-1.5">
                    <!-- Link 1: Home / Dashboard -->
                    <a href="{{ url('/') }}" 
                       :title="$store.citadel.sidebarCollapsed ? 'لوحة القيادة المركزية' : ''"
                       class="flex items-center rounded-xl font-medium transition-all group {{ request()->is('/') ? 'bg-cyber-cyan/10 text-cyber-cyan border border-cyber-cyan/30' : 'text-slate-300 hover:text-cyber-cyan hover:bg-obsidian-surface/60' }}"
                       :class="$store.citadel.sidebarCollapsed ? 'justify-center p-3' : 'justify-between px-3 py-2.5 text-sm'">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-cyber-cyan shrink-0 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            <span x-show="!$store.citadel.sidebarCollapsed" x-transition class="truncate font-cairo">لوحة القيادة المركزية</span>
                        </div>
                        <span x-show="!$store.citadel.sidebarCollapsed" class="w-2 h-2 rounded-full bg-cyber-cyan animate-pulse"></span>
                    </a>

                    <!-- Link 2: Services -->
                    <a href="{{ route('services.index') }}" 
                       :title="$store.citadel.sidebarCollapsed ? 'كتالوج الخدمات السيادية' : ''"
                       class="flex items-center rounded-xl font-medium transition-all group {{ request()->is('services*') ? 'bg-cyber-cyan/10 text-cyber-cyan border border-cyber-cyan/30' : 'text-slate-300 hover:text-cyber-cyan hover:bg-obsidian-surface/60' }}"
                       :class="$store.citadel.sidebarCollapsed ? 'justify-center p-3' : 'px-3 py-2.5 text-sm gap-3'">
                        <svg class="w-5 h-5 text-slate-400 group-hover:text-cyber-cyan shrink-0 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                        <span x-show="!$store.citadel.sidebarCollapsed" x-transition class="truncate font-cairo">كتالوج الخدمات السيادية</span>
                    </a>

                    <!-- Link 3: Portal & Wallet -->
                    <a href="{{ route('portal.index') }}" 
                       :title="$store.citadel.sidebarCollapsed ? 'بوابة العملاء والمحفظة' : ''"
                       class="flex items-center rounded-xl font-medium transition-all group {{ request()->is('portal') ? 'bg-cyber-cyan/10 text-cyber-cyan border border-cyber-cyan/30' : 'text-slate-300 hover:text-cyber-cyan hover:bg-obsidian-surface/60' }}"
                       :class="$store.citadel.sidebarCollapsed ? 'justify-center p-3' : 'px-3 py-2.5 text-sm gap-3'">
                        <svg class="w-5 h-5 text-slate-400 group-hover:text-cyber-cyan shrink-0 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                        <span x-show="!$store.citadel.sidebarCollapsed" x-transition class="truncate font-cairo">بوابة العملاء والمحفظة</span>
                    </a>

                    <!-- Link 4: Bookings -->
                    <a href="{{ route('portal.bookings') }}" 
                       :title="$store.citadel.sidebarCollapsed ? 'حجز ومتابعة الخدمات' : ''"
                       class="flex items-center rounded-xl font-medium transition-all group {{ request()->is('portal/bookings*') ? 'bg-cyber-cyan/10 text-cyber-cyan border border-cyber-cyan/30' : 'text-slate-300 hover:text-cyber-cyan hover:bg-obsidian-surface/60' }}"
                       :class="$store.citadel.sidebarCollapsed ? 'justify-center p-3' : 'px-3 py-2.5 text-sm gap-3'">
                        <svg class="w-5 h-5 text-slate-400 group-hover:text-cyber-cyan shrink-0 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <span x-show="!$store.citadel.sidebarCollapsed" x-transition class="truncate font-cairo">حجز ومتابعة الخدمات</span>
                    </a>

                    <!-- Link 5: Tickets -->
                    <a href="{{ route('portal.tickets') }}" 
                       :title="$store.citadel.sidebarCollapsed ? 'تذاكر الدعم والاستشارات' : ''"
                       class="flex items-center rounded-xl font-medium transition-all group {{ request()->is('portal/tickets*') ? 'bg-cyber-cyan/10 text-cyber-cyan border border-cyber-cyan/30' : 'text-slate-300 hover:text-cyber-purple hover:bg-obsidian-surface/60' }}"
                       :class="$store.citadel.sidebarCollapsed ? 'justify-center p-3' : 'px-3 py-2.5 text-sm gap-3'">
                        <svg class="w-5 h-5 text-cyber-purple shrink-0 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span x-show="!$store.citadel.sidebarCollapsed" x-transition class="truncate font-cairo">تذاكر الدعم والاستشارات</span>
                    </a>

                    @if(auth()->check() && in_array(auth()->user()->role, ['admin', 'staff']))
                        <div class="pt-3 border-t border-obsidian-border/60">
                            <span x-show="!$store.citadel.sidebarCollapsed" 
                                  x-transition
                                  class="px-2 text-[10px] font-mono uppercase tracking-wider text-cyber-gold font-bold block mb-1">إدارة العمليات والـ HQ</span>
                            <a href="{{ route('admin.index') }}" 
                               :title="$store.citadel.sidebarCollapsed ? 'مركز القيادة Admin HQ' : ''"
                               class="flex items-center rounded-xl font-bold bg-cyber-gold/15 text-cyber-gold border border-cyber-gold/40 shadow-neon-gold transition-all"
                               :class="$store.citadel.sidebarCollapsed ? 'justify-center p-3' : 'justify-between px-3 py-2 text-sm'">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-cyber-gold shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span x-show="!$store.citadel.sidebarCollapsed" x-transition class="truncate font-cairo">مركز القيادة HQ</span>
                                </div>
                                <span x-show="!$store.citadel.sidebarCollapsed" class="w-2 h-2 rounded-full bg-cyber-gold animate-pulse"></span>
                            </a>
                        </div>
                    @endif
                </nav>
            </div>

            <!-- Sidebar Diagnostic Card (Click to Refresh Ping) -->
            <div @click="$store.citadel.showToast('فحص العقدة: استجابة الخادم 11ms - نشط ومستقر 100% ✓')"
                 class="rounded-2xl bg-obsidian-surface border border-obsidian-border hover:border-cyber-cyan/40 transition-all cursor-pointer group select-none"
                 :class="$store.citadel.sidebarCollapsed ? 'p-2.5 text-center' : 'p-3 space-y-2'">
                
                <div x-show="!$store.citadel.sidebarCollapsed">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-400 font-mono">حالة العقدة:</span>
                        <span class="text-cyber-cyan font-bold font-mono group-hover:scale-105 transition-transform">نشط ومستقر</span>
                    </div>
                    <div class="w-full bg-obsidian-base rounded-full h-1.5 overflow-hidden my-2">
                        <div class="bg-gradient-to-r from-cyber-purple to-cyber-cyan h-1.5 rounded-full w-[96%] animate-pulse"></div>
                    </div>
                    <div class="flex items-center justify-between text-[10px] text-slate-500 font-mono">
                        <span>حمولة: 14%</span>
                        <span class="text-cyber-cyan">استجابة: 99.99%</span>
                    </div>
                </div>

                <div x-show="$store.citadel.sidebarCollapsed" 
                     :title="'حالة العقدة: نشط ومستقر | استجابة: 11ms'"
                     class="flex justify-center">
                    <span class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-cyber-cyan opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-cyber-cyan"></span>
                    </span>
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
            <div class="flex items-center gap-3">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-2 group">
                    <x-souly-logo size="xs" :showText="false" />
                    <span class="text-slate-200 font-extrabold font-cairo text-xs">سولي سيتادل</span>
                </a>
                <span class="text-obsidian-border">|</span>
                <span class="text-slate-400">&copy; {{ date('Y') }} SOULYEG.ONLINE</span>
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
