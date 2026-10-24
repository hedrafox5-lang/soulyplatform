@extends('layouts.master')

@section('layout-body')
<div class="min-h-screen flex flex-col w-full bg-obsidian-base relative pb-24 safe-bottom">
    
    <!-- Native Mobile Top Bar (Elevated App Header) -->
    <header class="sticky top-0 z-40 w-full glass-obsidian border-b border-obsidian-border/70 px-4 py-2.5 safe-top">
        <div class="flex items-center justify-between gap-2">
            
            <!-- Left: Brand Insignia & Live Status Pulse -->
            <a href="{{ url('/') }}" class="flex items-center gap-2.5 group shrink-0">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-cyber-purple via-obsidian-surface to-cyber-cyan p-[1.5px] shadow-neon-cyan group-active:scale-95 transition-transform">
                    <div class="w-full h-full bg-obsidian-surface rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-cyber-cyan animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                </div>
                <div class="leading-tight">
                    <div class="flex items-center gap-1.5">
                        <span class="font-extrabold text-sm text-white font-cairo block">سولي سيتادل</span>
                        <span class="w-1.5 h-1.5 rounded-full bg-cyber-cyan animate-ping"></span>
                    </div>
                    <span class="text-[9px] font-mono text-slate-400">SOULYEG.ONLINE</span>
                </div>
            </a>

            <!-- Right: Quick Wallet Pill, Notifications, Replay Intro & Menu Drawer -->
            <div class="flex items-center gap-2">
                
                <!-- Quick Wallet Balance Pill -->
                <a href="{{ url('/portal') }}" 
                   class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl bg-obsidian-surface border border-cyber-cyan/30 text-xs font-mono group active:scale-95 transition-all">
                    <svg class="w-3.5 h-3.5 text-cyber-cyan" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                    <span class="text-cyber-cyan font-bold text-[11px]" x-text="`${$store.citadel.walletBalance} ج.م`">{{ $citadelWalletBalance ?? '3,500.00' }} ج.م</span>
                </a>

                <!-- Notification Bell -->
                <button @click="$dispatch('open-notification-center')" 
                        class="relative p-2 rounded-xl bg-obsidian-surface border border-obsidian-border text-slate-300 hover:text-cyber-gold transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span class="absolute top-1 right-1 w-2 h-2 rounded-full bg-cyber-gold animate-pulse"></span>
                </button>

                <!-- Replay Cinematic Story Loader Button -->
                <button onclick="SoulyLoader.replay()" 
                        title="إعادة العرض السينمائي"
                        class="p-2 rounded-xl bg-obsidian-surface border border-obsidian-border text-slate-400 hover:text-cyber-cyan transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </button>

                <!-- Action Menu Drawer Toggle -->
                <button @click="$store.citadel.mobileNavOpen = true" 
                        class="p-2 rounded-xl bg-obsidian-surface border border-obsidian-border text-slate-300 hover:text-cyber-cyan focus:outline-none transition-colors">
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
         class="fixed inset-0 z-50 bg-black/75 backdrop-blur-md"
         style="display: none;">
        
        <div @click.away="$store.citadel.mobileNavOpen = false" 
             class="fixed inset-y-0 right-0 max-w-xs w-full glass-obsidian border-l border-obsidian-border p-6 flex flex-col justify-between safe-top safe-bottom">
            <div class="space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-obsidian-border">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-cyber-cyan animate-pulse"></span>
                        <span class="font-extrabold text-base text-slate-100 font-cairo">بوابة التحكم والخدمات</span>
                    </div>
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
                    <a href="{{ url('/#services') }}" @click="$store.citadel.mobileNavOpen = false" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-300 hover:text-cyber-cyan text-sm font-medium">
                        <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                        <span>كتالوج الخدمات الست</span>
                    </a>
                    <a href="{{ url('/portal') }}" @click="$store.citadel.mobileNavOpen = false" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-300 hover:text-cyber-gold text-sm font-medium">
                        <svg class="w-5 h-5 text-cyber-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                        <span>محفظتي وطلباتي</span>
                    </a>
                    <button type="button" @click="$store.citadel.mobileNavOpen = false; SoulyLoader.replay();" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-300 hover:text-cyber-cyan text-sm font-medium text-right">
                        <svg class="w-5 h-5 text-cyber-cyan" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>إعادة العرض السينمائي (25s)</span>
                    </button>
                </nav>
            </div>

            <!-- Drawer Bottom Diagnostics -->
            <div class="p-3 rounded-xl bg-obsidian-surface border border-obsidian-border text-xs font-mono space-y-1">
                <div class="flex justify-between text-slate-400">
                    <span>توقيت القاهرة:</span>
                    <span class="text-cyber-gold" x-text="$store.citadel.cairoTime">--:--</span>
                </div>
                <div class="flex justify-between text-slate-400">
                    <span>محرك التخزين:</span>
                    <span class="text-cyber-cyan uppercase" x-text="$store.citadel.storageEngine">IDB</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Souly AI Quick Sheet (Triggered by Elevated Center Orb) -->
    <div x-show="$store.citadel.aiSheetOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 bg-black/75 backdrop-blur-md flex items-end justify-center"
         style="display: none;">
        
        <div @click.away="$store.citadel.aiSheetOpen = false" 
             class="w-full max-w-lg glass-obsidian border-t border-x border-cyber-purple/50 rounded-t-3xl p-6 safe-bottom shadow-[0_-20px_50px_rgba(123,44,191,0.35)] space-y-5">
            
            <div class="w-12 h-1.5 rounded-full bg-obsidian-border mx-auto"></div>

            <div class="flex items-center justify-between pb-3 border-b border-obsidian-border">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-cyber-purple to-cyber-cyan p-[1px] shadow-neon-purple flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-base text-white font-cairo">سولي AI · موظف المبيعات والدعم</h4>
                        <span class="text-xs text-cyber-cyan font-mono">متصل الآن 24/7 · فهم مصري وعربي</span>
                    </div>
                </div>
                <button @click="$store.citadel.aiSheetOpen = false" class="p-1.5 rounded-lg text-slate-400 hover:text-white">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Instant Prompt Suggestions -->
            <div class="space-y-2">
                <span class="text-[11px] font-mono text-slate-400">أسئلة شائعة لبدء المحادثة:</span>
                <div class="grid grid-cols-1 gap-2">
                    <button @click="$store.citadel.aiSheetOpen = false; window.dispatchEvent(new CustomEvent('send-ai-prompt', { detail: 'أريد معرفة أسعار باقات نظام BrandERP للشركات' }))" 
                            class="p-2.5 rounded-xl bg-obsidian-surface hover:bg-cyber-cyan/10 border border-obsidian-border hover:border-cyber-cyan/30 text-right text-xs text-slate-200 transition-all font-cairo">
                        💼 ما هي أسعار باقات نظام BrandERP والربط الضريبي؟
                    </button>
                    <button @click="$store.citadel.aiSheetOpen = false; window.dispatchEvent(new CustomEvent('send-ai-prompt', { detail: 'كيف يعمل موظف الذكاء الاصطناعي على الواتساب؟' }))" 
                            class="p-2.5 rounded-xl bg-obsidian-surface hover:bg-cyber-purple/10 border border-obsidian-border hover:border-cyber-purple/30 text-right text-xs text-slate-200 transition-all font-cairo">
                        🤖 كيف يمكن لموظف الذكاء الاصطناعي الرد على عملاء متجري؟
                    </button>
                    <button @click="$store.citadel.aiSheetOpen = false; window.dispatchEvent(new CustomEvent('send-ai-prompt', { detail: 'أحتاج فحص أمني واختبار اختراق لسيرفر مشروعي' }))" 
                            class="p-2.5 rounded-xl bg-obsidian-surface hover:bg-cyber-gold/10 border border-obsidian-border hover:border-cyber-gold/30 text-right text-xs text-slate-200 transition-all font-cairo">
                        🛡️ أريد حجز فحص أمن سيبراني واختبار اختراق عاجل.
                    </button>
                </div>
            </div>

            <div class="pt-2">
                <a href="{{ url('/portal#ai-chat') }}" 
                   class="w-full py-3 rounded-xl bg-gradient-to-r from-cyber-purple via-obsidian-surface to-cyber-cyan border border-cyber-cyan/40 text-center text-white font-extrabold text-xs font-cairo shadow-neon-purple block active:scale-95 transition-transform">
                    فتح شات الذكاء الاصطناعي الكامل 💬
                </a>
            </div>

        </div>
    </div>

    <!-- Main Mobile Content Area -->
    <main class="flex-1 p-4 overflow-y-auto">
        @yield('content')
    </main>

    <!-- Native Fixed Glass Bottom Navigation Bar (5-Tab Elevated Shell) -->
    <nav class="fixed bottom-0 inset-x-0 z-40 glass-obsidian border-t border-obsidian-border/90 px-3 py-2 safe-bottom shadow-[0_-10px_30px_rgba(0,0,0,0.8)]">
        <div class="max-w-md mx-auto grid grid-cols-5 items-center">
            
            <!-- Tab 1: Home -->
            <a href="{{ url('/') }}" 
               class="mobile-dock-tab flex flex-col items-center gap-1 py-1 {{ request()->is('/') ? 'text-cyber-cyan' : 'text-slate-400 hover:text-cyber-cyan' }} transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span class="text-[10px] font-medium font-cairo">الرئيسية</span>
            </a>

            <!-- Tab 2: Services -->
            <a href="{{ url('/#services') }}" 
               class="mobile-dock-tab flex flex-col items-center gap-1 py-1 text-slate-400 hover:text-cyber-cyan transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
                <span class="text-[10px] font-medium font-cairo">الخدمات</span>
            </a>

            <!-- Tab 3: Center Elevated Glowing Orb (سولي AI) -->
            <div class="flex justify-center -mt-6">
                <button id="mobile-souly-ai-orb"
                        type="button"
                        class="w-14 h-14 rounded-full bg-gradient-to-tr from-cyber-purple via-obsidian-surface to-cyber-cyan p-[2.5px] shadow-neon-purple focus:outline-none transition-transform relative group">
                    <div class="w-full h-full rounded-full bg-obsidian-base flex flex-col items-center justify-center">
                        <svg class="w-6 h-6 text-cyber-cyan animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <span class="absolute -bottom-4 text-[9px] font-extrabold font-cairo text-cyber-cyan whitespace-nowrap">سولي AI</span>
                </button>
            </div>

            <!-- Tab 4: Wallet & Orders -->
            <a href="{{ url('/portal') }}" 
               class="mobile-dock-tab flex flex-col items-center gap-1 py-1 text-slate-400 hover:text-cyber-gold transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
                <span class="text-[10px] font-medium font-cairo">محفظتي</span>
            </a>

            <!-- Tab 5: Profile / Drawer Menu -->
            <button @click="$store.citadel.mobileNavOpen = true" 
                    type="button"
                    class="mobile-dock-tab flex flex-col items-center gap-1 py-1 text-slate-400 hover:text-cyber-purple transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <span class="text-[10px] font-medium font-cairo">القائمة</span>
            </button>

        </div>
    </nav>

</div>
@endsection
