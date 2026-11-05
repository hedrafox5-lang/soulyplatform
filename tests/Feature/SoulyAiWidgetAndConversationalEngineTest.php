<?php

namespace Tests\Feature;

use App\Models\AiConversation;
use App\Services\SoulyAiService;
use Tests\TestCase;

class SoulyAiWidgetAndConversationalEngineTest extends TestCase
{
    /**
     * Test extractVisitorName filters questions and small-talk and only extracts real names.
     */
    public function test_extract_visitor_name_filters_conversational_questions_and_small_talk(): void
    {
        $service = app(SoulyAiService::class);

        // False positives that must NOT be extracted as names
        $this->assertNull($service->extractVisitorName('اهلا'));
        $this->assertNull($service->extractVisitorName('اسمك اية'));
        $this->assertNull($service->extractVisitorName('اسمك ايه'));
        $this->assertNull($service->extractVisitorName('مين انت'));
        $this->assertNull($service->extractVisitorName('مين معايا'));
        $this->assertNull($service->extractVisitorName('عامل ايه'));
        $this->assertNull($service->extractVisitorName('عندكم ايه'));
        $this->assertNull($service->extractVisitorName('ازيك'));
        $this->assertNull($service->extractVisitorName('شكرا'));
        $this->assertNull($service->extractVisitorName('مين صاحب الشركة'));

        // Real explicit name introductions that MUST be extracted
        $this->assertEquals('طارق الزيات', $service->extractVisitorName('اسمي طارق الزيات'));
        $this->assertEquals('محمود علي', $service->extractVisitorName('معاك محمود علي'));
        $this->assertEquals('سارة إبراهيم', $service->extractVisitorName('حضرتك معاك سارة إبراهيم'));
    }

    /**
     * Test AI agent chat endpoint answers general greetings and identity questions with distinct non-canned replies.
     */
    public function test_ai_agent_chat_handles_greetings_and_identity_without_fixed_canned_repeats(): void
    {
        // 1. Identity Question: "اسمك اية"
        $identityResponse = $this->postJson('/api/ai/chat', [
            'message' => 'اسمك اية',
        ]);
        $identityResponse->assertStatus(200);
        $identityData = $identityResponse->json();
        $this->assertNotEmpty($identityData['reply']);
        $this->assertStringNotContainsString('يا اسمك اية', $identityData['reply']);
        $this->assertStringContainsString('سولي', $identityData['reply']);

        // 2. Greeting: "اهلا"
        $greetingResponse = $this->postJson('/api/ai/chat', [
            'message' => 'اهلا',
        ]);
        $greetingResponse->assertStatus(200);
        $greetingData = $greetingResponse->json();
        $this->assertNotEmpty($greetingData['reply']);
        $this->assertStringNotContainsString('يا اهلا', $greetingData['reply']);

        // 3. Founder inquiry: "مين صاحب الشركة"
        $founderResponse = $this->postJson('/api/ai/chat', [
            'message' => 'مين صاحب الشركة',
        ]);
        $founderResponse->assertStatus(200);
        $founderData = $founderResponse->json();
        $this->assertNotEmpty($founderData['reply']);
        $this->assertTrue(
            str_contains($founderData['reply'], 'هدرا') ||
            str_contains($founderData['reply'], 'Hedra')
        );

        // 4. Cleanup test conversations
        if (!empty($identityData['conversation_id'])) {
            $conv = AiConversation::where('session_uuid', $identityData['conversation_id'])->first();
            if ($conv) {
                $conv->messages()->delete();
                $conv->delete();
            }
        }
    }

    /**
     * Test Souly AI widget template contains toggle and close event hooks.
     */
    public function test_souly_ai_widget_template_contains_robust_toggle_and_close_handlers(): void
    {
        $rendered = view('partials.souly-ai-widget')->render();

        $this->assertStringContainsString('@close-souly-ai.window="closeWidget()"', $rendered);
        $this->assertStringContainsString('@toggle-souly-ai.window="toggleWidget()"', $rendered);
        $this->assertStringContainsString('toggleWidget()', $rendered);
        $this->assertStringContainsString('closeWidget()', $rendered);
        $this->assertStringContainsString('z-[99990]', $rendered);
    }
}
