@extends('portal.layout')

@section('title', 'فاتورة #' . $invoice->invoice_number . ' | بوابة سولي')

@section('content')
<div class="space-y-6 max-w-3xl mx-auto">
    <div class="flex items-center justify-between bg-slate-900 border border-slate-800 p-5 rounded-3xl">
        <a href="{{ route('portal.invoices.index') }}" class="text-xs text-slate-400 hover:text-white flex items-center gap-1 font-bold">
            <span>→ العودة للفواتير</span>
        </a>
        <button onclick="window.print()" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-cyan-400 text-xs font-bold transition-all">
            طباعة الفاتورة 🖨️
        </button>
    </div>

    @include('partials.public-invoice-card', ['invoice' => $invoice])
</div>
@endsection