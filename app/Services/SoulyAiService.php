<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SoulyAIService
{
    protected string $apiKey;
    protected string $model;
    protected string $endpoint;

    public function __construct()
    {
        $this->apiKey   = (string)config('services.ai.api_key', env('SOULY_AI_KEY', ''));
        $this->model    = (string)Setting::get('souly_ai_model', 'souly-fast-v1');
        $this->endpoint = (string)config('services.ai.endpoint', env('SOULY_AI_ENDPOINT', ''));
    }

    /**
     * Generate grounded responses with safe system fallback.
     */
    public function askCopilot(string $prompt, array $history = []): array
    {
        if (empty($this->apiKey) || empty($this->endpoint)) {
            return $this->fallbackResponse('مفتاح الذكاء الاصطناعي غير مهيأ حالياً، يرجى مراجعة إعدادات الخادم.');
        }

        try {
            $payload = [
                'model'    => $this->model,
                'prompt'   => $prompt,
                'history'  => $history,
                'system'   => 'أنت سولي، المساعد التقني الذكي لمنصة سولي للتكنولوجيا. أجب بدقة واحترافية وبشكل موجز.',
            ];

            $response = Http::timeout(10)
                ->withToken($this->apiKey)
                ->post($this->endpoint, $payload);

            if ($response->successful()) {
                $reply = $response->json('reply') ?? $response->json('choices.0.message.content');
                if (!empty($reply)) {
                    return ['success' => true, 'reply' => trim($reply)];
                }
            }

            Log::warning('Souly AI API Warning: ' . $response->status());
            return $this->fallbackResponse();

        } catch (\Throwable $e) {
            Log::error('Souly AI Service Exception: ' . $e->getMessage());
            return $this->fallbackResponse();
        }
    }

    /**
     * Fallback reply when AI service is unavailable.
     */
    protected function fallbackResponse(string $custom = ''): array
    {
        return [
            'success' => false,
            'reply'   => !empty($custom) ? $custom : 'أهلاً بك! يواجه المستشار التقني ضغطاً مؤقتاً، يمكنك ترك استفسارك وسيقوم فريق الدعم بالرد الفوري.',
        ];
    }
}
