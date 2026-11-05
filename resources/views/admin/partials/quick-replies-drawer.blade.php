<!-- ===================================================================== -->
<!-- FLOATING STAFF QUICK-REPLY VAULT DRAWER & INLINE CMS                  -->
<!-- ===================================================================== -->
<div x-data="quickRepliesDrawer()" 
     x-init="initDrawer()"
     @open-canned-drawer.window="isOpen = true"
     @keydown.window.alt.slash="toggleDrawer()"
     class="relative z-50">

    <!-- Floating Trigger Pill Button (Bottom Left on Desktop, Mobile Safe) -->
    <div class="fixed bottom-6 left-6 z-40">
        <button type="button" 
                @click="toggleDrawer()"
                title="خزينة الردود السريعة (Alt + /)"
                class="group relative flex items-center gap-2.5 px-4 py-3 rounded-2xl bg-gradient-to-r from-obsidian-surface via-obsidian-card to-obsidian-surface border border-cyber-gold/50 shadow-neon-gold hover:scale-105 active:scale-95 transition-all">
            <!-- Pulsing Icon -->
            <div class="relative w-7 h-7 rounded-xl bg-cyber-gold/20 text-cyber-gold border border-cyber-gold/40 flex items-center justify-center font-black text-xs">
                ⚡
                <span class="absolute -top-1 -right-1 w-2 h-2 bg-cyber-gold rounded-full animate-ping"></span>
            </div>

            <div class="text-right">
                <div class="flex items-center gap-1.5">
                    <span class="text-xs font-bold text-white font-cairo">الردود السريعة</span>
                    <span class="px-1.5 py-0.2 rounded text-[9px] font-mono bg-cyber-gold/20 text-cyber-gold font-bold">VAULT</span>
                </div>
                <div class="text-[9px] text-slate-400 font-mono">Alt + /</div>
            </div>
        </button>
    </div>

    <!-- Backdrop Overlay -->
    <div x-show="isOpen" 
         x-transition:enter="transition-opacity ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="isOpen = false"
         class="fixed inset-0 bg-obsidian-base/80 backdrop-blur-sm z-50"
         style="display: none;"></div>

    <!-- Slide-Over Drawer Viewport -->
    <div x-show="isOpen"
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full"
         class="fixed inset-y-0 right-0 w-full sm:w-[480px] lg:w-[540px] bg-obsidian-card border-l border-cyber-gold/30 shadow-[0_0_50px_rgba(0,0,0,0.9)] z-50 flex flex-col justify-between overflow-hidden"
         style="display: none;">

        <!-- Drawer Header Strip -->
        <div class="p-5 border-b border-obsidian-border bg-obsidian-surface/90 space-y-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-cyber-gold/20 text-cyber-gold border border-cyber-gold/40 flex items-center justify-center font-black text-base shadow-sm">
                        ⚡
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-sm font-black text-white font-cairo">خزينة الردود الذكية الفورية</h2>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-cyber-gold/20 text-cyber-gold">
                                <span x-text="filteredResponses.length"></span> رد جاهز
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-400 font-cairo">إدراج لحظي في تذاكر الدعم والـ CRM بنقرة زر واحدة</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" 
                            @click="showCreateForm = !showCreateForm"
                            class="px-3 py-1.5 rounded-xl bg-obsidian-base hover:bg-slate-800 text-cyber-cyan border border-cyber-cyan/30 text-xs font-cairo font-bold transition-all flex items-center gap-1">
                        <span x-text="showCreateForm ? 'إلغاء' : '+ إضافة رد'"></span>
                    </button>

                    <button type="button" @click="isOpen = false" class="p-2 text-slate-400 hover:text-white rounded-xl hover:bg-obsidian-border transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Fast Search & Filter Bar -->
            <div class="relative">
                <input type="text" 
                       x-model="searchQuery" 
                       placeholder="ابحث بالاسم، الكود المختصر (/erp, /price, /cyber) أو الكلمات الدلالية..." 
                       class="w-full bg-obsidian-base border border-obsidian-border rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-500 font-cairo focus:border-cyber-gold focus:outline-none transition-colors">
                <div class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-xs">
                    🔍
                </div>
            </div>

            <!-- Categories Tabs -->
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-[11px] font-cairo font-bold scrollbar-none">
                <button type="button" 
                        @click="selectedCategory = 'all'"
                        :class="selectedCategory === 'all' ? 'bg-cyber-gold text-obsidian-base font-black' : 'bg-obsidian-base text-slate-400 hover:text-white border border-obsidian-border'"
                        class="px-3 py-1.5 rounded-xl transition-all shrink-0">
                    الكل (<span x-text="responses.length"></span>)
                </button>
                <template x-for="cat in categories" :key="cat.id">
                    <button type="button" 
                            @click="selectedCategory = cat.id"
                            :class="selectedCategory === cat.id ? 'bg-cyber-gold text-obsidian-base font-black' : 'bg-obsidian-base text-slate-400 hover:text-white border border-obsidian-border'"
                            class="px-3 py-1.5 rounded-xl transition-all shrink-0" 
                            x-text="cat.name">
                    </button>
                </template>
            </div>
        </div>

        <!-- Inline Quick Create Form (Expandable) -->
        <div x-show="showCreateForm" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="p-5 border-b border-cyber-cyan/30 bg-obsidian-surface space-y-3 font-cairo text-xs"
             style="display: none;">
            <div class="flex items-center justify-between">
                <span class="font-bold text-white flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-cyber-cyan"></span>
                    <span>إضافة رد جاهز جديد فوراً للخزينة</span>
                </span>
                <span class="text-[10px] text-slate-400">سيظهر للطاقم فور الحفظ</span>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <select x-model="newForm.category_id" class="bg-obsidian-base border border-obsidian-border rounded-xl px-3 py-2 text-white focus:border-cyber-cyan focus:outline-none">
                    <option value="">اختر التصنيف...</option>
                    <template x-for="c in categories" :key="c.id">
                        <option :value="c.id" x-text="c.name"></option>
                    </template>
                </select>

                <input type="text" x-model="newForm.shortcut_code" placeholder="الكود المختصر (مثل: /custom-code)" 
                       class="bg-obsidian-base border border-obsidian-border rounded-xl px-3 py-2 text-white font-mono text-left focus:border-cyber-cyan focus:outline-none">
            </div>

            <input type="text" x-model="newForm.title" placeholder="عنوان الرد (مثال: عرض صيانة الشركات السنوي)" 
                   class="w-full bg-obsidian-base border border-obsidian-border rounded-xl px-3 py-2 text-white focus:border-cyber-cyan focus:outline-none">

            <textarea x-model="newForm.content" rows="3" placeholder="نص الرد الكامل الذي سيتم إدراجه أو نسخه..." 
                      class="w-full bg-obsidian-base border border-obsidian-border rounded-xl px-3 py-2 text-white focus:border-cyber-cyan focus:outline-none"></textarea>

            <div class="flex items-center justify-between gap-3 pt-1">
                <input type="text" x-model="newForm.tags" placeholder="كلمات دلالية مفصولة بفاصلة (صيانة, عروض, خصم)" 
                       class="flex-1 bg-obsidian-base border border-obsidian-border rounded-xl px-3 py-1.5 text-slate-300 focus:border-cyber-cyan focus:outline-none">

                <button type="button" @click="saveNewResponse()" :disabled="saving"
                        class="px-4 py-2 rounded-xl bg-cyber-cyan text-obsidian-base font-black hover:opacity-90 disabled:opacity-50 transition-all shrink-0">
                    <span x-text="saving ? 'جاري الحفظ...' : 'حفظ الرد ⚡'"></span>
                </button>
            </div>
            <div x-show="formError" x-text="formError" class="text-[10px] text-rose-400 font-bold"></div>
        </div>

        <!-- Canned Responses Viewport List -->
        <div class="flex-1 p-5 overflow-y-auto space-y-3 font-cairo text-xs scrollbar-thin">
            <!-- Loading Indicator -->
            <div x-show="loading" class="text-center py-12 text-slate-400 space-y-2">
                <div class="w-8 h-8 border-2 border-cyber-gold border-t-transparent rounded-full animate-spin mx-auto"></div>
                <p>جاري تحميل خزينة الردود...</p>
            </div>

            <!-- Empty State -->
            <div x-show="!loading && filteredResponses.length === 0" class="text-center py-16 text-slate-500 space-y-2">
                <div class="text-3xl">📭</div>
                <p>لا توجد ردود تطابق بحثك حالياً.</p>
            </div>

            <!-- Response Items Loop -->
            <template x-for="item in filteredResponses" :key="item.id">
                <div class="p-4 rounded-2xl bg-obsidian-surface border border-obsidian-border hover:border-cyber-gold/50 transition-all space-y-3 group shadow-sm">
                    <!-- Item Header -->
                    <div class="flex items-start justify-between gap-3">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-bold text-white text-xs" x-text="item.title"></span>
                                <span class="px-2 py-0.5 rounded-md font-mono text-[10px] font-bold bg-cyber-gold/15 text-cyber-gold border border-cyber-gold/30" 
                                      x-text="item.shortcut_code"></span>
                            </div>
                            <div class="flex items-center gap-2 text-[10px] text-slate-400">
                                <span class="text-slate-300 font-bold" x-text="item.category ? item.category.name : 'عام'"></span>
                                <span>·</span>
                                <span class="font-mono" x-text="item.usage_count + ' استخدام'"></span>
                            </div>
                        </div>

                        <!-- Toast on Action -->
                        <div x-show="copiedId === item.id" 
                             x-transition 
                             class="px-2.5 py-1 rounded-lg bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 text-[10px] font-bold shrink-0">
                            ✓ تم بنجاح!
                        </div>
                    </div>

                    <!-- Item Text Preview -->
                    <div class="p-3 rounded-xl bg-obsidian-base border border-obsidian-border/80 text-slate-300 text-[11px] leading-relaxed whitespace-pre-line line-clamp-4 group-hover:line-clamp-none transition-all font-tahoma"
                         x-text="item.content"></div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end gap-2 pt-1 border-t border-obsidian-border/60">
                        <!-- Direct Insert to Active Textarea -->
                        <button type="button" 
                                @click="insertIntoActiveTextarea(item)"
                                class="px-3.5 py-1.5 rounded-xl bg-cyber-cyan hover:bg-emerald-400 text-obsidian-base font-black font-cairo text-xs transition-all flex items-center gap-1.5 active:scale-95 shadow-sm">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                            <span>إدراج في صندوق الرد ✍️</span>
                        </button>

                        <!-- Copy to Clipboard -->
                        <button type="button" 
                                @click="copyToClipboard(item)"
                                class="px-3 py-1.5 rounded-xl bg-obsidian-base hover:bg-slate-800 text-slate-200 hover:text-white border border-obsidian-border text-xs font-cairo transition-all flex items-center gap-1 active:scale-95">
                            <svg class="w-3.5 h-3.5 text-cyber-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            <span>نسخ 📋</span>
                        </button>
                    </div>
                </div>
            </template>
        </div>

        <!-- Drawer Footer Status -->
        <div class="p-3 border-t border-obsidian-border bg-obsidian-surface/90 flex items-center justify-between text-[11px] font-cairo text-slate-400">
            <span>اختصار الفتح السريع: <kbd class="px-1.5 py-0.5 rounded bg-obsidian-base border border-obsidian-border font-mono text-[10px] text-cyber-gold">Alt + /</kbd></span>
            <a href="{{ route('admin.canned-responses.index') }}" class="text-cyber-cyan hover:underline font-bold">
                إدارة الخزينة كاملة ↗
            </a>
        </div>

    </div>

