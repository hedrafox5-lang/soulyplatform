<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentGatewayService
{
    protected string $apiKey;
    protected string $secretKey;
    protected string $cardChannelId;
    protected string $walletChannelId;
    protected string $checkoutViewId;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = (string)Setting::get('payment_api_key', config('services.payment.api_key', ''));
        $this->secretKey = (string)Setting::get('payment_hmac_secret', config('services.payment.hmac_secret', ''));
        $this->cardChannelId = (string)Setting::get('payment_card_id', config('services.payment.card_id', ''));
        $this->walletChannelId = (string)Setting::get('payment_wallet_id', config('services.payment.wallet_id', ''));
        $this->checkoutViewId = (string)Setting::get('payment_iframe_id', config('services.payment.iframe_id', ''));
        $this->baseUrl = (string)config('services.payment.base_url', 'https://api.paymentgateway.local');
    }

    public function requestAccessToken(): ?string
    {
        try {
            $res = Http::post("{$this->baseUrl}/auth/tokens", ['api_key' => $this->apiKey]);
            return $res->json('token');
        } catch (\Throwable $e) {
            Log::error('Gateway Auth Token Error: ' . $e->getMessage());
            return null;
        }
    }

    public function registerInvoiceOrder(string $token, float $cents, string $reference): ?int
    {
        try {
            $res = Http::post("{$this->baseUrl}/ecommerce/orders", [
                'auth_token'        => $token,
                'delivery_needed'   => 'false',
                'amount_cents'      => (int)round($cents),
                'currency'          => 'EGP',
                'merchant_order_id' => $reference,
                'items'             => [],
            ]);
            return $res->json('id');
        } catch (\Throwable $e) {
            Log::error('Gateway Order Error: ' . $e->getMessage());
            return null;
        }
    }

    public function issueCheckoutToken(string $token, int $orderId, float $cents, array $user, string $channel = 'card'): ?string
    {
        $channelId = ($channel === 'wallet') ? $this->walletChannelId : $this->cardChannelId;
        try {
            $res = Http::post("{$this->baseUrl}/acceptance/payment_keys", [
                'auth_token'     => $token,
                'amount_cents'   => (int)round($cents),
                'expiration'     => 3600,
                'order_id'       => $orderId,
                'billing_data'   => [
                    'first_name'   => $user['name'] ?? 'Client',
                    'last_name'    => 'Souly',
                    'email'        => $user['email'] ?? 'client@soulyeg.com',
                    'phone_number' => $user['phone'] ?? '01000000000',
                    'apartment'    => 'NA', 'floor' => 'NA', 'street' => 'NA', 'building' => 'NA',
                    'shipping_method' => 'NA', 'postal_code' => 'NA', 'city' => 'Cairo', 'country' => 'EG', 'state' => 'Cairo',
                ],
                'currency'       => 'EGP',
                'integration_id' => (int)$channelId,
            ]);
            return $res->json('token');
        } catch (\Throwable $e) {
            Log::error('Gateway Checkout Token Error: ' . $e->getMessage());
            return null;
        }
    }

    public function buildRedirectUrl(string $token): string
    {
        return "{$this->baseUrl}/acceptance/iframes/{$this->checkoutViewId}?payment_token={$token}";
    }

    public function verifySignature(array $p, ?string $sig): bool
    {
        if (empty($sig) || empty($this->secretKey)) return false;
        $keys = ['amount_cents', 'created_at', 'currency', 'error_occured', 'has_parent_transaction', 'id', 'integration_id', 'is_3d_secure', 'is_auth', 'is_capture', 'is_refunded', 'is_standalone_payment', 'is_voided', 'order', 'owner', 'pending', 'source_data_pan', 'source_data_sub_type', 'source_data_type', 'success'];
        $buf = '';
        foreach ($keys as $k) { $buf .= (string)($p[$k] ?? ''); }
        return hash_equals(hash_hmac('sha512', $buf, $this->secretKey), $sig);
    }
}
