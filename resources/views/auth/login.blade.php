@extends(request()->header('sec-ch-ua-mobile') === '?1' || request()->query('view') === 'mobile' ? 'layouts.mobile' : 'layouts.desktop')

@section('title', 'تسجيل الدخول | سولي')
@section('page-title', 'تسجيل الدخول')

@section('content')
<div class="min-h-[78vh] flex items-center justify-center py-6 px-3 sm:px-6"
     x-data="{ 
         isSubmitting: false, 
         showPass: false 
     }">
    
    <div class="w-full max-w-md mx-auto space-y-5">
        
        <!-- شعار الصقر والترحيب -->
        <div class="text-center space-y-2">
            <div class="flex justify-center">
                <a href="{{ url('/') }}" class="inline-block transform active:scale-95 transition-transform">
                    <x-souly-logo size="md" :showText="false" />
                </a>
            </div>
            
            <h1 class="text-2xl sm:text-3xl font-black text-white font-cairo tracking-tight">
                تسجيل الدخول إلى <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyber-cyan to-cyber-gold">منظومة سولي</span>
            </h1>
            <p class="text-xs text-slate-400 font-cairo">
                أدخل رقم هاتفك أو بريدك للوصول إلى محفظتك وحجوزاتك
            </p>
        </div>

        <!-- كارت تسجيل الدخول -->
        <div class="p-5 sm:p-7 rounded-3xl glass-obsidian border border-obsidian-border shadow-2xl space-y-5">
            
            @if ($errors->any())
                <div class="p-3.5 rounded-2xl bg-rose-500/10 border border-rose-500/40 text-xs text-rose-300 font-cairo space-y-1">
                    @foreach ($errors->all() as $error)
                        <div class="flex items-center gap-1.5">
                            <span class="text-rose-400 font-bold shrink-0">⚠</span>
                            <span>{{ $error }}</span>
                        </div>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" @submit="isSubmitting = true" class="space-y-4">
                @csrf

                <!-- الحقل المزدوج: موبايل أو إيميل -->
                <div class="space-y-1.5 text-right">
                    <label for="login" class="block text-xs font-bold text-slate-300 font-cairo">رقم الموبايل أو البريد الإلكتروني</label>
                    <div class="relative">
                        <input type="text" 
                               id="login" 
                               name="phone" 
                               value="{{ old('phone', old('login')) }}" 
                               required 
                               autofocus
                               autocomplete="username"
                               dir="ltr"
                               placeholder="01xxxxxxxxx أو your-email@domain.com"
                               class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-4 py-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-cyber-cyan transition-colors font-mono text-left">
                    </div>
                </div>

                <!-- كلمة المرور مع زر إظهار/إخفاء للموبايل -->
                <div class="space-y-1.5 text-right">
                    <div class="flex items-center justify-between">
                        <label for="password" class="block text-xs font-bold text-slate-300 font-cairo">كلمة المرور</label>
                        <span class="text-[11px] text-slate-500 font-mono">افتراضياً: password123</span>
                    </div>
                    <div class="relative">
                        <input :type="showPass ? 'text' : 'password'" 
                               id="password" 
                               name="password" 
                               required
                               autocomplete="current-password"
                               placeholder="••••••••"
                               class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-4 py-3 pl-11 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-cyber-cyan transition-colors font-mono">
                        
                        <button type="button" 
                                @click="showPass = !showPass" 
                                class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-white p-1 focus:outline-none"
                                tabindex="-1">
                            <svg x-show="!showPass" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg x-show="showPass" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- تذكرني ونسيان كلمة المرور -->
                <div class="flex items-center justify-between pt-1">
                    <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded bg-obsidian-surface border-obsidian-border text-cyber-cyan focus:ring-0">
                        <span class="text-xs text-slate-400 font-cairo">تذكر تسجيل دخولي</span>
                    </label>
                    
                    <a href="https://wa.me/201090623326?text=أحتاج%20مساعدة%20في%20استعادة%20حسابي%20في%20سولي" 
                       target="_blank" rel="noopener noreferrer" 
                       class="text-xs text-cyber-cyan hover:underline font-cairo">
                        نسيت كلمة المرور؟
                    </a>
                </div>

                <!-- زر الدخول -->
                <button type="submit"
                        :disabled="isSubmitting"
                        :class="isSubmitting ? 'opacity-70 cursor-wait' : 'active:scale-98'"
                        class="w-full py-3.5 px-4 rounded-xl btn-citadel-primary text-sm font-bold flex items-center justify-center gap-2 shadow-neon-cyan transition-all">
                    
                    <template x-if="!isSubmitting">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                            </svg>
                            <span>دخول آمن للمنظومة</span>
                        </div>
                    </template>

                    <template x-if="isSubmitting">
                        <div class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-obsidian-base" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            <span>جاري التحقق والدخول...</span>
                        </div>
                    </template>
                </button>
            </form>

            <div class="pt-3 border-t border-obsidian-border/60 text-center">
                <p class="text-xs text-slate-400 font-cairo">
                    ليس لديك حساب حتى الآن؟
                    <a href="{{ route('register') }}" class="text-cyber-gold font-bold hover:underline transition-colors mr-1">
                        أنشئ حسابك برقم الموبايل
                    </a>
                </p>
            </div>

            @if(app()->isLocal() && \App\Models\Setting::get('dev_quick_login_enabled', true))
                <div class="mt-6 pt-5 border-t border-obsidian-border/60 text-center space-y-2">
                    <p class="text-[11px] font-mono text-slate-400 font-cairo">
                        🛠️ دخول سريع لبيئة التطوير (Developer Quick-Access):
                    </p>
                    <div class="flex items-center justify-center gap-2 flex-wrap text-xs font-cairo">
                        <a href="{{ route('login.demo', 'admin') }}" class="px-2.5 py-1.5 rounded-lg bg-obsidian-surface hover:bg-amber-500/20 text-cyber-gold border border-amber-500/40 text-[10px] sm:text-[11px] font-bold transition-all active:scale-95 inline-flex items-center gap-1">
                            👑 [المدير العام]
                        </a>
                        <span class="text-slate-600">|</span>
                        <a href="{{ route('login.demo', 'client') }}" class="px-2.5 py-1.5 rounded-lg bg-obsidian-surface hover:bg-cyber-cyan/20 text-cyber-cyan border border-cyber-cyan/40 text-[10px] sm:text-[11px] font-bold transition-all active:scale-95 inline-flex items-center gap-1">
                            👤 [عميل تجريبي]
                        </a>
                        <span class="text-slate-600">|</span>
                        <a href="{{ route('login.demo', 'staff') }}" class="px-2.5 py-1.5 rounded-lg bg-obsidian-surface hover:bg-purple-500/20 text-purple-300 border border-purple-500/40 text-[10px] sm:text-[11px] font-bold transition-all active:scale-95 inline-flex items-center gap-1">
                            🔧 [مهندس سولي]
                        </a>
                    </div>
                </div>
            @endif
        </div>

    </div>
</div>
@endsection