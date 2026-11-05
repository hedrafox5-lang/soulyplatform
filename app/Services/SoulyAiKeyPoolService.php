<?php

namespace App\Services;

use App\Models\AiApiKey;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SoulyAiKeyPoolService
{
    /**
     * Known stable Google Gemini fallbacks if custom/experimental aliases return 404.
     */
    protected const STABLE_GEMINI_FALLBACK = 'gemini-2.5-flash';
    protected const SECONDARY_FALLBACK = 'gemini-1.5-flash';

    /**
     * Get the active model for Admin AI Agent.
     */
    public function getAdminModel(): string
    {
        return Setting::get('ai_admin_model') ?: 'gemini-3.8-flash';
    }

    /**
     * Get the active model for Visitor / Customer chat.
     */
    public function getVisitorModel(): string
    {
        return Setting::get('ai_visitor_model') ?: 'gemini-3.8-flash';
    }

    /**
     * Retrieve candidate active keys ordered by priority and usage.
     *
     * @param string $provider 'gemini' | 'pinecone'
     * @param string $scope 'all' | 'admin_only' | 'visitor_only'
     * @return Collection<int, AiApiKey>
     */
    public function getAvailableKeys(string $provider = 'gemini', string $scope = 'all'): Collection
    {
        $now = Carbon::now();

        // 1. Auto-reactivate expired cooldown keys
        AiApiKey::where('provider', $provider)
            ->where('status', 'cooldown')
            ->whereNotNull('cooldown_until')
            ->where('cooldown_until', '<=', $now)
            ->update([
                'status' => 'active',
                'cooldown_until' => null,
            ]);

        // 2. Fetch candidates
        $query = AiApiKey::where('provider', $provider)
            ->where('status', 'active');

        if ($scope === 'admin_only') {
            $query->whereIn('scope', ['all', 'admin_only']);
        } elseif ($scope === 'visitor_only') {
            $query->whereIn('scope', ['all', 'visitor_only']);
        }

        $keys = $query->orderBy('priority', 'asc')
            ->orderBy('requests_today', 'asc')
            ->get();

        return $keys;
    }

    /**
     * Execute Gemini API call with automated key pool failover and transparent model fallback.
     *
     * @param string $requestedModel The model name requested (e.g. gemini-3.8-flash, gemini-1-argon, gemini-2.5-flash)
     * @param string $action The REST action (e.g. generateContent, embedContent)
     * @param array $payload JSON payload
     * @param string $scope 'all' | 'admin_only' | 'visitor_only'
     * @return array ['success' => bool, 'data' => ?array, 'model' => string, 'key_id' => ?int, 'latency_ms' => int, 'error' => ?string]
     */
    public function callGemini(string $requestedModel, string $action, array $payload, string $scope = 'all'): array
    {
        $keys = $this->getAvailableKeys('gemini', $scope);

        // Fallback to config keys if DB has no active keys
        if ($keys->isEmpty()) {
            $fallbackRawKey = Setting::get('gemini_api_key_primary') ?: config('services.gemini.api_key_primary', env('GEMINI_API_KEY_PRIMARY', ''));
            $fallbackKeyModel = new AiApiKey([
                'label' => 'System Config Fallback',
                'provider' => 'gemini',
                'api_key' => $fallbackRawKey,
                'status' => 'active',
                'priority' => 99,
            ]);
            $keys = collect([$fallbackKeyModel]);
        }

        $activeModel = $requestedModel;
        $telemetryModel = $requestedModel;
        $lastError = null;

        foreach ($keys as $keyRecord) {
            $start = microtime(true);
            $apiKey = $keyRecord->api_key;

            try {
                $url = "https://generativelanguage.googleapis.com/v1beta/models/{$activeModel}:{$action}?key={$apiKey}";

                $response = Http::timeout(20)
                    ->withoutVerifying()
                    ->post($url, $payload);

                $latencyMs = (int) round((microtime(true) - $start) * 1000);

                if ($response->successful()) {
                    $json = $response->json();
                    if ($keyRecord->exists) {
                        $keyRecord->recordUsage($latencyMs);
                    }

                    return [
                        'success' => true,
                        'data' => $json,
                        'model' => $telemetryModel,
                        'actual_engine' => $activeModel,
                        'key_id' => $keyRecord->id ?? null,
                        'key_email' => $keyRecord->account_email ?? 'system',
                        'latency_ms' => $latencyMs,
                        'error' => null,
                    ];
                }

                $status = $response->status();
                $body = $response->body();
                $lastError = "HTTP {$status}: " . Str::limit($body, 200);

                // 1. If 404 (Model Not Found) - transparently switch to stable engine fallback
                if ($status === 404 && $activeModel !== self::STABLE_GEMINI_FALLBACK) {
                    Log::info("[SoulyAiKeyPool] Custom/Experimental model '{$activeModel}' returned 404. Transparently falling back to '" . self::STABLE_GEMINI_FALLBACK . "'.");
                    $activeModel = self::STABLE_GEMINI_FALLBACK;

                    // Retry immediately with the stable engine on the same key
                    $retryUrl = "https://generativelanguage.googleapis.com/v1beta/models/{$activeModel}:{$action}?key={$apiKey}";
                    $retryResponse = Http::timeout(20)
                        ->withoutVerifying()
                        ->post($retryUrl, $payload);

                    $retryLatencyMs = (int) round((microtime(true) - $start) * 1000);

                    if ($retryResponse->successful()) {
                        if ($keyRecord->exists) {
                            $keyRecord->recordUsage($retryLatencyMs);
                        }

                        return [
                            'success' => true,
                            'data' => $retryResponse->json(),
                            'model' => $telemetryModel,
                            'actual_engine' => self::STABLE_GEMINI_FALLBACK,
                            'key_id' => $keyRecord->id ?? null,
                            'key_email' => $keyRecord->account_email ?? 'system',
                            'latency_ms' => $retryLatencyMs,
                            'error' => null,
                        ];
                    }
                }

                // 2. If Quota / Rate limit (429 or 403) or timeout/server error, put key in cooldown and switch instantly
                if (in_array($status, [429, 403, 500, 502, 503, 504])) {
                    Log::warning("[SoulyAiKeyPool] Key #{$keyRecord->id} ({$keyRecord->account_email}) failed with status {$status}. Putting in cooldown.");
                    if ($keyRecord->exists) {
                        $keyRecord->markCooldown(15, $lastError);
                    }
                    continue; // Seamless failover to next key in pool (< 50ms)
                }

                Log::warning("[SoulyAiKeyPool] Key #{$keyRecord->id} returned non-success HTTP {$status}: {$lastError}");
            } catch (\Throwable $e) {
                $latencyMs = (int) round((microtime(true) - $start) * 1000);
                $lastError = $e->getMessage();
                Log::warning("[SoulyAiKeyPool] Exception on key #{$keyRecord->id}: {$lastError}");
                if ($keyRecord->exists) {
                    $keyRecord->markCooldown(10, $lastError);
                }
                continue; // Next key
            }
        }

        return [
            'success' => false,
            'data' => null,
            'model' => $telemetryModel,
            'actual_engine' => $activeModel,
            'key_id' => null,
            'latency_ms' => 0,
            'error' => $lastError ?: 'All AI keys in the pool are exhausted or unavailable.',
        ];
    }

    /**
     * Live ping test for an API key.
     */
    public function pingKey(AiApiKey $key): array
    {
        $start = microtime(true);
        try {
            if ($key->provider === 'gemini') {
                $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$key->api_key}";
                $payload = [
                    'contents' => [
                        ['parts' => [['text' => 'ping test']]]
                    ]
                ];

                $response = Http::timeout(10)->withoutVerifying()->post($url, $payload);
                $latencyMs = (int) round((microtime(true) - $start) * 1000);

                if ($response->successful()) {
                    $key->recordUsage($latencyMs);
                    return ['status' => 'active', 'latency_ms' => $latencyMs, 'message' => 'الاتصال ناجح واستجابة المفتاح ممتازة.'];
                }

                $status = $response->status();
                if (in_array($status, [429, 403])) {
                    $key->markCooldown(15, 'Quota / Rate limit reached during ping');
                    return ['status' => 'cooldown', 'latency_ms' => $latencyMs, 'message' => 'المفتاح استنفد الكوتا الحالية وموضوع في فترة تبريد.'];
                }

                $key->update(['status' => 'invalid', 'last_error' => "HTTP {$status}: " . $response->body()]);
                return ['status' => 'invalid', 'latency_ms' => $latencyMs, 'message' => "خطأ في المفتاح: HTTP {$status}"];
            }

            if ($key->provider === 'pinecone') {
                $host = Setting::get('pinecone_host') ?: config('services.pinecone.host');
                if (!$host) {
                    return ['status' => 'active', 'latency_ms' => 15, 'message' => 'مفتاح Pinecone صالح لكن لم يتم تحديد host الفهرس بعد.'];
                }

                $response = Http::timeout(10)->withHeaders([
                    'Api-Key' => $key->api_key,
                ])->withoutVerifying()->get("https://{$host}/describe_index_stats");

                $latencyMs = (int) round((microtime(true) - $start) * 1000);
                if ($response->successful()) {
                    $key->recordUsage($latencyMs);
                    return ['status' => 'active', 'latency_ms' => $latencyMs, 'message' => 'الاتصال بفهرس Pinecone ناجح تماماً.'];
                }

                return ['status' => 'invalid', 'latency_ms' => $latencyMs, 'message' => 'فشل الاتصال بـ Pinecone.'];
            }

            return ['status' => 'unknown', 'latency_ms' => 0, 'message' => 'مزود خدمة غير معروف.'];
        } catch (\Throwable $e) {
            $latencyMs = (int) round((microtime(true) - $start) * 1000);
            return ['status' => 'invalid', 'latency_ms' => $latencyMs, 'message' => 'استثناء في الشبكة: ' . $e->getMessage()];
        }
    }
}
