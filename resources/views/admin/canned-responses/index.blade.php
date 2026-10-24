@extends('admin.layout')

@section('title', 'خزينة الردود الذكية للمبيعات والدعم الفني | سولي ديدجيتال سيتادل')
@section('page-title', 'خزينة الردود الذكية السريعة ⚡')

@section('admin-content')
<div class="space-y-8" x-data="cannedVaultController({
    categories: @json($categories),
    responses: @json($allResponses)
})">

    <!-- ===================================================================== -->
    <!-- 1. TOP HEADER & SEARCH BAR                                            -->
    <!-- ===================================================================== -->
    <div class="glass-obsidian rounded-3xl border border-cyber-gold/40 p-6 sm:p-8 space-y-6 shadow-neon-gold">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-cyber-gold animate-pulse"></span>
                    <span class="text-xs font-mono text-cyber-gold font-bold uppercase tracking-wider">SMART CANNED RESPONSES VAULT · 25+ READY RESPONSES</span>
                </div>
                <h2 class="text-xl sm:text-2xl font-black text-white font-cairo">
                    خزينة الردود الذكية المعتمدة للمبيعات والدعم الفني
                </h2>
                <p class="text-xs text-slate-400 font-readex">
                    انسخ أقوى الردود البيعية والهندسية بنقرة زر واحدة للصقها فوراً في واتساب أو تذاكر الدعم مع عداد آلي لمعدل الاستخدام.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <button type="button" @click="openCreateModal = true"
                        class="px-5 py-2.5 rounded-2xl bg-gradient-to-r from-cyber-gold to-cyber-cyan text-obsidian-base font-extrabold text-xs font-cairo shadow-neon-gold hover:scale-105 active:scale-95 transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    <span>إضافة رد جاهز جديد</span>
                </button>
            </div>
        </div>

        <!-- Live Instant Search Bar -->
        <div class="relative">
            <input type="text" 
                   x-model="searchQuery" 
                   placeholder="ابحث داخل الردود الذكية (بالكلمة، الكود المختصر مثل /erp-price، أو الوسوم)..." 
                   class="w-full bg-obsidian-surface border border-obsidian-border text-xs sm:text-sm text-white placeholder-slate-500 rounded-2xl px-12 py-3.5 focus:outline-none focus:border-cyber-gold focus:ring-1 focus:ring-cyber-gold/40 transition-all font-cairo">
            <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none text-slate-400">
                <svg class="w-5 h-5 text-cyber-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <div class="absolute inset-y-0 left-4 flex items-center">
                <span class="text-[11px] font-mono text-slate-400" x-text="`${filteredResponses.length} رد مطابق`"></span>
            </div>
        </div>

        <!-- Quick Shortcut Filters (Chips) -->
        <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs">
            <span class="text-[11px] text-slate-500 font-mono shrink-0">أكواد سريعة:</span>
            <button type="button" @click="searchQuery = '/erp'" class="px-2.5 py-1 rounded-lg bg-obsidian-surface hover:bg-slate-700 text-cyber-cyan border border-obsidian-border font-mono text-[11px] transition-colors">/erp</button>
            <button type="button" @click="searchQuery = '/ai'" class="px-2.5 py-1 rounded-lg bg-obsidian-surface hover:bg-slate-700 text-cyber-cyan border border-obsidian-border font-mono text-[11px] transition-colors">/ai</button>
            <button type="button" @click="searchQuery = '/cyber'" class="px-2.5 py-1 rounded-lg bg-obsidian-surface hover:bg-slate-700 text-cyber-gold border border-obsidian-border font-mono text-[11px] transition-colors">/cyber</button>
            <button type="button" @click="searchQuery = '/maint'" class="px-2.5 py-1 rounded-lg bg-obsidian-surface hover:bg-slate-700 text-cyber-gold border border-obsidian-border font-mono text-[11px] transition-colors">/maint</button>
            <button type="button" @click="searchQuery = '/wallet'" class="px-2.5 py-1 rounded-lg bg-obsidian-surface hover:bg-slate-700 text-emerald-400 border border-obsidian-border font-mono text-[11px] transition-colors">/wallet</button>
            <button type="button" @click="searchQuery = '/obj'" class="px-2.5 py-1 rounded-lg bg-obsidian-surface hover:bg-slate-700 text-purple-300 border border-obsidian-border font-mono text-[11px] transition-colors">/obj (الاعتراضات)</button>
        </div>
    </div>

    <!-- ===================================================================== -->
    <!-- 2. CATEGORY TABS                                                      -->
    <!-- ===================================================================== -->
    <div class="flex items-center gap-2 overflow-x-auto pb-2 text-xs font-cairo font-bold">
        <button type="button" @click="activeCategorySlug = 'all'" 
                class="px-4 py-2 rounded-xl transition-all shrink-0"
                :class="activeCategorySlug === 'all' ? 'bg-cyber-gold text-obsidian-base shadow-neon-gold' : 'bg-obsidian-surface text-slate-300 hover:text-white border border-obsidian-border'">
            كافة الردود ({{ $allResponses->count() }})
        </button>

        @foreach($categories as $cat)
            <button type="button" @click="activeCategorySlug = '{{ $cat->slug }}'" 
                    class="px-4 py-2 rounded-xl transition-all shrink-0 flex items-center gap-1.5"
                    :class="activeCategorySlug === '{{ $cat->slug }}' ? 'bg-cyber-gold text-obsidian-base shadow-neon-gold' : 'bg-obsidian-surface text-slate-300 hover:text-white border border-obsidian-border'">
                <span>{{ $cat->name }}</span>
                <span class="text-[10px] font-mono opacity-80">({{ $cat->responses->count() }})</span>
            </button>
        @endforeach
    </div>

    <!-- Copy Feedback Animated Toast -->
    <div x-show="showCopySuccess" x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-4"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform translate-y-4"
         class="fixed bottom-8 left-8 z-50 p-4 rounded-2xl bg-gradient-to-r from-emerald-500 to-cyber-cyan text-obsidian-base font-black text-sm font-cairo shadow-neon-cyan flex items-center gap-3"
         style="display: none;">
        <svg class="w-6 h-6 animate-bounce" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
        </svg>
        <span>تم نسخ الرد بنجاح! جاهز للصق فوراً في واتساب أو الشات 📋</span>
    </div>

    <!-- ===================================================================== -->
    <!-- 3. CANNED RESPONSES CARDS GRID                                        -->
    <!-- ===================================================================== -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <template x-for="item in filteredResponses" :key="item.id">
            <div class="p-6 rounded-3xl glass-obsidian border border-obsidian-border hover:border-cyber-gold/50 transition-all flex flex-col justify-between space-y-4 group">
                
                <!-- Card Header -->
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="font-mono text-xs font-bold px-2.5 py-1 rounded-lg bg-cyber-gold/15 text-cyber-gold border border-cyber-gold/30"
                              x-text="item.shortcut_code">
                        </span>

                        <span class="text-[10px] font-mono text-slate-400 flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-cyber-cyan"></span>
                            <span x-text="`${item.usage_count} استخدام`"></span>
                        </span>
                    </div>

                    <h3 class="text-base font-bold text-white font-cairo group-hover:text-cyber-gold transition-colors"
                        x-text="item.title">
                    </h3>

                    <!-- Message Body Preview Box -->
                    <div class="p-4 rounded-2xl bg-obsidian-surface border border-obsidian-border/80 text-xs text-slate-300 font-readex leading-relaxed whitespace-pre-line max-h-48 overflow-y-auto select-all"
                         x-text="item.content">
                    </div>
                </div>

                <!-- Card Footer & Action Buttons -->
                <div class="pt-3 border-t border-obsidian-border/60 flex items-center justify-between gap-3">
                    <!-- One-Click Copy Button -->
                    <button type="button" @click="copyResponse(item)"
                            class="flex-1 py-2.5 px-4 rounded-xl bg-gradient-to-r from-cyber-gold to-cyber-cyan hover:opacity-95 text-obsidian-base font-extrabold text-xs font-cairo shadow-neon-gold active:scale-95 transition-all flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                        <span>نسخ الرد فوراً 📋</span>
                    </button>

                    <!-- Delete Trigger -->
                    <form :action="`/admin/canned-responses/${item.id}`" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا الرد؟')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" title="حذف" class="p-2.5 rounded-xl bg-obsidian-surface hover:bg-rose-500/20 text-slate-500 hover:text-rose-400 border border-obsidian-border transition-colors">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </form>
                </div>

            </div>
        </template>
    </div>

    <!-- Empty State -->
    <template x-if="filteredResponses.length === 0">
        <div class="p-12 text-center rounded-3xl glass-obsidian border border-obsidian-border space-y-3">
            <div class="text-sm font-bold text-white font-cairo">لا توجد ردود مطابقة للبحث</div>
            <p class="text-xs text-slate-400 font-readex">
                جرب البحث بكلمة أخرى أو تصفح الأقسام الكاملة.
            </p>
        </div>
    </template>

    <!-- ===================================================================== -->
    <!-- 4. ADD NEW CANNED RESPONSE MODAL                                      -->
    <!-- ===================================================================== -->
    <div x-show="openCreateModal" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 bg-black/80 backdrop-blur-md flex items-center justify-center p-4 overflow-y-auto"
         style="display: none;">
        
        <div @click.away="openCreateModal = false"
             class="relative w-full max-w-xl glass-obsidian border border-cyber-gold/40 rounded-3xl p-6 sm:p-8 space-y-6 shadow-neon-gold max-h-[90vh] overflow-y-auto">
            
            <div class="flex items-center justify-between border-b border-obsidian-border pb-4">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-cyber-gold animate-pulse"></span>
                    <h3 class="text-xl font-black text-white font-cairo">إضافة رد ذكي جديد للخزينة</h3>
                </div>
                <button type="button" @click="openCreateModal = false" class="p-2 text-slate-400 hover:text-white rounded-lg">✕</button>
            </div>

            <form action="{{ route('admin.canned-responses.store') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Category & Shortcut -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-300 font-readex">القسم التصنيفي</label>
                        <select name="category_id" required
                                class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-4 py-2.5 text-xs text-white font-cairo focus:outline-none focus:border-cyber-gold">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-300 font-readex">الكود المختصر (Shortcut)</label>
                        <input type="text" name="shortcut_code" required placeholder="/erp-discount"
                               class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-4 py-2.5 text-xs text-white font-mono focus:outline-none focus:border-cyber-gold">
                    </div>
                </div>

                <!-- Title -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-300 font-readex">عنوان الرد (للبحث الداخلي السريع)</label>
                    <input type="text" name="title" required placeholder="مثال: خصم خاص لعملاء الفروع المتعددة"
                           class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-4 py-2.5 text-xs text-white font-cairo focus:outline-none focus:border-cyber-gold">
                </div>

                <!-- Content -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-300 font-readex">نص الرسالة الكامل (جاهز للنسخ والإرسال)</label>
                    <textarea name="content" rows="6" required placeholder="أهلاً بك يا فندم! يسعدنا تقديم عرض خاص..."
                              class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl p-3.5 text-xs text-white font-readex placeholder-slate-500 focus:outline-none focus:border-cyber-gold leading-relaxed"></textarea>
                </div>

                <!-- Tags -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-300 font-readex">الوسوم والكلمات المفتاحية (مفصولة بفاصلة)</label>
                    <input type="text" name="tags" placeholder="erp, خصم, فروع, اسعار"
                           class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-4 py-2.5 text-xs text-white font-cairo focus:outline-none focus:border-cyber-gold">
                </div>

                <!-- Submit Button -->
                <button type="submit"
                        class="w-full py-3.5 px-4 rounded-xl bg-gradient-to-r from-cyber-gold to-cyber-cyan text-obsidian-base font-extrabold text-xs sm:text-sm font-cairo shadow-neon-gold hover:scale-[1.01] active:scale-95 transition-all">
                    حفظ الرد في الخزينة
                </button>
            </form>
        </div>
    </div>

