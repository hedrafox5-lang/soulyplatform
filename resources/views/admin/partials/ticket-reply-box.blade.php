<div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-xl text-right">
    <h4 class="text-sm font-bold text-white mb-3 flex items-center justify-end gap-2">
        <span>إضافة رد أو تحديث للتذكرة</span>
        <span class="text-cyan-400">💬</span>
    </h4>
    
    <form method="POST" action="{{ route('admin.tickets.reply', $ticket->id) }}" class="space-y-4">
        @csrf
        
        <div>
            <textarea name="message" rows="4" dir="rtl" required
                placeholder="اكتب ردك هنا للعميل، أو اكتب ملاحظتك الفنية المستورة..."
                class="w-full bg-slate-950 border border-slate-800 rounded-xl p-3 text-xs text-slate-200 focus:outline-none focus:border-cyan-500 font-sans leading-relaxed"></textarea>
        </div>

        <!-- Internal Engineer Note Checkbox -->
        <div class="flex items-center justify-end gap-3 bg-slate-950/70 p-3 rounded-xl border border-slate-800/80">
            <label for="is_internal_note" class="text-xs text-slate-300 font-medium cursor-pointer select-none">
                <span class="text-amber-400 font-bold ml-1">🔒 ملاحظة داخلية للمهندسين فقط:</span>
                (لن تظهر للعميل في بوابته إطلاقاً)
            </label>
            <input type="checkbox" id="is_internal_note" name="is_internal_note" value="1"
                class="w-4 h-4 rounded border-slate-700 bg-slate-900 text-amber-500 focus:ring-amber-500 focus:ring-offset-slate-950">
        </div>

        <div class="flex items-center justify-between pt-1">
            <span class="text-[11px] text-slate-500">سيتم إرسال إشعار فوري للعميل في حال كان الرد عاماً.</span>
            <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-cyan-500 hover:bg-cyan-400 text-slate-950 transition-all shadow-lg shadow-cyan-500/20">
                إرسال الرد
            </button>
        </div>
    </form>
</div>
