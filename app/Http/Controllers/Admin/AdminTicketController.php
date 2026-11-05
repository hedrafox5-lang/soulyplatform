<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Services\SoulyNotificationService;
use Illuminate\Http\Request;

class AdminTicketController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        $tickets = Ticket::with(['user', 'latestReply'])
            ->when($status, fn($q) => $q->where('status', $status))
            ->latest('updated_at')
            ->paginate(15);

        return view('admin.tickets.index', compact('tickets'));
    }

    public function show(Ticket $ticket)
    {
        $ticket->load(['user', 'replies.user']);
        return view('admin.tickets.show', compact('ticket'));
    }

    public function reply(Request $request, Ticket $ticket, SoulyNotificationService $notifier)
    {
        $validated = $request->validate([
            'message'          => ['required', 'string'],
            'is_internal_note' => ['nullable', 'boolean'],
            'status'           => ['required', 'in:open,in_progress,answered,resolved,closed'],
        ]);

        $isInternal = $request->boolean('is_internal_note');

        $reply = TicketReply::create([
            'ticket_id'        => $ticket->id,
            'user_id'          => auth()->id(),
            'message'          => strip_tags(trim($validated['message'])),
            'is_internal_note' => $isInternal,
        ]);

        $ticket->update(['status' => $validated['status']]);

        if (!$isInternal && $ticket->user) {
            $notifier->notify(
                $ticket->user,
                "رد جديد على التذكرة #{$ticket->ticket_number}",
                "قام الدعم الفني بالرد على تذكرتك: " . Str::limit($validated['message'], 60),
                route('portal.tickets.show', $ticket->id)
            );
        }

        return back()->with('citadel_toast', $isInternal ? 'تم تسجيل الملاحظة الداخلية للمهندسين بنجاح.' : 'تم إرسال الرد للعميل وتحديث التذكرة.');
    }
}
