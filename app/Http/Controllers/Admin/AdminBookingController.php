<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminBookingController extends Controller
{
    /**
     * List all platform bookings with status filter and search.
     */
    public function index(Request $request): View
    {
        $query = Booking::with(['user', 'service', 'package', 'assignedStaff']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('booking_number', 'like', "%{$search}%")
                  ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%"));
            });
        }

        $bookings = $query->latest()->paginate(15);
        $staffMembers = User::whereIn('role', ['admin', 'staff'])->where('is_active', true)->get();

        return view('admin.bookings.index', compact('bookings', 'staffMembers'));
    }

    /**
     * Update booking status, staff assignment, and engineering report.
     */
    public function update(Request $request, Booking $booking): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:new,confirmed,in_progress,completed,cancelled'],
            'payment_status' => ['required', 'string', 'in:unpaid,pending_verification,paid,refunded'],
            'assigned_staff_id' => ['nullable', 'exists:users,id'],
            'staff_report' => ['nullable', 'string', 'max:5000'],
            'scheduled_at' => ['nullable', 'date'],
        ]);

        $booking->update($validated);

        return back()->with('citadel_toast', [
            'type' => 'success',
            'message' => "تم تحديث بيانات الحجز ({$booking->booking_number}) بنجاح!"
        ]);
    }
}