</div>

<script>
function quickRepliesDrawer() {
    return {
        isOpen: false,
        loading: false,
        saving: false,
        showCreateForm: false,
        searchQuery: '',
        selectedCategory: 'all',
        copiedId: null,
        formError: '',
        categories: [],
        responses: [],
        newForm: {
            category_id: '',
            shortcut_code: '',
            title: '',
            content: '',
            tags: ''
        },

        async initDrawer() {
            await this.loadData();
        },

        toggleDrawer() {
            this.isOpen = !this.isOpen;
        },

        async loadData() {
            this.loading = true;
            try {
                const res = await fetch('{{ route('admin.canned-responses.data') }}');
                const data = await res.json();
                if (data.status === 'success') {
                    this.categories = data.categories || [];
                    this.responses = data.responses || [];
                }
            } catch (e) {
                console.error('Failed to load canned responses', e);
            } finally {
                this.loading = false;
            }
        },

        get filteredResponses() {
            let list = this.responses;

            if (this.selectedCategory !== 'all') {
                list = list.filter(r => r.category_id == this.selectedCategory);
            }

            if (this.searchQuery && this.searchQuery.trim() !== '') {
                const q = this.searchQuery.trim().toLowerCase();
                list = list.filter(r => {
                    const matchTitle = (r.title || '').toLowerCase().includes(q);
                    const matchCode = (r.shortcut_code || '').toLowerCase().includes(q);
                    const matchContent = (r.content || '').toLowerCase().includes(q);
                    const matchTags = Array.isArray(r.tags_json) && r.tags_json.some(t => (t || '').toLowerCase().includes(q));
                    return matchTitle || matchCode || matchContent || matchTags;
                });
            }

            return list;
        },

        async copyToClipboard(item) {
            try {
                await navigator.clipboard.writeText(item.content);
                this.flashSuccess(item.id);
                this.incrementCounter(item);
            } catch (e) {
                console.error('Clipboard copy error', e);
            }
        },

        async insertIntoActiveTextarea(item) {
            // Find active or target textarea on the page
            const activeElem = document.activeElement;
            let targetTextarea = (activeElem && activeElem.tagName === 'TEXTAREA') ? activeElem : null;

            if (!targetTextarea) {
                // Priority order: engineering report, note, reply, message, or any textarea
                targetTextarea = document.querySelector('textarea[name="note"]') ||
                                 document.querySelector('textarea[name="message"]') ||
                                 document.querySelector('textarea[name="engineering_report"]') ||
                                 document.querySelector('textarea[name="content"]') ||
                                 document.querySelector('textarea');
            }

            if (targetTextarea) {
                const startPos = targetTextarea.selectionStart || targetTextarea.value.length;
                const endPos = targetTextarea.selectionEnd || targetTextarea.value.length;
                const currentVal = targetTextarea.value;
                
                const insertion = (currentVal.length > 0 && !currentVal.endsWith('\n') ? '\n\n' : '') + item.content;
                targetTextarea.value = currentVal.substring(0, startPos) + insertion + currentVal.substring(endPos);

                // Dispatch input event for reactive frameworks
                targetTextarea.dispatchEvent(new Event('input', { bubbles: true }));
                targetTextarea.dispatchEvent(new Event('change', { bubbles: true }));

                // Focus and scroll
                targetTextarea.focus();
                targetTextarea.scrollIntoView({ behavior: 'smooth', block: 'center' });

                // Visual highlight glow
                targetTextarea.classList.add('ring-2', 'ring-cyber-cyan');
                setTimeout(() => targetTextarea.classList.remove('ring-2', 'ring-cyber-cyan'), 2000);

                this.flashSuccess(item.id);
                this.incrementCounter(item);

                // Close drawer after 800ms so staff can see where it was inserted
                setTimeout(() => {
                    this.isOpen = false;
                }, 800);
            } else {
                // If no textarea found on page, fallback to copy
                await this.copyToClipboard(item);
                alert('تم نسخ الرد بنجاح إلى الحافظة (لا يوجد صندوق كتابة بالصفحة الحالية)');
            }
        },

        async incrementCounter(item) {
            item.usage_count = (item.usage_count || 0) + 1;
            try {
                await fetch(`/admin/canned-responses/${item.id}/increment-usage`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
            } catch (e) {}
        },

        flashSuccess(id) {
            this.copiedId = id;
            setTimeout(() => {
                if (this.copiedId === id) this.copiedId = null;
            }, 2000);
        },

        async saveNewResponse() {
            this.formError = '';
            if (!this.newForm.category_id || !this.newForm.title || !this.newForm.shortcut_code || !this.newForm.content) {
                this.formError = 'يرجى ملء جميع الحقول المطلوبة (التصنيف، العنوان، الكود، المحتوى).';
                return;
            }

            if (!this.newForm.shortcut_code.startsWith('/')) {
                this.newForm.shortcut_code = '/' + this.newForm.shortcut_code;
            }

            this.saving = true;
            try {
                const res = await fetch('{{ route('admin.canned-responses.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(this.newForm)
                });

                const data = await res.json();
                if (data.status === 'success' && data.response) {
                    this.responses.unshift(data.response);
                    this.newForm = { category_id: '', shortcut_code: '', title: '', content: '', tags: '' };
                    this.showCreateForm = false;
                    this.flashSuccess(data.response.id);
                } else {
                    this.formError = data.message || 'حدث خطأ أثناء حفظ الرد الجاهز.';
                }
            } catch (e) {
                this.formError = 'تعذر الاتصال بالخادم لحفظ الرد.';
            } finally {
                this.saving = false;
            }
        }
    };
}
</script>
