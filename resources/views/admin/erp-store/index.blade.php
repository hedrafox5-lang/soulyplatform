@extends('admin.layout')

@section('title', 'إدارة متجر وأوردرات BrandERP | مركز القيادة العليا')
@section('page-title', 'إدارة متجر وأوردرات BrandERP')

@section('admin-content')
<div class="space-y-8" x-data="{ currentTab: '{{ $activeTab }}', proposalDiscount: 25, proposalSelectedItems: [] }">

    {{-- ===================================================================== --}}
    {{-- 1. TOP METRICS STRIP                                                  --}}
    {{-- ===================================================================== --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="p-5 rounded-2xl glass-obsidian border border-cyber-cyan/40 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs text-slate-400 font-cairo block">إجمالي عناصر المتجر</span>
                <span class="text-2xl font-black font-mono text-cyber-cyan">{{ $counts['catalog_total'] }}</span>
            </div>
            <span class="text-2xl">🏪</span>
        </div>

        <div class="p-5 rounded-2xl glass-obsidian border border-purple-500/40 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs text-slate-400 font-cairo block">المميزات الدقيقة الحية</span>
                <span class="text-2xl font-black font-mono text-purple-300">{{ $counts['features_count'] }}</span>
            </div>
            <span class="text-2xl">⚡</span>
        </div>

        <div class="p-5 rounded-2xl glass-obsidian border border-amber-500/40 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs text-slate-400 font-cairo block">طلبات الشراء المعلقة</span>
                <span class="text-2xl font-black font-mono text-cyber-gold">{{ $counts['orders_pending'] }}</span>
            </div>
            <span class="text-2xl">⏳</span>
        </div>

        <div class="p-5 rounded-2xl glass-obsidian border border-rose-500/40 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs text-slate-400 font-cairo block">طلبات البرمجة الخاصة</span>
                <span class="text-2xl font-black font-mono text-rose-400">{{ $counts['custom_pending'] }}</span>
            </div>
            <span class="text-2xl">💡</span>
        </div>
    </div>

    {{-- ===================================================================== --}}
    {{-- 2. TAB CONTROLS NAVIGATION                                            --}}
    {{-- ===================================================================== --}}
    <div class="glass-obsidian rounded-2xl border border-obsidian-border p-2 flex items-center gap-2 overflow-x-auto scrollbar-none font-cairo text-xs font-bold">
        <a href="{{ route('admin.erp_store.index', ['tab' => 'catalog']) }}"
           class="px-5 py-2.5 rounded-xl transition-all flex items-center gap-2 shrink-0
           {{ $activeTab === 'catalog' ? 'bg-cyber-cyan text-obsidian-base font-black shadow-neon-cyan' : 'bg-obsidian-surface text-slate-300 hover:text-white' }}">
            <span>📦 الكتالوج والمميزات</span>
            <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold" :class="'{{ $activeTab }}' === 'catalog' ? 'bg-black/20 text-black' : 'bg-slate-800 text-slate-400'">{{ $counts['catalog_total'] }}</span>
        </a>

        <a href="{{ route('admin.erp_store.index', ['tab' => 'orders']) }}"
           class="px-5 py-2.5 rounded-xl transition-all flex items-center gap-2 shrink-0
           {{ $activeTab === 'orders' ? 'bg-cyber-cyan text-obsidian-base font-black shadow-neon-cyan' : 'bg-obsidian-surface text-slate-300 hover:text-white' }}">
            <span>📑 طلبات الشراء والتراخيص</span>
            @if($counts['orders_pending'] > 0)
                <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-amber-500 text-black animate-pulse">{{ $counts['orders_pending'] }} جديد</span>
            @endif
        </a>

        <a href="{{ route('admin.erp_store.index', ['tab' => 'custom_requests']) }}"
           class="px-5 py-2.5 rounded-xl transition-all flex items-center gap-2 shrink-0
           {{ $activeTab === 'custom_requests' ? 'bg-cyber-cyan text-obsidian-base font-black shadow-neon-cyan' : 'bg-obsidian-surface text-slate-300 hover:text-white' }}">
            <span>💡 طلبات البرمجة الخاصة</span>
            @if($counts['custom_pending'] > 0)
                <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-rose-500 text-white">{{ $counts['custom_pending'] }}</span>
            @endif
        </a>

        <a href="{{ route('admin.erp_store.index', ['tab' => 'proposals']) }}"
           class="px-5 py-2.5 rounded-xl transition-all flex items-center gap-2 shrink-0
           {{ $activeTab === 'proposals' ? 'bg-cyber-cyan text-obsidian-base font-black shadow-neon-cyan' : 'bg-obsidian-surface text-slate-300 hover:text-white' }}">
            <span>🎯 إرسال اقتراح مهندس المبيعات</span>
            <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-purple-500/20 text-purple-300">{{ $counts['proposals_sent'] }}</span>
        </a>
    </div>

    {{-- ===================================================================== --}}
    {{-- 3. TAB 1: CATALOG MANAGEMENT                                          --}}
    {{-- ===================================================================== --}}
    @if($activeTab === 'catalog')
        <div class="space-y-6">
            
            {{-- Filter Form --}}
            <form action="{{ route('admin.erp_store.index') }}" method="GET" class="glass-obsidian rounded-2xl border border-obsidian-border p-4 flex flex-col md:flex-row items-center justify-between gap-4">
                <input type="hidden" name="tab" value="catalog">

                <div class="flex items-center gap-3 w-full md:w-auto flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث بكود البند أو الاسم أو القسم..." class="w-full bg-obsidian-surface text-xs text-slate-200 placeholder-slate-500 rounded-xl px-4 py-2 border border-obsidian-border focus:border-cyber-cyan focus:outline-none font-cairo">
                    
                    <select name="tier" class="bg-obsidian-surface text-xs text-slate-200 rounded-xl px-4 py-2 border border-obsidian-border focus:border-cyber-cyan focus:outline-none font-cairo">
                        <option value="all">كافة الطبقات</option>
                        <option value="core" {{ request('tier') === 'core' ? 'selected' : '' }}>النواة (Core)</option>
                        <option value="module" {{ request('tier') === 'module' ? 'selected' : '' }}>موديولات (16)</option>
                        <option value="feature" {{ request('tier') === 'feature' ? 'selected' : '' }}>مميزات دقيقة (500)</option>
                        <option value="bundle" {{ request('tier') === 'bundle' ? 'selected' : '' }}>باقات مجمعة (6)</option>
                    </select>

                    <select name="department" class="bg-obsidian-surface text-xs text-slate-200 rounded-xl px-4 py-2 border border-obsidian-border focus:border-cyber-cyan focus:outline-none font-cairo">
                        <option value="all">كافة الأقسام</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept }}" {{ request('department') === $dept ? 'selected' : '' }}>{{ $dept }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit" class="px-5 py-2 rounded-xl btn-citadel-primary text-xs font-cairo font-bold">تطبيق الفلتر</button>
                    @if(request()->hasAny(['search', 'tier', 'department']))
                        <a href="{{ route('admin.erp_store.index', ['tab' => 'catalog']) }}" class="px-3 py-2 rounded-xl bg-obsidian-surface text-slate-400 text-xs font-cairo">إلغاء</a>
                    @endif
                </div>
            </form>

            {{-- Catalog Table & Tactical Mobile Action Cards --}}
            <div class="rounded-3xl glass-obsidian border border-obsidian-border overflow-hidden">
                
                <!-- Desktop Table (Hidden on Mobile) -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-right text-xs font-cairo">
                        <thead>
                            <tr class="border-b border-obsidian-border bg-obsidian-surface/60 text-slate-400">
                                <th class="p-4">الكود</th>
                                <th class="p-4">البند والاسم</th>
                                <th class="p-4">القسم / الطبقة</th>
                                <th class="p-4">السعر (EGP)</th>
                                <th class="p-4 text-center">فلاش ديل ⚡</th>
                                <th class="p-4 text-center">ترشيح مهندس ⭐</th>
                                <th class="p-4 text-center">نشط / مفعل</th>
                                <th class="p-4 text-left">تعديل سريع</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-obsidian-border/60">
                            @foreach($catalogItems as $item)
                                <tr class="hover:bg-obsidian-surface/40 transition-colors">
                                    <td class="p-4 font-mono font-bold text-cyber-cyan">{{ $item->code ?? 'N/A' }}</td>
                                    <td class="p-4">
                                        <span class="font-bold text-white block">{{ $item->name_ar }}</span>
                                        <span class="text-[11px] text-slate-400 line-clamp-1">{{ $item->short_desc_ar }}</span>
                                    </td>
                                    <td class="p-4">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-800 text-slate-300 border border-slate-700">
                                            {{ $item->department_ar ?? $item->tier }}
                                        </span>
                                    </td>
                                    <td class="p-4 font-mono font-bold text-cyber-gold">{{ number_format($item->price, 0) }}</td>
                                    
                                    {{-- Flash Deal Toggle --}}
                                    <td class="p-4 text-center">
                                        <form action="{{ route('admin.erp_store.toggle_catalog', $item) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="field" value="is_flash_deal">
                                            <button type="submit" class="px-2 py-1 rounded-lg text-[10px] font-bold {{ $item->is_flash_deal ? 'bg-rose-500/20 text-rose-300 border border-rose-500/40' : 'text-slate-500 hover:text-slate-300' }}">
                                                {{ $item->is_flash_deal ? '⚡ فلاش ديل' : 'عادي' }}
                                            </button>
                                        </form>
                                    </td>

                                    {{-- Sales Engineer Pick Toggle --}}
                                    <td class="p-4 text-center">
                                        <form action="{{ route('admin.erp_store.toggle_catalog', $item) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="field" value="is_sales_engineer_pick">
                                            <button type="submit" class="px-2 py-1 rounded-lg text-[10px] font-bold {{ $item->is_sales_engineer_pick ? 'bg-purple-500/20 text-purple-300 border border-purple-500/40' : 'text-slate-500 hover:text-slate-300' }}">
                                                {{ $item->is_sales_engineer_pick ? '⭐ مرشح' : 'عادي' }}
                                            </button>
                                        </form>
                                    </td>

                                    {{-- Active Toggle --}}
                                    <td class="p-4 text-center">
                                        <form action="{{ route('admin.erp_store.toggle_catalog', $item) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="field" value="is_active">
                                            <button type="submit" class="w-8 h-5 rounded-full p-0.5 transition-colors {{ $item->is_active ? 'bg-cyber-cyan' : 'bg-slate-700' }} inline-flex items-center">
                                                <span class="w-4 h-4 rounded-full bg-black transform transition-transform {{ $item->is_active ? 'translate-x-3' : 'translate-x-0' }}"></span>
                                            </button>
                                        </form>
                                    </td>

                                    {{-- Inline Price Edit Form --}}
                                    <td class="p-4 text-left">
                                        <form action="{{ route('admin.erp_store.update_catalog', $item) }}" method="POST" class="flex items-center justify-end gap-1.5">
                                            @csrf
                                            <input type="number" name="price" value="{{ (int)$item->price }}" class="w-20 bg-obsidian-surface text-slate-200 text-xs font-mono px-2 py-1 rounded border border-obsidian-border text-center">
                                            <button type="submit" class="px-2.5 py-1 rounded bg-slate-800 text-cyber-cyan hover:bg-slate-700 text-[10px] font-bold">حفظ</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Tactical Action Cards (Visible on < 768px only) -->
                <div class="md:hidden space-y-3 p-3">
                    @foreach($catalogItems as $item)
                        <div class="p-4 rounded-2xl bg-obsidian-surface border border-obsidian-border space-y-3 shadow-md">
                            <div class="flex items-center justify-between border-b border-obsidian-border/70 pb-2">
                                <span class="font-mono font-black text-cyber-cyan text-xs">{{ $item->code ?? 'N/A' }}</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-800 text-slate-300">
                                    {{ $item->department_ar ?? $item->tier }}
                                </span>
                            </div>

                            <div>
                                <h4 class="font-bold text-white text-sm font-cairo leading-snug">{{ $item->name_ar }}</h4>
                                <p class="text-[11px] text-slate-400 font-cairo mt-0.5 line-clamp-2">{{ $item->short_desc_ar }}</p>
                            </div>

                            <!-- Mobile Quick Price Edit -->
                            <form action="{{ route('admin.erp_store.update_catalog', $item) }}" method="POST" class="p-2.5 rounded-xl bg-obsidian-card border border-obsidian-border flex items-center justify-between gap-2">
                                @csrf
                                <span class="text-xs font-cairo text-slate-300 font-bold">السعر:</span>
                                <div class="flex items-center gap-1.5">
                                    <input type="number" name="price" value="{{ (int)$item->price }}" class="w-24 bg-obsidian-surface text-cyber-gold font-mono font-black text-xs px-2.5 py-1 rounded-lg border border-obsidian-border text-center">
                                    <span class="text-[10px] font-mono text-slate-400">EGP</span>
                                    <button type="submit" class="px-3 py-1 rounded-lg bg-cyber-cyan text-obsidian-base font-black text-[10px]">حفظ</button>
                                </div>
                            </form>

                            <!-- Toggles Row -->
                            <div class="grid grid-cols-3 gap-2 pt-1">
                                <form action="{{ route('admin.erp_store.toggle_catalog', $item) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="field" value="is_flash_deal">
                                    <button type="submit" class="w-full py-1.5 rounded-xl text-[10px] font-bold font-cairo text-center border
                                        {{ $item->is_flash_deal ? 'bg-rose-500/20 text-rose-300 border-rose-500/40' : 'bg-obsidian-card text-slate-400 border-obsidian-border' }}">
                                        ⚡ فلاش
                                    </button>
                                </form>

                                <form action="{{ route('admin.erp_store.toggle_catalog', $item) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="field" value="is_sales_engineer_pick">
                                    <button type="submit" class="w-full py-1.5 rounded-xl text-[10px] font-bold font-cairo text-center border
                                        {{ $item->is_sales_engineer_pick ? 'bg-purple-500/20 text-purple-300 border-purple-500/40' : 'bg-obsidian-card text-slate-400 border-obsidian-border' }}">
                                        ⭐ مرشح
                                    </button>
                                </form>

                                <form action="{{ route('admin.erp_store.toggle_catalog', $item) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="field" value="is_active">
                                    <button type="submit" class="w-full py-1.5 rounded-xl text-[10px] font-bold font-cairo text-center border
                                        {{ $item->is_active ? 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30' : 'bg-slate-800 text-slate-500 border-slate-700' }}">
                                        {{ $item->is_active ? '✓ نشط' : '✕ معطل' }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination Links --}}
                <div class="p-4 border-t border-obsidian-border bg-obsidian-surface/30">
                    {{ $catalogItems->links() }}
                </div>
            </div>

                {{-- Pagination Links --}}
                <div class="p-4 border-t border-obsidian-border bg-obsidian-surface/30">
                    {{ $catalogItems->links() }}
                </div>
            </div>

        </div>
    @endif

    {{-- ===================================================================== --}}
    {{-- 4. TAB 2: ORDERS & LICENSES MANAGEMENT                                --}}
    {{-- ===================================================================== --}}
    @if($activeTab === 'orders')
        <div class="space-y-6">
            <div class="rounded-3xl glass-obsidian border border-obsidian-border overflow-hidden">
                
                <!-- Desktop Table (Hidden on Mobile) -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-right text-xs font-cairo">
                        <thead>
                            <tr class="border-b border-obsidian-border bg-obsidian-surface/60 text-slate-400">
                                <th class="p-4">رقم الطلب</th>
                                <th class="p-4">العميل</th>
                                <th class="p-4">الهاتف</th>
                                <th class="p-4">العناصر</th>
                                <th class="p-4">المبلغ</th>
                                <th class="p-4">طريقة السداد</th>
                                <th class="p-4">مفتاح الترخيص</th>
                                <th class="p-4">الحالة</th>
                                <th class="p-4 text-left">إجراءات الاعتماد</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-obsidian-border/60">
                            @foreach($orders as $ord)
                                <tr class="hover:bg-obsidian-surface/40 transition-colors">
                                    <td class="p-4 font-mono font-bold text-cyber-cyan">{{ $ord->order_number }}</td>
                                    <td class="p-4">
                                        <span class="font-bold text-white block">{{ $ord->customer_name ?? ($ord->user->name ?? 'عميل زائر') }}</span>
                                        <span class="text-[10px] text-slate-400">{{ $ord->customer_email ?? ($ord->user->email ?? '') }}</span>
                                    </td>
                                    <td class="p-4 font-mono text-slate-300">{{ $ord->customer_phone ?? ($ord->user->phone ?? '—') }}</td>
                                    <td class="p-4">
                                        <span class="px-2 py-0.5 rounded text-[10px] bg-slate-800 text-slate-300">
                                            {{ count($ord->items_json ?? []) }} عنصر
                                        </span>
                                    </td>
                                    <td class="p-4 font-mono font-bold text-white">{{ number_format($ord->total_amount, 2) }} EGP</td>
                                    <td class="p-4 font-bold text-slate-300">{{ $ord->payment_method }}</td>
                                    <td class="p-4 font-mono text-[11px] text-cyber-gold select-all">{{ $ord->license_key ?? 'لم يتم الإصدار' }}</td>
                                    <td class="p-4">
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold
                                            {{ $ord->status === 'active' ? 'bg-emerald-500/20 text-emerald-400' : ($ord->status === 'cancelled' ? 'bg-rose-500/20 text-rose-400' : 'bg-amber-500/20 text-amber-300') }}">
                                            {{ $ord->status }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-left">
                                        @if($ord->status !== 'active')
                                            <form action="{{ route('admin.erp_store.order_status', $ord) }}" method="POST" class="inline-block">
                                                @csrf
                                                <input type="hidden" name="status" value="active">
                                                <button type="submit" class="px-3 py-1 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-[11px]">
                                                    اعتماد وتوليد الترخيص ✓
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-emerald-400 font-bold text-[11px]">معتمد ومفعل ✓</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Tactical Action Cards (Visible on < 768px only) -->
                <div class="md:hidden space-y-3 p-3">
                    @foreach($orders as $ord)
                        <div class="p-4 rounded-2xl bg-obsidian-surface border border-obsidian-border space-y-3 shadow-md">
                            <div class="flex items-center justify-between border-b border-obsidian-border/70 pb-2">
                                <span class="font-mono font-black text-cyber-cyan text-xs">{{ $ord->order_number }}</span>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold font-cairo
                                    {{ $ord->status === 'active' ? 'bg-emerald-500/20 text-emerald-400' : ($ord->status === 'cancelled' ? 'bg-rose-500/20 text-rose-400' : 'bg-amber-500/20 text-amber-300') }}">
                                    {{ $ord->status }}
                                </span>
                            </div>

                            <div class="flex items-start justify-between">
                                <div>
                                    <span class="text-xs font-bold text-white font-cairo block">{{ $ord->customer_name ?? ($ord->user->name ?? 'عميل زائر') }}</span>
                                    <span class="text-[10px] text-slate-400 font-mono">{{ $ord->customer_phone ?? ($ord->user->phone ?? '—') }}</span>
                                </div>
                                <div class="text-left font-mono">
                                    <span class="text-sm font-black text-cyber-gold">{{ number_format($ord->total_amount, 2) }} EGP</span>
                                    <span class="text-[9px] font-cairo text-slate-400 block">{{ $ord->payment_method }}</span>
                                </div>
                            </div>

                            <div class="p-2.5 rounded-xl bg-obsidian-card border border-obsidian-border space-y-1 text-[11px] font-cairo">
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-400 text-[10px]">العناصر المطلوبة:</span>
                                    <span class="text-white font-bold">{{ count($ord->items_json ?? []) }} عنصر</span>
                                </div>
                                <div class="flex items-center justify-between text-[10px]">
                                    <span class="text-slate-500">الترخيص:</span>
                                    <span class="text-cyber-cyan font-mono truncate max-w-[170px] select-all">{{ $ord->license_key ?? 'لم يصدر بعد' }}</span>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 pt-1">
                                @if($ord->status !== 'active')
                                    <form action="{{ route('admin.erp_store.order_status', $ord) }}" method="POST" class="flex-1">
                                        @csrf
                                        <input type="hidden" name="status" value="active">
                                        <button type="submit" class="w-full py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-black text-xs font-cairo shadow-sm text-center active:scale-95">
                                            اعتماد وتوليد الترخيص ✓
                                        </button>
                                    </form>
                                @else
                                    <div class="flex-1 py-2 text-center text-emerald-400 font-bold text-xs font-cairo bg-emerald-500/10 rounded-xl border border-emerald-500/20">
                                        تم الاعتماد وتفعيل الترخيص ✓
                                    </div>
                                @endif

                                @if($ord->customer_phone || ($ord->user && $ord->user->phone))
                                    @php $phone = $ord->customer_phone ?? $ord->user->phone; @endphp
                                    <a href="tel:{{ $phone }}" class="p-2 rounded-xl bg-obsidian-card border border-obsidian-border text-slate-300 text-xs">
                                        📞
                                    </a>
                                    <a href="https://wa.me/2{{ preg_replace('/^0/', '', $phone) }}" target="_blank" class="p-2 rounded-xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-400 text-xs font-bold">
                                        💬
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="p-4 border-t border-obsidian-border">
                    {{ $orders->links() }}
                </div>
            </div>
        </div>
    @endif

    {{-- ===================================================================== --}}
    {{-- 5. TAB 3: CUSTOM FEATURE PROGRAMMING REQUESTS                         --}}
    {{-- ===================================================================== --}}
    @if($activeTab === 'custom_requests')
        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($customRequests as $req)
                    <div class="p-6 rounded-3xl glass-obsidian border border-obsidian-border space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="px-2.5 py-0.5 rounded text-[10px] font-mono font-bold bg-purple-500/20 text-purple-300">
                                #REQ-{{ $req->id }} · {{ $req->industry }}
                            </span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold
                                {{ $req->status === 'approved' ? 'bg-emerald-500/20 text-emerald-400' : ($req->status === 'rejected' ? 'bg-rose-500/20 text-rose-400' : 'bg-amber-500/20 text-amber-300') }}">
                                {{ $req->status }}
                            </span>
                        </div>

                        <div class="space-y-1">
                            <h4 class="text-sm font-black text-white font-cairo">{{ $req->feature_title }}</h4>
                            <p class="text-xs text-slate-300 font-cairo leading-relaxed">{{ $req->feature_description }}</p>
                            <div class="text-[11px] text-slate-400 pt-1 font-mono">العميل: {{ $req->customer_name }} — {{ $req->customer_phone }}</div>
                        </div>

                        {{-- Reply Form --}}
                        <form action="{{ route('admin.erp_store.custom_reply', $req) }}" method="POST" class="pt-3 border-t border-obsidian-border space-y-2">
                            @csrf
                            <textarea name="admin_response" rows="2" placeholder="اكتب رد مهندس البرمجيات، والجدوى، والتكلفة المقترحة..." class="w-full bg-obsidian-surface text-slate-200 text-xs rounded-xl p-3 border border-obsidian-border focus:border-cyber-cyan focus:outline-none font-cairo">{{ $req->admin_response }}</textarea>
                            
                            <div class="flex items-center justify-between">
                                <select name="status" class="bg-obsidian-surface text-slate-200 text-xs rounded-lg px-3 py-1.5 border border-obsidian-border font-cairo">
                                    <option value="under_review" {{ $req->status === 'under_review' ? 'selected' : '' }}>قيد الدراسة الفنية</option>
                                    <option value="approved" {{ $req->status === 'approved' ? 'selected' : '' }}>موافقة واعتماد البرمجة</option>
                                    <option value="rejected" {{ $req->status === 'rejected' ? 'selected' : '' }}>اعتذار لعدم الجدوى</option>
                                </select>
                                <button type="submit" class="px-4 py-1.5 rounded-xl btn-citadel-primary text-xs font-bold font-cairo">إرسال الرد للعميل</button>
                            </div>
                        </form>
                    </div>
                @endforeach
            </div>
            <div class="p-4">
                {{ $customRequests->links() }}
            </div>
        </div>
    @endif

    {{-- ===================================================================== --}}
    {{-- 6. TAB 4: DISPATCH SALES ENGINEER PROPOSALS TO CLIENTS               --}}
    {{-- ===================================================================== --}}
    @if($activeTab === 'proposals')
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            {{-- Dispatch Form (Col 5) --}}
            <div class="lg:col-span-5 p-6 sm:p-8 rounded-3xl glass-obsidian border border-cyber-cyan/40 space-y-6">
                <div class="space-y-1">
                    <div class="inline-flex items-center gap-2 px-3 py-0.5 rounded-full text-[10px] font-mono font-bold bg-cyber-cyan/15 text-cyber-cyan border border-cyber-cyan/30">
                        SALES ENGINEER UPSELL DISPATCHER
                    </div>
                    <h3 class="text-lg font-black text-white font-cairo">إرسال اقتراح مخصص لعميل محدد</h3>
                    <p class="text-xs text-slate-400 font-cairo leading-relaxed">
                        اختر العميل والموديولات أو الباقات المرشحة له، وحدد نسبة خصم استثنائية لتصل إلى لوحة تحكمه فوراً.
                    </p>
                </div>

                <form action="{{ route('admin.erp_store.send_proposal') }}" method="POST" class="space-y-4 text-xs font-cairo">
                    @csrf
                    
                    <div class="space-y-1">
                        <label class="text-slate-300 font-bold">حدد العميل المستهدف *</label>
                        <select name="user_id" required class="w-full bg-obsidian-surface text-slate-200 rounded-xl px-4 py-2.5 border border-obsidian-border focus:border-cyber-cyan focus:outline-none">
                            <option value="">-- اختر من قائمة العملاء المسجلين --</option>
                            @foreach($clients as $c)
                                <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->email }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="text-slate-300 font-bold">عنوان الاقتراح / العرض الترويجي *</label>
                        <input type="text" name="title" required placeholder="مثال: ترقية استراتيجية لنشاطك: موديول الحسابات والمخازن بخصم 25%" class="w-full bg-obsidian-surface text-slate-200 rounded-xl px-4 py-2.5 border border-obsidian-border focus:border-cyber-cyan focus:outline-none">
                    </div>

                    <div class="space-y-1">
                        <label class="text-slate-300 font-bold">حدد الموديولات أو الباقات المرشحة *</label>
                        <div class="max-h-48 overflow-y-auto custom-scrollbar p-3 rounded-xl bg-obsidian-surface border border-obsidian-border space-y-2">
                            @foreach($proposalCatalogItems as $pItem)
                                <label class="flex items-center justify-between text-xs cursor-pointer hover:bg-obsidian-card p-1.5 rounded">
                                    <div class="flex items-center gap-2">
                                        <input type="checkbox" name="proposed_items[]" value="{{ $pItem->slug }}" class="text-cyber-cyan">
                                        <span class="text-white">{{ $pItem->name_ar }}</span>
                                    </div>
                                    <span class="font-mono text-cyber-gold">{{ number_format($pItem->price, 0) }} EGP</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-slate-300 font-bold">نسبة الخصم الاستثنائية (%) *</label>
                        <input type="number" name="discount_percent" value="25" min="0" max="90" required class="w-full bg-obsidian-surface text-slate-200 font-mono text-center text-sm font-bold rounded-xl px-4 py-2 border border-obsidian-border focus:border-cyber-cyan focus:outline-none">
                    </div>

                    <div class="space-y-1">
                        <label class="text-slate-300 font-bold">رسالة مهندس المبيعات المصاحبة</label>
                        <textarea name="notes" rows="2" placeholder="مرحباً أ/ فلان، قمنا بدراسة حجم مبيعاتك وننصحك بشدة بهذه الحزمة لتوفير 30 ساعة شهرياً..." class="w-full bg-obsidian-surface text-slate-200 rounded-xl p-3 border border-obsidian-border focus:border-cyber-cyan focus:outline-none"></textarea>
                    </div>

                    <button type="submit" class="w-full py-3 rounded-xl btn-citadel-primary text-xs font-bold font-cairo shadow-neon-cyan">
                        إرسال الاقتراح وإشعار العميل فوراً 🎯
                    </button>
                </form>
            </div>

            {{-- Recent Sent Proposals Table (Col 7) --}}
            <div class="lg:col-span-7 rounded-3xl glass-obsidian border border-obsidian-border p-6 space-y-4">
                <h3 class="text-base font-black text-white font-cairo">المقترحات المرسلة للعملاء مؤخراً:</h3>

                <div class="overflow-x-auto">
                    <table class="w-full text-right text-xs font-cairo">
                        <thead>
                            <tr class="border-b border-obsidian-border text-slate-400">
                                <th class="pb-2">العميل</th>
                                <th class="pb-2">عنوان الاقتراح</th>
                                <th class="pb-2">الخصم</th>
                                <th class="pb-2">القيمة بعد الخصم</th>
                                <th class="pb-2">الحالة</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-obsidian-border/60">
                            @foreach($proposals as $prop)
                                <tr>
                                    <td class="py-3 font-bold text-white">{{ $prop->user->name ?? 'عميل' }}</td>
                                    <td class="py-3 text-slate-300">{{ $prop->title }}</td>
                                    <td class="py-3 font-mono text-rose-400 font-bold">{{ $prop->discount_percent }}%</td>
                                    <td class="py-3 font-mono font-bold text-cyber-cyan">{{ number_format($prop->total_after_discount, 2) }} EGP</td>
                                    <td class="py-3">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-500/20 text-purple-300">
                                            {{ $prop->status }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    @endif

</div>
@endsection
