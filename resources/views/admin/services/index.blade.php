@extends('admin.layout')

@section('title', 'إدارة الخدمات والباقات والأيقونات CMS | سولي')
@section('page-title', 'إدارة الخدمات والباقات CMS')

@section('admin-content')
<div class="space-y-8" x-data="{
    openPackageModal: false,
    openIconModal: false,
    selectedService: null,
    setupPackage(srv) {
        this.selectedService = srv;
        this.openPackageModal = true;
    },
    setupIcon(srv) {
        this.selectedService = srv;
        this.openIconModal = true;
    }
}">

    <!-- Top Bar -->
    <div class="glass-obsidian rounded-3xl border border-obsidian-border p-6 sm:p-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="space-y-1">
            <h2 class="text-xl sm:text-2xl font-black text-white font-cairo flex items-center gap-2.5">
                <span class="w-3 h-3 rounded-full bg-cyber-cyan animate-pulse"></span>
                <span>كتالوج الخدمات والباقات والأيقونات التفاعلية (CMS)</span>
            </h2>
            <p class="text-xs text-slate-400 font-cairo">
                تعديل الأسعار والخصومات، إضافة باقات جديدة، وإدارة مصفوفة الأيقونات التفاعلية للقطاعات الستة.
            </p>
        </div>
    </div>

    <!-- Services Grid -->
    <div class="space-y-8">
        @foreach($services as $srv)
            <div class="p-6 sm:p-8 rounded-3xl glass-obsidian border border-obsidian-border space-y-6">
                
                <!-- Service Header -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-obsidian-border/80 pb-4">
                    <div class="flex items-center gap-3">
                        <span class="w-3.5 h-3.5 rounded-full" style="background-color: {{ $srv->accent_color }};"></span>
                        <div>
                            <h3 class="text-lg font-bold text-white font-cairo">{{ $srv->name }}</h3>
                            <div class="text-xs text-slate-400 font-mono">Slug: {{ $srv->slug }}</div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" @click="setupPackage({{ Js::from($srv) }})"
                                class="px-3.5 py-1.5 rounded-xl bg-obsidian-surface hover:bg-slate-700 text-cyber-cyan border border-cyber-cyan/40 text-xs font-bold font-cairo transition-all">
                            + إضافة باقة جديدة
                        </button>
                        <button type="button" @click="setupIcon({{ Js::from($srv) }})"
                                class="px-3.5 py-1.5 rounded-xl bg-obsidian-surface hover:bg-slate-700 text-cyber-gold border border-cyber-gold/40 text-xs font-bold font-cairo transition-all">
                            + إضافة أيقونة
                        </button>
                        <a href="{{ route('services.show', $srv->slug) }}" target="_blank" class="px-3 py-1.5 rounded-xl text-xs text-slate-400 hover:text-white transition-colors">
                            معاينة الصفحة ↗
                        </a>
                    </div>
                </div>

                <!-- Packages List in Service -->
                <div class="space-y-3">
                    <h4 class="text-xs font-bold text-slate-400 font-cairo">الباقات المسجلة ({{ $srv->packages->count() }}):</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($srv->packages as $pkg)
                            <div class="p-4 rounded-2xl bg-obsidian-surface border border-obsidian-border space-y-3 flex flex-col justify-between">
                                <div class="space-y-1">
                                    <div class="flex items-center justify-between">
                                        <span class="font-bold text-white text-xs font-cairo">{{ $pkg->name }}</span>
                                        <span class="text-[10px] font-mono text-cyber-cyan">{{ $pkg->slug }}</span>
                                    </div>
                                    <div class="text-lg font-black font-mono text-white">
                                        {{ number_format($pkg->price, 2) }} <span class="text-[10px] text-slate-400 font-normal">EGP / {{ $pkg->unit_label }}</span>
                                    </div>
                                    <div class="text-[11px] text-slate-400 font-cairo">
                                        الضمان: <strong class="text-cyber-gold font-mono">{{ $pkg->warranty_days }}</strong> <span class="font-cairo">يوم</span>
                                    </div>
                                </div>

                                <div class="pt-2 border-t border-obsidian-border/50 flex justify-end">
                                    <form action="{{ route('admin.services.packages.destroy', $pkg) }}" method="POST" onsubmit="return confirm('تأكيد حذف هذه الباقة؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-400 hover:text-rose-300 text-[11px] font-cairo">
                                            حذف الباقة ✕
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Icons Count Summary -->
                <div class="flex items-center justify-between text-xs text-slate-400 font-cairo pt-2 border-t border-obsidian-border/50">
                    <div>
                        إجمالي الأيقونات التفاعلية: <strong class="text-white font-mono">{{ $srv->icons->count() }}</strong> <span class="font-cairo">أيقونة</span>
                        (أنشطة: {{ $srv->icons->where('group_type', 'industry')->count() }} · مزايا: {{ $srv->icons->where('group_type', 'feature')->count() }})
                    </div>
                </div>

            </div>
        @endforeach
    </div>

    <!-- Create Package Modal -->
    <div x-show="openPackageModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-md flex items-center justify-center p-4 overflow-y-auto" style="display: none;">
        <div @click.away="openPackageModal = false" class="relative w-full max-w-lg glass-obsidian border border-cyber-cyan/40 rounded-3xl p-6 sm:p-8 space-y-5">
            <div class="flex items-center justify-between border-b border-obsidian-border pb-3">
                <h3 class="text-lg font-bold text-white font-cairo">
                    إضافة باقة لخدمة: <span class="text-cyber-cyan" x-text="selectedService?.name"></span>
                </h3>
                <button type="button" @click="openPackageModal = false" class="text-slate-400 hover:text-white">✕</button>
            </div>

            <form :action="`/admin/services/${selectedService?.id}/packages`" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 font-cairo mb-1">اسم الباقة</label>
                        <input type="text" name="name" required placeholder="باقة الانطلاق" class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-3 py-2 text-xs text-white font-cairo">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 font-cairo mb-1">Slug</label>
                        <input type="text" name="slug" required placeholder="starter" class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-3 py-2 text-xs text-white font-mono">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 font-cairo mb-1">السعر (EGP)</label>
                        <input type="number" step="0.01" name="price" required placeholder="850" class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-3 py-2 text-xs text-white font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 font-cairo mb-1">نظام المحاسبة</label>
                        <select name="billing_type" class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-3 py-2 text-xs text-white font-cairo">
                            <option value="monthly">شهري (monthly)</option>
                            <option value="yearly">سنوي (yearly)</option>
                            <option value="hourly">بالساعة (hourly)</option>
                            <option value="one_time">مرة واحدة (one_time)</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 font-cairo mb-1">تسمية الوحدة</label>
                        <input type="text" name="unit_label" placeholder="شهر / ساعة" value="شهر" class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-3 py-2 text-xs text-white font-cairo">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 font-cairo mb-1">أيام الضمان</label>
                        <input type="number" name="warranty_days" value="30" class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-3 py-2 text-xs text-white font-mono">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 font-cairo mb-1">المزايا (كل ميزة في سطر منفصل)</label>
                    <textarea name="features" rows="4" placeholder="كاشير أوفلاين فائق السرعة&#10;إدارة المخزون&#10;ربط الفاتورة الإلكترونية" class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl p-3 text-xs text-white font-cairo"></textarea>
                </div>

                <button type="submit" class="btn-citadel-primary w-full py-3 text-xs">
                    إضافة الباقة فوراً
                </button>
            </form>
        </div>
    </div>

    <!-- Create Icon Modal -->
    <div x-show="openIconModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-md flex items-center justify-center p-4 overflow-y-auto" style="display: none;">
        <div @click.away="openIconModal = false" class="relative w-full max-w-lg glass-obsidian border border-cyber-gold/40 rounded-3xl p-6 sm:p-8 space-y-5">
            <div class="flex items-center justify-between border-b border-obsidian-border pb-3">
                <h3 class="text-lg font-bold text-white font-cairo">
                    إضافة أيقونة تفاعلية لخدمة: <span class="text-cyber-gold" x-text="selectedService?.name"></span>
                </h3>
                <button type="button" @click="openIconModal = false" class="text-slate-400 hover:text-white">✕</button>
            </div>

            <form :action="`/admin/services/${selectedService?.id}/icons`" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 font-cairo mb-1">نوع العنصر</label>
                        <select name="group_type" class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-3 py-2 text-xs text-white font-cairo">
                            <option value="industry">نشاط / قطاع تجاري (industry)</option>
                            <option value="feature">ميزة تقنية (feature)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 font-cairo mb-1">العنوان</label>
                        <input type="text" name="title" required placeholder="سوبر ماركت وهايبر" class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-3 py-2 text-xs text-white font-cairo">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 font-cairo mb-1">اسم الأيقونة أو SVG</label>
                        <input type="text" name="icon_svg_or_name" required value="shopping-cart" class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-3 py-2 text-xs text-white font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 font-cairo mb-1">نص الشارة</label>
                        <input type="text" name="badge_text" placeholder="حل متكامل" class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl px-3 py-2 text-xs text-white font-cairo">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 font-cairo mb-1">ملخص نافذة التفاصيل السريعة (0ms Popup)</label>
                    <textarea name="popup_summary" rows="3" required placeholder="تخصيص كامل لنقاط البيع السريعة وربط الموازين الباركود..." class="w-full bg-obsidian-surface border border-obsidian-border rounded-xl p-3 text-xs text-white font-cairo"></textarea>
                </div>

                <button type="submit" class="btn-citadel-gold w-full py-3 text-xs">
                    إضافة الأيقونة التفاعلية
                </button>
            </form>
        </div>
    </div>

</div>
@endsection
