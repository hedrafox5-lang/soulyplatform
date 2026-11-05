<div x-data="{ open: false, key: '', template: '', title: '' }" 
     @open-whatsapp-gear.window="open = true; key = $event.detail.key; template = $event.detail.template; title = $event.detail.title"
     x-show="open" 
     x-cloak 
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
    
    <div @click.away="open = false" class="w-full max-w-lg bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-2xl relative text-right">
        <!-- Header -->
        <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-4">
            <span class="text-xs font-mono text-cyan-400 bg-cyan-950/60 px-2.5 py-1 rounded-lg border border-cyan-800/40" x-text="key"></span>
            <h3 class="text-base font-bold text-white flex items-center gap-2">
                <span>تعديل قالب رسائل الواتساب الدائمة</span> ⚙️
            </h3>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('admin.settings.update_whatsapp_template') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="template_key" :value="key">
            
            <div>
                <label class="block text-xs text-slate-400 mb-1.5" x-text="'نص رسالة: ' + title"></label>
                <textarea name="template_content" x-model="template" rows="5" dir="rtl"
                    class="w-full bg-slate-950 border border-slate-800 rounded-2xl p-3 text-sm text-slate-200 focus:outline-none focus:border-cyan-500 font-sans leading-relaxed"></textarea>
            </div>

            <!-- Variables Helper -->
            <div class="bg-slate-950/60 p-3 rounded-xl border border-slate-800/60 text-[11px] text-slate-400 space-y-1">
                <span class="text-amber-400 font-bold block">المتغيرات الذكية المدعومة تلقائياً:</span>
                <code>{name}</code> اسم العميل | <code>{invoice_number}</code> رقم الفاتورة | <code>{total_amount}</code> المبلغ | <code>{invoice_url}</code> رابط الفاتورة والضمان
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between pt-2">
                <button type="button" @click="open = false" 
                    class="px-4 py-2 rounded-xl text-xs text-slate-400 hover:text-white transition-colors">
                    إلغاء
                </button>
                <button type="submit" 
                    class="px-5 py-2.5 rounded-xl text-xs font-bold bg-cyan-500 hover:bg-cyan-400 text-slate-950 transition-all shadow-lg shadow-cyan-500/20">
                    حفظ القالب الدائم
                </button>
            </div>
        </form>
    </div>
</div>