<?php

namespace Tests\Feature;

use App\Models\AiConversation;
use App\Models\AiMessage;
use App\Models\Lead;
use App\Models\Setting;
use App\Models\User;
use App\Services\SoulyAiService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Tests\TestCase;

class SoulyAiInfrastructureTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test Gemini dual-key failover rotates seamlessly to secondary key on 429/403/500
     * and logs the key rotation event.
     */
    public function test_gemini_dual_key_failover_rotates_on_rate_limit_or_error(): void
    {
        $primaryKey = 'TEST_PRIMARY_KEY_FAILOVER_1';
        $secondaryKey = 'TEST_SECONDARY_KEY_FAILOVER_2';

        Setting::set('gemini_api_key_primary', $primaryKey, 'ai');
        Setting::set('gemini_api_key_secondary', $secondaryKey, 'ai');

        Log::spy();

        Http::fake([
            "*key={$primaryKey}*" => Http::response([
                'error' => ['code' => 429, 'message' => 'Resource has been exhausted (e.g. check quota).']
            ], 429),
            "*key={$secondaryKey}*" => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => 'مرحباً بك! أنا سولي AI أعمل بنجاح عبر المفتاح الاحتياطي.']
                            ]
                        ]
                    ]
                ]
            ], 200),
        ]);

        $aiService = new SoulyAiService();
        $response = $aiService->callGemini('gemini-1.5-flash:generateContent', [
            'contents' => [['parts' => [['text' => 'اختبار الجاهزية']]]]
        ]);

        $this->assertNotNull($response);
        $this->assertEquals(
            'مرحباً بك! أنا سولي AI أعمل بنجاح عبر المفتاح الاحتياطي.',
            $response['candidates'][0]['content']['parts'][0]['text']
        );

        Log::shouldHaveReceived('info')->withArgs(function ($message) {
            return str_contains($message, 'Automated failover activated: rotating to Secondary Gemini key');
        });
    }

    /**
     * Test 768-dimensional float embedding generation and batch processing.
     */
    public function test_pinecone_768_dim_embedding_generation_and_batch_processing(): void
    {
        $aiService = new SoulyAiService();

        // 1. Single embedding generation returns exactly 768 float values
        $single = $aiService->generateEmbedding('سولي منظومة الحلول الرقمية والهندسية المتكاملة');
        $this->assertIsArray($single);
        $this->assertCount(768, $single);
        $this->assertIsFloat($single[0]);

        // 2. Batch embedding generation returns 768 float values for each text
        $texts = [
            'خدمة كاشير BrandERP والفاتورة الإلكترونية',
            'صيانة الشبكات والخوادم الميدانية بخصم 50%',
            'موظف الذكاء الاصطناعي لواتساب المبيعات 24/7',
        ];
        $batch = $aiService->batchGenerateEmbeddings($texts);
        $this->assertCount(3, $batch);
        foreach ($batch as $emb) {
            $this->assertCount(768, $emb);
            $this->assertIsFloat($emb[0]);
        }
    }

    /**
     * Test persistent multi-layer visitor identity and memory lifecycle:
     * - First visit: anonymous visitor with UUID
     * - Name introduction: "اسمي طارق الدسوقي" -> saved in conversation & recognized
     * - Retention: Returning visitor history endpoint returns personal welcome
     */
    public function test_persistent_visitor_identity_and_conversational_memory(): void
    {
        $visitorUuid = (string) Str::uuid();
        $fingerprint = 'fp_' . md5('test-browser-agent');

        // Step 1: Initial conversation as anonymous visitor
        $res1 = $this->postJson(route('api.ai.chat'), [
            'message' => 'السلام عليكم، عاوز أعرف مواعيد العمل',
            'visitor_uuid' => $visitorUuid,
            'device_fingerprint' => $fingerprint,
        ]);

        $res1->assertStatus(200);
        $sessionUuid = $res1->json('session_uuid') ?: $res1->json('conversation_id');
        $this->assertNotEmpty($sessionUuid);

        $conv1 = AiConversation::where('session_uuid', $sessionUuid)->first();
        $this->assertNotNull($conv1);
        $this->assertEquals($visitorUuid, $conv1->visitor_uuid);
        $this->assertEquals($fingerprint, $conv1->device_fingerprint);
        $this->assertNull($conv1->visitor_name);

        // Step 2: Visitor introduces name
        $res2 = $this->postJson(route('api.ai.chat'), [
            'message' => 'اسمي م. طارق الدسوقي',
            'conversation_id' => $sessionUuid,
            'visitor_uuid' => $visitorUuid,
            'device_fingerprint' => $fingerprint,
        ]);

        $res2->assertStatus(200);
        $conv2 = $conv1->fresh();
        $this->assertEquals('طارق الدسوقي', $conv2->visitor_name);
        $this->assertStringContainsString('طارق الدسوقي', $res2->json('reply'));

        // Step 3: Returning visit - History endpoint recognizes visitor by visitor_uuid
        $resHistory = $this->getJson(route('api.ai.history', ['visitor_uuid' => $visitorUuid]));
        $resHistory->assertStatus(200);
        $this->assertEquals('طارق الدسوقي', $resHistory->json('visitor_name'));
        $this->assertStringContainsString('طارق الدسوقي', $resHistory->json('welcome_message'));
        $this->assertGreaterThanOrEqual(2, count($resHistory->json('messages')));
    }

    /**
     * Test Pinecone sync upsert payload has 768-dimension vectors and remote fallback resilience.
     */
    public function test_pinecone_sync_upserts_768_dim_vectors_in_batch(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin_' . Str::random(6) . '@soulyeg.online',
            'phone' => '010' . rand(10000000, 99999999),
        ]);

        $upsertCaptured = false;
        $vectorDimension = 0;

        Http::fake([
            '*pinecone.io/vectors/upsert*' => function (\Illuminate\Http\Client\Request $request) use (&$upsertCaptured, &$vectorDimension) {
                $upsertCaptured = true;
                $payload = json_decode($request->body(), true) ?: $request->data();
                if (!empty($payload['vectors'][0]['values'])) {
                    $vectorDimension = count($payload['vectors'][0]['values']);
                }
                return Http::response(['upsertedCount' => count($payload['vectors'] ?? [])], 200);
            },
        ]);

        $response = $this->actingAs($admin)->postJson(route('admin.settings.sync_pinecone'));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertTrue($upsertCaptured, 'Pinecone upsert endpoint was not invoked');
        $this->assertEquals(768, $vectorDimension, 'Pinecone vectors must have dimension 768');
    }

    /**
     * Test Zero Empty Response Guarantee and trained domain knowledge rules
     * (BrandERP 2,000 EGP, Maintenance 400 EGP/hr 3h min, Cyber NDA & kids < 18).
     */
    public function test_zero_empty_response_guarantee_and_trained_knowledge_rules(): void
    {
        // Simulate both Gemini keys offline/failing to force intelligent local synthesis
        Http::fake([
            '*generativelanguage.googleapis.com*' => Http::response(['error' => 'API Unavailable'], 503),
            '*pinecone.io*' => Http::response(['error' => 'Pinecone Unavailable'], 503),
        ]);

        $visitorUuid = (string) Str::uuid();

        // 1. Initial history returns updated standard welcoming prompt
        $historyRes = $this->getJson(route('api.ai.history', ['visitor_uuid' => $visitorUuid]));
        $historyRes->assertStatus(200);
        $this->assertStringContainsString('أهلاً بيك في منظومة سولي', $historyRes->json('welcome_message'));

        // 2. BrandERP Inquiry -> Must mention 2,000 EGP Core and package suggestion
        $erpRes = $this->postJson(route('api.ai.chat'), [
            'message' => 'عاوز أعرف أسعار كاشير BrandERP والمخازن',
            'visitor_uuid' => $visitorUuid,
        ]);
        $erpRes->assertStatus(200);
        $this->assertNotEmpty($erpRes->json('reply'));
        $this->assertStringContainsString('2,000', $erpRes->json('reply'));
        $this->assertNotEmpty($erpRes->json('package_suggestions'));
        $this->assertStringContainsString('2,000', $erpRes->json('package_suggestions')[0]['price']);

        // 3. Maintenance Inquiry -> Must mention 400 EGP/hr, 3 hours min, and board repair exclusion
        $maintRes = $this->postJson(route('api.ai.chat'), [
            'message' => 'عاوز مهندس صيانة يجيلي يفحص السيرفر والشبكة',
            'visitor_uuid' => $visitorUuid,
            'conversation_id' => $erpRes->json('conversation_id'),
        ]);
        $maintRes->assertStatus(200);
        $this->assertNotEmpty($maintRes->json('reply'));
        $this->assertStringContainsString('400', $maintRes->json('reply'));
        $this->assertStringContainsString('3', $maintRes->json('reply'));
        $this->assertStringContainsString('البورد', $maintRes->json('reply'));

        // 4. Cybersecurity Inquiry -> Must mention 700 to 5,000 EGP and children under 18
        $cyberRes = $this->postJson(route('api.ai.chat'), [
            'message' => 'عندي استفسار عن خدمات الأمن السيبراني وحماية هواتف أطفالي',
            'visitor_uuid' => $visitorUuid,
            'conversation_id' => $erpRes->json('conversation_id'),
        ]);
        $cyberRes->assertStatus(200);
        $this->assertNotEmpty($cyberRes->json('reply'));
        $this->assertStringContainsString('700', $cyberRes->json('reply'));
        $this->assertStringContainsString('18', $cyberRes->json('reply'));

        // 5. Arbitrary unknown prompt -> Guarantees non-empty response
        $randomRes = $this->postJson(route('api.ai.chat'), [
            'message' => 'xyz abc ??? 12345',
            'visitor_uuid' => (string) Str::uuid(),
        ]);
        $randomRes->assertStatus(200);
        $this->assertNotEmpty($randomRes->json('reply'));
        $this->assertGreaterThan(10, mb_strlen($randomRes->json('reply')));
    }
}
