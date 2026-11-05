<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
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
        $status = $request->query('status');

        $transactions = WalletTransaction::with(['user', 'wallet'])
            ->when($status, fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(15);

        $pendingDeposits = $transactions;
        $pendingCount = WalletTransaction::where('status', 'pending')->count();
        $approvedSum = (float) WalletTransaction::where('status', 'approved')->where('type', 'deposit')->sum('amount');

        $totalCash = (float) Wallet::sum('cash_balance');
        $totalBonus = (float) Wallet::sum('bonus_balance');

        $gateways = [
            'vodafone_cash' => Setting::get('payment_vodafone_cash', Setting::get('vodafone_cash_number', '01026042784')),
            'instapay'      => Setting::get('payment_instapay', Setting::get('instapay_handle', 'souly@instapay')),
            'bank_account'  => Setting::get('payment_bank_account', Setting::get('bank_name', 'CIB Bank - Souly Tech')),
        ];
        $gatewaySettings = $gateways;

        $users = User::where('is_active', true)->select('id', 'name', 'phone', 'email')->get();

        return view('admin.wallets.index', compact(
            'transactions',
            'pendingDeposits',
            'pendingCount',
            'approvedSum',
            'totalCash',
            'totalBonus',
            'gateways',
            'gatewaySettings',
            'users'
        ));
    }

    /**
     * Approve manual deposit receipt with atomic row lock.
     */
    public function approve(Request $request, WalletTransaction $transaction, SoulyNotificationService $notifier)
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

    public function approveDeposit(Request $request, WalletTransaction $transaction, SoulyNotificationService $notifier)
    {
        return $this->approve($request, $transaction, $notifier);
    }

    /**
     * Reject manual deposit receipt.
     */
    public function reject(Request $request, WalletTransaction $transaction, SoulyNotificationService $notifier)
    {
        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        if ($transaction->status !== 'pending') {
            return back()->with('error', 'تمت معالجة هذه المعاملة مسبقاً.');
        }

        $reason = $validated['reason'] ?? 'لم يتم استلام المبلغ في الحساب البنكي أو المحفظة';

        $transaction->update([
            'status' => 'rejected',
            'notes'  => $reason,
        ]);

        if ($transaction->user) {
            $notifier->notify(
                $transaction->user,
                'تم رفض إيصال الإيداع',
                "تم رفض الإيصال للسبب التالي: {$reason}",
                route('portal.wallet')
            );
        }

        return back()->with('citadel_toast', 'تم رفض الإيصال وإشعار العميل.');
    }

    public function rejectDeposit(Request $request, WalletTransaction $transaction, SoulyNotificationService $notifier)
    {
        return $this->reject($request, $transaction, $notifier);
    }

    /**
     * Grant conditional promotional bonus balance to a user.
     */
    public function chargeBonusGift(Request $request, SoulyNotificationService $notifier)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'amount'  => ['required', 'numeric', 'min:1'],
            'reason'  => ['nullable', 'string', 'max:255'],
        ]);

        try {
            DB::transaction(function () use ($validated, $notifier) {
                $wallet = Wallet::firstOrCreate(
                    ['user_id' => $validated['user_id']],
                    ['cash_balance' => 0.00, 'bonus_balance' => 0.00, 'currency' => 'EGP']
                );

                $lockedWallet = Wallet::where('id', $wallet->id)->lockForUpdate()->first();
                $lockedWallet->increment('bonus_balance', (float)$validated['amount']);

                $reason = $validated['reason'] ?? 'مكافأة ولاء ترويجية خاصة';

                WalletTransaction::create([
                    'wallet_id'   => $lockedWallet->id,
                    'user_id'     => $validated['user_id'],
                    'type'        => 'bonus_grant',
                    'amount'      => (float)$validated['amount'],
                    'currency'    => 'EGP',
                    'status'      => 'completed',
                    'reference'   => 'BONUS-' . strtoupper(Str::random(6)),
                    'description' => "مكافأة ولاء ترويجية: {$reason}",
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

    public function grantBonus(Request $request, SoulyNotificationService $notifier)
    {
        return $this->chargeBonusGift($request, $notifier);
    }

    /**
     * Update manual payment receiving gateways numbers & handles.
     */
    public function updateGateways(Request $request)
    {
        $validated = $request->validate([
            'vodafone_cash_number' => ['nullable', 'string', 'max:50'],
            'instapay_handle'      => ['nullable', 'string', 'max:100'],
            'bank_account_iban'    => ['nullable', 'string', 'max:100'],
            'bank_name'            => ['nullable', 'string', 'max:150'],
        ]);

        foreach ($validated as $key => $val) {
            if (!is_null($val)) {
                Setting::set($key, $val, 'payment', 'إعدادات استقبال مدفوعات المحفظة');
            }
        }

        return back()->with('citadel_toast', 'تم تحديث بيانات بوابات الاستقبال اليدوي بنجاح.');
    }
}