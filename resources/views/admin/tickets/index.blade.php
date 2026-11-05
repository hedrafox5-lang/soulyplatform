@extends('admin.layout')

@section('title', 'مكتب تذاكر الدعم الفني المؤسسي | سولي')

@section('admin-content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-900 border border-slate-800 p-6 rounded-3xl">
        <div>
            <h1 class="text-xl font-black text-white flex items-center gap-2">
                <span>🎫 تذاكر الدعم الفني المؤسسي</span>
            </h1>
            <p class="text-xs text-slate-400 mt-1">إدارة استفسارات العملاء ومشاكل الصيانة والأجهزة مع دعم الملاحظات الهندسية الداخلية.</p>
        </div>
    </div>

    <!-- Tickets Table -->
    <div class="bg-slate-900 border border-slate-800 rounded-3xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead>
                    <tr class="bg-slate-950/70 border-b border-slate-800 text-slate-400">
                        <th class="py-3.5 px-4 font-bold">رقم التذكرة</th>
                        <th class="py-3.5 px-4 font-bold">الموضوع والقطاع</th>
                        <th class="py-3.5 px-4 font-bold">العميل</th>
                        <th class="py-3.5 px-4 font-bold">المسؤول</th>
                        <th class="py-3.5 px-4 font-bold">الحالة</th>
                        <th class="py-3.5 px-4 font-bold">آخر تحديث</th>
                        <th class="py-3.5 px-4 font-bold text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($tickets as $ticket)
                    <tr class="hover:bg-slate-800/30 transition-colors">
                        <td class="py-3 px-4 font-mono font-bold text-cyan-400">
                            #{{ $ticket->ticket_number }}
                        </td>
                        <td class="py-3 px-4">
                            <span class="font-bold text-white block">{{ $ticket->subject }}</span>
                            <span class="text-[10px] text-slate-500">{{ $ticket->scope_label_ar ?? $ticket->department }}</span>
                        </td>
                        <td class="py-3 px-4">
                            <span class="text-white block font-bold">{{ $ticket->user?->name ?? 'عميل' }}</span>
                            <span class="text-[10px] text-slate-500 font-mono">{{ $ticket->user?->phone }}</span>
                        </td>
                        <td class="py-3 px-4 text-slate-300">
                            {{ $ticket->assignedStaff?->name ?? 'غير مسندة' }}
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase
                                {{ in_array($ticket->status, ['resolved', 'closed']) ? 'bg-emerald-950 border border-emerald-800 text-emerald-400' : 'bg-cyan-950 border border-cyan-800 text-cyan-400' }}">
                                {{ $ticket->status }}
                            </span>
                        </td>
                        <td class="py-3 px-4 font-mono text-slate-500">
                            {{ $ticket->updated_at->diffForHumans() }}
                        </td>
                        <td class="py-3 px-4 text-center">
                            <a href="{{ route('admin.tickets.show', $ticket->id) }}" class="px-3.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-cyan-400 font-bold transition-all">
                                فتح التذكرة →
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-slate-500">لا توجد تذاكر دعم فني مسجلة حالياً.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tickets->hasPages())
        <div class="p-4 border-t border-slate-800">
            {{ $tickets->links() }}
        </div>
        @endif
    </div>
</div>
@endsection