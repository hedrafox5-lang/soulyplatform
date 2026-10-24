<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WalletTransaction;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminWalletController extends Controller
{
    /**
     * List all wallet transactions with status filter.
     */
    public function index(Request $request): View
    {
        $query = WalletTransaction::with(['user', 'wallet', 'reviewer']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $transactions = $query->latest()->paginate(15);

        $pendingCount = WalletTransaction::where('type', 'deposit')->where('status', 'pending')->count();
        $approvedSum = WalletTransaction::where('type', 'deposit')->where('status', 'approved')->sum('amount');

        return view('admin.wallets.index', compact('transactions', 'pendingCount', 'approvedSum'));
    }

    /**
     * 1-Click Approve pending wallet deposit and increment client balance atomically.
     */
    public function approve(Request $request, WalletTransaction $transaction): RedirectResponse
    {
        if ($transaction->status !== 'pending' || $transaction->type !== 'deposit') {
            return back()->with('citadel_toast', [
                'type' => 'error',
                'message' => 'هذه المعاملة ليست قيد المراجعة أو تم البت فيها مسبقاً.'
            ]);
        }

        $adminNotes = $request->input('admin_notes', 'تم التحقق واعتماد الإيداع بنجاح وإضافة الرصيد.');

        DB::transaction(function () use ($transaction, $adminNotes) {
            $transaction->status = 'approved';
            $transaction->reviewed_by = auth()->id();
            $transaction->admin_notes = $adminNotes;
            $transaction->save();

            // Atomically increment client wallet balance
            $wallet = $transaction->wallet;
            $wallet->increment('balance', $transaction->amount);
        });

        return back()->with('citadel_toast', [
            'type' => 'success',
            'message' => "تم اعتماد الإيداع برقم ({$transaction->reference_code}) وإضافة ({$transaction->amount} ج.م) لمحفظة العميل بنجاح!"
        ]);
    }

    /**
     * 1-Click Reject pending wallet deposit.
     */
    public function reject(Request $request, WalletTransaction $transaction): RedirectResponse
    {
        if ($transaction->status !== 'pending') {
            return back()->with('citadel_toast', [
                'type' => 'error',
                'message' => 'هذه المعاملة تم البت فيها مسبقاً.'
            ]);
        }

        $adminNotes = $request->input('admin_notes', 'تم رفض طلب الشحن لعدم تطابق بيانات التحويل.');

        $transaction->status = 'rejected';
        $transaction->reviewed_by = auth()->id();
        $transaction->admin_notes = $adminNotes;
        $transaction->save();

        return back()->with('citadel_toast', [
            'type' => 'warning',
            'message' => "تم رفض معاملة الإيداع ({$transaction->reference_code})."
        ]);
    }
}
