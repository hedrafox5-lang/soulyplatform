<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Invoice;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Services\PaymentGatewayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    /**
     * Handle automated gateway callback and update balances atomically.
     */
    public function handleCallback(Request $request, PaymentGatewayService $gateway)
    {
        $payload = $request->all();
        $signature = $request->query('hmac', $request->header('X-Hmac-Signature'));

        if (!$gateway->verifySignature($payload, $signature)) {
            Log::warning('Payment Webhook: Invalid Signature Rejected.');
            return response()->json(['status' => 'rejected'], 403);
        }

        $isSuccess = filter_var($payload['success'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $merchantOrderId = $payload['order']['merchant_order_id'] ?? ($payload['merchant_order_id'] ?? null);

        if (!$isSuccess || !$merchantOrderId) {
            return response()->json(['status' => 'ignored']);
        }

        try {
            DB::transaction(function () use ($merchantOrderId, $payload) {
                // 1. Check if order is a Wallet Top-up
                if (str_starts_with($merchantOrderId, 'TOPUP-')) {
                    $trx = WalletTransaction::where('reference', $merchantOrderId)
                        ->where('status', 'pending')
                        ->lockForUpdate()
                        ->first();

                    if ($trx) {
                        $trx->update(['status' => 'completed']);
                        $wallet = Wallet::where('id', $trx->wallet_id)->lockForUpdate()->first();
                        $wallet?->increment('cash_balance', (float)$trx->amount);
                    }
                }
                // 2. Check if order is an Official Invoice
                elseif (str_starts_with($merchantOrderId, 'INV-')) {
                    $invoice = Invoice::where('invoice_number', $merchantOrderId)
                        ->where('payment_status', 'unpaid')
                        ->lockForUpdate()
                        ->first();

                    if ($invoice) {
                        $invoice->update([
                            'payment_status' => 'paid',
                            'paid_at'        => now(),
                            'payment_method' => 'online_gateway',
                        ]);
                    }
                }
                // 3. Check if order is an Express Booking
                elseif (str_starts_with($merchantOrderId, 'BK-')) {
                    $booking = Booking::where('booking_number', $merchantOrderId)
                        ->where('payment_status', 'unpaid')
                        ->lockForUpdate()
                        ->first();

                    if ($booking) {
                        $booking->update([
                            'payment_status' => 'paid',
                            'payment_method' => 'online_gateway',
                        ]);
                    }
                }
            });

            return response()->json(['status' => 'processed']);
        } catch (\Throwable $e) {
            Log::error('Payment Webhook Processing Error: ' . $e->getMessage());
            return response()->json(['status' => 'error'], 500);
        }
    }
}
