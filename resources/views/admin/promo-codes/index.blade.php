@extends('admin.layout')

@section('title', 'أكواد الخصم والعروض الترويجية | سولي')

@section('admin-content')
<div class="space-y-6" x-data="{ showModal: false }">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-900 border border-slate-800 p-6 rounded-3xl">
        <div>
            <h1 class="text-xl font-black text-white flex items-center gap-2">
                <span>🏷️ أكواد الخصم والحملات الترويجية</span>
            </h1>
            <p class="text-xs text-slate-400 mt-1">إنشاء وإدارة كوبونات الخصم للمتاجر، أكواد الصيانة، ورصيد البونص المشروط.</p>
        </div>
        <button type="button" @click="showModal = true"
                class="px-5 py-2.5 rounded-2xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-bold text-xs transition-all shadow-lg shadow-cyan-500/20">
            + إنشاء كود جديد
        </button>
    </div>

    <!-- Codes Table -->
    <div class="bg-slate-900 border border-slate-800 rounded-3xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead>
                    <tr class="bg-slate-950/70 border-b border-slate-800 text-slate-400">
                        <th class="py-3.5 px-4 font-bold">الكود</th>
                        <th class="py-3.5 px-4 font-bold">النوع</th>
                        <th class="py-3.5 px-4 font-bold">القيمة</th>
                        <th class="py-3.5 px-4 font-bold">الحد الأدنى للطلب</th>
                        <th class="py-3.5 px-4 font-bold">مرات الاستخدام</th>
                        <th class="py-3.5 px-4 font-bold">الحالة</th>
                        <th class="py-3.5 px-4 font-bold text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($promoCodes as $code)
                    <tr class="hover:bg-slate-800/30 transition-colors">
                        <td class="py-3 px-4 font-mono font-bold text-cyan-400">
                            {{ $code->code }}
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-0.5 rounded-lg bg-slate-800 text-slate-300 text-[10px]">
                                {{ $code->type === 'percentage' ? 'نسبة مئوية %' : ($code->type === 'bonus_balance' ? 'رصيد بونص' : 'مبلغ مقطوع') }}
                            </span>
                        </td>
                        <td class="py-3 px-4 font-mono font-bold text-white">
                            {{ $code->type === 'percentage' ? ($code->value . '%') : ($code->value . ' ج.م') }}
                        </td>
                        <td class="py-3 px-4 font-mono text-slate-400">
                            {{ $code->min_order_amount ? ($code->min_order_amount . ' ج.م') : '-' }}
                        </td>
                        <td class="py-3 px-4 font-mono text-slate-400">
                            {{ $code->usages_count }} / {{ $code->max_uses_total ?: '∞' }}
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $code->is_active ? 'bg-emerald-950 border border-emerald-800 text-emerald-400' : 'bg-slate-800 text-slate-500' }}">
                                {{ $code->is_active ? 'نشط' : 'معطل' }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <div class="inline-flex items-center gap-1.5">
                                <form method="POST" action="{{ route('admin.promo_codes.toggle', $code->id) }}">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-[11px] font-bold">
                                        {{ $code->is_active ? 'تعطيل' : 'تفعيل' }}
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.promo_codes.destroy', $code->id) }}" onsubmit="return confirm('هل أنت متأكد من حذف هذا الكود؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1 rounded-lg bg-rose-950/60 hover:bg-rose-900 text-rose-400 text-[11px] font-bold">
                                        حذف
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-slate-500">لا توجد أكواد خصم مسجلة حالياً.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create Modal -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
        <div @click.away="showModal = false" class="w-full max-w-md bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-2xl relative text-right space-y-4">
            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                <span>➕ إنشاء كود ترويجي جديد</span>
            </h3>

            <form method="POST" action="{{ route('admin.promo_codes.store') }}" class="space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block text-slate-400 mb-1">رمز الكود (Code):</label>
                    <input type="text" name="code" required placeholder="SOULY2026" class="w-full bg-slate-950 border border-slate-800 rounded-xl p-2.5 text-white uppercase font-mono">
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-slate-400 mb-1">النوع:</label>
                        <select name="type" required class="w-full bg-slate-950 border border-slate-800 rounded-xl p-2.5 text-white">
                            <option value="fixed">مبلغ مقطوع (ج.م)</option>
                            <option value="percentage">نسبة مئوية (%)</option>
                            <option value="bonus_balance">شحن رصيد بونص</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-slate-400 mb-1">القيمة:</label>
                        <input type="number" step="0.01" name="value" required placeholder="50" class="w-full bg-slate-950 border border-slate-800 rounded-xl p-2.5 text-white font-mono">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-slate-400 mb-1">الحد الأدنى للطلب:</label>
                        <input type="number" step="0.01" name="min_order_amount" placeholder="0.00" class="w-full bg-slate-950 border border-slate-800 rounded-xl p-2.5 text-white font-mono">
                    </div>
                    <div>
                        <label class="block text-slate-400 mb-1">مرات الاستخدام لكل عميل:</label>
                        <input type="number" name="max_uses_per_user" value="1" required class="w-full bg-slate-950 border border-slate-800 rounded-xl p-2.5 text-white font-mono">
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="showModal = false" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-400 text-xs font-bold">إلغاء</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 text-xs font-bold shadow-md">حفظ الكود</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection