@extends(request()->header('sec-ch-ua-mobile') === '?1' || request()->query('view') === 'mobile' ? 'layouts.mobile' : 'layouts.desktop')

@section('title', 'إنشاء حساب جديد | Souly Digital Citadel')
@section('page-title', 'الانضمام إلى قلعة سولي')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center py-8">
    <div class="w-full max-w-lg mx-auto space-y-6">
        
        <!-- Header Insignia (Falcon Cyber-Emblem) -->
        <div class="text-center space-y-3">
            <div class="flex justify-center">
                <a href="{{ url('/') }}">
                    <x-souly-logo size="md" :showText="false" />
                </a>
            </div>
            
            <h1 class="text-2xl sm:text-3xl font-black text-white font-cairo">
                إنشاء حساب <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyber-cyan to-cyber-gold">سيادي جديد</span>
            </h1>
            <p class="text-xs text-slate-400 font-readex">
                انضم لعملاء ومؤسسات قلعة سولي وتمتع بمحفظة ذكية فورية لإدارة خدماتك
            </p>
        </div>

        <!-- Automatic Benefits Badge -->
        <div class="p-3.5 rounded-2xl bg-cyber-cyan/10 border border-cyber-cyan/30 flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-cyber-cyan/20 text-cyber-cyan flex items-center justify-center shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <div class="text-xs font-readex leading-relaxed">
                <span class="text-white font-bold block">تفعيل فوري للمحفظة الرقمية (0.00 EGP)</span>
                <span class="text-slate-400 text-[11px]">شحن فوري عبر فودافون كاش وإنستاباي مع حجز الخدمات بضغطة زر واحدة.</span>
            </div>
        </div>

        <!-- Registration Form Card -->
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

            <form action="{{ route('register') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Name -->
                <div class="space-y-1.5">
                    <label for="name" class="block text-xs font-bold text-slate-300 font-readex">الاسم بالكامل أو اسم المسؤول</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                           placeholder="مثال: م. أحمد الخولي"
                           class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-4 py-3 text-xs sm:text-sm text-white placeholder-slate-500 focus:outline-none focus:border-cyber-cyan transition-colors font-cairo">
                </div>

                <!-- Email & Phone Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label for="email" class="block text-xs font-bold text-slate-300 font-readex">البريد الإلكتروني</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required
                               placeholder="user@domain.com"
                               class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-4 py-3 text-xs sm:text-sm text-white placeholder-slate-500 focus:outline-none focus:border-cyber-cyan transition-colors font-mono">
                    </div>

                    <div class="space-y-1.5">
                        <label for="phone" class="block text-xs font-bold text-slate-300 font-readex">رقم الهاتف / الواتساب</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone') }}" required
                               placeholder="010XXXXXXXX"
                               class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-4 py-3 text-xs sm:text-sm text-white placeholder-slate-500 focus:outline-none focus:border-cyber-cyan transition-colors font-mono">
                    </div>
                </div>

                <!-- Company Name (Optional) -->
                <div class="space-y-1.5">
                    <label for="company_name" class="block text-xs font-bold text-slate-300 font-readex">اسم الشركة أو المنشأة (اختياري)</label>
                    <input type="text" id="company_name" name="company_name" value="{{ old('company_name') }}"
                           placeholder="مثال: شركة النور للتوزيع والتجارة"
                           class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-4 py-3 text-xs sm:text-sm text-white placeholder-slate-500 focus:outline-none focus:border-cyber-cyan transition-colors font-cairo">
                </div>

                <!-- Passwords Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label for="password" class="block text-xs font-bold text-slate-300 font-readex">كلمة المرور (8 أحرف فأكثر)</label>
                        <input type="password" id="password" name="password" required
                               placeholder="••••••••"
                               class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-4 py-3 text-xs sm:text-sm text-white placeholder-slate-500 focus:outline-none focus:border-cyber-cyan transition-colors font-mono">
                    </div>

                    <div class="space-y-1.5">
                        <label for="password_confirmation" class="block text-xs font-bold text-slate-300 font-readex">تأكيد كلمة المرور</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                               placeholder="••••••••"
                               class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-4 py-3 text-xs sm:text-sm text-white placeholder-slate-500 focus:outline-none focus:border-cyber-cyan transition-colors font-mono">
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                        class="w-full mt-2 py-3.5 px-4 rounded-xl bg-gradient-to-r from-cyber-cyan via-cyber-gold to-cyber-purple text-obsidian-base font-extrabold text-sm font-cairo shadow-neon-cyan hover:scale-[1.01] active:scale-95 transition-all flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <span>إنشاء الحساب وتفعيل المحفظة</span>
                </button>
            </form>

            <div class="pt-4 border-t border-obsidian-border/60 text-center">
                <p class="text-xs text-slate-400 font-readex">
                    لديك حساب بالفعل؟
                    <a href="{{ route('login') }}" class="text-cyber-cyan font-bold hover:underline font-cairo">
                        تسجيل الدخول هنا
                    </a>
                </p>
            </div>
        </div>

    </div>
</div>
@endsection
