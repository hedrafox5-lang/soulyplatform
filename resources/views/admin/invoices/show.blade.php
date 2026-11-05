@extends('admin.layout')

@section('title', 'ØªÙØ§ØµÙŠÙ„ Ø§Ù„ÙØ§ØªÙˆØ±Ø© #' . $invoice->invoice_number . ' | Ø³ÙˆÙ„ÙŠ')

@section('admin-content')
<div class="space-y-6 max-w-4xl mx-auto">
    <!-- Top Action Bar -->
    <div class="flex items-center justify-between bg-slate-900 border border-slate-800 p-5 rounded-3xl">
        <a href="{{ route('admin.invoices.index') }}" class="text-xs text-slate-400 hover:text-white flex items-center gap-1.5 font-bold">
            <span>â†’ Ø§Ù„Ø¹ÙˆØ¯Ø© Ù„Ù„ÙÙˆØ§ØªÙŠØ±</span>
        </a>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold transition-all">
                Ø·Ø¨Ø§Ø¹Ø© ðŸ–¨ï¸
            </button>
            <form method="POST" action="{{ route('admin.invoices.update_status', $invoice->id) }}" class="inline-flex items-center gap-2">
                @csrf
                <select name="payment_status" onchange="this.form.submit()" class="bg-slate-950 border border-slate-700 rounded-xl px-3 py-1.5 text-xs text-white">
                    <option value="unpaid" {{ $invoice->payment_status === 'unpaid' ? 'selected' : '' }}>Ù…Ø¹Ù„Ù‚Ø© (Unpaid)</option>
                    <option value="paid" {{ $invoice->payment_status === 'paid' ? 'selected' : '' }}>Ù…Ø³Ø¯Ø¯Ø© (Paid)</option>
                    <option value="refunded" {{ $invoice->payment_status === 'refunded' ? 'selected' : '' }}>Ù…Ø³ØªØ±Ø¯Ø© (Refunded)</option>
                </select>
            </form>
        </div>
    </div>

    <!-- Invoice Sheet -->
    @include('partials.public-invoice-card', ['invoice' => $invoice])
</div>
@endsection