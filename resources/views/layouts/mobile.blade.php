@extends('layouts.master')

@section('layout-body')
<div x-data="{
        mobileMemberMenuOpen: false,
        mobileHomeHubOpen: false,
        mobileAdminStoresSheetOpen: false,
        mobileAdminMenuSheetOpen: false,
        headerCompact: false,

        init() {
            this.closeAllPanels();
            window.addEventListener('scroll', () => {
                this.headerCompact = (window.scrollY || window.pageYOffset) > 35;
            }, { passive: true });
        },

        haptic() {
            if (navigator.vibrate) {
                try { navigator.vibrate(12); } catch(e) {}
            }
        },

        closeAllPanels(except = null) {
            if (except !== 'memberMenu') this.mobileMemberMenuOpen = false;
            if (except !== 'homeHub') this.mobileHomeHubOpen = false;
            if (except !== 'adminStores') this.mobileAdminStoresSheetOpen = false;
            if (except !== 'adminMenu') this.mobileAdminMenuSheetOpen = false;

            if (this.$store && this.$store.citadel) {
                if (except !== 'mobileNav') this.$store.citadel.mobileNavOpen = false;
                if (except !== 'commandPalette') this.$store.citadel.commandPaletteOpen = false;
                if (except !== 'ai') this.$store.citadel.aiSheetOpen = false;
            }
        },

        toggleSheet(sheetName) {
            this.haptic();
            const willOpen = !this[sheetName];
            this.closeAllPanels(willOpen ? sheetName : null);
            this[sheetName] = willOpen;
        },

        toggleAiChat() {
            this.haptic();
            const isCurrentlyOpen = Boolean(this.$store && this.$store.citadel && this.$store.citadel.aiSheetOpen);
            const nextState = !isCurrentlyOpen;
            this.closeAllPanels(nextState ? 'ai' : null);
            if (this.$store && this.$store.citadel) {
                this.$store.citadel.aiSheetOpen = nextState;
            }
        },

        isAiOpen() {
            return Boolean(this.$store && this.$store.citadel && this.$store.citadel.aiSheetOpen);
        }
     }"
     @keydown.escape.window="closeAllPanels()"
     class="min-h-screen flex flex-col w-full bg-obsidian-base relative overflow-x-clip max-w-[100vw]"
     dir="rtl">

    <!-- ===================================================================== -->
    <!-- 1. TOP MOBILE HEADER                                                  -->
    <!-- ===================================================================== -->
    <header class="sticky top-0 z-40 w-full backdrop-blur-2xl bg-[#05050A]/90 border-b border-white/10 px-3 safe-top transition-all duration-200"
            :class="headerCompact ? 'h-13 shadow-[0_6px_25px_rgba(0,0,0,0.85)] border-cyber-cyan/30' : 'h-15'">
        
        <div class="h-full flex items-center justify-between gap-2">
            <!-- Brand Logo & Page Title -->
            <div class="flex items-center gap-2 min-w-0">
                <a href="{{ route('home') }}" @click="haptic()" class="relative flex items-center shrink-0">
                    <x-souly-logo size="xs" :showText="false" />
                    <span class="absolute -bottom-0.5 -left-0.5 w-2 h-2 rounded-full bg-emerald-400 ring-2 ring-[#05050A]"></span>
                </a>
                <div class="min-w-0">
                    <h1 class="text-xs font-black text-white font-cairo truncate max-w-[140px] sm:max-w-[200px]">
                        @hasSection('page-title')
                            @yield('page-title')
                        @else
                            سولي | Souly
                        @endif
                    </h1>
                    <div class="flex items-center gap-1 text-[9px] text-cyber-cyan font-mono">
                        <span class="w-1 h-1 rounded-full bg-cyber-cyan animate-ping"></span>
                        <span>SOULY VAULT OS</span>
                    </div>
                </div>
            </div>

            <!-- Left Actions -->
            <div class="flex items-center gap-1.5 shrink-0">
                <!-- Search Button -->
                <button type="button"
                        @click.stop="haptic(); closeAllPanels('commandPalette'); if ($store.citadel) $store.citadel.commandPaletteOpen = true;"
                        aria-label="بحث سريع"
                        class="w-8 h-8 rounded-xl bg-[#121422] border border-white/10 text-slate-300 active:scale-90 flex items-center justify-center transition-all cursor-pointer">
                    <svg class="w-4 h-4 text-cyber-cyan" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </button>

                @guest
                    <!-- Quick Login / Register Pill -->
                    <a href="{{ route('login') }}"
                       @click="haptic()"
                       class="px-3 py-1.5 rounded-xl bg-gradient-to-r from-cyber-cyan to-[#00BB9B] text-[#05050A] font-black text-[11px] font-cairo shadow-[0_0_15px_rgba(0,245,212,0.4)] flex items-center gap-1 active:scale-95 transition-all">
                        <span>دخول</span>
                        <span>🔑</span>
                    </a>
                @endguest

                @auth
                    @if(auth()->user()->isAdmin() || auth()->user()->isStaff())
                        <a href="{{ route('admin.index') }}"
                           class="px-2.5 py-1 rounded-xl bg-amber-500/15 text-cyber-gold border border-amber-500/40 text-[10px] font-black font-cairo flex items-center gap-1">
                            <span>🛡️</span>
                            <span>القيادة</span>
                        </a>
                    @else
                        <!-- Client Wallet Chip -->
                        <a href="{{ route('portal.wallet') }}"
                           @click="haptic()"
                           class="flex items-center gap-1 px-2.5 py-1 rounded-xl bg-[#121422] border border-emerald-500/40 text-[11px] font-bold font-mono text-emerald-400 active:scale-95 transition-all">
                            <span>💳</span>
                            <span>{{ number_format(auth()->user()?->wallet?->balance ?? 0, 0) }}</span>
                            <span class="text-[9px] font-cairo">ج.م</span>
                        </a>
                    @endif

                    <!-- Notification Bell -->
                    <button @click.stop="haptic(); closeAllPanels('notifications'); if ($store.citadel) $store.citadel.openNotificationCenter();"
                            type="button"
                            class="relative w-8 h-8 rounded-xl bg-[#121422] border border-white/10 text-slate-300 flex items-center justify-center active:scale-90 transition-all cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <template x-if="$store.citadel && $store.citadel.unreadNotificationsCount > 0">
                            <span class="absolute -top-1 -right-1 min-w-[15px] h-[15px] px-1 rounded-full bg-rose-500 text-[9px] font-bold text-white flex items-center justify-center font-mono animate-pulse"
                                  x-text="$store.citadel.unreadNotificationsCount"></span>
                        </template>
                    </button>
                @endauth

                <!-- Mobile Drawer Trigger -->
                <button type="button"
                        @click.stop="haptic(); if ($store.citadel) $store.citadel.mobileNavOpen = true;"
                        aria-label="القائمة الجانبية"
                        class="w-8 h-8 rounded-xl bg-[#121422] border border-white/10 text-slate-200 active:scale-90 flex items-center justify-center transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </header>

    <!-- ===================================================================== -->
    <!-- 2. MAIN CONTENT VIEWPORT                                              -->
    <!-- ===================================================================== -->
    <main class="flex-1 w-full max-w-[100vw] overflow-x-clip px-3 pt-3 pb-28 safe-bottom">
        @yield('content')
    </main>

    <!-- ===================================================================== -->
    <!-- 3. MOBILE FLOATING APP BOTTOM BAR                                     -->
    <!-- ===================================================================== -->
    <div class="fixed bottom-0 inset-x-0 z-40 px-2.5 pb-2 pt-1 safe-bottom pointer-events-none">
        <nav class="pointer-events-auto h-16 w-full max-w-md mx-auto rounded-2xl bg-[#080914]/95 backdrop-blur-2xl border border-white/15 shadow-[0_-8px_35px_rgba(0,0,0,0.9),0_0_20px_rgba(0,245,212,0.12)] px-1.5">
            <div class="h-full grid grid-cols-5 items-center text-center relative">
                
                @guest
                    <!-- ==================== GUEST BOTTOM BAR ==================== -->
                    <!-- Tab 1: سولي AI (موقع الرئيسية سابقاً) -->
                    <button type="button"
                            @click.stop="toggleAiChat()"
                            :class="isAiOpen() ? 'text-cyber-cyan font-black' : 'text-slate-400 hover:text-white'"
                            class="relative flex flex-col items-center justify-center gap-0.5 py-1 transition-all cursor-pointer">
                        <template x-if="isAiOpen()">
                            <span class="absolute -top-1 w-5 h-0.5 rounded-full bg-cyber-cyan shadow-[0_0_8px_#00F5D4]"></span>
                        </template>
                        <div class="relative">
                            <span class="text-lg">🤖</span>
                            <span class="absolute -top-0.5 -right-1 w-1.5 h-1.5 rounded-full bg-cyber-cyan animate-pulse"></span>
                        </div>
                        <span class="text-[10px] font-cairo">سولي AI</span>
                    </button>

                    <!-- Tab 2: المتجر (يودي لمتجر الهاردوير مباشرة) -->
                    <a href="{{ url('/store/hardware') }}"
                       @click="haptic()"
                       class="relative flex flex-col items-center justify-center gap-0.5 py-1 transition-all {{ request()->is('store/hardware*') ? 'text-cyber-cyan font-black' : 'text-slate-400 hover:text-white' }}">
                        @if(request()->is('store/hardware*'))
                            <span class="absolute -top-1 w-5 h-0.5 rounded-full bg-cyber-cyan shadow-[0_0_8px_#00F5D4]"></span>
                        @endif
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <span class="text-[10px] font-cairo">المتجر</span>
                    </a>

                    <!-- Tab 3 (الوسط): الرئيسية (موقع سولي AI سابقاً) -->
                    <div class="flex flex-col items-center justify-center -mt-6">
                        <a href="{{ route('home') }}"
                           @click="haptic()"
                           class="relative flex flex-col items-center justify-center focus:outline-none active:scale-90 transition-transform cursor-pointer">
                            <div class="relative w-13 h-13 rounded-2xl rotate-45 bg-gradient-to-tr from-cyber-cyan via-[#121422] to-cyber-purple p-[1.5px] shadow-[0_0_25px_rgba(0,245,212,0.45)] flex items-center justify-center"
                                 :class="{{ request()->routeIs('home') ? 'ring-2 ring-cyber-cyan scale-105' : '' }}">
                                <div class="w-full h-full rounded-2xl bg-[#070812] -rotate-45 flex items-center justify-center relative overflow-hidden">
                                    <svg class="w-6 h-6 text-cyber-cyan" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                    </svg>
                                </div>
                            </div>
                            <span class="text-[9px] font-black font-cairo text-cyber-cyan mt-1">الرئيسية</span>
                        </a>
                    </div>

                    <!-- Tab 4: الخدمات (القطاعات سابقاً) -->
                    <a href="{{ route('services.index') }}"
                       @click="haptic()"
                       class="relative flex flex-col items-center justify-center gap-0.5 py-1 transition-all {{ request()->routeIs('services.*') ? 'text-cyber-cyan font-black' : 'text-slate-400 hover:text-white' }}">
                        @if(request()->routeIs('services.*'))
                            <span class="absolute -top-1 w-5 h-0.5 rounded-full bg-cyber-cyan shadow-[0_0_8px_#00F5D4]"></span>
                        @endif
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                        <span class="text-[10px] font-cairo">الخدمات</span>
                    </a>

                    <!-- Tab 5: دخول (سريع سابقاً) -->
                    <a href="{{ route('login') }}"
                       @click="haptic()"
                       class="relative flex flex-col items-center justify-center gap-0.5 py-1 transition-all {{ request()->routeIs('login') ? 'text-cyber-gold font-black' : 'text-slate-400 hover:text-white' }}">
                        @if(request()->routeIs('login'))
                            <span class="absolute -top-1 w-5 h-0.5 rounded-full bg-cyber-gold shadow-[0_0_8px_#FFB800]"></span>
                        @endif
                        <svg class="w-5 h-5 text-cyber-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                        <span class="text-[10px] font-cairo text-cyber-gold font-bold">دخول</span>
                    </a>
                @endguest

                @auth
                    @if(auth()->user()->isAdmin() || auth()->user()->isStaff())
                        <!-- Admin Dock -->
                        <a href="{{ route('admin.index') }}" @click="haptic()" class="flex flex-col items-center justify-center gap-0.5 py-1 {{ request()->routeIs('admin.index') ? 'text-cyber-gold font-black' : 'text-slate-400' }}">
                            <span class="text-base">🛰️</span>
                            <span class="text-[10px] font-cairo">القيادة</span>
                        </a>
                        <a href="{{ route('admin.bookings.index') }}" @click="haptic()" class="flex flex-col items-center justify-center gap-0.5 py-1 {{ request()->routeIs('admin.bookings.*') ? 'text-cyber-gold font-black' : 'text-slate-400' }}">
                            <span class="text-base">📅</span>
                            <span class="text-[10px] font-cairo">الحجوزات</span>
                        </a>
                        <div class="flex flex-col items-center justify-center -mt-6">
                            <button type="button" @click.stop="toggleAiChat()" class="relative flex flex-col items-center justify-center cursor-pointer">
                                <div class="w-13 h-13 rounded-2xl bg-gradient-to-tr from-amber-500 to-cyber-gold p-[1.5px] shadow-neon-gold flex items-center justify-center">
                                    <div class="w-full h-full rounded-2xl bg-[#070812] flex items-center justify-center">
                                        <span class="text-lg" x-text="isAiOpen() ? '✕' : '🤖'">🤖</span>
                                    </div>
                                </div>
                                <span class="text-[9px] font-black font-cairo text-cyber-gold mt-1">سولي AI</span>
                            </button>
                        </div>
                        <button type="button" @click.stop="toggleSheet('mobileAdminStoresSheetOpen')" class="flex flex-col items-center justify-center gap-0.5 py-1 text-slate-400 cursor-pointer">
                            <span class="text-base">🏪</span>
                            <span class="text-[10px] font-cairo">المتاجر</span>
                        </button>
                        <button type="button" @click.stop="toggleSheet('mobileAdminMenuSheetOpen')" class="flex flex-col items-center justify-center gap-0.5 py-1 text-slate-400 cursor-pointer">
                            <span class="text-base">⚙️</span>
                            <span class="text-[10px] font-cairo">العمليات</span>
                        </button>
                    @else
                        <!-- ==================== MEMBER LOGGED-IN BOTTOM BAR ==================== -->
                        <!-- Tab 1: شات سولي AI -->
                        <button type="button"
                                @click.stop="toggleAiChat()"
                                :class="isAiOpen() ? 'text-cyber-cyan font-black' : 'text-slate-400 hover:text-white'"
                                class="relative flex flex-col items-center justify-center gap-0.5 py-1 transition-all cursor-pointer">
                            <span class="text-base">🤖</span>
                            <span class="text-[10px] font-cairo">سولي AI</span>
                        </button>

                        <!-- Tab 2: المتجر (يفتح popup menu: المتجر، الطلبات، الحجوزات، اجهزتي) -->
                        <button type="button"
                                @click.stop="toggleSheet('mobileMemberMenuOpen')"
                                :class="mobileMemberMenuOpen || {{ request()->is('store*') || request()->is('portal/hardware*') || request()->is('portal/devices*') || request()->is('portal/bookings*') ? 'true' : 'false' }} ? 'text-cyber-cyan font-black' : 'text-slate-400 hover:text-white'"
                                class="relative flex flex-col items-center justify-center gap-0.5 py-1 transition-all cursor-pointer">
                            <template x-if="mobileMemberMenuOpen">
                                <span class="absolute -top-1 w-5 h-0.5 rounded-full bg-cyber-cyan shadow-[0_0_8px_#00F5D4]"></span>
                            </template>
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            <span class="text-[10px] font-cairo">المتجر</span>
                        </button>

                        <!-- Tab 3 (الوسط): زر الرئيسية يفتح بوب اب باختيارين: الرئيسية و لوحة التحكم -->
                        <div class="flex flex-col items-center justify-center -mt-6">
                            <button type="button"
                                    @click.stop="toggleSheet('mobileHomeHubOpen')"
                                    class="relative flex flex-col items-center justify-center focus:outline-none active:scale-90 transition-transform cursor-pointer">
                                <div class="relative w-13 h-13 rounded-2xl rotate-45 bg-gradient-to-tr from-cyber-cyan via-[#121422] to-cyber-purple p-[1.5px] shadow-[0_0_25px_rgba(0,245,212,0.45)] flex items-center justify-center"
                                     :class="mobileHomeHubOpen ? 'ring-2 ring-cyber-cyan scale-105' : ''">
                                    <div class="w-full h-full rounded-2xl bg-[#070812] -rotate-45 flex items-center justify-center relative overflow-hidden">
                                        <svg class="w-6 h-6 text-cyber-cyan" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                        </svg>
                                    </div>
                                </div>
                                <span class="text-[9px] font-black font-cairo text-cyber-cyan mt-1">الرئيسية ▾</span>
                            </button>
                        </div>

                        <!-- Tab 4: المحفظة -->
                        <a href="{{ route('portal.wallet') }}"
                           @click="haptic()"
                           class="relative flex flex-col items-center justify-center gap-0.5 py-1 transition-all {{ request()->routeIs('portal.wallet') ? 'text-emerald-400 font-black' : 'text-slate-400 hover:text-white' }}">
                            @if(request()->routeIs('portal.wallet'))
                                <span class="absolute -top-1 w-5 h-0.5 rounded-full bg-emerald-400 shadow-[0_0_8px_#10B981]"></span>
                            @endif
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                            <span class="text-[10px] font-cairo">محفظتي</span>
                        </a>

                        <!-- Tab 5: لوحة التحكم المباشرة -->
                        <a href="{{ route('portal.index') }}"
                           @click="haptic()"
                           class="relative flex flex-col items-center justify-center gap-0.5 py-1 transition-all {{ request()->routeIs('portal.index') ? 'text-cyber-cyan font-black' : 'text-slate-400 hover:text-white' }}">
                            @if(request()->routeIs('portal.index'))
                                <span class="absolute -top-1 w-5 h-0.5 rounded-full bg-cyber-cyan shadow-[0_0_8px_#00F5D4]"></span>
                            @endif
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span class="text-[10px] font-cairo">لوحتي</span>
                        </a>
                    @endif
                @endauth

            </div>
        </nav>
    </div>

    <!-- ===================================================================== -->
    <!-- 4. POPUP 1: الرئيسية في النص (اختيارين: الرئيسية و لوحة التحكم)         -->
    <!-- ===================================================================== -->
    <div x-show="mobileHomeHubOpen"
         x-cloak
         style="display: none;"
         @click="mobileHomeHubOpen = false"
         class="fixed inset-0 z-[99998] bg-black/80 backdrop-blur-md flex items-end justify-center">
        <div @click.stop
             x-show="mobileHomeHubOpen"
             x-transition:enter="transition ease-out duration-250 transform"
             x-transition:enter-start="translate-y-full"
             x-transition:enter-end="translate-y-0"
             class="w-full max-w-lg bg-[#0B0C16] border-t border-cyber-cyan/50 rounded-t-3xl p-5 space-y-4 safe-bottom shadow-[0_-15px_40px_rgba(0,0,0,0.9)]">
            
            <div class="w-12 h-1.5 rounded-full bg-slate-600 mx-auto" @click="mobileHomeHubOpen = false"></div>
            
            <div class="flex items-center justify-between border-b border-white/10 pb-3">
                <span class="text-sm font-black text-white font-cairo flex items-center gap-2">
                    <span>⚡</span>
                    <span>اختر وجهتك السريعة</span>
                </span>
                <button type="button" @click="mobileHomeHubOpen = false" class="text-slate-400 hover:text-white cursor-pointer">✕</button>
            </div>

            <!-- Choice 1: الصفحة الرئيسية & Choice 2: لوحة التحكم -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs font-cairo font-bold">
                <a href="{{ route('home') }}"
                   @click="mobileHomeHubOpen = false"
                   class="p-4 rounded-2xl bg-[#121422] border border-cyber-cyan/40 hover:border-cyber-cyan flex items-center justify-between gap-3 group transition-all">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-cyber-cyan/15 text-cyber-cyan flex items-center justify-center text-xl shrink-0">
                            🏠
                        </div>
                        <div>
                            <strong class="text-sm font-bold text-white block group-hover:text-cyber-cyan">الصفحة الرئيسية</strong>
                            <span class="text-[11px] text-slate-400 font-normal">استعراض المنظومة والخدمات</span>
                        </div>
                    </div>
                    <span class="text-cyber-cyan font-bold text-xs">انتقال ⇥</span>
                </a>

                <a href="{{ route('portal.index') }}"
                   @click="mobileHomeHubOpen = false"
                   class="p-4 rounded-2xl bg-[#121422] border border-cyber-purple/40 hover:border-cyber-purple flex items-center justify-between gap-3 group transition-all">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-cyber-purple/20 text-purple-300 flex items-center justify-center text-xl shrink-0">
                            📊
                        </div>
                        <div>
                            <strong class="text-sm font-bold text-white block group-hover:text-purple-300">لوحة التحكم</strong>
                            <span class="text-[11px] text-slate-400 font-normal">إدارة الحساب ومتابعة العمليات</span>
                        </div>
                    </div>
                    <span class="text-purple-300 font-bold text-xs">فتح ⇥</span>
                </a>
            </div>
        </div>
    </div>

    <!-- ===================================================================== -->
    <!-- 5. POPUP 2: منيو المتجر للعضو (المتجر، الطلبات، الحجوزات، أجهزتي)       -->
    <!-- ===================================================================== -->
    <div x-show="mobileMemberMenuOpen"
         x-cloak
         style="display: none;"
         @click="mobileMemberMenuOpen = false"
         class="fixed inset-0 z-[99998] bg-black/80 backdrop-blur-md flex items-end justify-center">
        <div @click.stop
             x-show="mobileMemberMenuOpen"
             x-transition:enter="transition ease-out duration-250 transform"
             x-transition:enter-start="translate-y-full"
             x-transition:enter-end="translate-y-0"
             class="w-full max-w-lg bg-[#0B0C16] border-t border-cyber-cyan/50 rounded-t-3xl p-5 space-y-4 safe-bottom shadow-[0_-15px_40px_rgba(0,0,0,0.9)] max-h-[88dvh] overflow-y-auto">
            
            <div class="w-12 h-1.5 rounded-full bg-slate-600 mx-auto" @click="mobileMemberMenuOpen = false"></div>
            
            <div class="flex items-center justify-between border-b border-white/10 pb-3">
                <span class="text-sm font-black text-white font-cairo flex items-center gap-2">
                    <span class="text-lg">🏪</span>
                    <span>مركز المتاجر وإدارة المقتنيات</span>
                </span>
                <button type="button" @click="mobileMemberMenuOpen = false" class="text-slate-400 hover:text-white cursor-pointer">✕</button>
            </div>

            <!-- The 4 Required Items -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 text-xs font-cairo font-bold">
                <!-- 1. المتجر -->
                <a href="{{ url('/store/hardware') }}"
                   @click="mobileMemberMenuOpen = false"
                   class="p-3.5 rounded-2xl bg-[#121422] border border-white/10 hover:border-cyber-cyan flex items-center justify-between gap-3 group transition-all">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">🛒</span>
                        <div>
                            <strong class="text-sm text-white block group-hover:text-cyber-cyan">المتجر</strong>
                            <span class="text-[10px] text-slate-400 font-normal">أجهزة كاشير، هاردوير، وطابعات</span>
                        </div>
                    </div>
                    <span class="text-cyber-cyan font-bold text-xs">⇥</span>
                </a>

                <!-- 2. الطلبات -->
                <a href="{{ route('portal.hardware') }}"
                   @click="mobileMemberMenuOpen = false"
                   class="p-3.5 rounded-2xl bg-[#121422] border border-white/10 hover:border-purple-400 flex items-center justify-between gap-3 group transition-all">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">📦</span>
                        <div>
                            <strong class="text-sm text-white block group-hover:text-purple-300">الطلبات</strong>
                            <span class="text-[10px] text-slate-400 font-normal">تتبع الشحنات وسيريالات الضمان</span>
                        </div>
                    </div>
                    <span class="text-purple-300 font-bold text-xs">⇥</span>
                </a>

                <!-- 3. الحجوزات -->
                <a href="{{ route('portal.bookings') }}"
                   @click="mobileMemberMenuOpen = false"
                   class="p-3.5 rounded-2xl bg-[#121422] border border-white/10 hover:border-cyber-gold flex items-center justify-between gap-3 group transition-all">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">📅</span>
                        <div>
                            <strong class="text-sm text-white block group-hover:text-cyber-gold">الحجوزات</strong>
                            <span class="text-[10px] text-slate-400 font-normal">الزيارات الهندسية وخدمات سولي</span>
                        </div>
                    </div>
                    <span class="text-cyber-gold font-bold text-xs">⇥</span>
                </a>

                <!-- 4. أجهزتي -->
                <a href="{{ route('portal.devices') }}"
                   @click="mobileMemberMenuOpen = false"
                   class="p-3.5 rounded-2xl bg-[#121422] border border-emerald-500/30 hover:border-emerald-400 flex items-center justify-between gap-3 group transition-all">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">💻</span>
                        <div>
                            <strong class="text-sm text-white block group-hover:text-emerald-400">أجهزتي</strong>
                            <span class="text-[10px] text-slate-400 font-normal">أجهزتك المسجلة وطلب صيانتها</span>
                        </div>
                    </div>
                    <span class="text-emerald-400 font-bold text-xs">⇥</span>
                </a>
            </div>

            <!-- Additional Fast Access: متجر BrandERP -->
            <div class="pt-2 border-t border-white/10">
                <a href="{{ url('/store/branderp') }}"
                   @click="mobileMemberMenuOpen = false"
                   class="p-3 rounded-2xl bg-gradient-to-r from-cyber-cyan/15 to-[#121422] border border-cyber-cyan/30 flex items-center justify-between text-xs font-cairo">
                    <div class="flex items-center gap-2.5">
                        <span>🏪</span>
                        <span class="text-white font-bold">متجر منظومة BrandERP (البرمجيات والكاشير السحابي)</span>
                    </div>
                    <span class="text-cyber-cyan font-bold">فتح ⇥</span>
                </a>
            </div>
        </div>
    </div>

    <!-- C. Admin Tactical Menu Sheet -->
    <div x-show="mobileAdminMenuSheetOpen"
         x-cloak
         style="display: none;"
         @click="mobileAdminMenuSheetOpen = false"
         class="fixed inset-0 z-[99998] bg-black/85 backdrop-blur-md flex items-end justify-center">
        <div @click.stop class="w-full max-w-lg bg-[#0B0C16] border-t border-cyber-gold/50 rounded-t-3xl p-5 space-y-4 safe-bottom max-h-[85dvh] overflow-y-auto">
            <div class="w-12 h-1.5 rounded-full bg-slate-600 mx-auto"></div>
            <div class="flex items-center justify-between pb-3 border-b border-white/10">
                <h3 class="text-sm font-black text-white font-cairo">🛡️ عمليات المدير التكتيكية</h3>
                <button type="button" @click="mobileAdminMenuSheetOpen = false" class="p-1.5 text-slate-400 hover:text-white cursor-pointer">✕</button>
            </div>
            <div class="grid grid-cols-2 gap-2 text-xs font-cairo font-bold">
                <a href="{{ route('admin.bookings.index') }}" class="p-3 rounded-2xl bg-[#121422] border border-white/10 text-white">📅 حجوزات الخدمات</a>
                <a href="{{ route('admin.leads.index') }}" class="p-3 rounded-2xl bg-[#121422] border border-white/10 text-white">🎯 عملاء CRM</a>
                <a href="{{ route('admin.wallets.index') }}" class="p-3 rounded-2xl bg-[#121422] border border-white/10 text-white">💳 اعتمادات المحافظ</a>
                <a href="{{ route('admin.services.index') }}" class="p-3 rounded-2xl bg-[#121422] border border-white/10 text-white">💼 باقات الخدمات</a>
                <a href="{{ route('portal.tickets') }}" class="p-3 rounded-2xl bg-[#121422] border border-white/10 text-white">🎫 تذاكر الدعم</a>
                <a href="{{ route('admin.settings.index') }}" class="p-3 rounded-2xl bg-[#121422] border border-amber-500/30 text-cyber-gold">⚙️ إعدادات النظام</a>
            </div>
        </div>
    </div>

    <!-- 6. MOBILE SLIDE-OVER DRAWER -->
    <div x-show="$store.citadel && $store.citadel.mobileNavOpen"
         x-cloak
         style="display: none;"
         @click="$store.citadel.mobileNavOpen = false"
         class="fixed inset-0 z-[99998] bg-black/80 backdrop-blur-md">
        <div @click.stop
             x-show="$store.citadel && $store.citadel.mobileNavOpen"
             x-transition:enter="transition ease-out duration-250 transform"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             class="fixed inset-y-0 right-0 max-w-xs w-full bg-[#0B0C16] border-l border-white/10 p-6 flex flex-col justify-between safe-top safe-bottom overflow-y-auto">
            <div class="space-y-5">
                <div class="flex items-center justify-between pb-4 border-b border-white/10">
                    <div class="flex items-center gap-2.5">
                        <x-souly-logo size="xs" :showText="false" />
                        <span class="font-extrabold text-sm text-white font-cairo">بوابة قلعة سولي</span>
                    </div>
                    <button type="button" @click="$store.citadel.mobileNavOpen = false" class="p-1.5 text-slate-400 hover:text-white cursor-pointer">✕</button>
                </div>

                <button @click="$store.citadel && $store.citadel.installApp(); $store.citadel.mobileNavOpen = false"
                        type="button"
                        class="w-full flex items-center justify-between p-3 rounded-2xl btn-citadel-primary text-xs shadow-md cursor-pointer">
                    <span>📱 تثبيت تطبيق سولي</span>
                    <span class="text-[10px] bg-black/20 px-2 py-0.5 rounded-full font-black">فوري</span>
                </button>

                <nav class="space-y-1.5 text-xs font-bold font-cairo">
                    <a href="{{ url('/') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->is('/') ? 'bg-cyber-cyan/15 text-cyber-cyan border border-cyber-cyan/30' : 'text-slate-300' }}">🏠 الرئيسية</a>
                    <a href="{{ route('services.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->is('services*') ? 'bg-cyber-cyan/15 text-cyber-cyan border border-cyber-cyan/30' : 'text-slate-300' }}">⚡ كتالوج الخدمات</a>
                    <a href="{{ url('/store/hardware') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-300">🖥️ متجر الهاردوير</a>
                    <a href="{{ url('/store/branderp') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-300">🏪 متجر BrandERP</a>
                    @auth
                        <a href="{{ route('portal.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-cyber-cyan">📊 لوحة التحكم</a>
                        <a href="{{ route('portal.devices') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-emerald-400">💻 أجهزتي وصيانتها</a>
                        <a href="{{ route('portal.bookings') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-300">📅 حجوزاتي الهندسية</a>
                        <a href="{{ route('portal.wallet') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-emerald-400">💳 محفظتي الرقمية</a>
                    @endauth
                </nav>
            </div>

            <div class="pt-4 border-t border-white/10 mt-6">
                @auth
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full py-2.5 rounded-xl bg-rose-500/15 border border-rose-500/30 text-rose-400 font-bold text-xs font-cairo cursor-pointer">
                            تسجيل الخروج
                        </button>
                    </form>
                @else
                    <div class="grid grid-cols-2 gap-2">
                        <a href="{{ route('login') }}" class="py-2.5 rounded-xl bg-[#121422] border border-white/10 text-center text-xs font-cairo font-bold text-white">دخول</a>
                        <a href="{{ route('register') }}" class="py-2.5 rounded-xl btn-citadel-primary text-center text-xs font-cairo font-black">حساب جديد</a>
                    </div>
                @endauth
            </div>
        </div>
    </div>

</div>
@endsection