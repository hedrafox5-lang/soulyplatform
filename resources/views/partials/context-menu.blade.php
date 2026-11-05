<!-- Souly Digital Citadel: Smart Role-Aware Cyber Context Menu -->
<div id="souly-context-menu" 
     x-show="$store.citadel && $store.citadel.contextMenuOpen" 
     x-transition:enter="transition ease-out duration-150"
     x-transition:enter-start="opacity-0 scale-95"
     x-transition:enter-end="opacity-100 scale-100"
     x-transition:leave="transition ease-in duration-100"
     x-transition:leave-start="opacity-100 scale-100"
     x-transition:leave-end="opacity-0 scale-95"
     :style="`top: ${$store.citadel.contextMenuY}px; left: ${$store.citadel.contextMenuX}px;`"
     @click.away="$store.citadel.contextMenuOpen = false"
     class="fixed z-[99999] w-64 glass-obsidian border border-cyber-cyan/50 rounded-2xl p-2.5 shadow-[0_10px_35px_rgba(0,0,0,0.85)] select-none text-right font-sans space-y-1.5"
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
            <span class="text-[10px] font-cairo text-slate-400 font-bold">ضيف سيادي</span>
        @endauth
    </div>

    <!-- Action Links -->
    <nav class="space-y-0.5 text-xs font-cairo font-semibold">
        <a href="{{ route('services.index') }}" 
           @click="$store.citadel.contextMenuOpen = false"
           class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-xl text-slate-300 hover:text-white hover:bg-cyber-cyan/15 hover:border hover:border-cyber-cyan/30 transition-all">
            <svg class="w-4 h-4 text-cyber-cyan" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
            </svg>
            <span>استكشاف الخدمات السيادية</span>
        </a>

        <button type="button" 
                @click="$store.citadel.contextMenuOpen = false; $store.citadel.openSoulyAi()" 
                class="w-full flex items-center gap-2.5 px-2.5 py-1.5 rounded-xl text-slate-300 hover:text-white hover:bg-cyber-purple/20 hover:border hover:border-cyber-purple/40 transition-all text-right cursor-pointer">
            <svg class="w-4 h-4 text-cyber-purple animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
            <span>استشارة سولي AI 🤖</span>
        </button>

        <button type="button" 
                @click="$store.citadel.contextMenuOpen = false; $store.citadel.commandPaletteOpen = true" 
                class="w-full flex items-center justify-between px-2.5 py-1.5 rounded-xl text-slate-300 hover:text-white hover:bg-obsidian-surface transition-all text-right cursor-pointer">
            <div class="flex items-center gap-2.5">
                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <span>البحث الذكي في القلعة</span>
            </div>
            <kbd class="text-[9px] font-mono px-1 py-0.5 rounded bg-obsidian-base text-slate-400 border border-obsidian-border">⌘K</kbd>
        </button>

        @auth
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
                </div>
            @endif

            <form action="{{ route('logout') }}" method="POST" class="pt-1 border-t border-obsidian-border/60">
                @csrf
                <button type="submit" 
                        class="w-full flex items-center gap-2.5 px-2.5 py-1.5 rounded-xl text-rose-400 hover:bg-rose-500/15 transition-all text-right font-bold text-xs cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span>تسجيل الخروج</span>
                </button>
            </form>
        @else
            <div class="pt-1 border-t border-obsidian-border/60 grid grid-cols-2 gap-1.5">
                <a href="{{ route('login') }}" 
                   @click="$store.citadel.contextMenuOpen = false"
                   class="px-2.5 py-1.5 rounded-xl btn-citadel-secondary text-center text-xs font-bold">
                    دخول
                </a>
                <a href="{{ route('register') }}" 
                   @click="$store.citadel.contextMenuOpen = false"
                   class="px-2.5 py-1.5 rounded-xl btn-citadel-primary text-center text-xs font-black">
                    حساب جديد
                </a>
            </div>
        @endauth

        <div class="pt-1.5 border-t border-obsidian-border/80 space-y-0.5">
            <button type="button" 
                    @click="$store.citadel.copyCurrentUrl(); $store.citadel.contextMenuOpen = false" 
                    class="w-full flex items-center gap-2.5 px-2.5 py-1.5 rounded-xl text-slate-400 hover:text-cyber-cyan hover:bg-obsidian-surface transition-all text-right text-[11px] cursor-pointer">
                <svg class="w-3.5 h-3.5 text-cyber-cyan" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
                <span>نسخ رابط الصفحة الحالية</span>
            </button>
        </div>
    </nav>
</div>