@extends('layouts.master')

@section('title', 'متجر العتاد والتدريع التكنولوجي المعتمد | سولي')
@section('meta_description', 'أجهزة نقاط البيع POS، سيرفرات وتجهيزات الشبكات، بطاقات التخزين العالية، وأجهزة الحماية والتدريع السيبراني للشركات في مصر.')

@section('layout-body')
<div class="relative min-h-screen pb-24 pt-4" 
     x-data="soulyMobileHardwareStore()" 
     x-init="initStore()">

    <!-- 1. شريط علوي للهاتف مع زر الرجوع ومؤشر السلة -->
    <div class="px-4 mb-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('home') }}" class="w-10 h-10 rounded-xl bg-obsidian-card border border-obsidian-border flex items-center justify-center text-slate-300 hover:text-cyber-cyan active:scale-95 transition-all">
                <svg class="w-5 h-5 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h1 class="text-lg font-black text-white font-cairo flex items-center gap-2">
                    متجر الهاردوير <span class="text-xs px-2 py-0.5 rounded-full bg-cyber-cyan/10 border border-cyber-cyan/30 text-cyber-cyan font-mono">SOULY-HW</span>
                </h1>
                <p class="text-[11px] text-slate-400 font-cairo">عتاد هندسي معتمد مع ضمان رقمي وفاتورة فورية</p>
            </div>
        </div>

        <!-- زر السلة العلوي -->
        <button type="button" 
                @click="cartDrawerOpen = true"
                class="relative w-10 h-10 rounded-xl bg-obsidian-card border border-cyber-cyan/40 flex items-center justify-center text-cyber-cyan shadow-neon-cyan active:scale-95 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
            <span x-show="totalCartCount > 0" 
                  x-text="totalCartCount" 
                  class="absolute -top-1.5 -right-1.5 w-5 h-5 rounded-full bg-cyber-crimson text-white text-[10px] font-mono font-bold flex items-center justify-center border border-obsidian-base"></span>
        </button>
    </div>

    <!-- 2. حقل البحث السريع للموبايل -->
    <div class="px-4 mb-4">
        <form method="GET" action="{{ route('store.hardware') }}" class="relative">
            <input type="text" 
                   name="search" 
                   value="{{ request('search') }}"
                   placeholder="ابحث عن اسم الجهاز، الموديل، أو كود القطعة..." 
                   class="w-full bg-obsidian-card/90 border border-obsidian-border text-white text-xs rounded-2xl py-3 pr-10 pl-10 focus:border-cyber-cyan focus:outline-none transition-all placeholder:text-slate-500 font-cairo">
            <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-slate-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            @if(request('search'))
                <a href="{{ route('store.hardware') }}" class="absolute inset-y-0 left-3 flex items-center text-slate-400 hover:text-white">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </a>
            @endif
        </form>
    </div>

    <!-- 3. شريط أقسام العتاد بالسحب باللمس (Touch Horizontal Chips) -->
    <div class="mb-5 overflow-x-auto scrollbar-none px-4 flex items-center gap-2 snap-x">
        <a href="{{ route('store.hardware') }}" 
           class="snap-start shrink-0 px-3.5 py-2 rounded-xl text-xs font-bold font-cairo border transition-all {{ !request('category') ? 'bg-cyber-cyan text-obsidian-base border-cyber-cyan shadow-neon-cyan' : 'bg-obsidian-card text-slate-300 border-obsidian-border hover:border-slate-600' }}">
            الكل ({{ $products->total() }})
        </a>

        @foreach($categories as $cat)
            @php
                $slug = is_array($cat) ? ($cat['slug'] ?? '') : ($cat->slug ?? '');
                $nameAr = is_array($cat) ? ($cat['name_ar'] ?? '') : ($cat->name_ar ?? '');
                $isSelected = request('category') === $slug;
            @endphp
            <a href="{{ route('store.hardware', ['category' => $slug]) }}" 
               class="snap-start shrink-0 px-3.5 py-2 rounded-xl text-xs font-bold font-cairo border transition-all {{ $isSelected ? 'bg-cyber-cyan text-obsidian-base border-cyber-cyan shadow-neon-cyan' : 'bg-obsidian-card text-slate-300 border-obsidian-border hover:border-slate-600' }}">
                {{ $nameAr }}
            </a>
        @endforeach
    </div>

    <!-- 4. شبكة المنتجات المخصصة للموبايل (2 Columns Grid) -->
    <div class="px-4">
        @if($products->isEmpty())
            <div class="p-8 text-center bg-obsidian-card border border-obsidian-border rounded-3xl my-8 space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-cyber-cyan/10 border border-cyber-cyan/30 text-cyber-cyan flex items-center justify-center mx-auto text-xl font-mono">∅</div>
                <h3 class="text-sm font-bold text-white font-cairo">لا توجد أجهزة متطابقة حالياً</h3>
                <p class="text-xs text-slate-400 font-cairo">جرب البحث بكلمة أخرى أو اختر قسماً آخر من شريط الأقسام أعلاه.</p>
                <a href="{{ route('store.hardware') }}" class="inline-block px-4 py-2 rounded-xl btn-souly-outline text-xs font-cairo mt-2">عرض كل المنتجات</a>
            </div>
        @else
            <div class="grid grid-cols-2 gap-3 sm:gap-4">
                @foreach($products as $product)
                    @php
                        $inStock = ($product->stock_quantity ?? $product->stock_qty ?? 0) > 0;
                        $price = (float)($product->price ?? 0);
                        $discountPrice = !empty($product->discount_price) ? (float)$product->discount_price : null;
                        $finalPrice = $discountPrice ?: $price;
                    @endphp
                    <div class="bg-obsidian-card/95 border border-obsidian-border hover:border-cyber-cyan/40 rounded-2xl p-2.5 flex flex-col justify-between transition-all relative overflow-hidden group shadow-lg">
                        
                        <!-- شارة التوفر أو الخصم -->
                        <div class="absolute top-2 right-2 z-10 flex flex-col gap-1">
                            @if(!$inStock)
                                <span class="px-1.5 py-0.5 rounded-md bg-cyber-crimson/90 text-white font-cairo text-[9px] font-bold">نفد من المخزن</span>
                            @elseif($discountPrice)
                                <span class="px-1.5 py-0.5 rounded-md bg-cyber-gold text-obsidian-base font-mono text-[9px] font-black">خصم ⚡</span>
                            @endif
                        </div>

                        <!-- صورة المنتج -->
                        <div class="relative w-full aspect-square rounded-xl bg-obsidian-surface border border-obsidian-border/50 overflow-hidden mb-2 flex items-center justify-center">
                            @if(!empty($product->image_url))
                                <img src="{{ $product->image_url }}" alt="{{ $product->name_ar }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="w-10 h-10 rounded-xl bg-cyber-cyan/10 border border-cyber-cyan/20 text-cyber-cyan flex items-center justify-center font-mono text-xs">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                                    </svg>
                                </div>
                            @endif
                        </div>

                        <!-- تفاصيل المنتج -->
                        <div class="space-y-1">
                            <span class="text-[10px] text-cyber-cyan font-mono block truncate">{{ $product->model_number ?? 'CERTIFIED-HW' }}</span>
                            <h3 class="text-xs font-bold text-white font-cairo line-clamp-2 leading-snug">{{ $product->name_ar }}</h3>
                            
                            <div class="pt-1 flex items-baseline gap-1.5">
                                <span class="text-sm font-black text-cyber-cyan font-mono">{{ number_format($finalPrice, 2) }}</span>
                                <span class="text-[10px] text-slate-400 font-cairo">ج.م</span>
                                @if($discountPrice)
                                    <span class="text-[10px] text-slate-500 line-through font-mono">{{ number_format($price, 0) }}</span>
                                @endif
                            </div>
                        </div>

                        <!-- زر الإضافة للموبايل -->
                        <div class="mt-2.5 pt-2 border-t border-obsidian-border/60">
                            @if($inStock)
                                <button type="button"
                                        @click="addToCart({
                                            id: {{ $product->id }},
                                            name: @js($product->name_ar),
                                            price: {{ $finalPrice }},
                                            model: @js($product->model_number ?? ''),
                                            warranty: {{ $product->warranty_months ?? 12 }},
                                            max: {{ $product->stock_quantity ?? $product->stock_qty ?? 10 }}
                                        })"
                                        class="w-full py-2 rounded-xl btn-citadel-primary text-xs font-bold font-cairo flex items-center justify-center gap-1 active:scale-95 transition-all">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    <span>إضافة للسلة</span>
                                </button>
                            @else
                                <button type="button" disabled class="w-full py-1.5 rounded-xl bg-obsidian-border/50 text-slate-500 text-[11px] font-cairo cursor-not-allowed">
                                    غير متوفر
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- ترقيم الصفحات -->
            <div class="mt-6">
                {{ $products->links() }}
            </div>
        @endif
    </div>

    <!-- 5. زر عائم ملتصق بأسفل الشاشة (Sticky Bottom Cart Floating Pill) -->
    <div x-cloak 
         x-show="totalCartCount > 0"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-10"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-10"
         class="fixed bottom-4 inset-x-4 z-40">
        <button type="button" 
                @click="cartDrawerOpen = true"
                class="w-full py-3.5 px-4 rounded-2xl bg-gradient-to-r from-cyber-cyan via-teal-400 to-cyber-cyan text-obsidian-base font-black font-cairo text-sm shadow-neon-cyan flex items-center justify-between active:scale-98 transition-all">
            <div class="flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-obsidian-base text-cyber-cyan text-xs flex items-center justify-center font-mono font-bold" x-text="totalCartCount"></span>
                <span>عرض سلة الأجهزة</span>
            </div>
            <div class="flex items-center gap-1 font-mono text-base">
                <span x-text="formatCurrency(cartSubtotal)"></span>
                <span class="text-xs font-cairo">ج.م ←</span>
            </div>
        </button>
    </div>

    <!-- 6. نافذة السلة السفلية المنبثقة (Mobile Bottom Sheet Modal) -->
    <div x-cloak
         x-show="cartDrawerOpen"
         class="fixed inset-0 z-50 flex items-end justify-center bg-black/80 backdrop-blur-sm"
         @click.self="cartDrawerOpen = false">
        
        <div x-show="cartDrawerOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="translate-y-full"
             x-transition:enter-end="translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="translate-y-0"
             x-transition:leave-end="translate-y-full"
             class="w-full max-w-lg bg-obsidian-card border-t border-cyber-cyan/40 rounded-t-3xl max-h-[85vh] flex flex-col shadow-neon-cyan">
            
            <!-- هيدر السلة ومقبض السحب -->
            <div class="p-4 border-b border-obsidian-border flex items-center justify-between relative">
                <div class="w-12 h-1 rounded-full bg-slate-600 absolute top-2 left-1/2 -translate-x-1/2"></div>
                <div class="flex items-center gap-2 pt-1">
                    <span class="text-base font-black text-white font-cairo">سلة المشتريات</span>
                    <span class="text-xs px-2 py-0.5 rounded-full bg-cyber-cyan/10 text-cyber-cyan font-mono" x-text="`${totalCartCount} أجهزة`"></span>
                </div>
                <button type="button" @click="cartDrawerOpen = false" class="text-slate-400 hover:text-white p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- محتويات السلة -->
            <div class="p-4 overflow-y-auto space-y-3 flex-1 scrollbar-none">
                <template x-if="cartItems.length === 0">
                    <div class="py-10 text-center text-slate-400 space-y-2">
                        <div class="text-3xl">🛒</div>
                        <p class="text-xs font-cairo">سلة المشتريات فارغة حتى الآن.</p>
                    </div>
                </template>

                <template x-for="(item, index) in cartItems" :key="item.id">
                    <div class="p-3 rounded-2xl bg-obsidian-surface border border-obsidian-border flex items-center justify-between gap-3">
                        <div class="space-y-0.5 flex-1 min-w-0">
                            <h4 class="text-xs font-bold text-white font-cairo truncate" x-text="item.name"></h4>
                            <p class="text-[10px] text-slate-400 font-mono" x-text="`${formatCurrency(item.price)} ج.م`"></p>
                        </div>
                        
                        <!-- أزرار زيادة ونقص الكمية -->
                        <div class="flex items-center gap-2 bg-obsidian-card border border-obsidian-border rounded-xl p-1">
                            <button type="button" @click="decreaseQty(index)" class="w-6 h-6 rounded-lg bg-obsidian-surface text-slate-300 font-bold flex items-center justify-center active:scale-95">-</button>
                            <span class="text-xs font-mono font-bold text-cyber-cyan px-1" x-text="item.quantity"></span>
                            <button type="button" @click="increaseQty(index)" class="w-6 h-6 rounded-lg bg-obsidian-surface text-slate-300 font-bold flex items-center justify-center active:scale-95">+</button>
                        </div>

                        <!-- حذف العنصر -->
                        <button type="button" @click="removeItem(index)" class="text-slate-500 hover:text-cyber-crimson p-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </template>
            </div>

            <!-- أسفل السلة وزر الانتقال للدفع -->
            <div class="p-4 border-t border-obsidian-border bg-obsidian-surface/60 space-y-3" x-show="cartItems.length > 0">
                <div class="flex items-center justify-between text-xs font-cairo text-slate-300">
                    <span>إجمالي الأجهزة:</span>
                    <span class="font-mono font-bold text-white" x-text="`${formatCurrency(cartSubtotal)} ج.م`"></span>
                </div>
                <div class="flex items-center justify-between text-xs font-cairo text-slate-300">
                    <span>مصاريف الشحن والتأمين:</span>
                    <span class="font-mono font-bold text-cyber-cyan">50.00 ج.م</span>
                </div>
                <div class="flex items-center justify-between text-sm font-black font-cairo text-white pt-2 border-t border-obsidian-border">
                    <span>المبلغ المستحق:</span>
                    <span class="font-mono text-base text-cyber-cyan" x-text="`${formatCurrency(cartSubtotal + 50)} ج.م`"></span>
                </div>

                <button type="button" 
                        @click="cartDrawerOpen = false; checkoutModalOpen = true"
                        class="w-full py-3.5 rounded-2xl btn-citadel-primary text-sm font-bold font-cairo shadow-neon-cyan flex items-center justify-center gap-2 active:scale-95">
                    <span>متابعة إتمام الطلب والدفع</span>
                    <span>←</span>
                </button>
            </div>
        </div>
    </div>

    <!-- 7. نافذة إتمام الطلب والدفع (Mobile Checkout Modal) -->
    <div x-cloak
         x-show="checkoutModalOpen"
         class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 bg-black/85 backdrop-blur-sm"
         @click.self="checkoutModalOpen = false">
        
        <div class="w-full max-w-lg bg-obsidian-card border border-cyber-cyan/40 rounded-t-3xl sm:rounded-3xl p-5 space-y-4 max-h-[90vh] overflow-y-auto scrollbar-none shadow-neon-cyan">
            
            <div class="flex items-center justify-between pb-3 border-b border-obsidian-border">
                <h3 class="text-sm font-extrabold text-white font-cairo flex items-center gap-2">
                    <span>تسجيل الطلب والعنوان</span>
                    <span class="text-xs text-cyber-cyan font-mono" x-text="`(${formatCurrency(cartSubtotal + 50)} ج.م)`"></span>
                </h3>
                <button type="button" @click="checkoutModalOpen = false" class="text-slate-400 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- فورم الدفع وتسجيل البيانات -->
            <form @submit.prevent="submitCheckout()" class="space-y-3 font-cairo text-xs text-right">
                
                @guest
                    <div>
                        <label class="block text-slate-300 font-bold mb-1">الاسم بالكامل *</label>
                        <input type="text" x-model="formData.name" required placeholder="أدخل اسم المستلم" 
                               class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-3 py-2.5 text-white focus:border-cyber-cyan focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-slate-300 font-bold mb-1">رقم الموبايل (واتساب) *</label>
                        <input type="tel" x-model="formData.phone" required placeholder="01xxxxxxxxx" dir="ltr"
                               class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-3 py-2.5 text-white focus:border-cyber-cyan focus:outline-none text-right">
                    </div>
                @endguest

                <div>
                    <label class="block text-slate-300 font-bold mb-1">المحافظة / المدينة *</label>
                    <input type="text" x-model="formData.city" required placeholder="مثال: القاهرة - التجمع الخامس" 
                           class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-3 py-2.5 text-white focus:border-cyber-cyan focus:outline-none">
                </div>

                <div>
                    <label class="block text-slate-300 font-bold mb-1">العنوان التفصيلي للاستلام *</label>
                    <textarea x-model="formData.shipping_address" required rows="2" placeholder="الشارع، رقم المبنى، علامة مميزة..." 
                              class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-3 py-2 text-white focus:border-cyber-cyan focus:outline-none"></textarea>
                </div>

                <!-- طريقة الدفع -->
                <div>
                    <label class="block text-slate-300 font-bold mb-1.5">طريقة السداد المعتمدة *</label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="p-2.5 rounded-xl border flex items-center gap-2 cursor-pointer transition-all"
                               :class="formData.payment_method === 'cash_on_delivery' ? 'bg-cyber-cyan/15 border-cyber-cyan text-white' : 'bg-obsidian-surface border-obsidian-border text-slate-400'">
                            <input type="radio" value="cash_on_delivery" x-model="formData.payment_method" class="hidden">
                            <span class="text-sm">💵</span>
                            <span class="text-[11px] font-bold">الدفع عند الاستلام</span>
                        </label>

                        <label class="p-2.5 rounded-xl border flex items-center gap-2 cursor-pointer transition-all"
                               :class="formData.payment_method === 'wallet' ? 'bg-cyber-cyan/15 border-cyber-cyan text-white' : 'bg-obsidian-surface border-obsidian-border text-slate-400'">
                            <input type="radio" value="wallet" x-model="formData.payment_method" class="hidden">
                            <span class="text-sm">⚡</span>
                            <span class="text-[11px] font-bold">محفظة سولي</span>
                        </label>
                    </div>
                </div>

                <!-- زر تأكيد الطلب الفوري -->
                <div class="pt-3">
                    <button type="submit" 
                            :disabled="isSubmitting"
                            class="w-full py-3.5 rounded-2xl btn-citadel-primary text-sm font-black font-cairo shadow-neon-cyan flex items-center justify-center gap-2 active:scale-95 disabled:opacity-50">
                        <span x-show="!isSubmitting">تأكيد حجز وشحن الطلب الآن 🚀</span>
                        <span x-show="isSubmitting">جارٍ معالجة الطلب...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- محرك المتجر والتخزين المحلي المخصص للموبايل -->
