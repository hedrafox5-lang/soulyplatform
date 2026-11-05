<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use App\Services\SoulyNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminTicketController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        $department = $request->query('department');

        $tickets = Ticket::with(['user', 'assignedStaff', 'latestReply'])
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($department, fn($q) => $q->where('department_scope', $department))
            ->latest('updated_at')
            ->paginate(15);

        $staffMembers = User::whereIn('role', ['admin', 'staff', 'technician'])->get();

        return view('admin.tickets.index', compact('tickets', 'staffMembers'));
    }

    public function show(Ticket $ticket)
    {
        $ticket->load(['user', 'assignedStaff', 'messages.user']);
        $staffMembers = User::whereIn('role', ['admin', 'staff', 'technician'])->get();

        return view('admin.tickets.show', compact('ticket', 'staffMembers'));
    }

    public function reply(Request $request, Ticket $ticket, SoulyNotificationService $notifier)
    {
        $validated = $request->validate([
            'message'          => ['required', 'string'],
            'is_internal_note' => ['nullable', 'boolean'],
            'status'           => ['nullable', 'in:open,in_progress,answered,resolved,closed'],
        ]);

        $isInternal = $request->boolean('is_internal_note');
        $newStatus = $validated['status'] ?? ($isInternal ? $ticket->status : 'answered');

        TicketMessage::create([
            'ticket_id'        => $ticket->id,
            'user_id'          => auth()->id(),
            'message'          => strip_tags(trim($validated['message'])),
            'is_internal_note' => $isInternal,
        ]);

        $ticket->update(['status' => $newStatus]);

        if (!$isInternal && $ticket->user) {
            $notifier->notify(
                $ticket->user,
                "رد جديد على التذكرة #{$ticket->ticket_number}",
                "قام فريق الدعم الفني بالرد على تذكرتك: " . Str::limit($validated['message'], 60),
                route('portal.tickets.show', $ticket->id)
            );
        }

        return back()->with('citadel_toast', $isInternal ? 'تم تسجيل الملاحظة الداخلية للمهندسين بنجاح.' : 'تم إرسال الرد للعميل وتحديث حالة التذكرة.');
    }

    public function updateStatus(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:open,in_progress,answered,resolved,closed'],
        ]);

        $ticket->update(['status' => $validated['status']]);

        return back()->with('citadel_toast', 'تم تحديث حالة تذكرة الدعم الفني.');
    }

    public function assign(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'assigned_to' => ['required', 'exists:users,id'],
        ]);

        $ticket->update(['assigned_to' => $validated['assigned_to']]);

        return back()->with('citadel_toast', 'تم إسناد التذكرة للموظف المختص.');
    }
}