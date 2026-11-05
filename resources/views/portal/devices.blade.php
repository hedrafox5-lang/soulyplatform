@extends('portal.layout')

@section('title', 'أجهزتي وإدارة الصيانة الهندسية | سولي')
@section('page-title', 'أجهزتي وصيانتها')

@section('portal-content')
@php
    $devices = $devices ?? collect();
    $wallet = $wallet ?? (auth()->check() ? auth()->user()->walletOrCreate() : null);
@endphp
<div class="space-y-6 sm:space-y-8" x-data="{
    openAddModal: false,
    openMaintenanceModal: false,
    openEditModal: false,
    selectedDevice: null,
    maintenanceType: 'on_site_visit',
    maintenanceHours: 3,
    hourlyRate: 400,
    selectForMaintenance(device) {
        this.selectedDevice = device;
        this.openMaintenanceModal = true;
    },
    selectForEdit(device) {
        this.selectedDevice = device;
        this.openEditModal = true;
    },
    get calculatedCost() {
        return (this.maintenanceHours * this.hourlyRate).toFixed(2);
    }
}">

    <!-- 1. TOP OVERVIEW & ACTIONS BANNER -->
    <div class="relative overflow-hidden rounded-3xl glass-obsidian border border-cyber-cyan/40 p-5 sm:p-7 lg:p-8 shadow-[0_4px_30px_rgba(0,245,212,0.1)]">
        <div class="absolute -top-16 -left-16 w-64 h-64 bg-cyber-cyan/15 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-5 sm:gap-6">
            <div class="space-y-2 text-right">
                <span class="px-3 py-1 rounded-full text-xs font-mono font-bold bg-cyber-cyan/15 text-cyber-cyan border border-cyber-cyan/30">
                    CLIENT HARDWARE VAULT & MAINTENANCE
                </span>
                <h2 class="text-lg sm:text-2xl font-black text-white font-cairo">
                    خزينة أجهزتي وجلسات الصيانة المعتمدة 💻
                </h2>
                <p class="text-xs text-slate-300 font-cairo max-w-xl leading-relaxed">
                    سجل أجهزتك التقنية وأنظمة الكاشير، وتابع تقارير الصيانة الدورية، واطلب زيارة مهندس صيانة ميداني أو تسليم الجهاز لمقر الشركة بسهولة وأمان.
                </p>
            </div>

            <button type="button" @click="openAddModal = true"
                    class="px-5 py-3 rounded-2xl btn-citadel-primary text-xs sm:text-sm font-cairo font-extrabold shadow-neon-cyan flex items-center gap-2 cursor-pointer shrink-0 active:scale-95 transition-all">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                <span>+ تسجيل جهاز جديد</span>
            </button>
        </div>
    </div>

    <!-- 2. QUICK METRICS STATS -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="rounded-2xl glass-obsidian border border-obsidian-border p-4 sm:p-5 flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-xs text-slate-400 font-cairo block">إجمالي الأجهزة المسجلة</span>
                <span class="text-2xl font-black font-mono text-white">{{ $devices->count() }}</span>
            </div>
            <div class="w-11 h-11 rounded-xl bg-cyber-cyan/15 border border-cyber-cyan/30 text-cyber-cyan flex items-center justify-center text-lg">
                💻
            </div>
        </div>

        <div class="rounded-2xl glass-obsidian border border-obsidian-border p-4 sm:p-5 flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-xs text-slate-400 font-cairo block">أجهزة سليمة وجاهزة للعمل</span>
                <span class="text-2xl font-black font-mono text-emerald-400">{{ $devices->where('status', 'healthy')->count() }}</span>
            </div>
            <div class="w-11 h-11 rounded-xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-400 flex items-center justify-center text-lg">
                ✓
            </div>
        </div>

        <div class="rounded-2xl glass-obsidian border border-obsidian-border p-4 sm:p-5 flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-xs text-slate-400 font-cairo block">أجهزة تحتاج صيانة أو قيد الإصلاح</span>
                <span class="text-2xl font-black font-mono text-amber-400">{{ $devices->whereIn('status', ['needs_maintenance', 'visit_requested', 'in_company_repair'])->count() }}</span>
            </div>
            <div class="w-11 h-11 rounded-xl bg-amber-500/15 border border-amber-500/30 text-amber-400 flex items-center justify-center text-lg">
                🔧
            </div>
        </div>
    </div>

    @if (isset($errors) && $errors->any())
        <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/40 text-xs text-rose-300 font-cairo space-y-1">
            @foreach ($errors->all() as $error)
                <div class="flex items-center gap-2">
                    <span class="text-rose-400 font-bold">⚠</span>
                    <span>{{ $error }}</span>
                </div>
            @endforeach
        </div>
    @endif

    <!-- 3. DEVICES GRID -->
    @if($devices->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($devices as $device)
                <div class="rounded-3xl glass-obsidian border border-obsidian-border hover:border-cyber-cyan/40 p-5 sm:p-6 space-y-4 flex flex-col justify-between transition-all group">
                    <div class="space-y-3">
                        <div class="flex items-start justify-between gap-3">
                            <div class="space-y-1 min-w-0">
                                <h3 class="text-base font-black text-white font-cairo truncate group-hover:text-cyber-cyan transition-colors">
                                    {{ $device->model_number }}
                                </h3>
                                @if(!empty($device->serial_number))
                                    <div class="text-[11px] font-mono text-slate-400 truncate">
                                        سيريال: <span class="text-cyber-cyan font-bold">{{ $device->serial_number }}</span>
                                    </div>
                                @endif
                            </div>

                            <span class="px-2.5 py-1 rounded-full text-[10px] font-cairo font-bold shrink-0
                                @if($device->status === 'healthy') bg-emerald-500/20 text-emerald-400 border border-emerald-500/30
                                @elseif($device->status === 'visit_requested') bg-cyber-cyan/20 text-cyber-cyan border border-cyber-cyan/30
                                @elseif($device->status === 'in_company_repair') bg-purple-500/20 text-purple-300 border border-purple-500/30
                                @elseif($device->status === 'warranty_active') bg-emerald-500/20 text-emerald-300 border border-emerald-500/30
                                @else bg-amber-500/20 text-amber-300 border border-amber-500/30
                                @endif">
                                {{ match($device->status) {
                                    'healthy' => 'سليم ويعمل ✓',
                                    'visit_requested' => 'تم طلب زيارة صيانة 🚀',
                                    'in_company_repair' => 'قيد الصيانة بالشركة ⚙️',
                                    'warranty_active' => 'ضمان ساري 🛡️',
                                    default => 'يحتاج فحص وصيانة 🔧'
                                } }}
                            </span>
                        </div>

                        <p class="text-xs text-slate-300 font-cairo leading-relaxed bg-obsidian-surface/60 p-3 rounded-xl border border-obsidian-border/80">
                            {{ Str::limit($device->details, 140) }}
                        </p>

                        @if(!empty($device->images_json) && is_array($device->images_json))
                            <div class="flex items-center gap-2 overflow-x-auto py-1">
                                @foreach($device->images_json as $img)
                                    <a href="{{ asset('storage/' . $img) }}" target="_blank" class="w-12 h-12 rounded-lg overflow-hidden border border-obsidian-border hover:border-cyber-cyan shrink-0">
                                        <img src="{{ asset('storage/' . $img) }}" alt="Device Photo" class="w-full h-full object-cover">
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="pt-3 border-t border-obsidian-border flex items-center justify-between gap-2 flex-wrap">
                        <button type="button" 
                                @click="selectForMaintenance({{ json_encode($device) }})"
                                class="px-3.5 py-2 rounded-xl btn-citadel-primary text-xs font-cairo font-bold flex items-center gap-1.5 shadow-neon-cyan cursor-pointer">
                            <span>🔧</span>
                            <span>طلب صيانة</span>
                        </button>

                        <div class="flex items-center gap-1.5">
                            <button type="button" 
                                    @click="selectForEdit({{ json_encode($device) }})"
                                    title="تعديل بيانات الجهاز"
                                    class="p-2 rounded-xl bg-obsidian-surface hover:bg-obsidian-card text-slate-300 hover:text-white border border-obsidian-border transition-colors cursor-pointer text-xs">
                                ✏️
                            </button>

                            @if(!in_array($device->status, ['visit_requested', 'in_company_repair']))
                                <form action="{{ route('portal.devices.destroy', $device) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا الجهاز نهائياً من قائمتك؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            title="حذف الجهاز"
                                            class="p-2 rounded-xl bg-obsidian-surface hover:bg-rose-500/20 text-slate-400 hover:text-rose-400 border border-obsidian-border hover:border-rose-500/40 transition-colors cursor-pointer text-xs">
                                        🗑️
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="p-10 rounded-3xl glass-obsidian border border-obsidian-border text-center space-y-4">
            <div class="w-16 h-16 mx-auto rounded-2xl bg-cyber-cyan/10 border border-cyber-cyan/30 text-cyber-cyan flex items-center justify-center text-3xl">
                💻
            </div>
            <div class="space-y-1">
                <h3 class="text-base font-bold text-white font-cairo">لا توجد أجهزة مسجلة في خزينتك حتى الآن</h3>
                <p class="text-xs text-slate-400 font-cairo max-w-md mx-auto">
                    سجل أجهزة الكاشير، الطابعات، أجهزة الباركود، أو السيرفرات الخاصة بك لتتمكن من جدولة الصيانة وطلب المهندس الميداني بضغطة زر.
                </p>
            </div>
            <button type="button" @click="openAddModal = true"
                    class="px-6 py-2.5 rounded-xl btn-citadel-primary text-xs font-cairo font-bold shadow-neon-cyan cursor-pointer">
                + تسجيل أول جهاز الآن
            </button>
        </div>
    @endif

    <!-- 4. MODAL: ADD NEW DEVICE -->
    <div x-show="openAddModal" x-cloak class="fixed inset-0 z-[99999] bg-black/80 backdrop-blur-md flex items-end sm:items-center justify-center p-0 sm:p-4 overflow-y-auto">
        <div @click.away="openAddModal = false" class="relative w-full max-w-lg glass-obsidian border border-cyber-cyan/40 rounded-t-3xl sm:rounded-3xl p-5 sm:p-7 space-y-5 shadow-2xl max-h-[90dvh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-obsidian-border pb-3">
                <h3 class="text-base sm:text-lg font-black text-white font-cairo">💻 تسجيل جهاز جديد في الخزينة</h3>
                <button type="button" @click="openAddModal = false" class="text-slate-400 hover:text-white p-1">✕</button>
            </div>

            <form action="{{ route('portal.devices.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs font-cairo text-right">
                @csrf
                <div class="space-y-1">
                    <label class="text-slate-300 font-bold block">اسم وموديل الجهاز *</label>
                    <input type="text" name="model_number" required placeholder="مثال: POS Sunmi T2s / طابعة إيصالات Xprinter" class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-3.5 py-2.5 text-white focus:border-cyber-cyan focus:outline-none">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-slate-300 font-bold block">الرقم التسلسلي (Serial Number)</label>
                        <input type="text" name="serial_number" placeholder="اختياري أو ملصق خلف الجهاز" class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-3.5 py-2.5 text-white font-mono focus:border-cyber-cyan focus:outline-none">
                    </div>
                    <div class="space-y-1">
                        <label class="text-slate-300 font-bold block">الحالة الراهنة للجهاز</label>
                        <select name="status" class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-3 py-2.5 text-white focus:border-cyber-cyan focus:outline-none">
                            <option value="needs_maintenance">يحتاج صيانة وفحص فني</option>
                            <option value="healthy">سليم ويعمل بكفاءة</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-slate-300 font-bold block">مواصفات الجهاز أو تفاصيل العطل *</label>
                    <textarea name="details" rows="3" required placeholder="اكتب وصفاً موجزاً لمواصفات الجهاز أو تفاصيل المشكلة التي تود فحصها..." class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-3.5 py-2.5 text-white focus:border-cyber-cyan focus:outline-none"></textarea>
                </div>

                <div class="space-y-1">
                    <label class="text-slate-300 font-bold block">صور توضيحية للجهاز أو العطل (اختياري - حتى 5 صور)</label>
                    <input type="file" name="images[]" multiple accept="image/*" class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-3.5 py-2 text-slate-300 text-[11px] focus:outline-none">
                </div>

                <div class="pt-3 border-t border-obsidian-border flex items-center justify-end gap-2">
                    <button type="button" @click="openAddModal = false" class="px-4 py-2.5 rounded-xl bg-obsidian-surface text-slate-300 font-bold">إلغاء</button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl btn-citadel-primary font-black shadow-neon-cyan">حفظ وتسجيل الجهاز ✓</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 5. MODAL: REQUEST MAINTENANCE -->
    <div x-show="openMaintenanceModal" x-cloak class="fixed inset-0 z-[99999] bg-black/80 backdrop-blur-md flex items-end sm:items-center justify-center p-0 sm:p-4 overflow-y-auto">
        <div @click.away="openMaintenanceModal = false" class="relative w-full max-w-lg glass-obsidian border border-cyber-cyan/40 rounded-t-3xl sm:rounded-3xl p-5 sm:p-7 space-y-5 shadow-2xl max-h-[90dvh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-obsidian-border pb-3">
                <div>
                    <h3 class="text-base sm:text-lg font-black text-white font-cairo">🔧 طلب صيانة هندسية للجهاز</h3>
                    <p class="text-xs text-cyber-cyan font-mono" x-text="selectedDevice?.model_number"></p>
                </div>
                <button type="button" @click="openMaintenanceModal = false" class="text-slate-400 hover:text-white p-1">✕</button>
            </div>

            <form :action="'/portal/devices/' + (selectedDevice?.id || '') + '/maintenance'" method="POST" class="space-y-4 text-xs font-cairo text-right">
                @csrf
                <div class="space-y-1.5">
                    <label class="text-slate-300 font-bold block">نوع الصيانة المطلوبة *</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <label class="p-3 rounded-xl border flex items-center gap-2 cursor-pointer transition-all"
                               :class="maintenanceType === 'on_site_visit' ? 'border-cyber-cyan bg-cyber-cyan/15 text-white' : 'border-obsidian-border bg-obsidian-surface text-slate-300'">
                            <input type="radio" name="maintenance_type" value="on_site_visit" x-model="maintenanceType" class="hidden">
                            <span>🚀</span>
                            <span class="font-bold">زيارة مهندس للموقع</span>
                        </label>
                        <label class="p-3 rounded-xl border flex items-center gap-2 cursor-pointer transition-all"
                               :class="maintenanceType === 'in_company' ? 'border-cyber-cyan bg-cyber-cyan/15 text-white' : 'border-obsidian-border bg-obsidian-surface text-slate-300'">
                            <input type="radio" name="maintenance_type" value="in_company" x-model="maintenanceType" class="hidden">
                            <span>🏢</span>
                            <span class="font-bold">صيانة بمقر سولي</span>
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-slate-300 font-bold block">الموعد المقترح *</label>
                        <input type="date" name="scheduled_at" required min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}" class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-3 py-2 text-white focus:border-cyber-cyan focus:outline-none">
                    </div>

                    <div class="space-y-1" x-show="maintenanceType === 'on_site_visit'">
                        <label class="text-slate-300 font-bold block">عدد الساعات التقديرية (الحد الأدنى 3)</label>
                        <input type="number" name="hours" min="3" max="12" x-model="maintenanceHours" class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-3 py-2 text-white font-mono focus:border-cyber-cyan focus:outline-none">
                    </div>

                    <div class="space-y-1" x-show="maintenanceType === 'in_company'">
                        <label class="text-slate-300 font-bold block">طريقة تسليم الجهاز</label>
                        <input type="text" name="delivery_method" placeholder="تسليم شخصي بمقر المعادي أو شركة شحن" class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-3 py-2 text-white focus:border-cyber-cyan focus:outline-none">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-slate-300 font-bold block">العنوان بالتفصيل *</label>
                    <input type="text" name="address" required placeholder="المدينة، الحي، اسم الفرع أو عنوان الشركة" class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-3.5 py-2.5 text-white focus:border-cyber-cyan focus:outline-none">
                </div>

                <div class="space-y-1">
                    <label class="text-slate-300 font-bold block">تفاصيل العطل أو متطلبات الفحص *</label>
                    <textarea name="issue_notes" rows="3" required placeholder="اشرح المشكلة بالتفصيل لمساعدة المهندس في تحضير قطع الغيار والأدوات المناسبة..." class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-3.5 py-2.5 text-white focus:border-cyber-cyan focus:outline-none"></textarea>
                </div>

                <div class="p-3.5 rounded-xl bg-obsidian-surface border border-cyber-cyan/30 flex items-center justify-between text-xs" x-show="maintenanceType === 'on_site_visit'">
                    <span class="text-slate-300">تكلفة الزيارة الهندسية المقدرة:</span>
                    <strong class="text-cyber-cyan font-mono text-sm" x-text="calculatedCost + ' EGP'"></strong>
                </div>

                <div class="pt-3 border-t border-obsidian-border flex items-center justify-end gap-2">
                    <button type="button" @click="openMaintenanceModal = false" class="px-4 py-2.5 rounded-xl bg-obsidian-surface text-slate-300 font-bold">إلغاء</button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl btn-citadel-primary font-black shadow-neon-cyan">تأكيد إرسال طلب الصيانة ✓</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 6. MODAL: EDIT DEVICE -->
    <div x-show="openEditModal" x-cloak class="fixed inset-0 z-[99999] bg-black/80 backdrop-blur-md flex items-end sm:items-center justify-center p-0 sm:p-4 overflow-y-auto">
        <div @click.away="openEditModal = false" class="relative w-full max-w-lg glass-obsidian border border-cyber-cyan/40 rounded-t-3xl sm:rounded-3xl p-5 sm:p-7 space-y-5 shadow-2xl max-h-[90dvh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-obsidian-border pb-3">
                <h3 class="text-base sm:text-lg font-black text-white font-cairo">✏️ تعديل بيانات الجهاز</h3>
                <button type="button" @click="openEditModal = false" class="text-slate-400 hover:text-white p-1">✕</button>
            </div>

            <form :action="'/portal/devices/' + (selectedDevice?.id || '')" method="POST" class="space-y-4 text-xs font-cairo text-right">
                @csrf
                @method('PUT')
                <div class="space-y-1">
                    <label class="text-slate-300 font-bold block">اسم وموديل الجهاز *</label>
                    <input type="text" name="model_number" required :value="selectedDevice?.model_number" class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-3.5 py-2.5 text-white focus:border-cyber-cyan focus:outline-none">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-slate-300 font-bold block">الرقم التسلسلي</label>
                        <input type="text" name="serial_number" :value="selectedDevice?.serial_number" class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-3.5 py-2.5 text-white font-mono focus:border-cyber-cyan focus:outline-none">
                    </div>
                    <div class="space-y-1">
                        <label class="text-slate-300 font-bold block">حالة الجهاز</label>
                        <select name="status" class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-3 py-2.5 text-white focus:border-cyber-cyan focus:outline-none">
                            <option value="needs_maintenance" :selected="selectedDevice?.status === 'needs_maintenance'">يحتاج صيانة</option>
                            <option value="healthy" :selected="selectedDevice?.status === 'healthy'">سليم ويعمل</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="text-slate-300 font-bold block">المواصفات وتفاصيل الجهاز *</label>
                    <textarea name="details" rows="3" required :value="selectedDevice?.details" class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-3.5 py-2.5 text-white focus:border-cyber-cyan focus:outline-none"></textarea>
                </div>

                <div class="pt-3 border-t border-obsidian-border flex items-center justify-end gap-2">
                    <button type="button" @click="openEditModal = false" class="px-4 py-2.5 rounded-xl bg-obsidian-surface text-slate-300 font-bold">إلغاء</button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl btn-citadel-primary font-black shadow-neon-cyan">تحديث البيانات ✓</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection