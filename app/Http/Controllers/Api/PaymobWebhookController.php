<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WalletTransaction;
use App\Services\PaymobPaymentService;
use App\Services\SoulyNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymobWebhookController extends Controller
{
    /**
     * Handle Paymob Transaction Processed Webhook (Automated Instant Deposit).
     */
    public function handleWebhook(Request $request, PaymobPaymentService $paymobService): JsonResponse
    {
        $payload = $request->all();

        // 1. Verify HMAC if secret is configured
        if (!empty(config('services.paymob.hmac_secret'))) {
            if (!$paymobService->verifyHmac($payload)) {
                Log::warning('[PaymobWebhook] HMAC signature mismatch.');
                return response()->json(['error' => 'Invalid signature'], 400);
            }
        }

        $obj = $payload['obj'] ?? $payload;
        $isSuccess = ($obj['success'] ?? false) === true;
        $merchantOrderId = $obj['order']['merchant_order_id'] ?? '';
        $amountCents = (int) ($obj['amount_cents'] ?? 0);
        $amountEgp = $amountCents / 100.0;
        $paymobTxId = $obj['id'] ?? null;

        if (!$isSuccess) {
            Log::info("[PaymobWebhook] Transaction {$paymobTxId} failed or was declined.");
            return response()->json(['status' => 'acknowledged_failure']);
        }

        // Extract internal transaction ID from merchant_order_id (e.g., SOULY_TX_15_...)
        if (preg_match('/SOULY_TX_(\d+)_/', $merchantOrderId, $matches)) {
            $txId = (int) $matches[1];
            $transaction = WalletTransaction::with('wallet.user')->find($txId);

            if ($transaction && $transaction->status === 'pending') {
                DB::transaction(function () use ($transaction, $amountEgp, $paymobTxId) {
                    $wallet = $transaction->wallet;
                    $wallet->balance += $amountEgp;
                    $wallet->save();

                    $transaction->status = 'approved';
                    $transaction->reference_number = "PAYMOB-{$paymobTxId}";
                    $transaction->admin_notes = "تم الاعتماد الفوري التلقائي عبر بوابة Paymob (عملية #{$paymobTxId})";
                    $transaction->save();

                    // Automated Notifications
                    SoulyNotificationService::send(
                        $transaction->user_id,
                        'wallet',
                        'تم شحن محفظتك تلقائياً بنجاح ⚡',
                        "تم اعتماد شحن محفظتك فورياً بمبلغ " . number_format($amountEgp, 2) . " ج.م عبر الدفع الإلكتروني (Paymob).",
                        route('portal.wallet'),
                        'wallet'
                    );

                    SoulyNotificationService::sendToAdmins(
                        'wallet',
                        'إيداع إلكتروني فوري معتمد',
                        "تم سداد وإيداع " . number_format($amountEgp, 2) . " ج.م تلقائياً عبر Paymob للعميل: {$wallet->user->name}",
                        route('admin.wallets.index'),
                        'wallet'
                    );
                });

                Log::info("[PaymobWebhook] Wallet transaction #{$txId} approved atomically for {$amountEgp} EGP.");
                return response()->json(['status' => 'success', 'credited' => $amountEgp]);
            }
        }

        return response()->json(['status' => 'ignored']);
    }
}
