<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Services\SoulyNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminWalletController extends Controller
{
    /**
     * Display wallet dashboard and pending manual deposit receipts.
     */
    public function index(Request $request)
    {
        $pendingDeposits = WalletTransaction::where('type', 'deposit')
            ->where('status', 'pending')
            ->with(['user', 'wallet'])
            ->latest()
            ->paginate(15);

        $totalCash = Wallet::sum('cash_balance');
        $totalBonus = Wallet::sum('bonus_balance');

        return view('admin.wallets.index', compact('pendingDeposits', 'totalCash', 'totalBonus'));
    }

    /**
     * Approve manual deposit receipt with atomic row lock.
     */
    public function approveDeposit(Request $request, WalletTransaction $transaction, SoulyNotificationService $notifier)
    {
        if ($transaction->status !== 'pending' || $transaction->type !== 'deposit') {
            return back()->with('error', 'هذه المعاملة ليست قيد الانتظار أو تمت معالجتها مسبقاً.');
        }

        try {
            DB::transaction(function () use ($transaction, $notifier) {
                $trx = WalletTransaction::where('id', $transaction->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $wallet = Wallet::where('id', $trx->wallet_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $trx->update([
                    'status' => 'completed',
                    'notes'  => 'تم اعتماد الإيداع يدوياً من الإدارة',
                ]);

                $wallet->increment('cash_balance', (float)$trx->amount);

                if ($trx->user) {
                    $notifier->notify(
                        $trx->user,
                        'تم اعتماد الإيداع بنجاح',
                        "تمت إضافة مبلغ {$trx->amount} ج.م إلى رصيدك النقدي في المحفظة.",
                        route('portal.wallet')
                    );
                }
            });

            return back()->with('citadel_toast', 'تم اعتماد الإيداع وإضافة الرصيد للمحفظة بنجاح.');
        } catch (\Throwable $e) {
            return back()->with('error', 'حدث خطأ أثناء معالجة الاعتماد: ' . $e->getMessage());
        }
    }

    /**
     * Reject manual deposit receipt.
     */
    public function rejectDeposit(Request $request, WalletTransaction $transaction, SoulyNotificationService $notifier)
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:255'],
        ]);

        if ($transaction->status !== 'pending') {
            return back()->with('error', 'تمت معالجة هذه المعاملة مسبقاً.');
        }

        $transaction->update([
            'status' => 'rejected',
            'notes'  => $validated['reason'],
        ]);

        if ($transaction->user) {
            $notifier->notify(
                $transaction->user,
                'تم رفض إيصال الإيداع',
                "تم رفض الإيصال للسبب التالي: {$validated['reason']}",
                route('portal.wallet')
            );
        }

        return back()->with('citadel_toast', 'تم رفض الإيصال وإشعار العميل.');
    }

    /**
     * Grant conditional promotional bonus balance to a user.
     */
    public function grantBonus(Request $request, SoulyNotificationService $notifier)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'amount'  => ['required', 'numeric', 'min:1'],
            'reason'  => ['required', 'string', 'max:255'],
        ]);

        try {
            DB::transaction(function () use ($validated, $notifier) {
                $wallet = Wallet::firstOrCreate(
                    ['user_id' => $validated['user_id']],
                    ['cash_balance' => 0.00, 'bonus_balance' => 0.00, 'currency' => 'EGP']
                );

                $lockedWallet = Wallet::where('id', $wallet->id)->lockForUpdate()->first();
                $lockedWallet->increment('bonus_balance', (float)$validated['amount']);

                WalletTransaction::create([
                    'wallet_id'   => $lockedWallet->id,
                    'user_id'     => $validated['user_id'],
                    'type'        => 'bonus_grant',
                    'amount'      => (float)$validated['amount'],
                    'currency'    => 'EGP',
                    'status'      => 'completed',
                    'reference'   => 'BONUS-' . strtoupper(Str::random(6)),
                    'description' => "مكافأة ولاء ترويجية: {$validated['reason']}",
                ]);

                $user = User::find($validated['user_id']);
                if ($user) {
                    $notifier->notify(
                        $user,
                        'هدية رصيد بونص في محفظتك 🎁',
                        "تمت إضافة {$validated['amount']} ج.م رصيد بونص ترويجي إلى محفظتك لاستخدامه في خدماتنا.",
                        route('portal.wallet')
                    );
                }
            });

            return back()->with('citadel_toast', 'تم شحن رصيد البونص للمستخدم بنجاح.');
        } catch (\Throwable $e) {
            return back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }
}
