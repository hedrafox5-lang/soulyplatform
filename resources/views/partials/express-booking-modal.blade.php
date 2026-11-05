<!-- ===================================================================== -->
<!-- SOULY 1-TAP EXPRESS BOOKING MODAL (STRICTLY CLOSED BY DEFAULT)        -->
<!-- ===================================================================== -->
<div x-data="expressBookingModalController()" 
     x-cloak
     @open-express-booking.window="openModal($event.detail)"
     @close-express-booking.window="closeModal()"
     @keydown.escape.window="if(isOpen) closeModal()"
     class="relative z-[99998]">

    <!-- Modal Backdrop -->
    <div x-show="isOpen" 
         x-cloak
         style="display: none;"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click.stop="closeModal()"
         class="fixed inset-0 bg-black/85 backdrop-blur-md z-[99998] cursor-pointer"></div>

    <!-- Modal Container: Slide-Up Bottom Sheet on Mobile / Centered Card on Desktop -->
    <div x-show="isOpen"
         x-cloak
         x-ref="bookingSheet"
         style="display: none;"
         x-transition:enter="transition ease-out duration-250"
         x-transition:enter-start="opacity-0 translate-y-full sm:translate-y-6 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-full sm:translate-y-6 sm:scale-95"
         class="fixed inset-x-0 bottom-0 sm:bottom-auto sm:top-1/2 sm:left-1/2 sm:-translate-x-1/2 sm:-translate-y-1/2 z-[99999] w-full max-w-lg bg-[#0B0C16]/98 backdrop-blur-2xl border-t sm:border border-cyber-cyan/40 rounded-t-3xl sm:rounded-3xl shadow-[0_-12px_45px_rgba(0,0,0,0.95)] max-h-[88dvh] sm:max-h-[90vh] flex flex-col overflow-hidden"
         @click.stop
         dir="rtl">

        <!-- Mobile Drag Handle -->
        <div class="w-12 h-1.5 bg-slate-600 rounded-full mx-auto my-2.5 sm:hidden shrink-0" @click="closeModal()"></div>

        <!-- Sticky Header -->
        <div class="flex items-center justify-between border-b border-white/10 px-5 py-3.5 bg-[#070812] shrink-0">
            <div class="flex items-center gap-2.5">
                <span class="w-2.5 h-2.5 rounded-full bg-cyber-cyan animate-pulse"></span>
                <div>
                    <h3 class="text-sm sm:text-base font-black text-white font-cairo">
                        حجز سريع فوري · 1-Tap Express
                    </h3>
                    <span class="text-[10px] text-slate-400 font-cairo block">تأكيد فوري في أقل من 30 ثانية</span>
                </div>
            </div>
            
            <button type="button" 
                    @click.stop="closeModal()" 
                    class="w-8 h-8 rounded-xl bg-[#121422] hover:bg-rose-500/20 text-slate-400 hover:text-rose-400 border border-white/10 flex items-center justify-center transition-all cursor-pointer">
                ✕
            </button>
        </div>

        <!-- Scrollable Form Body -->
        <div class="flex-1 overflow-y-auto overscroll-contain p-5 space-y-4">
            
            <!-- Target Service & Package Summary Banner -->
            <div class="p-3.5 rounded-2xl bg-gradient-to-r from-[#121422] via-[#0B0C16] to-[#121422] border border-cyber-cyan/30 flex items-center justify-between gap-3 text-right">
                <div class="space-y-0.5">
                    <span class="text-[10px] font-cairo font-bold text-cyber-cyan uppercase block" x-text="serviceName || 'منظومة سولي السيادية'"></span>
                    <h4 class="text-xs sm:text-sm font-extrabold text-white font-cairo" x-text="packageName || 'طلب استشارة وحجز فوري'"></h4>
                </div>

                <div class="text-left font-mono shrink-0">
                    <span class="text-base sm:text-lg font-black text-transparent bg-clip-text bg-gradient-to-r from-cyber-cyan to-cyber-gold" 
                          x-text="`${Math.floor(price).toLocaleString()} EGP`"></span>
                    <span class="text-[10px] text-slate-400 block font-cairo" x-text="hours > 1 ? `لـ ${hours} ساعات` : 'شاملة الضمان'"></span>
                </div>
            </div>

            <!-- Form Stage -->
            <form id="express-booking-form" @submit.prevent="submitBooking()" class="space-y-4">
                
                @guest
                    <div class="space-y-3">
                        <div class="space-y-1 text-right">
                            <label class="block text-xs font-bold text-slate-200 font-cairo">
                                الاسم بالكامل (أو اسم الشركة):
                            </label>
                            <div class="relative">
                                <input type="text" 
                                       x-model="formData.name" 
                                       @input="saveDraft()"
                                       required
                                       placeholder="مثال: م. أحمد محمد"
                                       class="w-full bg-[#121422] border text-xs text-white rounded-xl p-3 focus:outline-none font-cairo transition-colors"
                                       :class="formData.name ? (isNameValid ? 'border-emerald-500/60 focus:border-emerald-400' : 'border-rose-500/60 focus:border-rose-400') : 'border-white/10 focus:border-cyber-cyan'">
                                <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-xs" x-show="formData.name">
                                    <span x-show="isNameValid" class="text-emerald-400 font-bold">✓</span>
                                    <span x-show="!isNameValid" class="text-rose-400 font-bold">✕</span>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-1 text-right">
                            <label class="block text-xs font-bold text-slate-200 font-cairo">
                                رقم الموبايل والواتساب (مصري 11 رقم):
                            </label>
                            <div class="relative">
                                <input type="tel" 
                                       x-model="formData.phone" 
                                       @input="saveDraft()"
                                       required
                                       placeholder="01012345678"
                                       class="w-full bg-[#121422] border text-xs text-white rounded-xl p-3 focus:outline-none font-mono text-left transition-colors"
                                       :class="formData.phone ? (isPhoneValid ? 'border-emerald-500/60 focus:border-emerald-400' : 'border-rose-500/60 focus:border-rose-400') : 'border-white/10 focus:border-cyber-cyan'">
                                <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-xs" x-show="formData.phone">
                                    <span x-show="isPhoneValid" class="text-emerald-400 font-bold">✓</span>
                                    <span x-show="!isPhoneValid" class="text-rose-400 font-bold">✕</span>
                                </div>
                            </div>
                            <span class="text-[10px] text-slate-400 font-cairo block">
                                * يتم فتح حسابك وتفعيل شهادة الضمان آلياً برقم هاتفك.
                            </span>
                        </div>
                    </div>
                @else
                    @php
                        $authWallet = auth()->user()->walletOrCreate();
                    @endphp
                    <div class="p-3.5 rounded-2xl bg-[#121422] border border-white/10 space-y-1.5">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-cairo text-slate-300 font-bold">رصيد محفظتك الرقمية:</span>
                            <span class="text-sm font-mono font-bold text-cyber-cyan">
                                {{ number_format($authWallet->balance, 2) }} {{ $authWallet->currency }}
                            </span>
                        </div>
                        <template x-if="{{ $authWallet->balance }} >= price">
                            <div class="text-[11px] font-cairo text-emerald-400 flex items-center gap-1.5">
                                <span>✓</span>
                                <span>رصيدك يغطي التكلفة بالكامل مع تأكيد فوري!</span>
                            </div>
                        </template>
                    </div>
                @endguest

                <!-- Payment Method Tiles -->
                <div class="space-y-2 text-right">
                    <label class="block text-xs font-bold text-slate-300 font-cairo">
                        اختر وسيلة الدفع المناسبة:
                    </label>

                    <div class="grid grid-cols-2 gap-2 text-xs font-cairo">
                        @auth
                            <button type="button" 
                                    @click="formData.payment_method = 'wallet'"
                                    :disabled="{{ $authWallet->balance }} < price"
                                    class="p-2.5 rounded-xl border text-right transition-all flex flex-col justify-between gap-1 cursor-pointer"
                                    :class="formData.payment_method === 'wallet' ? 'bg-cyber-cyan/15 border-cyber-cyan text-white' : ({{ $authWallet->balance }} < price ? 'opacity-40 cursor-not-allowed bg-[#05050A] border-white/5 text-slate-500' : 'bg-[#121422] border-white/10 text-slate-300')">
                                <span class="font-bold flex items-center gap-1.5">
                                    <span>⚡</span>
                                    <span>محفظة سولي</span>
                                </span>
                                <span class="text-[10px] text-cyber-cyan font-mono">تأكيد لحظي 0ms</span>
                            </button>
                        @endauth

                        <button type="button" 
                                @click="formData.payment_method = 'instapay'"
                                class="p-2.5 rounded-xl border text-right transition-all flex flex-col justify-between gap-1 cursor-pointer"
                                :class="formData.payment_method === 'instapay' ? 'bg-cyber-cyan/15 border-cyber-cyan text-white' : 'bg-[#121422] border-white/10 text-slate-300'">
                            <span class="font-bold flex items-center gap-1.5">
                                <span>🟣</span>
                                <span>إنستاباي (InstaPay)</span>
                            </span>
                            <span class="text-[10px] text-slate-400 font-mono">souly@instapay</span>
                        </button>

                        <button type="button" 
                                @click="formData.payment_method = 'vodafone_cash'"
                                class="p-2.5 rounded-xl border text-right transition-all flex flex-col justify-between gap-1 cursor-pointer"
                                :class="formData.payment_method === 'vodafone_cash' ? 'bg-cyber-cyan/15 border-cyber-cyan text-white' : 'bg-[#121422] border-white/10 text-slate-300'">
                            <span class="font-bold flex items-center gap-1.5">
                                <span>🔴</span>
                                <span>فودافون كاش</span>
                            </span>
                            <span class="text-[10px] text-slate-400 font-mono">01090623326</span>
                        </button>

                        <button type="button" 
                                @click="formData.payment_method = 'on_site'"
                                class="p-2.5 rounded-xl border text-right transition-all flex flex-col justify-between gap-1 cursor-pointer"
                                :class="formData.payment_method === 'on_site' ? 'bg-cyber-cyan/15 border-cyber-cyan text-white' : 'bg-[#121422] border-white/10 text-slate-300'">
                            <span class="font-bold flex items-center gap-1.5">
                                <span>🤝</span>
                                <span>عند التنفيذ</span>
                            </span>
                            <span class="text-[10px] text-slate-400 font-cairo">بمقرك أو بعد الاستلام</span>
                        </button>
                    </div>
                </div>

                <!-- Optional Notes -->
                <div class="space-y-1 text-right">
                    <label class="block text-xs font-bold text-slate-300 font-cairo">
                        ملاحظات أو الموعد المفضل (اختياري):
                    </label>
                    <textarea x-model="formData.notes" 
                              rows="2"
                              placeholder="اكتب أي متطلبات خاصة أو الموعد الأنسب..."
                              class="w-full bg-[#121422] border border-white/10 text-xs text-white rounded-xl p-2.5 focus:border-cyber-cyan focus:outline-none font-cairo"></textarea>
                </div>

                <!-- Error message container -->
                <div x-show="errorMessage" x-text="errorMessage" class="p-3 rounded-xl bg-rose-950/60 border border-rose-500/40 text-rose-300 text-xs font-cairo"></div>
            </form>

        </div>

        <!-- Sticky Safe Bottom Submit Action -->
        <div class="sticky bottom-0 inset-x-0 bg-[#070812] border-t border-white/10 p-4 shrink-0 safe-bottom flex items-center gap-2.5">
            <button type="button"
                    @click.stop="closeModal()"
                    class="px-4 py-3.5 rounded-xl bg-[#121422] border border-white/10 text-slate-300 hover:text-white text-xs font-cairo font-bold cursor-pointer">
                إلغاء
            </button>

            <button type="submit" 
                    form="express-booking-form"
                    :disabled="isSubmitting"
                    class="flex-1 py-3.5 rounded-xl btn-citadel-primary text-xs sm:text-sm font-cairo font-black flex items-center justify-center gap-2 shadow-neon-cyan active:scale-95 transition-all cursor-pointer">
                <span x-show="!isSubmitting">تأكيد الحجز الفوري الآن ⚡</span>
                <span x-show="isSubmitting" class="flex items-center gap-2">
                    <span>جاري تسجيل الحجز...</span>
                </span>
            </button>
        </div>

    </div>

