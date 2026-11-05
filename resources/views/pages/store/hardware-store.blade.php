@extends('layouts.master')

@section('title', 'متجر الهاردوير وتجهيزات الأمن السيبراني | سولي')
@section('meta_description', 'تسوق أجهزة نقاط البيع POS، السيرفرات، بطاقات التخزين، ومعدات التدريع والأمن السيبراني المعتمدة مع ضمان رسمي من سولي.')

@section('layout-body')
<div class="relative z-10 py-10 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto" x-data="hardwareStorePage()">

    <!-- Hero Header -->
    <div class="mb-10 text-center sm:text-right flex flex-col sm:flex-row sm:items-center justify-between gap-6 border-b border-obsidian-border pb-8">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyber-cyan/10 border border-cyber-cyan/30 text-cyber-cyan text-xs font-bold mb-3">
                <span class="w-2 h-2 rounded-full bg-cyber-cyan animate-pulse"></span>
                <span>عتاد معتمد ومفحوص هندسياً</span>
            </div>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">
                متجر العتاد وتجهيزات <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyber-cyan to-cyber-purple">الأمن والـ POS</span>
            </h1>
            <p class="mt-2 text-sm text-slate-400 max-w-2xl">
                أجهزة نقاط البيع، خوادم الشبكات، وسائط التخزين، وحلول التدريع السيبراني الميداني مع ضمان سولي الرسمي وخدمات التركيب الفوري.
            </p>
        </div>

        <!-- Quick Cart Trigger Button -->
        <div class="flex items-center justify-center sm:justify-end gap-3">
            <button @click="cartDrawerOpen = true" 
                    type="button"
                    class="btn-citadel-primary px-5 py-3 rounded-2xl relative flex items-center gap-2.5 font-bold shadow-neon-cyan">
                <svg class="w-5 h-5 text-obsidian-base" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                <span>سلة العتاد</span>
                <span x-show="cartCount > 0" 
                      x-text="cartCount"
                      class="w-5 h-5 rounded-full bg-obsidian-base text-cyber-cyan text-xs flex items-center justify-center font-mono font-black">
                </span>
            </button>
        </div>
    </div>

    <!-- Filters & Search Toolbar -->
    <div class="mb-8 flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
        <!-- Category Filter Pills -->
        <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none">
            <a href="{{ route('store.hardware') }}" 
               class="px-4 py-2 rounded-xl text-xs font-bold transition-all shrink-0 {{ !request('category') ? 'bg-cyber-cyan text-obsidian-base shadow-neon-cyan' : 'bg-obsidian-surface border border-obsidian-border text-slate-300 hover:text-white' }}">
                جميع التجهيزات
            </a>
            @foreach($categories as $cat)
                @php
                    $catSlug = is_array($cat) ? $cat['slug'] : $cat->slug;
                    $catName = is_array($cat) ? $cat['name_ar'] : $cat->name_ar;
                    $isActive = request('category') === $catSlug;
                @endphp
                <a href="{{ route('store.hardware', ['category' => $catSlug]) }}" 
                   class="px-4 py-2 rounded-xl text-xs font-bold transition-all shrink-0 {{ $isActive ? 'bg-cyber-cyan text-obsidian-base shadow-neon-cyan' : 'bg-obsidian-surface border border-obsidian-border text-slate-300 hover:text-white' }}">
                    {{ $catName }}
                </a>
            @endforeach
        </div>

        <!-- Search Input -->
        <form action="{{ route('store.hardware') }}" method="GET" class="relative min-w-[260px]">
            @if(request('category'))
                <input type="hidden" name="category" value="{{ request('category') }}">
            @endif
            <input type="text" 
                   name="search" 
                   value="{{ request('search') }}"
                   placeholder="ابحث باسم الجهاز أو الموديل..."
                   class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-4 py-2.5 pr-10 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-cyber-cyan transition-colors">
            <button type="submit" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-cyber-cyan">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </button>
        </form>
    </div>

    <!-- Product Grid -->
    @if($products->isEmpty())
        <div class="glass-obsidian rounded-3xl p-12 text-center max-w-lg mx-auto my-12 border border-obsidian-border">
            <div class="w-16 h-16 rounded-2xl bg-cyber-cyan/10 border border-cyber-cyan/30 text-cyber-cyan flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-white mb-2">لا توجد أجهزة مطابقة حالياً</h3>
            <p class="text-xs text-slate-400 mb-6">جرّب اختيار تصنيف آخر أو تصفح كامل الكتالوج المعتمد.</p>
            <a href="{{ route('store.hardware') }}" class="btn-citadel-secondary px-6 py-2.5 rounded-xl text-xs font-bold inline-block">
                عرض كافة التجهيزات
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($products as $product)
                @php
                    $stock = $product->stock_quantity ?? $product->stock_qty ?? 0;
                    $isAvailable = $stock > 0;
                @endphp
                <div class="glass-obsidian rounded-2xl p-4 border border-obsidian-border hover:border-cyber-cyan/40 transition-all duration-300 flex flex-col justify-between group">
                    <div>
                        <!-- Product Image Box -->
                        <div class="relative w-full h-44 rounded-xl bg-obsidian-card border border-obsidian-border overflow-hidden mb-4 flex items-center justify-center">
                            @if(!empty($product->image_url))
                                <img src="{{ asset($product->image_url) }}" alt="{{ $product->name_ar }}" class="object-cover w-full h-full group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="text-cyber-cyan/40">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif

                            <!-- Stock Badge -->
                            <div class="absolute top-2.5 right-2.5">
                                @if($isAvailable)
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                                        متوفر ({{ $stock }})
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-500/10 text-rose-400 border border-rose-500/30">
                                        نفد المخزون
                                    </span>
                                @endif
                            </div>

                            @if(!empty($product->warranty_months))
                                <div class="absolute bottom-2.5 right-2.5">
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-cyber-gold/10 text-cyber-gold border border-cyber-gold/30">
                                        ضمان {{ $product->warranty_months }} شهر
                                    </span>
                                </div>
                            @endif
                        </div>

                        <!-- Details -->
                        <div class="space-y-1.5 mb-3">
                            <span class="text-[11px] font-mono text-slate-400 block">{{ $product->model_number ?? 'MOD-STD' }}</span>
                            <h3 class="font-extrabold text-sm text-white group-hover:text-cyber-cyan transition-colors line-clamp-1">
                                {{ $product->name_ar }}
                            </h3>
                            <p class="text-xs text-slate-400 line-clamp-2 leading-relaxed">
                                {{ $product->description_ar ?? 'جهاز عالي الأداء معتمد للربط مع شبكات ومنظومات الأعمال.' }}
                            </p>
                        </div>
                    </div>

                    <!-- Price & Action -->
                    <div class="pt-3 border-t border-obsidian-border flex items-center justify-between gap-2">
                        <div>
                            <span class="text-xs text-slate-400 block">السعر المعتمد</span>
                            <span class="text-base font-black text-cyber-cyan font-mono">{{ number_format($product->price, 2) }}</span>
                            <span class="text-[10px] text-slate-400">ج.م</span>
                        </div>

                        <button @click="addToCart({{ $product->id }}, '{{ addslashes($product->name_ar) }}', {{ (float)$product->price }}, {{ $stock }})"
                                type="button"
                                :disabled="{{ $isAvailable ? 'false' : 'true' }}"
                                class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 {{ $isAvailable ? 'btn-citadel-primary' : 'bg-obsidian-card text-slate-600 cursor-not-allowed' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>إضافة للسلة</span>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-10">
            {{ $products->links() }}
        </div>
    @endif

    <!-- Cart Drawer Modal -->
    <div x-cloak 
         x-show="cartDrawerOpen" 
         class="fixed inset-0 z-50 overflow-hidden" 
         aria-labelledby="slide-over-title" 
         role="dialog" 
         aria-modal="true">
        <div class="absolute inset-0 bg-black/80 backdrop-blur-sm transition-opacity" @click="cartDrawerOpen = false"></div>

        <div class="pointer-events-none fixed inset-y-0 left-0 flex max-w-full pr-10">
            <div class="pointer-events-auto w-screen max-w-md glass-obsidian-heavy border-r border-obsidian-border p-6 flex flex-col justify-between shadow-2xl">
                <div>
                    <!-- Drawer Header -->
                    <div class="flex items-center justify-between pb-4 border-b border-obsidian-border">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-cyber-cyan animate-pulse"></span>
                            <h2 class="text-lg font-extrabold text-white">سلة مشتريات العتاد</h2>
                        </div>
                        <button @click="cartDrawerOpen = false" class="text-slate-400 hover:text-white p-1">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Items List -->
                    <div class="mt-6 divide-y divide-obsidian-border max-h-[50vh] overflow-y-auto scrollbar-none">
                        <template x-if="cart.length === 0">
                            <div class="py-12 text-center text-slate-400 text-xs">
                                السلة فارغة حالياً. أضف بعض المنتجات للبدء.
                            </div>
                        </template>

                        <template x-for="(item, idx) in cart" :key="item.id">
                            <div class="py-3 flex items-center justify-between gap-3">
                                <div class="flex-1">
                                    <h4 class="text-xs font-bold text-white" x-text="item.name"></h4>
                                    <span class="text-[11px] font-mono text-cyber-cyan" x-text="item.price.toFixed(2) + ' ج.م'"></span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button @click="decreaseQty(idx)" class="w-6 h-6 rounded bg-obsidian-surface text-slate-300 flex items-center justify-center font-bold text-xs">-</button>
                                    <span class="font-mono text-xs text-white" x-text="item.quantity"></span>
                                    <button @click="increaseQty(idx)" class="w-6 h-6 rounded bg-obsidian-surface text-slate-300 flex items-center justify-center font-bold text-xs">+</button>
                                    <button @click="removeFromCart(idx)" class="text-rose-400 hover:text-rose-300 mr-2 text-xs">حذف</button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Checkout Form & Totals -->
                <div class="pt-6 border-t border-obsidian-border space-y-4">
                    <div class="space-y-1 text-xs">
                        <div class="flex justify-between text-slate-400">
                            <span>المجموع الفرعي:</span>
                            <span class="font-mono text-white" x-text="subtotal.toFixed(2) + ' ج.م'"></span>
                        </div>
                        <div class="flex justify-between text-slate-400">
                            <span>الشحن والتوصيل:</span>
                            <span class="font-mono text-white">50.00 ج.م</span>
                        </div>
                        <div class="flex justify-between text-sm font-bold text-white pt-2 border-t border-obsidian-border">
                            <span>الإجمالي المستحق:</span>
                            <span class="font-mono text-cyber-cyan" x-text="(subtotal + (subtotal > 0 ? 50 : 0)).toFixed(2) + ' ج.م'"></span>
                        </div>
                    </div>

                    <button @click="showCheckoutModal = true; cartDrawerOpen = false"
                            :disabled="cart.length === 0"
                            class="w-full py-3 rounded-xl btn-citadel-primary text-xs font-black shadow-neon-cyan disabled:opacity-50 disabled:cursor-not-allowed">
                        متابعة إتمام الطلب والدفع ←
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Final Checkout Modal -->
    <div x-cloak x-show="showCheckoutModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-md">
        <div class="glass-obsidian-heavy rounded-3xl border border-cyber-cyan/40 p-6 max-w-lg w-full space-y-4 max-h-[90vh] overflow-y-auto shadow-neon-cyan">
            <div class="flex items-center justify-between border-b border-obsidian-border pb-3">
                <h3 class="text-base font-extrabold text-white">بيانات شحن وإتمام طلب الهاردوير</h3>
                <button @click="showCheckoutModal = false" class="text-slate-400 hover:text-white">✕</button>
            </div>

            <form @submit.prevent="submitCheckout" class="space-y-3.5 text-xs">
                @guest
                <div>
                    <label class="block text-slate-300 mb-1">الاسم الكامل *</label>
                    <input type="text" x-model="checkoutForm.name" required class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl p-2.5 text-white">
                </div>
                <div>
                    <label class="block text-slate-300 mb-1">رقم الموبايل (المصري) *</label>
                    <input type="tel" x-model="checkoutForm.phone" required placeholder="01xxxxxxxxx" class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl p-2.5 text-white font-mono">
                </div>
                @endguest

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-slate-300 mb-1">المدينة / المحافظة *</label>
                        <input type="text" x-model="checkoutForm.city" required placeholder="القاهرة، الجيزة..." class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl p-2.5 text-white">
                    </div>
                    <div>
                        <label class="block text-slate-300 mb-1">كود الخصم (إن وجد)</label>
                        <input type="text" x-model="checkoutForm.promo_code" placeholder="PROMO2026" class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl p-2.5 text-white uppercase font-mono">
                    </div>
                </div>

                <div>
                    <label class="block text-slate-300 mb-1">عنوان التوصيل بالتفصيل *</label>
                    <input type="text" x-model="checkoutForm.shipping_address" required placeholder="اسم الشارع، رقم المبنى، علامة مميزة" class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl p-2.5 text-white">
                </div>

                <div>
                    <label class="block text-slate-300 mb-1">طريقة الدفع المعتمدة *</label>
                    <select x-model="checkoutForm.payment_method" class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl p-2.5 text-white">
                        <option value="cash_on_delivery">الدفع نقداً عند الاستلام (COD)</option>
                        <option value="wallet">خصم فوري من رصيد محفظة سولي</option>
                        <option value="card">بطاقة بنكية / فيزا / ماستركارد</option>
                        <option value="bank_transfer">تحويل بنكي / إنستاباي</option>
                    </select>
                </div>

                <div>
                    <label class="block text-slate-300 mb-1">ملاحظات تسليم خاصة</label>
                    <textarea x-model="checkoutForm.notes" rows="2" class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl p-2.5 text-white" placeholder="أي تعليمات خاصة بفحص الأجهزة..."></textarea>
                </div>

                <div x-show="errorMessage" class="p-3 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-400 text-xs" x-text="errorMessage"></div>

                <button type="submit" 
                        :disabled="isSubmitting"
                        class="w-full py-3 rounded-xl btn-citadel-primary font-black text-xs shadow-neon-cyan disabled:opacity-50">
                    <span x-show="!isSubmitting">تأكيد الطلب وإصدار الفاتورة ⚡</span>
                    <span x-show="isSubmitting">جارٍ معالجة وتأمين الطلب...</span>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function hardwareStorePage() {
    return {
        cartDrawerOpen: false,
        showCheckoutModal: false,
        isSubmitting: false,
        errorMessage: '',
        cart: JSON.parse(localStorage.getItem('souly_hw_cart') || '[]'),
        checkoutForm: {
            name: '',
            phone: '',
            city: '',
            shipping_address: '',
            promo_code: '',
            payment_method: 'cash_on_delivery',
            notes: ''
        },

        get cartCount() {
            return this.cart.reduce((total, item) => total + item.quantity, 0);
        },

        get subtotal() {
            return this.cart.reduce((total, item) => total + (item.price * item.quantity), 0);
        },

        saveCart() {
            localStorage.setItem('souly_hw_cart', JSON.stringify(this.cart));
        },

        addToCart(id, name, price, maxStock) {
            const existing = this.cart.find(item => item.id === id);
            if (existing) {
                if (existing.quantity < maxStock) {
                    existing.quantity++;
                } else {
                    if (window.Alpine && window.Alpine.store('citadel')) {
                        window.Alpine.store('citadel').showToast('تم بلوغ الحد الأقصى المتوفر في المخزن');
                    }
                    return;
                }
            } else {
                this.cart.push({ id, name, price, quantity: 1, maxStock });
            }
            this.saveCart();
            if (window.Alpine && window.Alpine.store('citadel')) {
                window.Alpine.store('citadel').showToast('تمت إضافة ' + name + ' إلى السلة ✓');
            }
        },

        increaseQty(idx) {
            if (this.cart[idx].quantity < this.cart[idx].maxStock) {
                this.cart[idx].quantity++;
                this.saveCart();
            }
        },

        decreaseQty(idx) {
            if (this.cart[idx].quantity > 1) {
                this.cart[idx].quantity--;
            } else {
                this.cart.splice(idx, 1);
            }
            this.saveCart();
        },

        removeFromCart(idx) {
            this.cart.splice(idx, 1);
            this.saveCart();
        },

        async submitCheckout() {
            this.isSubmitting = true;
            this.errorMessage = '';

            const payload = {
                items: this.cart.map(item => ({ id: item.id, quantity: item.quantity })),
                name: this.checkoutForm.name,
                phone: this.checkoutForm.phone,
                city: this.checkoutForm.city,
                shipping_address: this.checkoutForm.shipping_address,
                promo_code: this.checkoutForm.promo_code,
                payment_method: this.checkoutForm.payment_method,
                notes: this.checkoutForm.notes
            };

            try {
                const res = await fetch("{{ route('store.hardware.checkout') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await res.json();

                if (res.ok && data.success) {
                    this.cart = [];
                    this.saveCart();
                    this.showCheckoutModal = false;
                    if (window.Alpine && window.Alpine.store('citadel')) {
                        window.Alpine.store('citadel').showToast(data.message || 'تم تسجيل الطلب بنجاح');
                    }
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    }
                } else {
                    this.errorMessage = data.message || 'حدث خطأ أثناء معالجة الطلب، يرجى مراجعة البيانات.';
                }
            } catch (err) {
                this.errorMessage = 'تعذر الاتصال بالخادم، يرجى المحاولة لاحقاً.';
            } finally {
                this.isSubmitting = false;
            }
        }
    };
}
</script>
@endsection
