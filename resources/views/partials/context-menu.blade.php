<!-- Souly Digital Citadel: Smart Role-Aware Cyber Context Menu -->
<div id="souly-context-menu" 
     x-show="$store.citadel.contextMenuOpen" 
     x-transition:enter="transition ease-out duration-150"
     x-transition:enter-start="opacity-0 scale-95"
     x-transition:enter-end="opacity-100 scale-100"
     x-transition:leave="transition ease-in duration-100"
     x-transition:leave-start="opacity-100 scale-100"
     x-transition:leave-end="opacity-0 scale-95"
     :style="`top: ${$store.citadel.contextMenuY}px; left: ${$store.citadel.contextMenuX}px;`"
     @click.away="$store.citadel.contextMenuOpen = false"
     class="fixed z-[99999] w-64 glass-obsidian border border-cyber-cyan/50 rounded-2xl p-2.5 shadow-neon-cyan select-none text-right font-sans space-y-1.5"
     style="display: none;"
     dir="rtl">

    <!-- Role Header Badge -->
    <div class="px-2.5 py-2 border-b border-obsidian-border/80 flex items-center justify-between">
        @auth
            <div class="flex items-center gap-2">
                <div class="w-6 h-6 rounded-lg bg-cyber-cyan/20 border border-cyber-cyan/40 text-cyber-cyan flex items-center justify-center font-bold text-[10px]">
                    {{ mb_substr(auth()->user()->name, 0, 1) }}
                </div>
                <div>
                    <span class="text-xs font-bold text-white font-cairo block truncate max-w-[120px]">{{ auth()->user()->name }}</span>
                    <span class="text-[9px] font-mono text-cyber-cyan uppercase">{{ auth()->user()->role }}</span>
                </div>
            </div>
            @if(auth()->user()->role === 'client')
                <span class="text-[10px] font-mono text-cyber-gold font-bold bg-cyber-gold/10 px-2 py-0.5 rounded border border-cyber-gold/30">
                    {{ number_format(auth()->user()->walletOrCreate()->balance, 0) }} EGP
                </span>
            @endif
        @else
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-cyber-cyan animate-ping"></span>
                <span class="text-xs font-bold text-white font-cairo">قلعة سولي الرقمية</span>
            </div>
            <span class="text-[9px] font-mono text-slate-400">ضيف سيادي</span>
        @endauth
    </div>

    <!-- Action Links -->
    <nav class="space-y-0.5 text-xs font-cairo font-semibold">
        
        <!-- Common Primary: Services & AI -->
        <a href="{{ route('services.index') }}" 
           @click="$store.citadel.contextMenuOpen = false"
           class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-xl text-slate-300 hover:text-white hover:bg-cyber-cyan/15 hover:border hover:border-cyber-cyan/30 transition-all">
            <svg class="w-4 h-4 text-cyber-cyan" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
            </svg>
            <span>استكشاف الخدمات السيادية</span>
        </a>

        <button type="button" 
                @click="$store.citadel.contextMenuOpen = false; $dispatch('open-ai-chat-sheet')" 
                class="w-full flex items-center gap-2.5 px-2.5 py-1.5 rounded-xl text-slate-300 hover:text-white hover:bg-cyber-purple/20 hover:border hover:border-cyber-purple/40 transition-all text-right">
            <svg class="w-4 h-4 text-cyber-purple animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
            <span>استشارة سولي AI 🤖</span>
        </button>

        <button type="button" 
                @click="$store.citadel.contextMenuOpen = false; $store.citadel.commandPaletteOpen = true" 
                class="w-full flex items-center justify-between px-2.5 py-1.5 rounded-xl text-slate-300 hover:text-white hover:bg-obsidian-surface transition-all text-right">
            <div class="flex items-center gap-2.5">
                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <span>البحث الذكي في القلعة</span>
            </div>
            <kbd class="text-[9px] font-mono px-1 py-0.5 rounded bg-obsidian-base text-slate-400 border border-obsidian-border">⌘K</kbd>
        </button>

        @auth
            <!-- Role-Specific Destinations -->
            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'staff')
                <div class="pt-1 border-t border-obsidian-border/60">
                    <a href="{{ route('admin.index') }}" 
                       @click="$store.citadel.contextMenuOpen = false"
                       class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-xl text-cyber-gold hover:bg-cyber-gold/15 transition-all">
                        <svg class="w-4 h-4 text-cyber-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        </svg>
                        <span>مركز القيادة Admin HQ</span>
                    </a>
                    <a href="{{ route('admin.wallets.index') }}" 
                       @click="$store.citadel.contextMenuOpen = false"
                       class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-xl text-slate-300 hover:text-white hover:bg-obsidian-surface transition-all">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>اعتماد شحن المحافظ</span>
                    </a>
                    <a href="{{ route('admin.canned-responses.index') }}" 
                       @click="$store.citadel.contextMenuOpen = false"
                       class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-xl text-slate-300 hover:text-white hover:bg-obsidian-surface transition-all">
                        <svg class="w-4 h-4 text-cyber-cyan" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                        </svg>
                        <span>خزينة الردود الذكية</span>
                    </a>
                </div>
            @else
                <div class="pt-1 border-t border-obsidian-border/60">
                    <a href="{{ route('portal.index') }}" 
                       @click="$store.citadel.contextMenuOpen = false"
                       class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-xl text-slate-300 hover:text-white hover:bg-obsidian-surface transition-all">
                        <svg class="w-4 h-4 text-cyber-cyan" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                        <span>بوابة العميل والمحفظة</span>
                    </a>
                    <a href="{{ route('portal.bookings') }}" 
                       @click="$store.citadel.contextMenuOpen = false"
                       class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-xl text-slate-300 hover:text-white hover:bg-obsidian-surface transition-all">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <span>حجز ومتابعة الخدمات</span>
                    </a>
                    <a href="{{ route('portal.tickets') }}" 
                       @click="$store.citadel.contextMenuOpen = false"
                       class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-xl text-slate-300 hover:text-white hover:bg-obsidian-surface transition-all">
                        <svg class="w-4 h-4 text-cyber-purple" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span>تذاكر الدعم الهندسي</span>
                    </a>
                </div>
            @endif

            <!-- Logout -->
            <form action="{{ route('logout') }}" method="POST" class="pt-1 border-t border-obsidian-border/60">
                @csrf
                <button type="submit" 
                        class="w-full flex items-center gap-2.5 px-2.5 py-1.5 rounded-xl text-rose-400 hover:bg-rose-500/15 transition-all text-right font-bold text-xs">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span>تسجيل الخروج</span>
                </button>
            </form>
        @else
            <!-- Guest Actions -->
            <div class="pt-1 border-t border-obsidian-border/60 grid grid-cols-2 gap-1.5">
                <a href="{{ route('login') }}" 
                   @click="$store.citadel.contextMenuOpen = false"
                   class="px-2.5 py-1.5 rounded-xl bg-obsidian-surface border border-obsidian-border text-center text-slate-200 hover:text-cyber-cyan hover:border-cyber-cyan transition-all text-xs font-bold">
                    دخول
                </a>
                <a href="{{ route('register') }}" 
                   @click="$store.citadel.contextMenuOpen = false"
                   class="px-2.5 py-1.5 rounded-xl bg-cyber-cyan text-obsidian-base text-center font-black text-xs shadow-neon-cyan transition-all">
                    حساب جديد
                </a>
            </div>
        @endauth

        <!-- Universal Utilities -->
        <div class="pt-1.5 border-t border-obsidian-border/80 space-y-0.5">
            <button type="button" 
                    @click="$store.citadel.copyCurrentUrl(); $store.citadel.contextMenuOpen = false" 
                    class="w-full flex items-center gap-2.5 px-2.5 py-1.5 rounded-xl text-slate-400 hover:text-cyber-cyan hover:bg-obsidian-surface transition-all text-right text-[11px]">
                <svg class="w-3.5 h-3.5 text-cyber-cyan" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
                <span>نسخ رابط الصفحة الحالية</span>
            </button>

            <button type="button" 
                    @click="$store.citadel.installApp(); $store.citadel.contextMenuOpen = false" 
                    class="w-full flex items-center gap-2.5 px-2.5 py-1.5 rounded-xl text-slate-400 hover:text-cyber-gold hover:bg-obsidian-surface transition-all text-right text-[11px]">
                <svg class="w-3.5 h-3.5 text-cyber-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                <span>تثبيت تطبيق سولي 📱</span>
            </button>

            <button type="button" 
                    @click="SoulyLoader.replay(); $store.citadel.contextMenuOpen = false" 
                    class="w-full flex items-center gap-2.5 px-2.5 py-1.5 rounded-xl text-slate-400 hover:text-white hover:bg-obsidian-surface transition-all text-right text-[11px]">
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>إعادة العرض السينمائي (25s)</span>
            </button>
        </div>

    </nav>
</div>