</div>

<script>
    function expressBookingModalController() {
        return {
            isOpen: false,
            isSubmitting: false,
            errorMessage: '',

            serviceId: null,
            serviceSlug: 'brand-erp',
            serviceName: 'نظام BrandERP المتكامل',
            packageId: null,
            packageSlug: 'starter',
            packageName: 'باقة الانطلاق / استشارة فورية',
            price: 2000,
            hours: 1,

            formData: {
                name: '',
                phone: '',
                payment_method: '{{ auth()->check() && auth()->user()->wallet?->balance > 0 ? "wallet" : "instapay" }}',
                notes: ''
            },

            get isPhoneValid() {
                return /^01[0125][0-9]{8}$/.test((this.formData.phone || '').trim());
            },

            get isNameValid() {
                return (this.formData.name || '').trim().length >= 3;
            },

            init() {
                // Strictly force closed state on page load
                this.isOpen = false;

                try {
                    const savedDraft = sessionStorage.getItem('souly_express_draft');
                    if (savedDraft) {
                        const parsed = JSON.parse(savedDraft);
                        if (parsed.name && !this.formData.name) this.formData.name = parsed.name;
                        if (parsed.phone && !this.formData.phone) this.formData.phone = parsed.phone;
                    }
                } catch(e) {}

                if (!this.formData.name && window.SoulyIdentity) {
                    const identName = window.SoulyIdentity.getName();
                    if (identName) this.formData.name = identName;
                }
            },

            saveDraft() {
                try {
                    sessionStorage.setItem('souly_express_draft', JSON.stringify({
                        name: this.formData.name,
                        phone: this.formData.phone
                    }));
                } catch(e) {}
            },

            openModal(detail) {
                if (detail && typeof detail === 'object') {
                    this.serviceId = detail.serviceId || null;
                    this.serviceSlug = detail.serviceSlug || detail.service || 'brand-erp';
                    this.serviceName = detail.serviceName || 'منظومة سولي السيادية';
                    this.packageId = detail.packageId || null;
                    this.packageSlug = detail.packageSlug || detail.package || 'standard';
                    this.packageName = detail.packageName || 'باقة معتمدة';
                    this.price = parseFloat(detail.price) || 2000;
                    this.hours = parseInt(detail.hours) || 1;
                    if (detail.notes) this.formData.notes = detail.notes;
                }
                this.errorMessage = '';
                this.isOpen = true;
                if (window.SoulyUI) window.SoulyUI.lockScroll('express-booking');
            },

            closeModal() {
                this.isOpen = false;
                if (window.SoulyUI) window.SoulyUI.unlockScroll('express-booking');
                this.$nextTick(() => {
                    if (this.$refs.bookingSheet) {
                        this.$refs.bookingSheet.style.setProperty('display', 'none', 'important');
                    }
                });
            },

            async submitBooking() {
                @guest
                    if (!this.isNameValid) {
                        this.errorMessage = 'يرجى كتابة الاسم بالكامل (3 أحرف على الأقل).';
                        return;
                    }
                    if (!this.isPhoneValid) {
                        this.errorMessage = 'يرجى إدخال رقم موبايل مصري صحيح (11 رقماً يبدأ بـ 01).';
                        return;
                    }
                @endguest

                this.isSubmitting = true;
                this.errorMessage = '';

                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    const payload = {
                        service_id: this.serviceId,
                        service_slug: this.serviceSlug,
                        package_id: this.packageId,
                        package_slug: this.packageSlug,
                        quantity_or_hours: this.hours,
                        payment_method: this.formData.payment_method,
                        notes: this.formData.notes,
                        name: this.formData.name,
                        phone: this.formData.phone
                    };

                    const response = await fetch('{{ route("express-book.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify(payload)
                    });

                    const data = await response.json();

                    if (!response.ok || !data.success) {
                        this.errorMessage = data.message || 'حدث خطأ أثناء تسجيل الحجز، يرجى مراجعة البيانات.';
                        this.isSubmitting = false;
                        return;
                    }

                    try { sessionStorage.removeItem('souly_express_draft'); } catch(e) {}
                    if (this.formData.name && window.SoulyIdentity) {
                        window.SoulyIdentity.setName(this.formData.name);
                    }

                    if (data.redirect_url) {
                        window.location.href = data.redirect_url;
                    } else {
                        window.location.reload();
                    }

                } catch (err) {
                    this.errorMessage = 'تعذر الاتصال بالخادم، يرجى المحاولة مرة أخرى.';
                    this.isSubmitting = false;
                }
            }
        };
    }
</script>