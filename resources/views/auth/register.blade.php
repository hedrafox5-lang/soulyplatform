@extends(request()->header('sec-ch-ua-mobile') === '?1' || request()->query('view') === 'mobile' ? 'layouts.mobile' : 'layouts.desktop')

@section('title', 'إنشاء حساب جديد | سولي')
@section('page-title', 'الانضمام إلى سولي')

@section('content')
<div class="min-h-[82vh] flex items-center justify-center py-6 px-3 sm:px-6"
     x-data="{ 
         isSubmitting: false, 
         showPass: false, 
         showConfirmPass: false,
         phone: '{{ old('phone') }}'
     }">
    
    <div class="w-full max-w-md mx-auto space-y-5">
        
        <!-- الهيدر والشعار -->
        <div class="text-center space-y-2">
            <div class="flex justify-center">
                <a href="{{ url('/') }}" class="inline-block transform active:scale-95 transition-transform">
                    <x-souly-logo size="md" :showText="false" />
                </a>
            </div>
            
            <h1 class="text-2xl sm:text-3xl font-black text-white font-cairo tracking-tight">
                إنشاء حساب <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyber-cyan via-teal-300 to-cyber-gold">جديد في سولي</span>
            </h1>
            <p class="text-xs text-slate-400 font-cairo">
                سجّل برقم هاتفك لفتح محفظتك والتحكم في طلباتك فورياً
            </p>
        </div>

        <!-- ميزة المحفظة الفورية -->
        <div class="p-3.5 rounded-2xl bg-cyber-cyan/10 border border-cyber-cyan/30 flex items-center gap-3 shadow-inner">
            <div class="w-9 h-9 rounded-xl bg-cyber-cyan/20 text-cyber-cyan flex items-center justify-center shrink-0 shadow-neon-cyan">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="text-xs font-cairo leading-tight">
                <span class="text-white font-bold block mb-0.5">محفظة رقمية مفعّلة فور التسجيل</span>
                <span class="text-slate-400 text-[11px]">شحن سريع عبر فودافون كاش وإنستاباي مع حجز الخدمات بضغطة واحدة.</span>
            </div>
        </div>

        <!-- كارت الفورم -->
        <div class="p-5 sm:p-7 rounded-3xl glass-obsidian border border-obsidian-border shadow-2xl space-y-4">
            
            @if ($errors->any())
                <div class="p-3.5 rounded-2xl bg-rose-500/10 border border-rose-500/40 text-xs text-rose-300 font-cairo space-y-1">
                    @foreach ($errors->all() as $error)
                        <div class="flex items-center gap-2">
                            <span class="text-rose-400 font-bold shrink-0">⚠</span>
                            <span>{{ $error }}</span>
                        </div>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('register') }}" method="POST" @submit="isSubmitting = true" class="space-y-4">
                @csrf

                <!-- الاسم الكامل -->
                <div class="space-y-1.5 text-right">
                    <label for="name" class="block text-xs font-bold text-slate-300 font-cairo">الاسم بالكامل</label>
                    <div class="relative">
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                               autocomplete="name"
                               placeholder="مثال: أحمد مصطفى"
                               class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-4 py-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-cyber-cyan transition-all font-cairo">
                    </div>
                </div>

                <!-- رقم الموبايل (مصمم ومحسّن للموبايل المصري) -->
                <div class="space-y-1.5 text-right">
                    <label for="phone" class="block text-xs font-bold text-slate-300 font-cairo">رقم الموبايل (للتواصل وتأكيد العمليات)</label>
                    <div class="relative flex items-center rounded-xl bg-obsidian-surface border border-obsidian-border focus-within:border-cyber-cyan transition-colors overflow-hidden">
                        
                        <!-- بادئة الدولة وثابت الاتصال -->
                        <div class="px-3 py-3 bg-white/5 border-l border-obsidian-border text-slate-300 flex items-center gap-1 font-mono text-xs select-none">
                            <span class="text-sm">🇪🇬</span>
                            <span class="font-bold text-cyber-cyan">+20</span>
                        </div>

                        <input type="tel" 
                               id="phone" 
                               name="phone" 
                               x-model="phone"
                               required 
                               inputmode="numeric" 
                               pattern="[0-9]*"
                               autocomplete="tel"
                               dir="ltr"
                               placeholder="01xxxxxxxxx"
                               class="w-full bg-transparent px-3 py-3 text-sm text-white placeholder-slate-500 focus:outline-none font-mono text-left tracking-wider">
                    </div>
                    <span class="text-[10px] text-slate-400 font-cairo block mt-0.5">يقبل 010، 011، 012، 015 ويتم توحيد الصيغة تلقائياً.</span>
                </div>

                <!-- اسم الشركة أو النشاط (اختياري) -->
                <div class="space-y-1.5 text-right">
                    <label for="company_name" class="block text-xs font-bold text-slate-300 font-cairo">
                        الجهة أو المنشأة <span class="text-[10px] text-slate-500">(اختياري للشركات)</span>
                    </label>
                    <input type="text" id="company_name" name="company_name" value="{{ old('company_name') }}"
                           placeholder="مثال: مؤسسة الأمل للتجارة"
                           class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-4 py-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-cyber-cyan transition-all font-cairo">
                </div>

                <!-- كلمة المرور مع زر إظهار/إخفاء -->
                <div class="space-y-1.5 text-right">
                    <label for="password" class="block text-xs font-bold text-slate-300 font-cairo">كلمة المرور (6 خانات أو أكثر)</label>
                    <div class="relative">
                        <input :type="showPass ? 'text' : 'password'" 
                               id="password" 
                               name="password" 
                               required
                               autocomplete="new-password"
                               placeholder="••••••••"
                               class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-4 py-3 pl-11 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-cyber-cyan transition-all font-mono">
                        
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

                <!-- تأكيد كلمة المرور -->
                <div class="space-y-1.5 text-right">
                    <label for="password_confirmation" class="block text-xs font-bold text-slate-300 font-cairo">تأكيد كلمة المرور</label>
                    <div class="relative">
                        <input :type="showConfirmPass ? 'text' : 'password'" 
                               id="password_confirmation" 
                               name="password_confirmation" 
                               required
                               autocomplete="new-password"
                               placeholder="••••••••"
                               class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-4 py-3 pl-11 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-cyber-cyan transition-all font-mono">
                        
                        <button type="button" 
                                @click="showConfirmPass = !showConfirmPass" 
                                class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-white p-1 focus:outline-none"
                                tabindex="-1">
                            <svg x-show="!showConfirmPass" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg x-show="showConfirmPass" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- زر التسجيل المانع للضغط المتكرر على الموبايل -->
                <button type="submit"
                        :disabled="isSubmitting"
                        :class="isSubmitting ? 'opacity-70 cursor-wait' : 'active:scale-98'"
                        class="w-full mt-2 py-3.5 px-4 rounded-xl btn-citadel-primary text-sm font-bold flex items-center justify-center gap-2 shadow-neon-cyan transition-all">
                    
                    <template x-if="!isSubmitting">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            <span>إنشاء الحساب وتفعيل المحفظة</span>
                        </div>
                    </template>

                    <template x-if="isSubmitting">
                        <div class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-obsidian-base" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            <span>جاري تهيئة المنظومة...</span>
                        </div>
                    </template>
                </button>
            </form>

            <div class="pt-3 border-t border-obsidian-border/60 text-center">
                <p class="text-xs text-slate-400 font-cairo">
                    لديك حساب بالفعل؟
                    <a href="{{ route('login') }}" class="text-cyber-cyan font-bold hover:underline transition-colors mr-1">
                        تسجيل الدخول هنا
                    </a>
                </p>
            </div>
        </div>

    </div>
</div>
@endsection