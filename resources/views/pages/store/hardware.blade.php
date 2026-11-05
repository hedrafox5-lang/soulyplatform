@extends(request()->header('sec-ch-ua-mobile') === '?1' || request()->query('view') === 'mobile' ? 'layouts.mobile' : 'layouts.desktop')

@section('title', 'متجر قطع الهاردوير وأجهزة الأعمال والـ POS | سولي')

@section('meta_description', 'متجر سولي المعتمد لأجهزة الكاشير، الطابعات الحرارية، شاشات التاتش، هاردات SSD، الرامات، البطاريات، وأجهزة البصمة المتوافقة مع منظومة BrandERP.')

@section('content')
<div class="min-h-screen py-10 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-10"
     x-data="hardwareStore({
         products: {{ Js::from($products) }},
         userWallet: {{ (float) $userWalletBalance }},
         isAuth: {{ Auth::check() ? 'true' : 'false' }},
         userName: {{ Js::from(Auth::user()->name ?? '') }},
         userPhone: {{ Js::from(Auth::user()->phone ?? '') }}
     })">

    {{-- 1. HERO HEADER --}}
    <div class="relative overflow-hidden rounded-3xl glass-obsidian border border-cyber-cyan/40 p-6 sm:p-10 shadow-[0_4px_30px_rgba(0,245,212,0.12)]">
        <div class="absolute -top-20 -left-20 w-80 h-80 bg-cyber-cyan/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-20 -right-20 w-80 h-80 bg-cyber-gold/15 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="space-y-3 text-center md:text-right">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-mono font-bold bg-cyber-cyan/15 text-cyber-cyan border border-cyber-cyan/30">
                    <span>SOULY HARDWARE CITADEL & IT DEPOT</span>
                    <span class="w-1.5 h-1.5 rounded-full bg-cyber-cyan animate-pulse"></span>
                </div>
                <h1 class="text-2xl sm:text-4xl font-black text-white font-cairo tracking-tight">
                    متجر قطع الهاردوير وأجهزة الأعمال والصيانة 🖥️
                </h1>
                <p class="text-xs sm:text-sm text-slate-300 font-cairo max-w-2xl leading-relaxed">
                    معدات نقاط بيع، رامات، هاردات، بطاريات، ومعاجين تبريد معتمدة ومختبرة معملياً، متوافقة مع منظومات BrandERP وخدمات الصيانة الميدانية بضمان رسمي.
                </p>
            </div>

            {{-- Cart Quick Pill --}}
            <div class="flex items-center gap-3 shrink-0">
                <button type="button"
                        @click="cartDrawerOpen = true"
                        class="px-5 py-3 rounded-2xl bg-gradient-to-r from-cyber-cyan to-teal-500 text-obsidian-base font-black font-cairo text-sm flex items-center gap-3 shadow-neon-cyan hover:brightness-110 active:scale-95 transition-all cursor-pointer">
                    <span>🛒 سلة الأجهزة</span>
                    <span class="px-2 py-0.5 rounded-full bg-black/20 text-obsidian-base font-mono text-xs font-black" x-text="cartTotalCount">0</span>
                    <span class="font-mono text-xs border-r border-black/20 pr-2" x-text="cartTotalPrice.toLocaleString() + ' EGP'">0 EGP</span>
                </button>
            </div>
        </div>
    </div>

    {{-- 2. CATEGORY PILLS & INSTANT SEARCH BAR --}}
    <div class="space-y-4">
        <div class="flex flex-col md:flex-row items-center justify-between gap-4">
            
            {{-- Category Filter Buttons --}}
            <div class="flex items-center gap-2 overflow-x-auto w-full md:w-auto pb-2 md:pb-0 scrollbar-none">
                @foreach($categories as $cat)
                    <button type="button"
                            @click="selectedCategory = '{{ $cat['id'] }}'"
                            :class="selectedCategory === '{{ $cat['id'] }}' 
                                ? 'bg-cyber-cyan text-obsidian-base font-black shadow-neon-cyan border-cyber-cyan' 
                                : 'bg-obsidian-surface text-slate-300 hover:text-white border-obsidian-border'"
                            class="px-4 py-2.5 rounded-2xl text-xs font-cairo font-bold border transition-all flex items-center gap-2 shrink-0 cursor-pointer">
                        <span>{{ $cat['icon'] }}</span>
                        <span>{{ $cat['name_ar'] }}</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold"
                              :class="selectedCategory === '{{ $cat['id'] }}' ? 'bg-black/20 text-black' : 'bg-slate-800 text-slate-400'">
                            {{ $cat['count'] }}
                        </span>
                    </button>
                @endforeach
            </div>

            {{-- Search Bar --}}
            <div class="relative w-full md:w-80">
                <input type="text"
                       x-model="searchQuery"
                       placeholder="ابحث: طابعة، باركود، SSD، رامات، بطارية..."
                       class="w-full bg-obsidian-surface text-xs text-slate-200 placeholder-slate-500 rounded-2xl px-10 py-3 border border-obsidian-border focus:border-cyber-cyan focus:outline-none transition-all font-cairo">
                <div class="absolute inset-y-0 right-3.5 flex items-center pointer-events-none text-cyber-cyan">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <button x-show="searchQuery" @click="searchQuery = ''" class="absolute inset-y-0 left-3.5 flex items-center text-slate-500 hover:text-white text-xs">✕</button>
            </div>

        </div>
    </div>

    {{-- 3. PRODUCTS GRID --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
        <template x-for="product in filteredProducts" :key="product.id">
            <div class="p-5 rounded-3xl glass-obsidian border border-obsidian-border hover:border-cyber-cyan/50 flex flex-col justify-between gap-4 transition-all group shadow-sm hover:shadow-[0_4px_25px_rgba(0,245,212,0.1)]">
                
                <div class="space-y-3">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded bg-slate-800 text-slate-400" x-text="product.sku"></span>
                        <span class="text-[10px] font-mono font-bold px-2.5 py-0.5 rounded-full bg-cyber-purple/20 text-purple-300 border border-purple-500/30" x-text="product.brand"></span>
                    </div>

                    <h3 class="text-sm font-black text-white font-cairo leading-snug group-hover:text-cyber-cyan transition-colors" x-text="product.name_ar"></h3>

                    <div x-show="Number(product.compatible_with_branderp) === 1" class="flex items-center gap-1.5 text-[11px] font-cairo text-cyber-cyan font-bold bg-cyber-cyan/10 px-2.5 py-1 rounded-xl border border-cyber-cyan/30">
                        <span>✓</span>
                        <span>متوافق 100% مع منظومة BrandERP</span>
                    </div>

                    <div x-show="Number(product.compatible_with_branderp) === 0" class="flex items-center gap-1.5 text-[11px] font-cairo text-emerald-400 font-bold bg-emerald-500/10 px-2.5 py-1 rounded-xl border border-emerald-500/30">
                        <span>🔧</span>
                        <span>معتمد لترقيات وصيانة سولي الهندسية</span>
                    </div>

                    <p class="text-xs text-slate-400 font-cairo line-clamp-2 leading-relaxed" x-text="product.description_ar"></p>

                    <div class="flex items-center justify-between text-[11px] font-cairo pt-2 border-t border-obsidian-border/70">
                        <span class="text-emerald-400 flex items-center gap-1">
                            <span>🛡️ ضمان</span>
                            <span class="font-mono font-bold" x-text="product.warranty_months + ' شهر'"></span>
                        </span>
                        <span class="font-bold" :class="product.stock_qty > 0 ? 'text-slate-300' : 'text-rose-400'">
                            <span x-show="product.stock_qty > 0" x-text="'متوفر (' + product.stock_qty + ')'"></span>
                            <span x-show="product.stock_qty <= 0">نفد المخزون</span>
                        </span>
                    </div>
                </div>

                <div class="pt-3 border-t border-obsidian-border space-y-3">
                    <div class="flex items-baseline justify-between">
                        <div>
                            <span class="text-lg font-black font-mono text-cyber-gold" x-text="Number(product.price).toLocaleString()"></span>
                            <span class="text-[10px] font-cairo text-slate-400">ج.م</span>
                        </div>
                        <span x-show="product.old_price" class="text-xs font-mono text-slate-500 line-through" x-text="Number(product.old_price).toLocaleString() + ' EGP'"></span>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <button type="button"
                                @click="openSpecsModal(product)"
                                class="py-2 px-3 rounded-xl bg-obsidian-surface hover:bg-obsidian-card text-slate-300 hover:text-white border border-obsidian-border text-xs font-cairo font-bold transition-all text-center cursor-pointer">
                            المواصفات ⚙️
                        </button>

                        <button type="button"
                                @click="addToCart(product)"
                                :disabled="product.stock_qty <= 0"
                                class="py-2 px-3 rounded-xl btn-citadel-primary text-xs font-cairo font-bold transition-all text-center shadow-neon-cyan active:scale-95 disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer">
                            أضف للسلة 🛒
                        </button>
                    </div>
                </div>

            </div>
        </template>
    </div>

    {{-- Empty State --}}
    <div x-show="filteredProducts.length === 0" class="text-center py-16 space-y-3">
        <span class="text-4xl block">🔍</span>
        <h3 class="text-base font-bold text-white font-cairo">لم يتم العثور على أجهزة مطابقة للبحث</h3>
        <p class="text-xs text-slate-400 font-cairo">جرّب البحث بكلمة أخرى أو اختر تصنيفاً مختلفاً من القائمة أعلاه.</p>
        <button type="button" @click="selectedCategory = 'all'; searchQuery = ''" class="px-4 py-2 rounded-xl btn-citadel-primary text-xs font-cairo font-bold cursor-pointer">
            <span x-text="'عرض كافة الأجهزة (' + products.length + ')'"></span>
        </button>
    </div>

    {{-- 4. TECHNICAL SPECS MODAL --}}
    <div x-show="specsModalOpen" 
         x-transition.opacity 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
         style="display: none;">
        <div class="relative w-full max-w-lg glass-obsidian rounded-3xl border border-cyber-cyan/50 p-6 sm:p-8 space-y-6 shadow-2xl max-h-[90vh] overflow-y-auto"
             @click.away="specsModalOpen = false">
            
            <div class="flex items-center justify-between border-b border-obsidian-border pb-4">
                <div class="space-y-1">
                    <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-slate-800 text-cyber-cyan" x-text="activeProduct?.sku"></span>
                    <h3 class="text-base font-black text-white font-cairo" x-text="activeProduct?.name_ar"></h3>
                </div>
                <button type="button" @click="specsModalOpen = false" class="text-slate-400 hover:text-white text-lg cursor-pointer">✕</button>
            </div>

            <div class="space-y-2 text-xs font-cairo">
                <div class="p-3 rounded-xl bg-cyber-cyan/10 border border-cyber-cyan/30 flex items-center justify-between">
                    <span class="text-cyber-cyan font-bold">التصنيف والاعتماد:</span>
                    <span class="font-bold text-white" x-text="Number(activeProduct?.compatible_with_branderp) === 1 ? 'متوافق 100% مع BrandERP ✓' : 'معتمد للصيانة والترقيات الهندسية ✓'"></span>
                </div>
                <div class="p-3 rounded-xl bg-obsidian-surface border border-obsidian-border flex items-center justify-between">
                    <span class="text-slate-400">العلامة التجارية والمصنع:</span>
                    <span class="font-bold text-white font-mono" x-text="activeProduct?.brand"></span>
                </div>
                <div class="p-3 rounded-xl bg-obsidian-surface border border-obsidian-border flex items-center justify-between">
                    <span class="text-slate-400">مدة الضمان المعتمد:</span>
                    <span class="font-bold text-emerald-400 font-mono" x-text="activeProduct?.warranty_months + ' شهراً'"></span>
                </div>

                <div class="space-y-1.5 pt-2">
                    <span class="text-[11px] font-bold text-slate-400 block mb-1">المواصفات التقنية التفصيلية:</span>
                    <template x-for="(val, key) in getParsedSpecs(activeProduct)" :key="key">
                        <div class="p-2.5 rounded-lg bg-obsidian-surface/60 border border-obsidian-border flex items-center justify-between gap-3 text-[11px]">
                            <span class="text-slate-300 font-bold" x-text="key"></span>
                            <span class="text-white font-mono text-left font-bold" x-text="val"></span>
                        </div>
                    </template>
                </div>
            </div>

            <div class="pt-2 border-t border-obsidian-border flex items-center justify-between">
                <div>
                    <span class="text-xs text-slate-400 block font-cairo">السعر المعتمد:</span>
                    <span class="text-xl font-black font-mono text-cyber-gold" x-text="Number(activeProduct?.price || 0).toLocaleString() + ' EGP'"></span>
                </div>
                <button type="button" 
                        @click="addToCart(activeProduct); specsModalOpen = false" 
                        class="px-5 py-2.5 rounded-xl btn-citadel-primary text-xs font-cairo font-bold shadow-neon-cyan cursor-pointer">
                    إضافة للسلة وشراء 🛒
                </button>
            </div>
        </div>
    </div>

    {{-- 5. SHOPPING CART DRAWER --}}
    <div x-show="cartDrawerOpen" 
         x-transition.opacity 
         class="fixed inset-0 z-50 flex items-center justify-end bg-black/70 backdrop-blur-sm"
         style="display: none;">
        <div class="relative w-full max-w-md h-full glass-obsidian border-r border-cyber-cyan/40 p-6 flex flex-col justify-between gap-6 overflow-y-auto"
             @click.away="cartDrawerOpen = false">
            
            <div class="space-y-3">
                <div class="flex items-center justify-between border-b border-obsidian-border pb-4">
                    <div class="flex items-center gap-2">
                        <span class="text-2xl">🛒</span>
                        <h3 class="text-lg font-black text-white font-cairo">سلة أجهزة الهاردوير</h3>
                    </div>
                    <button type="button" @click="cartDrawerOpen = false" class="text-slate-400 hover:text-white text-lg cursor-pointer">✕</button>
                </div>

                <div class="space-y-3">
                    <template x-for="item in cart" :key="item.id">
                        <div class="p-3.5 rounded-2xl bg-obsidian-surface border border-obsidian-border flex items-center justify-between gap-3 text-xs font-cairo">
                            <div class="space-y-1 flex-1">
                                <h4 class="font-bold text-white text-[11px]" x-text="item.name_ar"></h4>
                                <span class="text-cyber-gold font-mono font-bold" x-text="Number(item.price).toLocaleString() + ' EGP'"></span>
                            </div>

                            <div class="flex items-center gap-2">
                                <button type="button" @click="updateCartQty(item.id, -1)" class="w-6 h-6 rounded bg-slate-800 text-slate-300 hover:text-white font-bold flex items-center justify-center cursor-pointer">-</button>
                                <span class="font-mono font-bold text-white text-xs" x-text="item.quantity"></span>
                                <button type="button" @click="updateCartQty(item.id, 1)" class="w-6 h-6 rounded bg-slate-800 text-slate-300 hover:text-white font-bold flex items-center justify-center cursor-pointer">+</button>
                                <button type="button" @click="removeFromCart(item.id)" class="text-rose-400 hover:text-rose-300 text-xs pr-1 cursor-pointer">✕</button>
                            </div>
                        </div>
                    </template>

                    <div x-show="cart.length === 0" class="text-center py-10 space-y-2 text-slate-400 text-xs font-cairo">
                        <span>سلة الهاردوير فارغة حالياً.</span>
                    </div>
                </div>
            </div>

            <div class="space-y-4 pt-4 border-t border-obsidian-border" x-show="cart.length > 0">
                <div class="space-y-1.5 text-xs font-cairo">
                    <div class="flex items-center justify-between text-slate-300">
                        <span>إجمالي الأجهزة:</span>
                        <span class="font-mono font-bold text-white" x-text="cartTotalPrice.toLocaleString() + ' EGP'"></span>
                    </div>
                    <div class="flex items-center justify-between text-slate-400 text-[11px]">
                        <span>الضمان والفحص:</span>
                        <span class="text-emerald-400 font-bold">مفحوص معملياً بالضمان الرسمي</span>
                    </div>
                </div>

                <button type="button"
                        @click="cartDrawerOpen = false; checkoutModalOpen = true"
                        class="w-full py-3 rounded-xl btn-citadel-primary text-xs font-cairo font-bold shadow-neon-cyan text-center block cursor-pointer">
                    متابعة عملية الشراء وتأكيد الطلب ⇥
                </button>
            </div>

        </div>
    </div>

    {{-- 6. CHECKOUT MODAL --}}
    <div x-show="checkoutModalOpen" 
         x-transition.opacity 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
         style="display: none;">
        <div class="relative w-full max-w-lg glass-obsidian rounded-3xl border border-cyber-cyan/50 p-6 sm:p-8 space-y-6 shadow-2xl max-h-[90vh] overflow-y-auto"
             @click.away="checkoutModalOpen = false">
            
            <div class="flex items-center justify-between border-b border-obsidian-border pb-3">
                <div class="space-y-1">
                    <h3 class="text-base font-black text-white font-cairo">تأكيد حجز وشراء أجهزة الهاردوير 📦</h3>
                    <span class="text-xs font-mono text-cyber-cyan font-bold" x-text="'الإجمالي: ' + cartTotalPrice.toLocaleString() + ' EGP'"></span>
                </div>
                <button type="button" @click="checkoutModalOpen = false" class="text-slate-400 hover:text-white text-lg cursor-pointer">✕</button>
            </div>

            <div x-show="checkoutError" class="p-3 rounded-xl bg-rose-500/15 border border-rose-500/40 text-rose-300 text-xs font-cairo" x-text="checkoutError"></div>

            <form @submit.prevent="submitCheckoutOrder()" class="space-y-4 text-xs font-cairo">
                <div class="space-y-1">
                    <label class="text-slate-300 font-bold">اسم المستلم أو اسم المؤسسة *</label>
                    <input type="text" x-model="checkoutForm.customer_name" required placeholder="أ/ أحمد إبراهيم — شركة الأمل" class="w-full bg-obsidian-surface text-slate-200 rounded-xl px-4 py-2.5 border border-obsidian-border focus:border-cyber-cyan focus:outline-none">
                </div>

                <div class="space-y-1">
                    <label class="text-slate-300 font-bold">رقم الهاتف (واتساب للتنسيق والشحن) *</label>
                    <input type="text" x-model="checkoutForm.customer_phone" required placeholder="01xxxxxxxxx" class="w-full bg-obsidian-surface text-slate-200 rounded-xl px-4 py-2.5 border border-obsidian-border focus:border-cyber-cyan focus:outline-none">
                </div>

                <div class="space-y-1">
                    <label class="text-slate-300 font-bold">عنوان الشحن والتوصيل بالتفصيل *</label>
                    <textarea x-model="checkoutForm.shipping_address" rows="2" required placeholder="المحافظة، المدينة، اسم الشارع، رقم العقار أو الفرع..." class="w-full bg-obsidian-surface text-slate-200 rounded-xl p-3 border border-obsidian-border focus:border-cyber-cyan focus:outline-none"></textarea>
                </div>

                <div class="space-y-2 pt-1">
                    <label class="text-slate-300 font-bold block">طريقة السداد المعتمدة *</label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                        <label class="p-3 rounded-xl border cursor-pointer transition-all flex flex-col justify-between"
                               :class="checkoutForm.payment_method === 'wallet' ? 'bg-cyber-cyan/15 border-cyber-cyan text-white' : 'bg-obsidian-surface border-obsidian-border text-slate-300'">
                            <input type="radio" x-model="checkoutForm.payment_method" value="wallet" class="hidden">
                            <span class="font-bold block">المحفظة الرقمية</span>
                            <span class="text-[10px] text-emerald-400 font-mono block mt-1" x-text="'رصيدك: ' + userWallet.toLocaleString() + ' EGP'"></span>
                        </label>

                        <label class="p-3 rounded-xl border cursor-pointer transition-all flex flex-col justify-between"
                               :class="checkoutForm.payment_method === 'transfer' ? 'bg-cyber-cyan/15 border-cyber-cyan text-white' : 'bg-obsidian-surface border-obsidian-border text-slate-300'">
                            <input type="radio" x-model="checkoutForm.payment_method" value="transfer" class="hidden">
                            <span class="font-bold block">تحويل بنكي / فودافون كاش</span>
                            <span class="text-[10px] text-slate-400 block mt-1">تأكيد مباشر مع الإدارة</span>
                        </label>

                        <label class="p-3 rounded-xl border cursor-pointer transition-all flex flex-col justify-between"
                               :class="checkoutForm.payment_method === 'cod_deposit' ? 'bg-cyber-cyan/15 border-cyber-cyan text-white' : 'bg-obsidian-surface border-obsidian-border text-slate-300'">
                            <input type="radio" x-model="checkoutForm.payment_method" value="cod_deposit" class="hidden">
                            <span class="font-bold block">دفع عند الاستلام</span>
                            <span class="text-[10px] text-slate-400 block mt-1">مع عربون شحن</span>
                        </label>
                    </div>
                </div>

                <div class="pt-3 border-t border-obsidian-border flex items-center justify-between gap-2">
                    <span class="text-[11px] text-slate-400">سيتم تسجيل الأرقام التسلسلية (Serial Numbers) في وثيقة الضمان الرسمية.</span>
                    <button type="submit" 
                            :disabled="submittingCheckout" 
                            class="px-6 py-2.5 rounded-xl btn-citadel-primary text-xs font-cairo font-bold shadow-neon-cyan disabled:opacity-50 shrink-0 cursor-pointer">
                        <span x-text="submittingCheckout ? 'جاري التأكيد...' : 'تأكيد الطلب الآن 🚀'"></span>
                    </button>
                </div>
            </form>

        </div>
    </div>

    {{-- FLOATING MOBILE HARDWARE CART BAR --}}
    <div class="fixed bottom-16 inset-x-0 z-40 px-3 py-2 pointer-events-none md:hidden"
         x-show="cartTotalCount > 0"
         x-transition>
        <div class="max-w-md mx-auto pointer-events-auto bg-[#070914]/95 backdrop-blur-2xl border border-cyber-cyan/40 rounded-2xl p-2.5 shadow-[0_-5px_25px_rgba(0,0,0,0.85)] flex items-center justify-between gap-3">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="relative p-2 rounded-xl bg-cyber-cyan/15 border border-cyber-cyan/30 text-cyber-cyan text-base shrink-0">
                    🛒
                    <span class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-cyber-cyan text-obsidian-base font-black text-[9px] flex items-center justify-center font-mono"
                          x-text="cartTotalCount"></span>
                </div>
                <div class="min-w-0">
                    <span class="text-[10px] text-slate-400 font-cairo block">إجمالي أجهزة الهاردوير:</span>
                    <div class="flex items-baseline gap-1">
                        <span class="text-sm font-black font-mono text-transparent bg-clip-text bg-gradient-to-r from-cyber-cyan to-cyber-gold"
                              x-text="cartTotalPrice.toLocaleString()"></span>
                        <span class="text-[9px] text-slate-400 font-mono">EGP</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-1.5 shrink-0">
                <button type="button"
                        @click="cartDrawerOpen = true"
                        class="px-3.5 py-2 rounded-xl btn-citadel-primary text-xs font-black font-cairo shadow-neon-cyan flex items-center gap-1.5 active:scale-95 transition-all">
                    <span>مراجعة السلة</span>
                    <span>⚡</span>
                </button>
            </div>
        </div>
    </div>

</div>

<script>
function hardwareStore(config) {
    return {
        products: config.products || [],
        userWallet: Number(config.userWallet || 0),
        isAuth: config.isAuth || false,
        selectedCategory: 'all',
        searchQuery: '',
        cart: [],
        cartDrawerOpen: false,
        specsModalOpen: false,
        activeProduct: null,
        checkoutModalOpen: false,
        submittingCheckout: false,
        checkoutError: '',
        checkoutForm: {
            customer_name: config.userName || '',
            customer_phone: config.userPhone || '',
            shipping_address: '',
            payment_method: (config.isAuth && Number(config.userWallet) > 0) ? 'wallet' : 'cod_deposit',
            notes: ''
        },

        get filteredProducts() {
            return this.products.filter(p => {
                let matchesCat = (this.selectedCategory === 'all') || (p.category === this.selectedCategory);
                if (this.selectedCategory === 'networking_servers' && p.category === 'networking_security') {
                    matchesCat = true;
                }
                const query = this.searchQuery.trim().toLowerCase();
                if (!query) return matchesCat;
                const matchesSearch = (p.name_ar && p.name_ar.toLowerCase().includes(query))
                    || (p.brand && p.brand.toLowerCase().includes(query))
                    || (p.sku && p.sku.toLowerCase().includes(query))
                    || (p.description_ar && p.description_ar.toLowerCase().includes(query));
                return matchesCat && matchesSearch;
            });
        },

        get cartTotalCount() {
            return this.cart.reduce((sum, item) => sum + item.quantity, 0);
        },

        get cartTotalPrice() {
            return this.cart.reduce((sum, item) => sum + (Number(item.price) * item.quantity), 0);
        },

        getParsedSpecs(product) {
            if (!product || !product.specs_json) return {};
            if (typeof product.specs_json === 'object') return product.specs_json;
            try {
                return JSON.parse(product.specs_json);
            } catch (e) {
                return { 'المواصفات': product.specs_json };
            }
        },

        addToCart(product) {
            if (!product) return;
            const existing = this.cart.find(i => i.id === product.id);
            if (existing) {
                existing.quantity += 1;
            } else {
                this.cart.push({
                    id: product.id,
                    sku: product.sku,
                    name_ar: product.name_ar,
                    price: Number(product.price),
                    quantity: 1
                });
            }
            this.cartDrawerOpen = true;
        },

        updateCartQty(id, delta) {
            const item = this.cart.find(i => i.id === id);
            if (!item) return;
            item.quantity += delta;
            if (item.quantity <= 0) {
                this.removeFromCart(id);
            }
        },

        removeFromCart(id) {
            this.cart = this.cart.filter(i => i.id !== id);
        },

        openSpecsModal(product) {
            this.activeProduct = product;
            this.specsModalOpen = true;
        },

        async submitCheckoutOrder() {
            this.checkoutError = '';
            if (this.cart.length === 0) {
                this.checkoutError = 'سلة المشتريات فارغة.';
                return;
            }

            this.submittingCheckout = true;
            try {
                const res = await fetch('{{ route("store.hardware.checkout") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        customer_name: this.checkoutForm.customer_name,
                        customer_phone: this.checkoutForm.customer_phone,
                        shipping_address: this.checkoutForm.shipping_address,
                        payment_method: this.checkoutForm.payment_method,
                        notes: this.checkoutForm.notes,
                        items: this.cart.map(i => ({ id: i.id, quantity: i.quantity }))
                    })
                });

                const data = await res.json();
                this.submittingCheckout = false;

                if (res.ok && data.success) {
                    alert(data.message);
                    this.cart = [];
                    this.checkoutModalOpen = false;
                    if (data.redirect_url) {
                        window.location.href = data.redirect_url;
                    } else {
                        window.location.reload();
                    }
                } else {
                    this.checkoutError = data.message || 'يرجى التأكد من البيانات المدخلة والمحاولة مرة أخرى.';
                }
            } catch (e) {
                this.submittingCheckout = false;
                this.checkoutError = 'تعذر الاتصال بالخادم، يرجى التحقق من اتصال الإنترنت والمحاولة مجدداً.';
            }
        }
    };
}
</script>
@endsection