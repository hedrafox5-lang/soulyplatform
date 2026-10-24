@extends(request()->header('sec-ch-ua-mobile') === '?1' || request()->query('view') === 'mobile' ? 'layouts.mobile' : 'layouts.desktop')

@section('title', 'تسجيل الدخول إلى قلعة سولي | Souly Digital Citadel')
@section('page-title', 'تسجيل الدخول السيادي')

@section('content')
<div class="min-h-[75vh] flex items-center justify-center py-8">
    <div class="w-full max-w-md mx-auto space-y-6">
        
        <!-- Header Insignia (Falcon Cyber-Emblem) -->
        <div class="text-center space-y-3">
            <div class="flex justify-center">
                <a href="{{ url('/') }}">
                    <x-souly-logo size="md" :showText="false" />
                </a>
            </div>
            
            <h1 class="text-2xl sm:text-3xl font-black text-white font-cairo">
                تسجيل الدخول إلى <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyber-cyan to-cyber-gold">القلعة</span>
            </h1>
            <p class="text-xs text-slate-400 font-readex">
                أدخل بياناتك للوصول إلى محفظتك الرقمية، حجوزاتك، وتذاكر الدعم الفني
            </p>
        </div>

        <!-- 1-Click Demo Login Box (For Testing & Instant Evaluation) -->
        <div class="p-4 rounded-2xl bg-gradient-to-br from-cyber-purple/15 via-obsidian-surface to-cyber-cyan/15 border border-cyber-cyan/30 space-y-2.5">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-mono font-bold text-cyber-cyan flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-cyber-cyan animate-pulse"></span>
                    <span>تجربة سريعة · 1-CLICK DEMO LOGIN</span>
                </span>
                <span class="text-[10px] font-mono text-slate-400">بدون إدخال كلمة سر</span>
            </div>

            <div class="grid grid-cols-3 gap-2 pt-1">
                <form action="{{ route('login.demo', 'client') }}" method="POST">
                    @csrf
                    <button type="submit" 
                            class="w-full py-2 px-2 rounded-xl bg-obsidian-surface hover:bg-cyber-cyan/20 border border-cyber-cyan/40 hover:border-cyber-cyan text-cyber-cyan text-[11px] font-bold font-cairo transition-all text-center">
                        عميل (3,500 EGP)
                    </button>
                </form>

                <form action="{{ route('login.demo', 'staff') }}" method="POST">
                    @csrf
                    <button type="submit" 
                            class="w-full py-2 px-2 rounded-xl bg-obsidian-surface hover:bg-cyber-gold/20 border border-cyber-gold/40 hover:border-cyber-gold text-cyber-gold text-[11px] font-bold font-cairo transition-all text-center">
                        فريق العمل (Staff)
                    </button>
                </form>

                <form action="{{ route('login.demo', 'admin') }}" method="POST">
                    @csrf
                    <button type="submit" 
                            class="w-full py-2 px-2 rounded-xl bg-obsidian-surface hover:bg-cyber-purple/30 border border-cyber-purple/50 hover:border-cyber-purple text-purple-300 text-[11px] font-bold font-cairo transition-all text-center">
                        الإدارة (Admin)
                    </button>
                </form>
            </div>
        </div>

        <!-- Standard Login Card -->
        <div class="p-6 sm:p-8 rounded-3xl glass-obsidian border border-obsidian-border space-y-5">
            
            @if ($errors->any())
                <div class="p-3.5 rounded-xl bg-rose-500/10 border border-rose-500/40 text-xs text-rose-300 font-readex space-y-1">
                    @foreach ($errors->all() as $error)
                        <div class="flex items-center gap-1.5">
                            <span class="text-rose-400 font-bold">⚠</span>
                            <span>{{ $error }}</span>
                        </div>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Email -->
                <div class="space-y-1.5">
                    <label for="email" class="block text-xs font-bold text-slate-300 font-readex">البريد الإلكتروني</label>
                    <div class="relative">
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                               placeholder="your-email@example.com"
                               class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-4 py-3 text-xs sm:text-sm text-white placeholder-slate-500 focus:outline-none focus:border-cyber-cyan transition-colors font-mono">
                    </div>
                </div>

                <!-- Password -->
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label for="password" class="block text-xs font-bold text-slate-300 font-readex">كلمة المرور</label>
                        <span class="text-[11px] text-slate-500 font-mono">افتراضياً: password</span>
                    </div>
                    <div class="relative">
                        <input type="password" id="password" name="password" required
                               placeholder="••••••••"
                               class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-4 py-3 text-xs sm:text-sm text-white placeholder-slate-500 focus:outline-none focus:border-cyber-cyan transition-colors font-mono">
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between pt-1">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded bg-obsidian-surface border-obsidian-border text-cyber-cyan focus:ring-0">
                        <span class="text-xs text-slate-400 font-readex">تذكر تسجيل دخولي</span>
                    </label>
                    
                    <a href="https://wa.me/201090623326?text=أحتاج%20مساعدة%20في%20استعادة%20حسابي%20في%20سولي" 
                       target="_blank" rel="noopener noreferrer" 
                       class="text-xs text-cyber-cyan hover:underline font-readex">
                        نسيت كلمة المرور؟
                    </a>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                        class="w-full py-3.5 px-4 rounded-xl bg-gradient-to-r from-cyber-cyan via-cyber-gold to-cyber-purple text-obsidian-base font-extrabold text-sm font-cairo shadow-neon-cyan hover:scale-[1.01] active:scale-95 transition-all flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                    <span>دخول آمن للقلعة</span>
                </button>
            </form>

            <div class="pt-4 border-t border-obsidian-border/60 text-center">
                <p class="text-xs text-slate-400 font-readex">
                    ليس لديك حساب حتى الآن؟
                    <a href="{{ route('register') }}" class="text-cyber-gold font-bold hover:underline font-cairo">
                        أنشئ حسابك فوراً واحصل على محفظة نشطة
                    </a>
                </p>
            </div>
        </div>

    </div>
</div>
@endsection
