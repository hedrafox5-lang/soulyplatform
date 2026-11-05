@extends((request()->query('view') === 'mobile' || request()->header('sec-ch-ua-mobile') === '?1' || preg_match('/(android|iphone|ipad|ipod|mobile|touch|fban|fbav|instagram)/i', request()->userAgent() ?? '')) ? 'layouts.mobile' : 'layouts.desktop')

@section('content')
<div class="space-y-5 sm:space-y-7 theme-client-portal pb-12" x-data="{ profileModalOpen: false }">
    
    <!-- Unified Compact Portal Header & Navigation -->
    <div class="glass-obsidian rounded-3xl border border-obsidian-border p-4 sm:p-6 space-y-4 shadow-[0_0_25px_rgba(0,245,212,0.08)]">
        
        <!-- User Welcome & Quick Actions Bar -->
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-obsidian-border/80 pb-3.5">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl bg-gradient-to-tr from-cyber-purple via-obsidian-surface to-cyber-cyan p-[1.5px] shadow-neon-cyan shrink-0">
                    <div class="w-full h-full bg-obsidian-surface rounded-2xl flex items-center justify-center font-bold font-cairo text-xs sm:text-sm text-cyber-cyan">
                        {{ mb_substr(auth()->user()->name, 0, 2) }}
                    </div>
                </div>

                <div class="space-y-0.5 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h2 class="text-sm sm:text-lg font-black text-white font-cairo truncate">{{ auth()->user()->name }}</h2>
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-mono font-bold uppercase shrink-0
                            @if(auth()->user()->role === 'admin') bg-cyber-purple/20 text-purple-300 border border-cyber-purple/40
                            @elseif(auth()->user()->role === 'staff') bg-cyber-gold/20 text-cyber-gold border border-cyber-gold/40
                            @else bg-cyber-cyan/15 text-cyber-cyan border border-cyber-cyan/30
                            @endif">
                            {{ auth()->user()->role === 'admin' ? 'الإدارة العليا' : (auth()->user()->role === 'staff' ? 'مهندس سولي' : 'عميل القلعة') }}
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-400 font-cairo truncate">
                        {{ auth()->user()->company_name ? auth()->user()->company_name . ' · ' : '' }}{{ auth()->user()->phone ?? auth()->user()->email }}
                    </p>
                </div>
            </div>

            <!-- Header Quick Actions: Edit Profile/Password, Wallet (Desktop), Logout -->
            <div class="flex items-center gap-2 shrink-0">
                <button type="button"
                        @click="profileModalOpen = true"
                        class="px-3 py-2 rounded-xl bg-obsidian-surface hover:bg-obsidian-card text-cyber-cyan border border-cyber-cyan/30 text-xs font-cairo font-bold flex items-center gap-1.5 transition-all cursor-pointer">
                    <span>⚙️</span>
                    <span>بياناتي والباسورد</span>
                </button>

                <a href="{{ route('portal.wallet') }}" 
                   class="hidden sm:flex items-center gap-2 px-3.5 py-2 rounded-xl bg-gradient-to-r from-cyber-gold/15 to-cyber-cyan/15 border border-cyber-cyan/40 hover:border-cyber-cyan transition-all text-xs font-mono">
                    <span class="text-slate-400">الرصيد:</span>
                    <strong class="text-cyber-cyan font-bold text-sm">{{ number_format(auth()->user()->walletOrCreate()->balance, 2) }}</strong>
                    <span class="text-[10px] text-slate-400">ج.م</span>
                </a>

                <form action="{{ route('logout') }}" method="POST" class="hidden sm:block">
                    @csrf
                    <button type="submit" 
                            title="تسجيل الخروج"
                            class="p-2 rounded-xl bg-obsidian-surface hover:bg-rose-500/20 text-slate-400 hover:text-rose-400 border border-obsidian-border hover:border-rose-500/30 transition-all text-xs font-cairo flex items-center gap-1.5 cursor-pointer">
                        <span>خروج</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <nav class="flex items-center gap-1.5 sm:gap-2 overflow-x-auto scrollbar-none pb-1 text-xs font-cairo font-bold">
            <a href="{{ route('portal.index') }}" 
               class="px-3.5 py-2 rounded-xl transition-all flex items-center gap-1.5 shrink-0
               {{ request()->routeIs('portal.index') ? 'bg-cyber-cyan text-obsidian-base font-black shadow-neon-cyan' : 'bg-obsidian-surface text-slate-300 hover:text-white border border-obsidian-border' }}">
                <span>📊</span>
                <span>لوحة حسابي</span>
            </a>

            <a href="{{ route('portal.devices') }}" 
               class="px-3.5 py-2 rounded-xl transition-all flex items-center gap-1.5 shrink-0
               {{ request()->routeIs('portal.devices*') ? 'bg-cyber-cyan text-obsidian-base font-black shadow-neon-cyan' : 'bg-obsidian-surface text-slate-300 hover:text-white border border-obsidian-border' }}">
                <span>💻</span>
                <span>أجهزتي وصيانتها</span>
            </a>

            <a href="{{ route('portal.bookings') }}" 
               class="px-3.5 py-2 rounded-xl transition-all flex items-center gap-1.5 shrink-0
               {{ request()->routeIs('portal.bookings*') ? 'bg-cyber-cyan text-obsidian-base font-black shadow-neon-cyan' : 'bg-obsidian-surface text-slate-300 hover:text-white border border-obsidian-border' }}">
                <span>📅</span>
                <span>حجوزاتي</span>
            </a>

            <a href="{{ route('portal.hardware') }}" 
               class="px-3.5 py-2 rounded-xl transition-all flex items-center gap-1.5 shrink-0
               {{ request()->routeIs('portal.hardware*') ? 'bg-cyber-cyan text-obsidian-base font-black shadow-neon-cyan' : 'bg-obsidian-surface text-slate-300 hover:text-white border border-obsidian-border' }}">
                <span>🖥️</span>
                <span>أوردرات الهاردوير</span>
            </a>

            <a href="{{ route('portal.erp_store') }}" 
               class="px-3.5 py-2 rounded-xl transition-all flex items-center gap-1.5 shrink-0
               {{ request()->routeIs('portal.erp_store*') ? 'bg-cyber-cyan text-obsidian-base font-black shadow-neon-cyan' : 'bg-obsidian-surface text-slate-300 hover:text-white border border-obsidian-border' }}">
                <span>🏪</span>
                <span>منظومتي في BrandERP</span>
            </a>

            <a href="{{ route('portal.wallet') }}" 
               class="px-3.5 py-2 rounded-xl transition-all flex items-center gap-1.5 shrink-0
               {{ request()->routeIs('portal.wallet') ? 'bg-cyber-cyan text-obsidian-base font-black shadow-neon-cyan' : 'bg-obsidian-surface text-slate-300 hover:text-white border border-obsidian-border' }}">
                <span>💳</span>
                <span>محفظتي</span>
            </a>

            <a href="{{ route('portal.tickets') }}" 
               class="px-3.5 py-2 rounded-xl transition-all flex items-center gap-1.5 shrink-0
               {{ request()->routeIs('portal.tickets*') ? 'bg-cyber-cyan text-obsidian-base font-black shadow-neon-cyan' : 'bg-obsidian-surface text-slate-300 hover:text-white border border-obsidian-border' }}">
                <span>🎫</span>
                <span>تذاكر الدعم</span>
            </a>
        </nav>

    </div>

    <!-- Portal Page Dynamic Content -->
    @yield('portal-content')

    <!-- ===================================================================== -->
    <!-- PROFILE & PASSWORD UPDATE MODAL (للعميل والأدمن)                       -->
    <!-- ===================================================================== -->
    <div x-show="profileModalOpen"
         x-cloak
         @keydown.escape.window="profileModalOpen = false"
         class="fixed inset-0 z-[99999] bg-black/80 backdrop-blur-md flex items-end sm:items-center justify-center p-0 sm:p-4 overflow-y-auto"
         style="display: none;">
        <div @click.away="profileModalOpen = false"
             x-show="profileModalOpen"
             x-transition
             class="relative w-full max-w-lg glass-obsidian border-t sm:border border-cyber-cyan/40 rounded-t-3xl sm:rounded-3xl p-5 sm:p-7 space-y-5 shadow-2xl max-h-[90dvh] overflow-y-auto safe-bottom">
            
            <div class="flex items-center justify-between border-b border-obsidian-border pb-3">
                <h3 class="text-base sm:text-lg font-black text-white font-cairo">⚙️ تعديل بيانات الحساب وكلمة المرور</h3>
                <button type="button" @click="profileModalOpen = false" class="p-1.5 text-slate-400 hover:text-white rounded-lg cursor-pointer">✕</button>
            </div>

            <form action="{{ Route::has('portal.profile.update') ? route('portal.profile.update') : url('/portal/profile') }}" method="POST" class="space-y-4 text-xs font-cairo text-right">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-slate-300 font-bold block">الاسم بالكامل *</label>
                        <input type="text" name="name" value="{{ auth()->user()->name }}" required class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-3.5 py-2.5 text-white focus:border-cyber-cyan focus:outline-none">
                    </div>
                    <div class="space-y-1">
                        <label class="text-slate-300 font-bold block">رقم الهاتف (واتساب) *</label>
                        <input type="text" name="phone" value="{{ auth()->user()->phone }}" required class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-3.5 py-2.5 text-white font-mono focus:border-cyber-cyan focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-slate-300 font-bold block">البريد الإلكتروني</label>
                        <input type="email" name="email" value="{{ auth()->user()->email }}" class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-3.5 py-2.5 text-white font-mono focus:border-cyber-cyan focus:outline-none">
                    </div>
                    <div class="space-y-1">
                        <label class="text-slate-300 font-bold block">اسم الشركة أو النشاط</label>
                        <input type="text" name="company_name" value="{{ auth()->user()->company_name }}" class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-3.5 py-2.5 text-white focus:border-cyber-cyan focus:outline-none">
                    </div>
                </div>

                <div class="pt-3 border-t border-obsidian-border space-y-3">
                    <span class="text-cyber-gold font-bold block">تغيير كلمة المرور (اتركها فارغة إذا لا ترغب في تغييرها):</span>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label class="text-slate-300 font-bold block">كلمة المرور الجديدة</label>
                            <input type="password" name="password" placeholder="••••••••" class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-3.5 py-2.5 text-white focus:border-cyber-cyan focus:outline-none">
                        </div>
                        <div class="space-y-1">
                            <label class="text-slate-300 font-bold block">تأكيد كلمة المرور الجديدة</label>
                            <input type="password" name="password_confirmation" placeholder="••••••••" class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-3.5 py-2.5 text-white focus:border-cyber-cyan focus:outline-none">
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-t border-obsidian-border flex items-center justify-end gap-2">
                    <button type="button" @click="profileModalOpen = false" class="px-4 py-2.5 rounded-xl bg-obsidian-surface text-slate-300 font-bold cursor-pointer">إلغاء</button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl btn-citadel-primary font-black shadow-neon-cyan cursor-pointer">حفظ التعديلات ✓</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection