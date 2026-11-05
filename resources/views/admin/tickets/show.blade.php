@extends('admin.layout')

@section('title', 'تذكرة الدعم #' . $ticket->ticket_number . ' | سولي')

@section('admin-content')
<div class="space-y-6 max-w-4xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-900 border border-slate-800 p-6 rounded-3xl">
        <div>
            <div class="flex items-center gap-3">
                <span class="text-xs font-mono font-bold text-cyan-400 bg-cyan-950 px-2.5 py-1 rounded-xl border border-cyan-800">#{{ $ticket->ticket_number }}</span>
                <h1 class="text-lg font-black text-white">{{ $ticket->subject }}</h1>
            </div>
            <p class="text-xs text-slate-400 mt-1">العميل: {{ $ticket->user?->name }} ({{ $ticket->user?->phone }}) | القسم: {{ $ticket->scope_label_ar ?? $ticket->department }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.tickets.index') }}" class="px-3 py-1.5 rounded-xl bg-slate-800 text-slate-300 text-xs font-bold hover:bg-slate-700">
                ← العودة للتذاكر
            </a>
            <form method="POST" action="{{ route('admin.tickets.status', $ticket->id) }}" class="inline-flex">
                @csrf
                <select name="status" onchange="this.form.submit()" class="bg-slate-950 border border-slate-700 rounded-xl px-3 py-1.5 text-xs text-white">
                    <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>مفتوحة (Open)</option>
                    <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>قيد العمل (In Progress)</option>
                    <option value="answered" {{ $ticket->status === 'answered' ? 'selected' : '' }}>تم الرد (Answered)</option>
                    <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>تم الحل (Resolved)</option>
                    <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>مغلقة (Closed)</option>
                </select>
            </form>
        </div>
    </div>

    <!-- Messages Timeline -->
    <div class="space-y-4">
        @forelse($ticket->messages as $msg)
        <div class="p-5 rounded-3xl border {{ $msg->is_internal_note ? 'bg-amber-950/20 border-amber-800/40' : ($msg->user_id === auth()->id() ? 'bg-cyan-950/20 border-cyan-800/40' : 'bg-slate-900 border-slate-800') }}">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold {{ $msg->is_internal_note ? 'text-amber-400' : 'text-cyan-400' }}">
                    {{ $msg->user?->name ?? 'مستخدم' }}
                    @if($msg->is_internal_note) <span class="text-[10px] font-normal px-2 py-0.5 rounded-md bg-amber-950 border border-amber-800 text-amber-300 ml-1">🔒 ملاحظة هندسية داخلية</span> @endif
                </span>
                <span class="text-[10px] font-mono text-slate-500">{{ $msg->created_at->format('Y-m-d H:i') }}</span>
            </div>
            <p class="text-xs text-slate-200 leading-relaxed whitespace-pre-wrap">{{ $msg->message }}</p>
        </div>
        @empty
        <div class="p-6 bg-slate-900 rounded-3xl border border-slate-800 text-center text-xs text-slate-500">
            لا توجد ردود بعد على هذه التذكرة.
        </div>
        @endforelse
    </div>

    <!-- Reply Box Component -->
    @include('admin.partials.ticket-reply-box', ['ticket' => $ticket])
</div>
@endsection