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

        <form method="POST" :action="'/admin/bookings/' + bookingId + '/complete-visit'" class="space-y-4">
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

            <div>
                <label class="block text-xs text-slate-400 mb-1.5">ملاحظات المهندس الفني وتوصيات الاستخدام:</label>
                <textarea name="technician_notes" rows="3" placeholder="ملاحظات حول حالة الأجهزة بعد الصيانة..."
                    class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 text-xs text-white focus:outline-none focus:border-cyan-500 leading-relaxed"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs text-slate-400 mb-1.5">شهادة الضمان الممنوحة:</label>
                    <select name="warranty_months" class="w-full bg-slate-950 border border-slate-800 rounded-xl p-2.5 text-xs text-white">
                        <option value="1">شهر واحد</option>
                        <option value="3" selected>3 أشهر</option>
                        <option value="6">6 أشهر</option>
                        <option value="12">سنة كاملة</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1.5">إصدار الفاتورة فورياً:</label>
                    <div class="p-2 bg-slate-950 border border-slate-800 rounded-xl text-center">
                        <span class="text-xs font-bold text-emerald-400">تلقائي مع QR ✓</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between pt-3 border-t border-slate-800">
                <button type="button" @click="open = false" 
                    class="px-4 py-2 rounded-xl text-xs text-slate-400 hover:text-white transition-colors">
                    إلغاء
                </button>
                <button type="submit" 
                    class="px-5 py-2.5 rounded-xl text-xs font-bold bg-amber-500 hover:bg-amber-400 text-slate-950 transition-all shadow-lg shadow-amber-500/20">
                    اعتماد وإنهاء الزيارة
                </button>
            </div>
        </form>
    </div>
</div>