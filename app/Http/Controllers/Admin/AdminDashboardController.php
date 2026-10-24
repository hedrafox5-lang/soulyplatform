<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Lead;
use App\Models\Service;
use App\Models\Ticket;
use App\Models\WalletTransaction;
use Illuminate\Contracts\View\View;

class AdminDashboardController extends Controller
{
    /**
     * Display the Admin & Staff Command Overview.
     */
    public function index(): View
    {
        $totalApprovedRevenue = (float) WalletTransaction::where('type', 'purchase')
            ->where('status', 'approved')
            ->sum('amount');

        $pendingDepositsCount = WalletTransaction::where('type', 'deposit')
            ->where('status', 'pending')
            ->count();

        $newBookingsCount = Booking::where('status', 'new')->count();
        $inProgressBookingsCount = Booking::where('status', 'in_progress')->count();

        $activeLeadsCount = Lead::whereIn('status', ['new', 'contacted', 'qualified'])->count();
        $openTicketsCount = Ticket::whereIn('status', ['open', 'client_replied'])->count();

        $recentPendingDeposits = WalletTransaction::with(['user', 'wallet'])
            ->where('type', 'deposit')
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        $recentBookings = Booking::with(['user', 'service', 'package'])
            ->latest()
            ->take(5)
            ->get();

        $recentLeads = Lead::with('interestedService')
            ->latest()
            ->take(5)
            ->get();

        $recentTickets = Ticket::with('user')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.index', compact(
            'totalApprovedRevenue',
            'pendingDepositsCount',
            'newBookingsCount',
            'inProgressBookingsCount',
            'activeLeadsCount',
            'openTicketsCount',
            'recentPendingDeposits',
            'recentBookings',
            'recentLeads',
            'recentTickets'
        ));
    }
}