<script>
function soulyMobileHardwareStore() {
    return {
        cartItems: [],
        cartDrawerOpen: false,
        checkoutModalOpen: false,
        isSubmitting: false,
        formData: {
            name: '',
            phone: '',
            city: '',
            shipping_address: '',
            payment_method: 'cash_on_delivery',
            promo_code: ''
        },
        initStore() {
            try {
                const saved = localStorage.getItem('souly_hw_cart');
                if (saved) {
                    this.cartItems = JSON.parse(saved);
                }
            } catch (e) {
                this.cartItems = [];
            }
        },
        saveCart() {
            try {
                localStorage.setItem('souly_hw_cart', JSON.stringify(this.cartItems));
            } catch (e) {}
        },
        get totalCartCount() {
            return this.cartItems.reduce((sum, item) => sum + item.quantity, 0);
        },
        get cartSubtotal() {
            return this.cartItems.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        },
        formatCurrency(num) {
            return Number(num || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },
        addToCart(product) {
            const existing = this.cartItems.find(i => i.id === product.id);
            if (existing) {
                existing.quantity++;
            } else {
                this.cartItems.push({
                    id: product.id,
                    name: product.name,
                    price: product.price,
                    quantity: 1
                });
            }
            this.saveCart();
            if (window.Alpine && window.Alpine.store('citadel')) {
                window.Alpine.store('citadel').showToast(`تمت إضافة (${product.name}) إلى السلة ✓`);
            }
        },
        increaseQty(index) {
            this.cartItems[index].quantity++;
            this.saveCart();
        },
        decreaseQty(index) {
            if (this.cartItems[index].quantity > 1) {
                this.cartItems[index].quantity--;
            } else {
                this.removeItem(index);
            }
            this.saveCart();
        },
        removeItem(index) {
            this.cartItems.splice(index, 1);
            this.saveCart();
        },
        async submitCheckout() {
            if (this.cartItems.length === 0) return;
            this.isSubmitting = true;

            const payload = {
                items: this.cartItems.map(i => ({ id: i.id, quantity: i.quantity })),
                name: this.formData.name,
                phone: this.formData.phone,
                city: this.formData.city,
                shipping_address: this.formData.shipping_address,
                payment_method: this.formData.payment_method,
                promo_code: this.formData.promo_code
            };

            try {
                const res = await fetch("{{ route('store.hardware.checkout') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await res.json();
                if (res.ok && data.success) {
                    this.cartItems = [];
                    this.saveCart();
                    this.checkoutModalOpen = false;
                    alert(data.message || 'تم تسجيل طلبك بنجاح!');
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        window.location.reload();
                    }
                } else {
                    alert(data.message || 'حدث خطأ أثناء معالجة الطلب.');
                }
            } catch (err) {
                alert('تعذر إرسال الطلب، يرجى التحقق من اتصال الإنترنت.');
            } finally {
                this.isSubmitting = false;
            }
        }
    };
}
</script>
@endsection