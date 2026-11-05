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
        $pendingDeposits = WalletTransaction::where('type', 'deposit')
            ->where('status', 'pending')
            ->with(['user', 'wallet'])
            ->latest()
            ->paginate(15);

        $totalCash = Wallet::sum('cash_balance');
        $totalBonus = Wallet::sum('bonus_balance');

        $gatewaySettings = [
            'vodafone_cash_number' => Setting::get('vodafone_cash_number', '01000000000'),
            'instapay_handle'      => Setting::get('instapay_handle', 'souly@instapay'),
            'bank_account_iban'    => Setting::get('bank_account_iban', 'EG00000000000000000000000'),
            'bank_name'            => Setting::get('bank_name', 'البنك الأهلي المصري'),
        ];

        return view('admin.wallets.index', compact('pendingDeposits', 'totalCash', 'totalBonus', 'gatewaySettings'));
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