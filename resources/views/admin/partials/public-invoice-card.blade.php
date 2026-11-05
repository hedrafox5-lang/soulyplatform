<div class="max-w-2xl mx-auto bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-2xl text-right text-slate-200 print:bg-white print:text-black print:border-none print:shadow-none">
    <!-- Header -->
    <div class="flex items-center justify-between border-b border-slate-800 pb-5 mb-5 print:border-gray-300">
        <div class="text-left font-mono">
            <span class="text-xs text-slate-500 block">INVOICE NUMBER</span>
            <span class="text-base font-bold text-cyan-400 print:text-black">#{{ $invoice->invoice_number }}</span>
        </div>
        <div>
            <h2 class="text-lg font-black text-white print:text-black">مؤسسة سولي للتكنولوجيا</h2>
            <p class="text-xs text-slate-400 print:text-gray-600">س.ت: 104928 | ب.ض: 629-410-852</p>
        </div>
    </div>

    <!-- Client Info -->
    <div class="grid grid-cols-2 gap-3 bg-slate-950/60 p-4 rounded-2xl border border-slate-800 mb-5 print:bg-gray-50 print:border-gray-200 text-xs">
        <div><span class="text-slate-500">العميل:</span> <strong class="text-white print:text-black">{{ $invoice->user->name ?? $invoice->client_name }}</strong></div>
        <div><span class="text-slate-500">الهاتف:</span> <span class="font-mono">{{ $invoice->user->phone ?? $invoice->client_phone }}</span></div>
        <div><span class="text-slate-500">التاريخ:</span> {{ $invoice->created_at->format('Y-m-d') }}</div>
        <div><span class="text-slate-500">حالة السداد:</span> 
            <span class="font-bold {{ $invoice->payment_status === 'paid' ? 'text-emerald-400' : 'text-amber-400' }}">
                {{ $invoice->payment_status === 'paid' ? 'مسددة بالكامل' : 'معلقة / غير مسددة' }}
            </span>
        </div>
    </div>

    <!-- Items Table -->
    <table class="w-full text-xs text-right mb-5">
        <thead>
            <tr class="border-b border-slate-800 text-slate-400 pb-2">
                <th class="py-2">البند / الخدمة</th>
                <th class="py-2 text-center">الكمية</th>
                <th class="py-2 text-left">الإجمالي</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-800/60">
            @foreach($invoice->items as $item)
            <tr>
                <td class="py-2.5 font-medium text-white print:text-black">{{ $item->item_name }}</td>
                <td class="py-2.5 text-center">{{ $item->quantity }}</td>
                <td class="py-2.5 text-left font-mono font-bold">{{ number_format($item->subtotal, 2) }} ج.م</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Total -->
    <div class="flex justify-between items-center bg-cyan-950/20 border border-cyan-800/40 p-4 rounded-2xl mb-5 print:bg-gray-100">
        <span class="text-xs font-bold text-cyan-300 print:text-black">المبلغ الإجمالي المستحق:</span>
        <span class="text-lg font-black font-mono text-cyan-400 print:text-black">{{ number_format($invoice->total_amount, 2) }} ج.م</span>
    </div>

    <!-- Action -->
    <div class="flex items-center justify-between pt-3 border-t border-slate-800 print:hidden">
        <button onclick="window.print()" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-xs font-bold text-white rounded-xl transition-all">
            🖨️ طباعة الفاتورة
        </button>
        <div class="text-[11px] text-slate-500 font-mono">
            رابط التحقق: {{ route('invoice.verify', $invoice->hash_id) }}
        </div>
    </div>
</div>
