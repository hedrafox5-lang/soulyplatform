<div x-data="{ open: false, bookingId: null }" 
     @open-parts-modal.window="open = true; bookingId = $event.detail.id"
     x-show="open" 
     x-cloak 
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
    
    <div @click.away="open = false" class="w-full max-w-md bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-2xl relative text-right">
        <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-4">
            <span class="text-xs font-mono text-amber-400 bg-amber-950/60 px-2.5 py-1 rounded-lg border border-amber-800/40">Field Service #<span x-text="bookingId"></span></span>
            <h3 class="text-base font-bold text-white flex items-center gap-2">
                <span>إنهاء الزيارة وتسجيل المستهلكات</span> 🛠️
            </h3>
        </div>

        <form method="POST" :action="'/admin/bookings/' + bookingId + '/complete'" class="space-y-4">
            @csrf
            
            <div>
                <label class="block text-xs text-slate-400 mb-1.5">تكلفة قطع الغيار الإضافية (إن وجدت):</label>
                <div class="relative">
                    <input type="number" step="0.01" name="parts_cost" placeholder="0.00"
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 pl-12 text-xs text-white focus:outline-none focus:border-cyan-500 font-mono">
                    <span class="absolute left-3 top-3 text-xs text-slate-500">ج.م</span>
                </div>
            </div>

            <div>
                <label class="block text-xs text-slate-400 mb-1.5">بيان وتفاصيل قطع الغيار المستهلكة:</label>
                <input type="text" name="parts_notes" placeholder="مثال: شاحن أصلي، بطارية داخلية، فحص كابلات..."
                    class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 text-xs text-white focus:outline-none focus:border-cyan-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs text-slate-400 mb-1.5">فترة الضمان المعتمدة:</label>
                    <select name="warranty_months" class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 text-xs text-white focus:outline-none focus:border-cyan-500">
                        <option value="1">شهر واحد</option>
                        <option value="3" selected>3 أشهر (قياسي)</option>
                        <option value="6">6 أشهر</option>
                        <option value="12">سنة كاملة</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1.5">حالة العمل:</label>
                    <div class="p-3 bg-slate-950 border border-slate-800 rounded-xl text-xs text-emerald-400 font-bold text-center">
                        اكتمال الزيارة ✅
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs text-slate-400 mb-1.5">الملاحظات الفنية للمهندس:</label>
                <textarea name="technician_notes" rows="2" placeholder="حالة الجهاز قبل وبعد، أي توصيات للعميل..."
                    class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 text-xs text-white focus:outline-none focus:border-cyan-500"></textarea>
            </div>

            <div class="p-3 bg-cyan-950/20 border border-cyan-800/40 rounded-xl text-[11px] text-cyan-300">
                💡 فور التأكيد، سيتم إنشاء الفاتورة الرسمية وشهادة الضمان للعميل تلقائياً برمز QR.
            </div>

            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" @click="open = false" class="px-4 py-2 rounded-xl text-xs text-slate-400 hover:text-white">إلغاء</button>
                <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold bg-emerald-500 hover:bg-emerald-400 text-slate-950 transition-all shadow-lg shadow-emerald-500/20">
                    اعتماد وإنهاء الزيارة
                </button>
            </div>
        </form>
    </div>
</div>