</div>

<!-- ===================================================================== -->
<!-- ALPINE CANNED VAULT CONTROLLER SCRIPT                                 -->
<!-- ===================================================================== -->
<script>
function cannedVaultController(config) {
    return {
        categories: config.categories || [],
        responses: config.responses || [],
        activeCategorySlug: 'all',
        searchQuery: '',
        openCreateModal: false,
        showCopySuccess: false,

        get filteredResponses() {
            let list = this.responses;

            // 1. Filter by category
            if (this.activeCategorySlug !== 'all') {
                list = list.filter(r => r.category && r.category.slug === this.activeCategorySlug);
            }

            // 2. Filter by search query
            if (this.searchQuery.trim()) {
                const q = this.searchQuery.toLowerCase().trim();
                list = list.filter(r => {
                    const matchTitle = (r.title || '').toLowerCase().includes(q);
                    const matchShortcut = (r.shortcut_code || '').toLowerCase().includes(q);
                    const matchContent = (r.content || '').toLowerCase().includes(q);
                    const matchTags = Array.isArray(r.tags_json) && r.tags_json.some(t => t.toLowerCase().includes(q));
                    return matchTitle || matchShortcut || matchContent || matchTags;
                });
            }

            return list;
        },

        copyResponse(item) {
            navigator.clipboard.writeText(item.content).then(() => {
                this.showCopySuccess = true;
                setTimeout(() => this.showCopySuccess = false, 2800);

                // Increment usage counter on server
                fetch(`/admin/canned-responses/${item.id}/increment-usage`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                }).then(res => res.json()).then(data => {
                    if (data.status === 'success') {
                        item.usage_count = data.usage_count;
                    }
                }).catch(() => {});
            });
        }
    };
}
</script>
@endsection
