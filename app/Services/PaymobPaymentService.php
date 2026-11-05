<?php

namespace App\Services;

use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymobPaymentService
{
    protected ?string $apiKey;
    protected ?string $integrationId;
    protected ?string $iframeId;
    protected ?string $hmacSecret;
    protected string $currency;

    public function __construct()
    {
        $this->apiKey = config('services.paymob.api_key');
        $this->integrationId = config('services.paymob.integration_id');
        $this->iframeId = config('services.paymob.iframe_id');
        $this->hmacSecret = config('services.paymob.hmac_secret');
        $this->currency = config('services.paymob.currency', 'EGP');
    }

    /**
     * Check if Paymob automated gateway is configured.
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey) && !empty($this->integrationId) && !empty($this->iframeId);
    }

    /**
     * Step 1 & 2: Authenticate and register Order with Paymob, generating Payment Key.
     *
     * @return string|null Iframe URL for client checkout
     */
    public function generateCheckoutUrl(float $amount, User $user, WalletTransaction $transaction): ?string
    {
        if (!$this->isConfigured()) {
            return null;
        }

        try {
            // 1. Authentication Request
            $authRes = Http::timeout(8)->post('https://accept.paymob.com/api/auth/tokens', [
                'api_key' => $this->apiKey,
            ]);

            if (!$authRes->successful()) {
                Log::error('[Paymob] Auth token request failed: ' . $authRes->body());
                return null;
            }

            $authToken = $authRes->json('token');

            // 2. Order Registration
            $amountCents = (int) round($amount * 100);
            $orderRes = Http::timeout(8)->post('https://accept.paymob.com/api/ecommerce/orders', [
                'auth_token' => $authToken,
                'delivery_needed' => false,
                'amount_cents' => $amountCents,
                'currency' => $this->currency,
                'merchant_order_id' => 'SOULY_TX_' . $transaction->id . '_' . time(),
                'items' => [
                    [
                        'name' => 'Souly Citadel Wallet Top-up',
                        'amount_cents' => $amountCents,
                        'description' => 'شحن محفظة سولي الرقمية',
                        'quantity' => 1,
                    ]
                ],
            ]);

            if (!$orderRes->successful()) {
                Log::error('[Paymob] Order registration failed: ' . $orderRes->body());
                return null;
            }

            $paymobOrderId = $orderRes->json('id');

            // 3. Payment Key Generation
            $billingData = [
                'apartment' => 'NA',
                'email' => $user->email,
                'floor' => 'NA',
                'first_name' => explode(' ', $user->name)[0] ?? 'عميل',
                'street' => 'NA',
                'building' => 'NA',
                'phone_number' => $user->phone ?? '01000000000',
                'shipping_method' => 'PKG',
                'postal_code' => 'NA',
                'city' => 'Cairo',
                'country' => 'EG',
                'last_name' => explode(' ', $user->name)[1] ?? 'سولي',
                'state' => 'Cairo',
            ];

            $paymentKeyRes = Http::timeout(8)->post('https://accept.paymob.com/api/acceptance/payment_keys', [
                'auth_token' => $authToken,
                'amount_cents' => $amountCents,
                'expiration' => 3600,
                'order_id' => $paymobOrderId,
                'billing_data' => $billingData,
                'currency' => $this->currency,
                'integration_id' => (int) $this->integrationId,
            ]);

            if (!$paymentKeyRes->successful()) {
                Log::error('[Paymob] Payment key request failed: ' . $paymentKeyRes->body());
                return null;
            }

            $paymentToken = $paymentKeyRes->json('token');

            return "https://accept.paymob.com/api/acceptance/iframes/{$this->iframeId}?payment_token={$paymentToken}";

        } catch (\Throwable $e) {
            Log::error('[Paymob] Checkout generation exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Verify Paymob Webhook HMAC signature.
     */
    public function verifyHmac(array $data): bool
    {
        if (empty($this->hmacSecret)) {
            return false;
        }

        $obj = $data['obj'] ?? $data;

        $concatenated = 
            ($obj['amount_cents'] ?? '') .
            ($obj['created_at'] ?? '') .
            ($obj['currency'] ?? '') .
            ($obj['error_occured'] ?? '') .
            ($obj['has_parent_transaction'] ?? '') .
            ($obj['id'] ?? '') .
            ($obj['integration_id'] ?? '') .
            ($obj['is_3d_secure'] ?? '') .
            ($obj['is_auth'] ?? '') .
            ($obj['is_capture'] ?? '') .
            ($obj['is_refunded'] ?? '') .
            ($obj['is_standalone_payment'] ?? '') .
            ($obj['is_voided'] ?? '') .
            ($obj['order']['id'] ?? '') .
            ($obj['owner'] ?? '') .
            ($obj['pending'] ?? '') .
            ($obj['source_data']['pan'] ?? '') .
            ($obj['source_data']['sub_type'] ?? '') .
            ($obj['source_data']['type'] ?? '') .
            ($obj['success'] ?? '');

        $calculatedHmac = hash_hmac('sha512', $concatenated, $this->hmacSecret);

        return hash_equals($calculatedHmac, $data['hmac'] ?? '');
    }
}
