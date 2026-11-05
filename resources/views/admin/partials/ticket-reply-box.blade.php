<div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl text-right">
    <h4 class="text-sm font-bold text-white mb-4 flex items-center justify-between">
        <span class="text-xs text-slate-400 font-normal">إضافة رد هندسي أو ملاحظة فنية سرية</span>
        <span class="flex items-center gap-1.5 text-cyan-400">
            <span>💬 إضافة رد للمحادثة</span>
        </span>
    </h4>
    
    <form method="POST" action="{{ route('admin.tickets.reply', $ticket->id) }}" class="space-y-4">
        @csrf
        
        <div>
            <textarea name="message" rows="4" dir="rtl" required
                placeholder="اكتب ردك هنا للعميل، أو اكتب ملاحظتك الهندسية المستورة..."
                class="w-full bg-slate-950 border border-slate-800 rounded-2xl p-3.5 text-xs text-slate-200 focus:outline-none focus:border-cyan-500 font-sans leading-relaxed"></textarea>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 items-center">
            <!-- Internal Engineer Note Checkbox -->
            <div class="flex items-center justify-between bg-slate-950/70 p-3 rounded-xl border border-slate-800/80">
                <label for="is_internal_note" class="text-xs text-slate-300 font-medium cursor-pointer select-none">
                    <span class="text-amber-400 font-bold ml-1">🔒 ملاحظة داخلية فقط:</span>
                    <span class="text-[10px] text-slate-500">(لن تظهر للعميل)</span>
                </label>
                <input type="checkbox" id="is_internal_note" name="is_internal_note" value="1"
                    class="w-4 h-4 rounded border-slate-700 bg-slate-900 text-amber-500 focus:ring-amber-500 focus:ring-offset-slate-950">
            </div>

            <!-- Ticket Status Select -->
            <div class="flex items-center justify-between bg-slate-950/70 p-3 rounded-xl border border-slate-800/80">
                <label class="text-xs text-slate-400">تحديث حالة التذكرة:</label>
                <select name="status" class="bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-xs text-white">
                    <option value="answered" selected>تم الرد (Answered)</option>
                    <option value="in_progress">قيد المتابعة (In Progress)</option>
                    <option value="resolved">تم الحل (Resolved)</option>
                    <option value="closed">إغلاق التذكرة (Closed)</option>
                </select>
            </div>
        </div>

        <div class="flex items-center justify-between pt-2">
            <span class="text-[11px] text-slate-500">سيتم إرسال إشعار فوري للعميل في حال كان الرد عاماً.</span>
            <button type="submit" class="px-6 py-2.5 rounded-xl text-xs font-bold bg-cyan-500 hover:bg-cyan-400 text-slate-950 transition-all shadow-lg shadow-cyan-500/20">
                إرسال الرد
            </button>
        </div>
    </form>
</div>