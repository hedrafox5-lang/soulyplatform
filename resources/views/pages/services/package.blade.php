@extends(request()->header('sec-ch-ua-mobile') === '?1' || request()->query('view') === 'mobile' ? 'layouts.mobile' : 'layouts.desktop')

@section('title', $package->name . ' - ' . $service->name . ' | سولي')
@section('page-title', $package->name)

@section('content')
<div class="space-y-14" x-data="packageController({
    basePrice: {{ (float) $package->price }},
    originalPrice: {{ (float) ($package->original_price ?? 0) }},
    minUnits: {{ (int) ($package->min_units ?? 1) }},
    unitLabel: '{{ addslashes($package->unit_label ?? 'شهر') }}',
    billingType: '{{ $package->billing_type }}',
    walletBalance: {{ (float) ($citadelWalletBalance ?? 0) }},
    warrantyDays: {{ (int) ($package->warranty_days ?? 30) }}
})">

    <!-- ===================================================================== -->
    <!-- 1. BREADCRUMBS & TOP BAR                                              -->
    <!-- ===================================================================== -->
    <div class="flex flex-wrap items-center justify-between gap-4 border-b border-obsidian-border/60 pb-5">
        <nav class="flex items-center gap-2 text-xs sm:text-sm text-slate-400 font-cairo flex-wrap">
            <a href="{{ route('home') }}" class="hover:text-cyber-cyan transition-colors">الرئيسية</a>
            <span class="text-slate-600">/</span>
            <a href="{{ route('services.index') }}" class="hover:text-cyber-cyan transition-colors">الخدمات</a>
            <span class="text-slate-600">/</span>
            <a href="{{ route('services.show', $service->slug) }}" class="hover:text-cyber-cyan transition-colors" style="color: {{ $service->accent_color }};">
                {{ $service->name }}
            </a>
            <span class="text-slate-600">/</span>
            <span class="text-white font-bold">{{ $package->name }}</span>
        </nav>

        <div class="flex items-center gap-3">
            @if($package->is_featured)
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold font-cairo bg-cyber-gold/15 text-cyber-gold border border-cyber-gold/40 animate-pulse">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    <span>الباقة الأكثر طلباً واكتمالاً</span>
                </span>
            @endif

            <a href="{{ route('services.show', $service->slug) }}" class="text-xs text-slate-400 hover:text-white flex items-center gap-1 bg-obsidian-surface px-3 py-1.5 rounded-lg border border-obsidian-border transition-colors">
                <svg class="w-3.5 h-3.5 rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
                <span>العودة لصفحة الخدمة</span>
            </a>
        </div>
    </div>

    <!-- ===================================================================== -->
    <!-- 2. LUXURY PACKAGE HERO & LIVE PRICING GRID                            -->
    <!-- ===================================================================== -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Left / Main Column: Package Overview & Value Proposition -->
        <div class="lg:col-span-7 space-y-6">
            
            <div class="space-y-3">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl font-cairo text-xs font-bold uppercase tracking-wider"
                     style="background-color: {{ $service->accent_color }}15; border: 1px solid {{ $service->accent_color }}50; color: {{ $service->accent_color }};">
                    <span>{{ $package->badge_text ?? 'مواصفات قياسية فائقة' }}</span>
                </div>

                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-white font-cairo leading-tight">
                    {{ $package->name }}
                </h1>

                <p class="text-base sm:text-lg text-slate-300 font-cairo leading-relaxed">
                    {{ $package->subtitle ?? 'حل هندسي متكامل ومصمم خصيصاً لتحقيق أعلى درجات الكفاءة التشغيلية والسيادة الرقمية.' }}
                </p>
            </div>

            <!-- Key Metric Micro-Badges -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-2">
                <div class="p-3.5 rounded-2xl glass-obsidian border border-obsidian-border text-center space-y-1">
                    <div class="text-[11px] text-slate-400 font-cairo">نظام المحاسبة</div>
                    <div class="text-xs sm:text-sm font-extrabold text-white font-cairo">
                        @if($package->billing_type === 'hourly') بالساعة
                        @elseif($package->billing_type === 'monthly') اشتراك شهري
                        @elseif($package->billing_type === 'yearly') اشتراك سنوي
                        @else دفعة واحدة
                        @endif
                    </div>
                </div>

                <div class="p-3.5 rounded-2xl glass-obsidian border border-obsidian-border text-center space-y-1">
                    <div class="text-[11px] text-slate-400 font-cairo">الضمان الهندسي</div>
                    <div class="text-xs sm:text-sm font-extrabold text-cyber-gold font-cairo">
                        {{ $package->warranty_days }} يوم معتمد
                    </div>
                </div>

                <div class="p-3.5 rounded-2xl glass-obsidian border border-obsidian-border text-center space-y-1">
                    <div class="text-[11px] text-slate-400 font-cairo">زمن الاستجابة (SLA)</div>
                    <div class="text-xs sm:text-sm font-extrabold text-cyber-cyan font-cairo">
                        أقل من 15 دقيقة
                    </div>
                </div>

                <div class="p-3.5 rounded-2xl glass-obsidian border border-obsidian-border text-center space-y-1">
                    <div class="text-[11px] text-slate-400 font-cairo">نسبة التوفر</div>
                    <div class="text-xs sm:text-sm font-extrabold text-cyber-purple font-cairo">
                        99.98% Uptime
                    </div>
                </div>
            </div>

            <!-- Features Summary Checklist -->
            <div class="rounded-3xl glass-obsidian border border-obsidian-border p-6 sm:p-8 space-y-5">
                <div class="flex items-center justify-between border-b border-obsidian-border/80 pb-4">
                    <h2 class="text-lg font-bold text-white font-cairo flex items-center gap-2.5">
                        <span class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $service->accent_color }};"></span>
                        <span>ماذا ستحصل تحديداً مع هذه الباقة؟</span>
                    </h2>
                    <span class="text-xs text-slate-400 font-mono">
                        {{ is_array($package->features_json) ? count($package->features_json) : 0 }} ميزة أساسية
                    </span>
                </div>

                <div class="grid grid-cols-1 gap-3.5">
                    @if(is_array($package->features_json) && count($package->features_json) > 0)
                        @foreach($package->features_json as $idx => $feat)
                            <div class="flex items-start gap-3 p-3 rounded-xl bg-obsidian-surface/60 border border-obsidian-border/50 hover:border-slate-700 transition-colors">
                                <div class="mt-0.5 w-5 h-5 rounded-lg flex items-center justify-center shrink-0" 
                                     style="background-color: {{ $service->accent_color }}20; color: {{ $service->accent_color }};">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <div class="text-xs sm:text-sm text-slate-200 font-cairo leading-relaxed">
                                    {{ $feat }}
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-xs text-slate-400">تشمل الباقة كافة الامتيازات والمواصفات القياسية المعتمدة في قلعة سولي.</div>
                    @endif
                </div>
            </div>

        </div>

        <!-- Right Column: Interactive Live Booking & Price Calculator Card -->
        <div class="lg:col-span-5 sticky top-24 space-y-6">
            <div class="rounded-3xl p-6 sm:p-8 glass-obsidian border border-cyber-cyan/30 shadow-[0_10px_40px_rgba(0,0,0,0.85)] relative overflow-hidden space-y-6">
                <!-- Background Ambient Glow -->
                <div class="absolute -top-20 -right-20 w-64 h-64 rounded-full blur-[100px] pointer-events-none opacity-20"
                     style="background-color: {{ $service->accent_color }};"></div>

                <div class="relative z-10 space-y-5">
                    
                    <!-- Pricing Header -->
                    <div class="flex items-start justify-between">
                        <div>
                            <span class="text-xs font-cairo text-slate-400 font-bold">التسعير الهندسي المعتمد</span>
                            <div class="flex items-baseline gap-2 mt-1">
                                <span class="text-4xl sm:text-5xl font-black font-mono text-white" x-text="computedTotalFormatted">
                                    {{ number_format($package->price, 2) }}
                                </span>
                                <span class="text-sm font-bold text-slate-400 font-cairo">ج.م</span>
                            </div>
                        </div>

                        <div class="text-left">
                            @if($package->original_price && $package->original_price > $package->price)
                                <div class="text-xs font-mono text-slate-500 line-through">
                                    <span x-text="computedOriginalTotalFormatted">{{ number_format($package->original_price, 2) }}</span> ج.م
                                </div>
                                <div class="text-[11px] font-bold text-emerald-400 font-mono mt-0.5">
                                    خصم <span x-text="discountPercent"></span>%
                                </div>
                            @endif
                            <div class="text-xs text-slate-400 mt-1 font-cairo" x-text="'لكل ' + unitLabel">
                                {{ $package->unit_label }}
                            </div>
                        </div>
                    </div>

                    <!-- Quantity / Units / Duration Adjuster -->
                    @if($package->billing_type === 'hourly' || $package->min_units > 1 || in_array($service->slug, ['maintenance', 'cybersecurity']))
                        <div class="p-4 rounded-2xl bg-obsidian-surface border border-obsidian-border space-y-3">
                            <div class="flex items-center justify-between text-xs font-cairo">
                                <span class="text-slate-300 font-bold">عدد الساعات / الوحدات المطلوبة:</span>
                                <span class="font-cairo text-cyber-cyan font-bold" x-text="units + ' ' + unitLabel"></span>
                            </div>

                            <div class="flex items-center justify-between gap-3">
                                <button type="button" @click="decrementUnits()" 
                                        class="w-10 h-10 rounded-xl bg-obsidian-border/70 hover:bg-slate-700 text-white font-mono font-bold flex items-center justify-center transition-all active:scale-95 text-lg">
                                    -
                                </button>

                                <div class="flex-1 text-center font-mono font-bold text-xl text-white">
                                    <span x-text="units"></span>
                                </div>

                                <button type="button" @click="incrementUnits()" 
                                        class="w-10 h-10 rounded-xl bg-cyber-cyan/20 hover:bg-cyber-cyan/30 text-cyber-cyan font-mono font-bold flex items-center justify-center transition-all active:scale-95 text-lg border border-cyber-cyan/30">
                                    +
                                </button>
                            </div>

                            @if($package->min_units > 1)
                                <div class="text-[10px] text-slate-400 text-center font-cairo">
                                    * الحد الأدنى للحجز هو {{ $package->min_units }} {{ $package->unit_label }}
                                </div>
                            @endif

                            @if($service->slug === 'maintenance')
                                <div class="p-2.5 rounded-xl bg-amber-500/10 border border-amber-500/30 text-[10px] text-amber-300 font-cairo text-center">
                                    ⚡ 400 ج.م/ساعة ثابتة بدون خصم على الساعات الإضافية. يُستثنى تماماً صيانة البورد والدوائر الإلكترونية الدقيقة.
                                </div>
                            @elseif($service->slug === 'cybersecurity')
                                <div class="p-2.5 rounded-xl bg-purple-500/10 border border-purple-500/30 text-[10px] text-purple-300 font-cairo text-center">
                                    🛡️ باقات معتمدة (700 إلى 5,000 ج.م/ساعة) مع توقيع NDA وحماية ومراقبة هواتف الأبناء < 18 سنة.
                                </div>
                            @endif
                        </div>
                    @elseif($package->billing_type === 'monthly')
                        <!-- Subscription Duration Selector with extra discount -->
                        <div class="p-4 rounded-2xl bg-obsidian-surface border border-obsidian-border space-y-3">
                            <div class="flex items-center justify-between text-xs font-cairo">
                                <span class="text-slate-300 font-bold">مدة الاشتراك:</span>
                                <span class="text-[11px] font-cairo font-bold text-cyber-gold" x-show="durationMonths === 12">خصم 20% للسداد السنوي</span>
                            </div>

                            <div class="grid grid-cols-3 gap-2">
                                <button type="button" @click="durationMonths = 1" 
                                        :class="durationMonths === 1 ? 'border-cyber-cyan bg-cyber-cyan/15 text-cyber-cyan' : 'border-obsidian-border text-slate-400 bg-obsidian-surface'"
                                        class="py-2.5 px-2 rounded-xl border text-xs font-cairo font-bold text-center transition-all">
                                    شهر واحد
                                </button>
                                <button type="button" @click="durationMonths = 6" 
                                        :class="durationMonths === 6 ? 'border-cyber-cyan bg-cyber-cyan/15 text-cyber-cyan' : 'border-obsidian-border text-slate-400 bg-obsidian-surface'"
                                        class="py-2.5 px-2 rounded-xl border text-xs font-cairo font-bold text-center transition-all">
                                    6 أشهر (-10%)
                                </button>
                                <button type="button" @click="durationMonths = 12" 
                                        :class="durationMonths === 12 ? 'border-cyber-gold bg-cyber-gold/15 text-cyber-gold shadow-sm' : 'border-obsidian-border text-slate-400 bg-obsidian-surface'"
                                        class="py-2.5 px-2 rounded-xl border text-xs font-cairo font-bold text-center transition-all">
                                    سنة (-20%)
                                </button>
                            </div>
                        </div>
                    @endif

                    <!-- Wallet Balance Live Preview -->
                    <div class="p-4 rounded-2xl border space-y-2.5 transition-all"
                         :class="hasSufficientBalance ? 'bg-cyber-cyan/5 border-cyber-cyan/30' : 'bg-obsidian-surface border-obsidian-border'">
                        <div class="flex items-center justify-between text-xs font-cairo">
                            <span class="text-slate-400">رصيد محفظتك الرقمية:</span>
                            <span class="font-cairo font-bold text-white">
                                <span class="font-mono">{{ number_format($citadelWalletBalance ?? 0, 2) }}</span> ج.م
                            </span>
                        </div>

                        <div class="flex items-center gap-2 pt-1">
                            <template x-if="hasSufficientBalance">
                                <div class="flex items-center gap-1.5 text-xs text-cyber-cyan font-bold font-cairo">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span>رصيدك كافٍ لإتمام الحجز الفوري بالخصم المباشر.</span>
                                </div>
                            </template>

                            <template x-if="!hasSufficientBalance">
                                <div class="flex items-center justify-between w-full text-xs text-amber-300 font-cairo">
                                    <span>المتبقي لشحن المحفظة:</span>
                                    <span class="font-cairo font-bold"><span class="font-mono" x-text="shortfallFormatted"></span> ج.م</span>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Primary Booking Call-to-Action -->
                    <div class="space-y-3 pt-2">
                        @auth
                            <a :href="bookingUrl" 
                               class="w-full py-4 px-6 rounded-2xl btn-citadel-primary text-sm sm:text-base flex items-center justify-center gap-3 text-center">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                <span>احجز هذه الباقة فوراً ⚡</span>
                            </a>
                        @else
                            <a :href="bookingUrl" 
                               @click.prevent="$dispatch('open-express-booking', {
                                   serviceId: {{ $service->id }},
                                   serviceSlug: '{{ $service->slug }}',
                                   serviceName: '{{ addslashes($service->name) }}',
                                   packageId: {{ $package->id }},
                                   packageSlug: '{{ $package->slug }}',
                                   packageName: '{{ addslashes($package->name) }}',
                                   price: calculatedPrice,
                                   hours: units
                               })"
                               class="w-full py-4 px-6 rounded-2xl btn-citadel-primary text-sm sm:text-base flex items-center justify-center gap-3 text-center">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                <span>احجز هذه الباقة فوراً ⚡</span>
                            </a>
                        @endauth

                        <a :href="whatsappUrl" target="_blank" rel="noopener noreferrer"
                           class="w-full py-3 px-6 rounded-2xl btn-citadel-secondary text-xs sm:text-sm flex items-center justify-center gap-2">
                            <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766 0-3.18-2.587-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.303-.058.116-.087.188-.173.289l-.26.302c-.087.087-.179.182-.077.357.101.174.451.745.969 1.206.667.593 1.229.776 1.403.863.173.087.275.072.376-.044.101-.116.433-.506.549-.68.116-.173.231-.144.39-.087s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/>
                            </svg>
                            <span>استشارة هندسية سريعة عبر واتساب</span>
                        </a>
                    </div>

                    <!-- Safety & Warranty Micro-Bullets -->
                    <div class="pt-3 border-t border-obsidian-border/60 space-y-2 text-[11px] text-slate-400 font-cairo">
                        <div class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-cyber-cyan"></span>
                            <span>ضمان استرداد كامل خلال 14 يوماً وفق وثيقة الالتزام الهندسي.</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-cyber-gold"></span>
                            <span>إمكانية الدفع عبر فودافون كاش، إنستاباي، أو التحويل البنكي.</span>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>

    <!-- ===================================================================== -->
    <!-- 3. TECHNICAL ARCHITECTURE & IMPLEMENTATION TIMELINE                   -->
    <!-- ===================================================================== -->
    <section class="rounded-3xl glass-obsidian border border-obsidian-border p-6 sm:p-10 space-y-8">
        <div class="space-y-2">
            <div class="flex items-center gap-2 text-xs font-mono text-cyber-cyan">
                <span>// WORKFLOW & EXECUTION PIPELINE</span>
            </div>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-white font-cairo">
                خريطة التنفيذ الهندسي والجدول الزمني للتشغيل
            </h2>
            <p class="text-xs sm:text-sm text-slate-400 font-cairo max-w-3xl">
                نتبع في قلعة سولي منهجية تشغيل سيادية صارمة ومقسمة إلى 3 مراحل تنفيذية لضمان تسليم منظومتك في أسرع وقت وبأعلى مواصفات الجودة:
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 relative">
            <!-- Stage 1 -->
            <div class="p-6 rounded-2xl bg-obsidian-surface border border-obsidian-border/80 hover:border-cyber-cyan/50 transition-all space-y-4 relative group">
                <div class="flex items-center justify-between">
                    <span class="text-2xl font-black font-mono text-cyber-cyan">01</span>
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-cairo font-bold bg-cyber-cyan/10 text-cyber-cyan border border-cyber-cyan/30">
                        اليوم 1 - 2
                    </span>
                </div>
                <h3 class="text-base font-bold text-white font-cairo">التدقيق الفني وتهيئة البيئة</h3>
                <p class="text-xs text-slate-400 font-cairo leading-relaxed">
                    فحص البنية التحتية، تجهيز السيرفر المعزول أو الأجهزة، وتعيين مهندس استشاري مسؤول عن حسابك مع فتح قناة دعم خاصة.
                </p>
                <ul class="text-[11px] text-slate-400 space-y-1.5 font-cairo pt-2 border-t border-obsidian-border/50">
                    <li class="flex items-center gap-1.5 text-slate-300">
                        <span class="text-cyber-cyan font-bold">✓</span> فحص التوافقية والأمان السيبراني
                    </li>
                    <li class="flex items-center gap-1.5 text-slate-300">
                        <span class="text-cyber-cyan font-bold">✓</span> تسليم بيانات الدخول الأولية
                    </li>
                </ul>
            </div>

            <!-- Stage 2 -->
            <div class="p-6 rounded-2xl bg-obsidian-surface border border-obsidian-border/80 hover:border-cyber-gold/50 transition-all space-y-4 relative group">
                <div class="flex items-center justify-between">
                    <span class="text-2xl font-black font-mono text-cyber-gold">02</span>
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-cairo font-bold bg-cyber-gold/10 text-cyber-gold border border-cyber-gold/30">
                        اليوم 3 - 5
                    </span>
                </div>
                <h3 class="text-base font-bold text-white font-cairo">التخصيص والربط ورفع البيانات</h3>
                <p class="text-xs text-slate-400 font-cairo leading-relaxed">
                    تخصيص الواجهات والعمليات وفق هوية ونشاط شركتك، رفع قواعد البيانات أو الأصناف، وربط قنوات الدفع أو بوابات الضرائب.
                </p>
                <ul class="text-[11px] text-slate-400 space-y-1.5 font-cairo pt-2 border-t border-obsidian-border/50">
                    <li class="flex items-center gap-1.5 text-slate-300">
                        <span class="text-cyber-gold font-bold">✓</span> استيراد وتدقيق البيانات السابقة
                    </li>
                    <li class="flex items-center gap-1.5 text-slate-300">
                        <span class="text-cyber-gold font-bold">✓</span> تفعيل التشفير والتكامل التقني
                    </li>
                </ul>
            </div>

            <!-- Stage 3 -->
            <div class="p-6 rounded-2xl bg-obsidian-surface border border-obsidian-border/80 hover:border-cyber-purple/50 transition-all space-y-4 relative group">
                <div class="flex items-center justify-between">
                    <span class="text-2xl font-black font-mono text-cyber-purple">03</span>
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-cairo font-bold bg-cyber-purple/10 text-cyber-purple border border-cyber-purple/30">
                        اليوم 6 - 7
                    </span>
                </div>
                <h3 class="text-base font-bold text-white font-cairo">الاختبار الصارم، التدريب، والتسليم</h3>
                <p class="text-xs text-slate-400 font-cairo leading-relaxed">
                    اختبار أداء النظام تحت الضغط (Stress Testing)، تدريب فريق العمل والمديرين عملياً، وإصدار وثيقة الضمان الهندسي المعتمدة.
                </p>
                <ul class="text-[11px] text-slate-400 space-y-1.5 font-cairo pt-2 border-t border-obsidian-border/50">
                    <li class="flex items-center gap-1.5 text-slate-300">
                        <span class="text-cyber-purple font-bold">✓</span> جلسات تدريب تفاعلية ومسجلة
                    </li>
                    <li class="flex items-center gap-1.5 text-slate-300">
                        <span class="text-cyber-purple font-bold">✓</span> انطلاق حي مع مراقبة مدار الساعة
                    </li>
                </ul>
            </div>
        </div>
    </section>

    <!-- ===================================================================== -->
    <!-- 4. ENGINEERING WARRANTY & SLA COMMITMENT                              -->
    <!-- ===================================================================== -->
    <section class="rounded-3xl p-6 sm:p-10 border border-cyber-gold/40 glass-obsidian relative overflow-hidden">
        <div class="absolute top-0 right-0 w-80 h-80 bg-cyber-gold/10 rounded-full blur-[120px] pointer-events-none"></div>

        <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            <div class="lg:col-span-8 space-y-4">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyber-gold/15 text-cyber-gold text-xs font-cairo font-bold border border-cyber-gold/30">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 1.944A11.954 11.954 0 012.166 5C2.056 5.649 2 6.319 2 7c0 5.225 3.34 9.67 8 11.317C14.66 16.67 18 12.225 18 7c0-.682-.057-1.35-.166-2.001A11.954 11.954 0 0110 1.944zM11 14a1 1 0 11-2 0 1 1 0 012 0zm0-7a1 1 0 10-2 0v3a1 1 0 102 0V7z" clip-rule="evenodd"/>
                    </svg>
                    <span>وثيقة الالتزام الهندسي والضمان السيادي</span>
                </div>

                <h3 class="text-2xl sm:text-3xl font-extrabold text-white font-cairo">
                    ضمان حقيقي لمدة {{ $package->warranty_days }} يوم بدون أي تكاليف خفية
                </h3>

                <p class="text-xs sm:text-sm text-slate-300 font-cairo leading-relaxed">
                    لا نترك عميلنا وحيداً أبداً. كل باقة مشمولة بضمان هندسي كامل يغطي تصحيح أي ثغرات أو أعطال، وتوفير التحديثات الدورية مجاناً طوال مدة الضمان، مع استجابة فورية للأعطال الطارئة في مدة لا تتجاوز 15 دقيقة عبر خط ساخن مخصص.
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
                    <div class="p-3 rounded-xl bg-obsidian-surface/80 border border-obsidian-border text-center">
                        <div class="text-cyber-gold font-mono font-black text-lg">100%</div>
                        <div class="text-[11px] text-slate-400 font-cairo font-bold">مطابقة للمواصفات</div>
                    </div>
                    <div class="p-3 rounded-xl bg-obsidian-surface/80 border border-obsidian-border text-center">
                        <div class="text-cyber-cyan font-mono font-black text-lg">256-bit</div>
                        <div class="text-[11px] text-slate-400 font-cairo font-bold">تشفير عسكري للبيانات</div>
                    </div>
                    <div class="p-3 rounded-xl bg-obsidian-surface/80 border border-obsidian-border text-center">
                        <div class="text-emerald-400 font-cairo font-bold text-lg"><span class="font-mono font-black">14</span> يوم</div>
                        <div class="text-[11px] text-slate-400 font-cairo font-bold">استرداد نقدي كامل</div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-4 flex flex-col items-center justify-center p-6 rounded-2xl bg-obsidian-surface/60 border border-cyber-gold/20 text-center space-y-3">
                <div class="w-16 h-16 rounded-2xl bg-cyber-gold/20 text-cyber-gold flex items-center justify-center border border-cyber-gold/40 shadow-sm">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <div class="text-sm font-bold text-white font-cairo">معتمد وموثق هندسياً</div>
                <p class="text-[11px] text-slate-400 font-cairo">
                    توقيع رقمي مسجل في قلعة سولي لحماية استثمارك وراحة بالك.
                </p>
            </div>
        </div>
    </section>

    <!-- ===================================================================== -->
    <!-- 5. EXPANDABLE FAQ ACCORDION                                           -->
    <!-- ===================================================================== -->
    <section class="rounded-3xl glass-obsidian border border-obsidian-border p-6 sm:p-10 space-y-6" x-data="{ openFaq: null }">
        <div class="space-y-2">
            <div class="text-xs font-mono text-cyber-cyan">// FREQUENTLY ASKED QUESTIONS</div>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-white font-cairo">
                الأسئلة الشائعة حول هذه الباقة
            </h2>
        </div>

        <div class="space-y-3">
            <!-- FAQ 1 -->
            <div class="rounded-2xl bg-obsidian-surface border border-obsidian-border/80 overflow-hidden transition-all">
                <button type="button" @click="openFaq = (openFaq === 1 ? null : 1)" 
                        class="w-full p-4 sm:p-5 text-right flex items-center justify-between text-white font-bold font-cairo text-sm sm:text-base hover:text-cyber-cyan transition-colors">
                    <span>هل يمكنني ترقية الباقة لاحقاً مع الحفاظ على كافة البيانات؟</span>
                    <svg class="w-5 h-5 text-slate-400 transition-transform duration-300 shrink-0" :class="openFaq === 1 ? 'rotate-180 text-cyber-cyan' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="openFaq === 1" x-collapse class="px-5 pb-5 text-xs sm:text-sm text-slate-300 font-cairo leading-relaxed border-t border-obsidian-border/50 pt-3">
                    نعم بكل تأكيد! تم بناء جميع منظومات قلعة سولي على بنية تحتية سحابية متقدمة تسمح بالترقية اللحظية بنقرة زر واحدة دون أي توقف في الخدمة وباحتفاظ كامل بكافة سجلاتك وبياناتك السابقة مع خصم القيمة المتبقية من اشتراكك القديم تلقائياً.
                </div>
            </div>

            <!-- FAQ 2 -->
            <div class="rounded-2xl bg-obsidian-surface border border-obsidian-border/80 overflow-hidden transition-all">
                <button type="button" @click="openFaq = (openFaq === 2 ? null : 2)" 
                        class="w-full p-4 sm:p-5 text-right flex items-center justify-between text-white font-bold font-cairo text-sm sm:text-base hover:text-cyber-cyan transition-colors">
                    <span>ما هي طرق السداد والشحن المتاحة داخل القلعة؟</span>
                    <svg class="w-5 h-5 text-slate-400 transition-transform duration-300 shrink-0" :class="openFaq === 2 ? 'rotate-180 text-cyber-cyan' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="openFaq === 2" x-collapse class="px-5 pb-5 text-xs sm:text-sm text-slate-300 font-cairo leading-relaxed border-t border-obsidian-border/50 pt-3">
                    يمكنك الدفع المباشر بالخصم من محفظتك الرقمية في سولي، أو شحن محفظتك فوراً عبر: فودافون كاش (Vodafone Cash)، تطبيق إنستاباي (InstaPay) اللحظي، أو التحويل البنكي المباشر مع إصدار إيصال رسمي معتمد.
                </div>
            </div>

            <!-- FAQ 3 -->
            <div class="rounded-2xl bg-obsidian-surface border border-obsidian-border/80 overflow-hidden transition-all">
                <button type="button" @click="openFaq = (openFaq === 3 ? null : 3)" 
                        class="w-full p-4 sm:p-5 text-right flex items-center justify-between text-white font-bold font-cairo text-sm sm:text-base hover:text-cyber-cyan transition-colors">
                    <span>هل تشمل الباقة تدريب موظفينا ودعم فني حي؟</span>
                    <svg class="w-5 h-5 text-slate-400 transition-transform duration-300 shrink-0" :class="openFaq === 3 ? 'rotate-180 text-cyber-cyan' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="openFaq === 3" x-collapse class="px-5 pb-5 text-xs sm:text-sm text-slate-300 font-cairo leading-relaxed border-t border-obsidian-border/50 pt-3">
                    نعم، يحصل فريقك على جلسات تدريب تفاعلية شاملة مباشرة مع مهندسينا المختصين، بالإضافة إلى دليل تدريبي وفيديوهات شرح مسجلة، وقناة دعم طارئة 24/7 لحل أي استفسارات أو مواقف طارئة فوراً.
                </div>
            </div>

            <!-- FAQ 4 -->
            <div class="rounded-2xl bg-obsidian-surface border border-obsidian-border/80 overflow-hidden transition-all">
                <button type="button" @click="openFaq = (openFaq === 4 ? null : 4)" 
                        class="w-full p-4 sm:p-5 text-right flex items-center justify-between text-white font-bold font-cairo text-sm sm:text-base hover:text-cyber-cyan transition-colors">
                    <span>ماذا يحدث بعد انتهاء فترة الضمان الهندسي المعتمدة؟</span>
                    <svg class="w-5 h-5 text-slate-400 transition-transform duration-300 shrink-0" :class="openFaq === 4 ? 'rotate-180 text-cyber-cyan' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="openFaq === 4" x-collapse class="px-5 pb-5 text-xs sm:text-sm text-slate-300 font-cairo leading-relaxed border-t border-obsidian-border/50 pt-3">
                    تستمر منظومتك في العمل بكامل طاقتها دون توقف. يمكنك بعد انتهاء فترة الضمان الاشتراك في عقود الصيانة الدورية أو التحديثات السحابية بأسعار تفضيلية مخفضة لعملاء القلعة، مع استمرار خدمة تذاكر الدعم الفني دون انقطاع.
                </div>
            </div>
        </div>
    </section>

    <!-- ===================================================================== -->
    <!-- 6. RELATED & ALTERNATIVE PACKAGES CAROUSEL/GRID                       -->
    <!-- ===================================================================== -->
    @if(isset($relatedPackages) && $relatedPackages->count() > 0)
        <section class="space-y-6 pt-6">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs font-mono text-slate-400">// PACKAGES COMPARISON</div>
                    <h3 class="text-xl sm:text-2xl font-bold text-white font-cairo">
                        باقات أخرى متاحة ضمن {{ $service->name }}
                    </h3>
                </div>

                <a href="{{ route('services.show', $service->slug) }}#packages-section" 
                   class="text-xs font-bold text-cyber-cyan hover:underline font-cairo flex items-center gap-1">
                    <span>عرض جدول المقارنة الكامل</span>
                    <svg class="w-4 h-4 rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($relatedPackages as $relPkg)
                    <div class="p-6 rounded-3xl glass-obsidian border border-obsidian-border hover:border-slate-600 transition-all flex flex-col justify-between space-y-4">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-cairo font-bold px-2.5 py-1 rounded-full bg-obsidian-card border border-obsidian-border text-slate-300">
                                    {{ $relPkg->badge_text ?? 'خيار بديل' }}
                                </span>
                                @if($relPkg->is_featured)
                                    <span class="text-[10px] font-bold text-cyber-gold bg-cyber-gold/10 px-2 py-0.5 rounded-full border border-cyber-gold/30 font-cairo">
                                        موصى بها
                                    </span>
                                @endif
                            </div>

                            <h4 class="text-lg font-bold text-white font-cairo">{{ $relPkg->name }}</h4>
                            <p class="text-xs text-slate-400 font-cairo line-clamp-2 leading-relaxed">
                                {{ $relPkg->subtitle }}
                            </p>

                            <div class="pt-2">
                                <div class="text-2xl font-black font-mono text-white">
                                    {{ number_format($relPkg->price, 2) }} <span class="text-xs font-normal text-slate-400 font-cairo">ج.م / {{ $relPkg->unit_label }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="pt-3 border-t border-obsidian-border/60">
                            <a href="{{ route('services.package', [$service->slug, $relPkg->slug]) }}" 
                               class="w-full py-2.5 px-4 rounded-xl btn-citadel-secondary text-xs font-bold text-center flex items-center justify-center gap-2">
                                <span>استعراض مواصفات الباقة</span>
                                <svg class="w-3.5 h-3.5 rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

</div>

<!-- ===================================================================== -->
<!-- ALPINE CONTROLLER SCRIPT                                              -->
<!-- ===================================================================== -->
<script>
function packageController(config) {
    return {
        basePrice: config.basePrice || 0,
        originalPrice: config.originalPrice || 0,
        minUnits: config.minUnits || 1,
        units: config.minUnits || 1,
        unitLabel: config.unitLabel || 'شهر',
        billingType: config.billingType || 'one_time',
        walletBalance: config.walletBalance || 0,
        durationMonths: 1,

        incrementUnits() {
            this.units++;
        },

        decrementUnits() {
            if (this.units > this.minUnits) {
                this.units--;
            }
        },

        get computedTotal() {
            let total = 0;
            if (this.billingType === 'monthly') {
                let monthlyRate = this.basePrice;
                let multiplier = this.durationMonths;
                if (this.durationMonths === 6) {
                    monthlyRate = monthlyRate * 0.90; // 10% discount
                } else if (this.durationMonths === 12) {
                    monthlyRate = monthlyRate * 0.80; // 20% discount
                }
                total = monthlyRate * multiplier;
            } else {
                total = this.basePrice * this.units;
            }
            return Math.max(0, total);
        },

        get computedOriginalTotal() {
            let refPrice = this.originalPrice > 0 ? this.originalPrice : (this.basePrice * 1.25);
            if (this.billingType === 'monthly') {
                return refPrice * this.durationMonths;
            }
            return refPrice * this.units;
        },

        get computedTotalFormatted() {
            return this.computedTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },

        get computedOriginalTotalFormatted() {
            return this.computedOriginalTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },

        get discountPercent() {
            if (this.computedOriginalTotal <= this.computedTotal) return 0;
            const diff = this.computedOriginalTotal - this.computedTotal;
            return Math.round((diff / this.computedOriginalTotal) * 100);
        },

        get hasSufficientBalance() {
            return this.walletBalance >= this.computedTotal;
        },

        get shortfallFormatted() {
            const diff = Math.max(0, this.computedTotal - this.walletBalance);
            return diff.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },

        get bookingUrl() {
            const serviceSlug = '{{ $service->slug }}';
            const packageSlug = '{{ $package->slug }}';
            let params = new URLSearchParams({
                service: serviceSlug,
                package: packageSlug,
                units: this.units,
                duration: this.durationMonths,
                total: this.computedTotal.toFixed(2)
            });
            return '{{ route('portal.bookings') }}?' + params.toString();
        },

        get whatsappUrl() {
            const text = encodeURIComponent(
                `مرحباً قلعة سولي.. أود الاستفسار وحجز باقة: {{ $package->name }} ضمن خدمة {{ $service->name }}.\n` +
                `المبلغ التقديري: ${this.computedTotalFormatted} ج.م`
            );
            return `https://wa.me/201090623326?text=${text}`;
        }
    };
}
</script>
@endsection