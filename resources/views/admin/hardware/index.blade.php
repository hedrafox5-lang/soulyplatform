@extends('admin.layout')

@section('title', 'إدارة متجر ومخزون الهاردوير | مركز القيادة العليا')
@section('page-title', 'إدارة متجر ومخزون الهاردوير والأجهزة')

@section('admin-content')
<div class="space-y-8" x-data="{ 
    currentTab: '{{ $activeTab }}', 
    addProductModal: false, 
    editProductModal: false,
    updateOrderModal: false,
    selectedProduct: null,
    selectedOrder: null,
    specsRaw: '',
    async saveInline(id, price, stock) {
        try {
            const res = await fetch(`{{ url('/admin/hardware/products') }}/${id}/inline`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ price: price, stock_qty: stock })
            });
            const data = await res.json();
            if (data.success) {
                alert(data.message);
                window.location.reload();
            }
        } catch (e) {
            alert('فشل التعديل اللحظي: ' + e.message);
        }
    }
}">

    {{-- ===================================================================== --}}
    {{-- 1. TOP STATS STRIP                                                    --}}
    {{-- ===================================================================== --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="p-5 rounded-2xl glass-obsidian border border-cyber-cyan/40 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs text-slate-400 font-cairo block">إجمالي أجهزة الهاردوير</span>
                <span class="text-2xl font-black font-mono text-cyber-cyan">{{ $counts['products_total'] }}</span>
            </div>
            <span class="text-2xl">🖥️</span>
        </div>

        <div class="p-5 rounded-2xl glass-obsidian border border-purple-500/40 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs text-slate-400 font-cairo block">أجهزة نقاط البيع POS</span>
                <span class="text-2xl font-black font-mono text-purple-300">{{ $counts['pos_count'] }}</span>
            </div>
            <span class="text-2xl">🛒</span>
        </div>

        <div class="p-5 rounded-2xl glass-obsidian border border-amber-500/40 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs text-slate-400 font-cairo block">طلبات قيد المراجعة</span>
                <span class="text-2xl font-black font-mono text-cyber-gold">{{ $counts['orders_pending'] }}</span>
            </div>
            <span class="text-2xl">⏳</span>
        </div>

        <div class="p-5 rounded-2xl glass-obsidian border border-emerald-500/40 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs text-slate-400 font-cairo block">شحنات سلمت مع الضمان</span>
                <span class="text-2xl font-black font-mono text-emerald-400">{{ $counts['orders_delivered'] }}</span>
            </div>
            <span class="text-2xl">🛡️</span>
        </div>
    </div>

    {{-- ===================================================================== --}}
    {{-- 2. TAB CONTROLS NAVIGATION                                            --}}
    {{-- ===================================================================== --}}
    <div class="glass-obsidian rounded-2xl border border-obsidian-border p-2 flex items-center justify-between gap-4 font-cairo text-xs font-bold">
        <div class="flex items-center gap-2 overflow-x-auto scrollbar-none">
            <a href="{{ route('admin.hardware.index', ['tab' => 'products']) }}"
               class="px-5 py-2.5 rounded-xl transition-all flex items-center gap-2 shrink-0
               {{ $activeTab === 'products' ? 'bg-cyber-cyan text-obsidian-base font-black shadow-neon-cyan' : 'bg-obsidian-surface text-slate-300 hover:text-white' }}">
                <span>📦 كتالوج الأجهزة والمخزون</span>
                <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold" :class="'{{ $activeTab }}' === 'products' ? 'bg-black/20 text-black' : 'bg-slate-800 text-slate-400'">{{ $counts['products_total'] }}</span>
            </a>

            <a href="{{ route('admin.hardware.index', ['tab' => 'orders']) }}"
               class="px-5 py-2.5 rounded-xl transition-all flex items-center gap-2 shrink-0
               {{ $activeTab === 'orders' ? 'bg-cyber-cyan text-obsidian-base font-black shadow-neon-cyan' : 'bg-obsidian-surface text-slate-300 hover:text-white' }}">
                <span>🚚 طلبات الشحن وتفعيل الضمان</span>
                @if($counts['orders_pending'] > 0)
                    <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-amber-500 text-black animate-pulse">{{ $counts['orders_pending'] }} جديد</span>
                @endif
            </a>
        </div>

        @if($activeTab === 'products')
            <button type="button" 
                    @click="addProductModal = true"
                    class="px-4 py-2.5 rounded-xl btn-citadel-primary text-xs font-cairo font-bold flex items-center gap-1.5 shadow-neon-cyan shrink-0">
                <span>➕ إضافة جهاز هاردوير جديد</span>
            </button>
        @endif
    </div>

    {{-- ===================================================================== --}}
    {{-- 3. PRODUCTS TAB CONTENT                                               --}}
    {{-- ===================================================================== --}}
    @if($activeTab === 'products')
        <div class="space-y-4">
            {{-- Filters Bar --}}
            <form action="{{ route('admin.hardware.index') }}" method="GET" class="glass-obsidian rounded-2xl border border-obsidian-border p-3 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs font-cairo">
                <input type="hidden" name="tab" value="products">

                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="بحث بالاسم، الـ SKU، أو الماركة..." 
                           class="w-full sm:w-64 bg-obsidian-surface text-slate-200 placeholder-slate-500 rounded-xl px-3 py-2 border border-obsidian-border focus:border-cyber-cyan focus:outline-none">

                    <select name="category" class="bg-obsidian-surface text-slate-300 rounded-xl px-3 py-2 border border-obsidian-border focus:border-cyber-cyan focus:outline-none">
                        <option value="all" {{ request('category') === 'all' ? 'selected' : '' }}>كافة التصنيفات</option>
                        <option value="pos_equipment" {{ request('category') === 'pos_equipment' ? 'selected' : '' }}>أجهزة كاشير ونقاط بيع</option>
                        <option value="storage_ram" {{ request('category') === 'storage_ram' ? 'selected' : '' }}>هاردات SSD ورامات</option>
                        <option value="networking_security" {{ request('category') === 'networking_security' ? 'selected' : '' }}>شبكات وأجهزة بصمة وتدريع</option>
                        <option value="workstations" {{ request('category') === 'workstations' ? 'selected' : '' }}>أجهزة ولابتوبات أعمال</option>
                    </select>

                    <button type="submit" class="px-4 py-2 rounded-xl bg-obsidian-surface hover:bg-obsidian-card border border-cyber-cyan/40 text-cyber-cyan font-bold transition-all">
                        تصفية
                    </button>
                </div>

                @if(request('search') || (request('category') && request('category') !== 'all'))
                    <a href="{{ route('admin.hardware.index', ['tab' => 'products']) }}" class="text-xs text-rose-400 hover:underline">إلغاء التصفية ✕</a>
                @endif
            </form>

            {{-- Products Table & Tactical Mobile Action Cards --}}
            <div class="rounded-3xl glass-obsidian border border-obsidian-border overflow-hidden">
                
                <!-- Desktop Table (Hidden on Mobile) -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-right text-xs font-cairo">
                        <thead class="bg-black/40 text-slate-400 border-b border-obsidian-border font-bold">
                            <tr>
                                <th class="p-4">SKU / الكود</th>
                                <th class="p-4">اسم المنتج</th>
                                <th class="p-4">التصنيف</th>
                                <th class="p-4">الماركة</th>
                                <th class="p-4">السعر</th>
                                <th class="p-4">المخزون</th>
                                <th class="p-4">الضمان</th>
                                <th class="p-4">التوافق مع ERP</th>
                                <th class="p-4">الحالة</th>
                                <th class="p-4 text-center">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-obsidian-border/60">
                            @forelse($products as $p)
                                <tr class="hover:bg-white/[0.02] transition-colors">
                                    <td class="p-4 font-mono font-bold text-cyber-cyan">{{ $p->sku }}</td>
                                    <td class="p-4 font-bold text-white max-w-xs truncate">{{ $p->name_ar }}</td>
                                    <td class="p-4 text-slate-300">
                                        <span class="px-2 py-0.5 rounded text-[11px] bg-slate-800 text-slate-300">{{ $p->category_name_ar }}</span>
                                    </td>
                                    <td class="p-4 font-mono font-bold text-purple-300">{{ $p->brand }}</td>
                                    <td class="p-4 font-mono font-black text-cyber-gold">{{ number_format($p->price, 0) }} EGP</td>
                                    <td class="p-4 font-mono font-bold {{ $p->stock_qty > 0 ? 'text-slate-300' : 'text-rose-400' }}">
                                        {{ $p->stock_qty }} قطعة
                                    </td>
                                    <td class="p-4 font-mono text-emerald-400">{{ $p->warranty_months }} شهر</td>
                                    <td class="p-4">
                                        @if($p->compatible_with_branderp)
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-cyber-cyan/15 text-cyber-cyan">معتمد ✓</span>
                                        @else
                                            <span class="text-slate-500">—</span>
                                        @endif
                                    </td>
                                    <td class="p-4">
                                        <form action="{{ route('admin.hardware.toggle_product', $p) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-2 py-0.5 rounded text-[10px] font-bold transition-all
                                                {{ $p->is_active ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-slate-800 text-slate-500' }}">
                                                {{ $p->is_active ? 'نشط' : 'معطل' }}
                                            </button>
                                        </form>
                                    </td>
                                    <td class="p-4 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <button type="button" 
                                                    @click="selectedProduct = {{ Js::from($p) }}; editProductModal = true" 
                                                    class="p-1.5 rounded-lg bg-obsidian-surface hover:bg-obsidian-card text-slate-300 hover:text-cyber-cyan text-xs font-bold transition-colors">
                                                ✏️ تعديل
                                            </button>

                                            <form action="{{ route('admin.hardware.delete_product', $p) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا المنتج نهائياً؟')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 text-xs font-bold transition-colors">
                                                    🗑️
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-10 text-slate-400">لا توجد منتجات هاردوير مسجلة حالياً.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Tactical Action Cards (Visible on < 768px only) -->
                <div class="md:hidden space-y-3 p-3">
                    @forelse($products as $p)
                        <div class="p-4 rounded-2xl bg-obsidian-surface border border-obsidian-border space-y-3 shadow-md">
                            <div class="flex items-center justify-between border-b border-obsidian-border/70 pb-2">
                                <span class="font-mono font-black text-cyber-cyan text-xs">{{ $p->sku }}</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold font-cairo bg-slate-800 text-slate-300">
                                    {{ $p->category_name_ar }}
                                </span>
                            </div>

                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <h4 class="font-bold text-white text-sm font-cairo">{{ $p->name_ar }}</h4>
                                    <span class="text-xs text-purple-300 font-mono font-bold">{{ $p->brand }}</span>
                                </div>
                                <div class="text-left font-mono" x-data="{ editing: false, newPrice: {{ $p->price }}, newStock: {{ $p->stock_qty }} }">
                                    <div x-show="!editing" @click="editing = true" class="cursor-pointer" title="انقر للتعديل اللحظي">
                                        <span class="text-sm font-black text-cyber-gold hover:underline">{{ number_format($p->price, 0) }} EGP ✎</span>
                                        <span class="text-[10px] text-slate-400 block hover:underline">{{ $p->stock_qty }} قطعة ✎</span>
                                    </div>
                                    <div x-show="editing" class="flex flex-col gap-1 bg-black/60 p-1.5 rounded-lg border border-cyber-gold/50" style="display:none;">
                                        <div class="flex items-center gap-1">
                                            <span class="text-[9px] text-slate-400">سعر:</span>
                                            <input type="number" x-model.number="newPrice" class="w-16 bg-obsidian-surface text-cyber-gold border border-obsidian-border text-xs px-1 py-0.5 rounded font-mono">
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <span class="text-[9px] text-slate-400">مخزون:</span>
                                            <input type="number" x-model.number="newStock" class="w-16 bg-obsidian-surface text-white border border-obsidian-border text-xs px-1 py-0.5 rounded font-mono">
                                        </div>
                                        <div class="flex items-center justify-end gap-1 pt-1">
                                            <button type="button" @click="saveInline({{ $p->id }}, newPrice, newStock); editing = false" class="px-2 py-0.5 bg-emerald-500 text-obsidian-base text-[10px] font-bold rounded">حفظ</button>
                                            <button type="button" @click="editing = false" class="px-1.5 py-0.5 bg-slate-700 text-white text-[10px] rounded">×</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="p-2.5 rounded-xl bg-obsidian-card border border-obsidian-border flex items-center justify-between text-[11px] font-cairo">
                                <div>
                                    <span class="text-slate-400 text-[10px] block">الضمان:</span>
                                    <span class="text-emerald-400 font-mono font-bold">{{ $p->warranty_months }} شهر</span>
                                </div>
                                <div>
                                    @if($p->compatible_with_branderp)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-cyber-cyan/15 text-cyber-cyan">معتمد لـ ERP ✓</span>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center gap-2 pt-1">
                                <button type="button" 
                                        @click="selectedProduct = {{ Js::from($p) }}; editProductModal = true" 
                                        class="flex-1 py-2 rounded-xl btn-citadel-primary text-xs font-bold font-cairo text-center">
                                    ✏️ تعديل المواصفات والسعر
                                </button>
                                <form action="{{ route('admin.hardware.toggle_product', $p) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-3 py-2 rounded-xl text-xs font-bold font-cairo border
                                        {{ $p->is_active ? 'bg-emerald-500/15 text-emerald-400 border-emerald-500/30' : 'bg-slate-800 text-slate-400 border-slate-700' }}">
                                        {{ $p->is_active ? 'نشط' : 'معطل' }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-slate-400 font-cairo text-xs bg-obsidian-surface rounded-2xl border border-obsidian-border">
                            لا توجد منتجات هاردوير مسجلة حالياً.
                        </div>
                    @endforelse
                </div>

                @if($products->hasPages())
                    <div class="p-4 border-t border-obsidian-border">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- ===================================================================== --}}
    {{-- 4. ORDERS TAB CONTENT                                                 --}}
    {{-- ===================================================================== --}}
    @if($activeTab === 'orders')
        <div class="space-y-4">
            {{-- Order Status Filters --}}
            <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none text-xs font-cairo font-bold">
                <a href="{{ route('admin.hardware.index', ['tab' => 'orders', 'status' => 'all']) }}" 
                   class="px-4 py-2 rounded-xl border transition-all {{ request('status', 'all') === 'all' ? 'bg-cyber-cyan text-obsidian-base font-black border-cyber-cyan' : 'bg-obsidian-surface text-slate-300 border-obsidian-border' }}">
                    كافة الأوردرات ({{ $counts['orders_total'] }})
                </a>
                <a href="{{ route('admin.hardware.index', ['tab' => 'orders', 'status' => 'pending_review']) }}" 
                   class="px-4 py-2 rounded-xl border transition-all {{ request('status') === 'pending_review' ? 'bg-amber-500 text-black font-black border-amber-500' : 'bg-obsidian-surface text-amber-300 border-obsidian-border' }}">
                    قيد المراجعة ({{ $counts['orders_pending'] }})
                </a>
                <a href="{{ route('admin.hardware.index', ['tab' => 'orders', 'status' => 'confirmed_preparing']) }}" 
                   class="px-4 py-2 rounded-xl border transition-all {{ request('status') === 'confirmed_preparing' ? 'bg-purple-600 text-white font-black border-purple-600' : 'bg-obsidian-surface text-purple-300 border-obsidian-border' }}">
                    جاري التجهيز ({{ $counts['orders_preparing'] }})
                </a>
                <a href="{{ route('admin.hardware.index', ['tab' => 'orders', 'status' => 'out_for_delivery']) }}" 
                   class="px-4 py-2 rounded-xl border transition-all {{ request('status') === 'out_for_delivery' ? 'bg-cyan-500 text-black font-black border-cyan-500' : 'bg-obsidian-surface text-cyan-300 border-obsidian-border' }}">
                    خرج للتوصيل ({{ $counts['orders_in_delivery'] }})
                </a>
                <a href="{{ route('admin.hardware.index', ['tab' => 'orders', 'status' => 'delivered_warranty_active']) }}" 
                   class="px-4 py-2 rounded-xl border transition-all {{ request('status') === 'delivered_warranty_active' ? 'bg-emerald-600 text-white font-black border-emerald-600' : 'bg-obsidian-surface text-emerald-300 border-obsidian-border' }}">
                    تم التسليم وتفعيل الضمان ({{ $counts['orders_delivered'] }})
                </a>
            </div>

            {{-- Orders Table & Tactical Mobile Action Cards --}}
            <div class="rounded-3xl glass-obsidian border border-obsidian-border overflow-hidden">
                
                <!-- Desktop Table (Hidden on Mobile) -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-right text-xs font-cairo">
                        <thead class="bg-black/40 text-slate-400 border-b border-obsidian-border font-bold">
                            <tr>
                                <th class="p-4">رقم الأوردر</th>
                                <th class="p-4">العميل / المؤسسة</th>
                                <th class="p-4">الهاتف</th>
                                <th class="p-4">الأجهزة المطلوبة</th>
                                <th class="p-4">الإجمالي</th>
                                <th class="p-4">السداد</th>
                                <th class="p-4">حالة الشحن والضمان</th>
                                <th class="p-4">الأرقام التسلسلية (Serials)</th>
                                <th class="p-4 text-center">إجراءات وتحديث</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-obsidian-border/60">
                            @forelse($orders as $ord)
                                <tr class="hover:bg-white/[0.02] transition-colors">
                                    <td class="p-4 font-mono font-bold text-cyber-cyan">{{ $ord->order_number }}</td>
                                    <td class="p-4">
                                        <div class="font-bold text-white">{{ $ord->customer_name }}</div>
                                        @if($ord->user)
                                            <span class="text-[10px] text-slate-400 font-mono">حساب: {{ $ord->user->email }}</span>
                                        @endif
                                    </td>
                                    <td class="p-4 font-mono text-slate-300">{{ $ord->customer_phone }}</td>
                                    <td class="p-4 text-slate-200">
                                        {{ count($ord->items_json ?? []) }} أجهزة
                                        <span class="text-[10px] text-slate-400 block truncate max-w-xs">
                                            {{ collect($ord->items_json)->pluck('name')->implode(', ') }}
                                        </span>
                                    </td>
                                    <td class="p-4 font-mono font-black text-cyber-gold">{{ number_format($ord->total_amount, 2) }} EGP</td>
                                    <td class="p-4">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold
                                            {{ $ord->payment_status === 'paid' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-amber-500/20 text-amber-300' }}">
                                            {{ $ord->payment_method === 'wallet' ? 'محفظة رقمية' : ($ord->payment_method === 'transfer' ? 'تحويل بنكي' : 'عند الاستلام') }}
                                        </span>
                                    </td>
                                    <td class="p-4">
                                        <span class="px-2.5 py-1 rounded-xl text-[10px] font-bold font-cairo
                                            {{ match($ord->status) {
                                                'pending_review' => 'bg-amber-500/20 text-amber-300 border border-amber-500/30',
                                                'confirmed_preparing' => 'bg-purple-500/20 text-purple-300 border border-purple-500/30',
                                                'out_for_delivery' => 'bg-cyan-500/20 text-cyan-300 border border-cyan-500/30',
                                                'delivered_warranty_active' => 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30',
                                                default => 'bg-slate-800 text-slate-400'
                                            } }}">
                                            {{ $ord->status_label_ar }}
                                        </span>
                                    </td>
                                    <td class="p-4 font-mono text-[11px] text-slate-300 max-w-xs truncate">
                                        {{ $ord->serial_numbers_notes ?? 'لم تُسجل بعد' }}
                                    </td>
                                    <td class="p-4 text-center">
                                        <button type="button" 
                                                @click="selectedOrder = {{ Js::from($ord) }}; updateOrderModal = true"
                                                class="px-3 py-1.5 rounded-xl bg-cyber-cyan/15 hover:bg-cyber-cyan/25 text-cyber-cyan border border-cyber-cyan/40 font-bold text-xs transition-colors">
                                            تحديث الحالة والسيريال ⚙️
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-10 text-slate-400">لا توجد طلبات هاردوير في هذه الحالة.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Tactical Action Cards (Visible on < 768px only) -->
                <div class="md:hidden space-y-3 p-3">
                    @forelse($orders as $ord)
                        <div class="p-4 rounded-2xl bg-obsidian-surface border border-obsidian-border space-y-3 shadow-md">
                            <div class="flex items-center justify-between border-b border-obsidian-border/70 pb-2">
                                <span class="font-mono font-black text-cyber-cyan text-xs">{{ $ord->order_number }}</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold font-cairo
                                    {{ match($ord->status) {
                                        'pending_review' => 'bg-amber-500/20 text-amber-300 border border-amber-500/30',
                                        'confirmed_preparing' => 'bg-purple-500/20 text-purple-300 border border-purple-500/30',
                                        'out_for_delivery' => 'bg-cyan-500/20 text-cyan-300 border border-cyan-500/30',
                                        'delivered_warranty_active' => 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30',
                                        default => 'bg-slate-800 text-slate-400'
                                    } }}">
                                    {{ $ord->status_label_ar }}
                                </span>
                            </div>

                            <div class="flex items-start justify-between">
                                <div>
                                    <span class="text-xs font-bold text-white font-cairo block">{{ $ord->customer_name }}</span>
                                    <span class="text-[10px] text-slate-400 font-mono">{{ $ord->customer_phone }}</span>
                                </div>
                                <div class="text-left font-mono">
                                    <span class="text-sm font-black text-cyber-gold">{{ number_format($ord->total_amount, 2) }} EGP</span>
                                    <span class="text-[9px] font-cairo block text-emerald-400 font-bold">
                                        {{ $ord->payment_method === 'wallet' ? 'محفظة' : ($ord->payment_method === 'transfer' ? 'تحويل' : 'استلام') }}
                                    </span>
                                </div>
                            </div>

                            <div class="p-2.5 rounded-xl bg-obsidian-card border border-obsidian-border space-y-1 text-[11px] font-cairo">
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-400 text-[10px]">الأجهزة المطلوبة:</span>
                                    <span class="text-white font-bold">{{ count($ord->items_json ?? []) }} أصناف</span>
                                </div>
                                <div class="text-[10px] text-slate-400 truncate">
                                    {{ collect($ord->items_json)->pluck('name')->implode(', ') }}
                                </div>
                                @if($ord->serial_numbers_notes)
                                    <div class="pt-1 border-t border-obsidian-border/70 flex items-center justify-between text-[10px]">
                                        <span class="text-slate-500">السيريالات:</span>
                                        <span class="text-purple-300 font-mono truncate max-w-[170px]">{{ $ord->serial_numbers_notes }}</span>
                                    </div>
                                @endif
                            </div>

                            <div class="flex items-center gap-2 pt-1">
                                <button type="button" 
                                        @click="selectedOrder = {{ Js::from($ord) }}; updateOrderModal = true"
                                        class="flex-1 py-2 rounded-xl btn-citadel-primary text-xs font-bold font-cairo text-center">
                                    تحديث الحالة والسيريال ⚙️
                                </button>
                                @if($ord->customer_phone)
                                    <a href="tel:{{ $ord->customer_phone }}" class="p-2 rounded-xl bg-obsidian-card border border-obsidian-border text-slate-300 text-xs">
                                        📞
                                    </a>
                                    <a href="https://wa.me/2{{ preg_replace('/^0/', '', $ord->customer_phone) }}" target="_blank" class="p-2 rounded-xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-400 text-xs font-bold">
                                        💬
                                    </a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-slate-400 font-cairo text-xs bg-obsidian-surface rounded-2xl border border-obsidian-border">
                            لا توجد طلبات هاردوير في هذه الحالة.
                        </div>
                    @endforelse
                </div>

                @if($orders->hasPages())
                    <div class="p-4 border-t border-obsidian-border">
                        {{ $orders->links() }}
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- ===================================================================== --}}
    {{-- 5. ADD PRODUCT MODAL                                                  --}}
    {{-- ===================================================================== --}}
    <div x-show="addProductModal" 
         x-transition.opacity 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
         style="display: none;">
        <div class="relative w-full max-w-xl glass-obsidian rounded-3xl border border-cyber-cyan/50 p-6 sm:p-8 space-y-6 shadow-2xl overflow-y-auto max-h-[90vh]"
             @click.away="addProductModal = false">
            
            <div class="flex items-center justify-between border-b border-obsidian-border pb-3">
                <h3 class="text-base font-black text-white font-cairo">إضافة جهاز هاردوير جديد 📦</h3>
                <button type="button" @click="addProductModal = false" class="text-slate-400 hover:text-white text-lg">✕</button>
            </div>

            <form action="{{ route('admin.hardware.store_product') }}" method="POST" class="space-y-4 text-xs font-cairo">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-slate-300 font-bold">اسم الجهاز بالعربية *</label>
                        <input type="text" name="name_ar" required placeholder="طابعة فواتير حرارية 80mm..." class="w-full bg-obsidian-surface text-slate-200 rounded-xl px-4 py-2 border border-obsidian-border focus:border-cyber-cyan focus:outline-none">
                    </div>

                    <div class="space-y-1">
                        <label class="text-slate-300 font-bold">الكود التعريفي (SKU) *</label>
                        <input type="text" name="sku" required placeholder="HW-POS-99" class="w-full bg-obsidian-surface text-slate-200 uppercase font-mono rounded-xl px-4 py-2 border border-obsidian-border focus:border-cyber-cyan focus:outline-none">
                    </div>

                    <div class="space-y-1">
                        <label class="text-slate-300 font-bold">التصنيف *</label>
                        <select name="category" required class="w-full bg-obsidian-surface text-slate-200 rounded-xl px-4 py-2 border border-obsidian-border focus:border-cyber-cyan focus:outline-none">
                            <option value="pos_equipment">أجهزة كاشير ونقاط بيع</option>
                            <option value="storage_ram">هاردات SSD ورامات</option>
                            <option value="networking_security">شبكات وأجهزة بصمة وكاميرات</option>
                            <option value="workstations">أجهزة ولابتوبات أعمال</option>
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="text-slate-300 font-bold">الماركة والمصنع *</label>
                        <input type="text" name="brand" required placeholder="Xprinter, Dell, Samsung..." class="w-full bg-obsidian-surface text-slate-200 rounded-xl px-4 py-2 border border-obsidian-border focus:border-cyber-cyan focus:outline-none">
                    </div>

                    <div class="space-y-1">
                        <label class="text-slate-300 font-bold">السعر بالجنيه (EGP) *</label>
                        <input type="number" step="0.01" name="price" required placeholder="2500" class="w-full bg-obsidian-surface text-slate-200 font-mono rounded-xl px-4 py-2 border border-obsidian-border focus:border-cyber-cyan focus:outline-none">
                    </div>

                    <div class="space-y-1">
                        <label class="text-slate-300 font-bold">السعر القديم قبل الخصم (اختياري)</label>
                        <input type="number" step="0.01" name="old_price" placeholder="3000" class="w-full bg-obsidian-surface text-slate-200 font-mono rounded-xl px-4 py-2 border border-obsidian-border focus:border-cyber-cyan focus:outline-none">
                    </div>

                    <div class="space-y-1">
                        <label class="text-slate-300 font-bold">الكمية في المخزن *</label>
                        <input type="number" name="stock_qty" value="10" required class="w-full bg-obsidian-surface text-slate-200 font-mono rounded-xl px-4 py-2 border border-obsidian-border focus:border-cyber-cyan focus:outline-none">
                    </div>

                    <div class="space-y-1">
                        <label class="text-slate-300 font-bold">مدة الضمان (شهور) *</label>
                        <input type="number" name="warranty_months" value="12" required class="w-full bg-obsidian-surface text-slate-200 font-mono rounded-xl px-4 py-2 border border-obsidian-border focus:border-cyber-cyan focus:outline-none">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-slate-300 font-bold">وصف المنتج</label>
                    <textarea name="description_ar" rows="2" placeholder="وصف موجز للمنتج وطبيعة عمله..." class="w-full bg-obsidian-surface text-slate-200 rounded-xl p-3 border border-obsidian-border focus:border-cyber-cyan focus:outline-none"></textarea>
                </div>

                <div class="space-y-1">
                    <label class="text-slate-300 font-bold">المواصفات الفنية التفصيلية (اكتب كل سطر: المفتاح: القيمة)</label>
                    <textarea name="specs_raw" rows="3" placeholder="السرعة: 260 مم / ثانية&#10;المنافذ: USB + LAN&#10;القاطع: آلي 1.5 مليون قطعية" class="w-full bg-obsidian-surface text-slate-200 font-mono rounded-xl p-3 border border-obsidian-border focus:border-cyber-cyan focus:outline-none"></textarea>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="compatible_with_branderp" id="compat_check" value="1" checked class="rounded bg-obsidian-surface border-obsidian-border text-cyber-cyan">
                    <label for="compat_check" class="text-slate-300 font-bold">متوافق 100% مع منظومة BrandERP</label>
                </div>

                <div class="pt-3 border-t border-obsidian-border flex justify-end gap-3">
                    <button type="button" @click="addProductModal = false" class="px-4 py-2 rounded-xl bg-obsidian-surface text-slate-400 hover:text-white">إلغاء</button>
                    <button type="submit" class="px-6 py-2 rounded-xl btn-citadel-primary text-xs font-bold font-cairo shadow-neon-cyan">حفظ المنتج ⇥</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===================================================================== --}}
    {{-- 6. EDIT PRODUCT MODAL                                                 --}}
    {{-- ===================================================================== --}}
    <div x-show="editProductModal" 
         x-transition.opacity 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
         style="display: none;">
        <div class="relative w-full max-w-xl glass-obsidian rounded-3xl border border-cyber-cyan/50 p-6 sm:p-8 space-y-6 shadow-2xl overflow-y-auto max-h-[90vh]"
             @click.away="editProductModal = false">
            
            <div class="flex items-center justify-between border-b border-obsidian-border pb-3">
                <h3 class="text-base font-black text-white font-cairo">تعديل بيانات الجهاز (<span x-text="selectedProduct?.sku"></span>)</h3>
                <button type="button" @click="editProductModal = false" class="text-slate-400 hover:text-white text-lg">✕</button>
            </div>

            <form :action="'/admin/hardware/products/' + selectedProduct?.id" method="POST" class="space-y-4 text-xs font-cairo">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-slate-300 font-bold">اسم الجهاز بالعربية *</label>
                        <input type="text" name="name_ar" :value="selectedProduct?.name_ar" required class="w-full bg-obsidian-surface text-slate-200 rounded-xl px-4 py-2 border border-obsidian-border focus:border-cyber-cyan focus:outline-none">
                    </div>

                    <div class="space-y-1">
                        <label class="text-slate-300 font-bold">الكود التعريفي (SKU) *</label>
                        <input type="text" name="sku" :value="selectedProduct?.sku" required class="w-full bg-obsidian-surface text-slate-200 uppercase font-mono rounded-xl px-4 py-2 border border-obsidian-border focus:border-cyber-cyan focus:outline-none">
                    </div>

                    <div class="space-y-1">
                        <label class="text-slate-300 font-bold">التصنيف *</label>
                        <select name="category" x-model="selectedProduct.category" required class="w-full bg-obsidian-surface text-slate-200 rounded-xl px-4 py-2 border border-obsidian-border focus:border-cyber-cyan focus:outline-none">
                            <option value="pos_equipment">أجهزة كاشير ونقاط بيع</option>
                            <option value="storage_ram">هاردات SSD ورامات</option>
                            <option value="networking_security">شبكات وأجهزة بصمة وكاميرات</option>
                            <option value="workstations">أجهزة ولابتوبات أعمال</option>
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="text-slate-300 font-bold">الماركة والمصنع *</label>
                        <input type="text" name="brand" :value="selectedProduct?.brand" required class="w-full bg-obsidian-surface text-slate-200 rounded-xl px-4 py-2 border border-obsidian-border focus:border-cyber-cyan focus:outline-none">
                    </div>

                    <div class="space-y-1">
                        <label class="text-slate-300 font-bold">السعر بالجنيه (EGP) *</label>
                        <input type="number" step="0.01" name="price" :value="selectedProduct?.price" required class="w-full bg-obsidian-surface text-slate-200 font-mono rounded-xl px-4 py-2 border border-obsidian-border focus:border-cyber-cyan focus:outline-none">
                    </div>

                    <div class="space-y-1">
                        <label class="text-slate-300 font-bold">الكمية في المخزن *</label>
                        <input type="number" name="stock_qty" :value="selectedProduct?.stock_qty" required class="w-full bg-obsidian-surface text-slate-200 font-mono rounded-xl px-4 py-2 border border-obsidian-border focus:border-cyber-cyan focus:outline-none">
                    </div>

                    <div class="space-y-1">
                        <label class="text-slate-300 font-bold">مدة الضمان (شهور) *</label>
                        <input type="number" name="warranty_months" :value="selectedProduct?.warranty_months" required class="w-full bg-obsidian-surface text-slate-200 font-mono rounded-xl px-4 py-2 border border-obsidian-border focus:border-cyber-cyan focus:outline-none">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-slate-300 font-bold">وصف المنتج</label>
                    <textarea name="description_ar" rows="2" :value="selectedProduct?.description_ar" class="w-full bg-obsidian-surface text-slate-200 rounded-xl p-3 border border-obsidian-border focus:border-cyber-cyan focus:outline-none"></textarea>
                </div>

                <div class="pt-3 border-t border-obsidian-border flex justify-end gap-3">
                    <button type="button" @click="editProductModal = false" class="px-4 py-2 rounded-xl bg-obsidian-surface text-slate-400 hover:text-white">إلغاء</button>
                    <button type="submit" class="px-6 py-2 rounded-xl btn-citadel-primary text-xs font-bold font-cairo shadow-neon-cyan">تحديث التعديلات ⇥</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===================================================================== --}}
    {{-- 7. UPDATE ORDER & SERIAL NUMBERS MODAL                                --}}
    {{-- ===================================================================== --}}
    <div x-show="updateOrderModal" 
         x-transition.opacity 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
         style="display: none;">
        <div class="relative w-full max-w-lg glass-obsidian rounded-3xl border border-cyber-cyan/50 p-6 sm:p-8 space-y-6 shadow-2xl"
             @click.away="updateOrderModal = false">
            
            <div class="flex items-center justify-between border-b border-obsidian-border pb-3">
                <div class="space-y-1">
                    <h3 class="text-base font-black text-white font-cairo">تحديث شحنة وضمان طلب الهاردوير</h3>
                    <span class="text-xs font-mono text-cyber-cyan font-bold" x-text="selectedOrder?.order_number"></span>
                </div>
                <button type="button" @click="updateOrderModal = false" class="text-slate-400 hover:text-white text-lg">✕</button>
            </div>

            <form :action="'/admin/hardware/orders/' + selectedOrder?.id + '/status'" method="POST" class="space-y-4 text-xs font-cairo">
                @csrf
                <div class="space-y-1">
                    <label class="text-slate-300 font-bold">مرحلة الطلب والشحن (الـ 4 مراحل) *</label>
                    <select name="status" x-model="selectedOrder.status" required class="w-full bg-obsidian-surface text-slate-200 rounded-xl px-4 py-2.5 border border-obsidian-border focus:border-cyber-cyan focus:outline-none">
                        <option value="pending_review">1. قيد المراجعة والاعتماد</option>
                        <option value="confirmed_preparing">2. جاري التجهيز والفحص الهندسي</option>
                        <option value="out_for_delivery">3. خرج للتوصيل / مع المهندس الميداني</option>
                        <option value="delivered_warranty_active">4. تم التسليم وتفعيل الضمان الرسمي</option>
                        <option value="cancelled">ملغي</option>
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="text-slate-300 font-bold">حالة السداد</label>
                    <select name="payment_status" x-model="selectedOrder.payment_status" class="w-full bg-obsidian-surface text-slate-200 rounded-xl px-4 py-2 border border-obsidian-border focus:border-cyber-cyan focus:outline-none">
                        <option value="pending">معلق (Pending)</option>
                        <option value="paid">تم السداد (Paid)</option>
                        <option value="refunded">مسترجع (Refunded)</option>
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="text-slate-300 font-bold">تسجيل الأرقام التسلسلية للأجهزة (Serial Numbers) *</label>
                    <textarea name="serial_numbers_notes" rows="3" :value="selectedOrder?.serial_numbers_notes" placeholder="مثال: SN-XPR-882194 / SN-SUN-00912..." class="w-full bg-obsidian-surface text-slate-200 font-mono rounded-xl p-3 border border-obsidian-border focus:border-cyber-cyan focus:outline-none"></textarea>
                    <span class="text-[10px] text-slate-400">ستظهر هذه الأرقام للعميل في لوحة تحكمه وتُعتمد في وثيقة الضمان.</span>
                </div>

                <div class="space-y-1">
                    <label class="text-slate-300 font-bold">ملاحظات الإدارة والفنيين</label>
                    <textarea name="admin_notes" rows="2" :value="selectedOrder?.admin_notes" placeholder="ملاحظات شركة الشحن أو موعد التسليم..." class="w-full bg-obsidian-surface text-slate-200 rounded-xl p-3 border border-obsidian-border focus:border-cyber-cyan focus:outline-none"></textarea>
                </div>

                <div class="pt-3 border-t border-obsidian-border flex justify-end gap-3">
                    <button type="button" @click="updateOrderModal = false" class="px-4 py-2 rounded-xl bg-obsidian-surface text-slate-400 hover:text-white">إلغاء</button>
                    <button type="submit" class="px-6 py-2 rounded-xl btn-citadel-primary text-xs font-bold font-cairo shadow-neon-cyan">تحديث وإشعار العميل ⇥</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
