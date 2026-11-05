<?php

namespace App\Http\Controllers;

use App\Models\AiConversation;
use App\Services\SoulyAiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AiAgentController extends Controller
{
    /**
     * Process message from interactive AI widget with visitor persistence & memory.
     */
    public function chat(Request $request, SoulyAiService $aiService): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
            'conversation_id' => ['nullable', 'string', 'max:64'],
            'visitor_uuid' => ['nullable', 'string', 'max:64'],
            'visitor_name' => ['nullable', 'string', 'max:100'],
            'visitor_phone' => ['nullable', 'string', 'max:50'],
            'device_fingerprint' => ['nullable', 'string', 'max:100'],
        ]);

        $result = $aiService->reply(
            message: $validated['message'],
            sessionUuid: $validated['conversation_id'] ?? null,
            visitorUuid: $validated['visitor_uuid'] ?? null,
            visitorName: $validated['visitor_name'] ?? null,
            visitorPhone: $validated['visitor_phone'] ?? null,
            deviceFingerprint: $validated['device_fingerprint'] ?? null
        );

        if (empty($result['reply']) || trim($result['reply']) === '') {
            $fallbacks = [
                "أهلاً بحضرتك في منظومة سولي 🌟 أنا سولي، مستشارك التقني الذكي.. يسعدني مساعدتك في استكشاف باقات BrandERP (النواة تبدأ من 2,000 ج.م فقط)، أو حجز زيارة صيانة هندسية، أو موظفي الـ AI. كيف أقدر أخدمك؟",
                "يسعدنا تواجدك معنا في سولي! مستشارك التقني في خدمتك دائماً.. تحب نتعرف على أنظمة الكاشير ونقاط البيع ولا خدمات الدعم الفني وحماية الأنظمة؟",
                "أهلاً وسهلاً بك في سولي! معك مستشارك الهندسي الذكي لمساعدتك في أتمتة وتأمين نشاطك التجاري. ما هو استفسارك اليوم؟"
            ];
            $result['reply'] = $fallbacks[array_rand($fallbacks)];
        }

        return response()->json($result);
    }

    /**
     * Retrieve previous messages and visitor memory state for a conversation or visitor UUID.
     */
    public function history(Request $request, ?string $sessionUuid = null): JsonResponse
    {
        $visitorUuid = $request->query('visitor_uuid');

        $query = AiConversation::with('messages');

        if ($sessionUuid && $sessionUuid !== 'undefined' && $sessionUuid !== 'null') {
            $query->where('session_uuid', $sessionUuid);
        } elseif ($visitorUuid) {
            $query->where('visitor_uuid', $visitorUuid)->latest('updated_at');
        } else {
            return response()->json([
                'session_uuid' => null,
                'visitor_uuid' => null,
                'visitor_name' => null,
                'welcome_message' => 'أهلاً بيك في منظومة سولي! أنا سولي، مستشارك التقني الذكي.. أتشرف باسم حضرتك الأول؟',
                'messages' => []
            ]);
        }

        $conversation = $query->first();

        // Fallback: If not found by sessionUuid but visitorUuid is provided, find latest by visitorUuid
        if (!$conversation && $visitorUuid) {
            $conversation = AiConversation::with('messages')
                ->where('visitor_uuid', $visitorUuid)
                ->latest('updated_at')
                ->first();
        }

        if (!$conversation) {
            return response()->json([
                'session_uuid' => null,
                'visitor_uuid' => $visitorUuid,
                'visitor_name' => null,
                'welcome_message' => 'أهلاً بيك في منظومة سولي! أنا سولي، مستشارك التقني الذكي.. أتشرف باسم حضرتك الأول؟',
                'messages' => []
            ]);
        }

        $recognizedName = $conversation->visitor_name;
        $welcomeMessage = $recognizedName
            ? "أهلاً بك يا {$recognizedName}، نورت منصة سولي مجدداً! تحب نكمل استفسارنا السابق ولا نناقش موضوع جديد؟"
            : "أهلاً بيك في منظومة سولي! أنا سولي، مستشارك التقني الذكي.. أتشرف باسم حضرتك الأول؟";

        return response()->json([
            'session_uuid' => $conversation->session_uuid,
            'visitor_uuid' => $conversation->visitor_uuid,
            'visitor_name' => $recognizedName,
            'welcome_message' => $welcomeMessage,
            'messages' => $conversation->messages->map(fn ($m) => [
                'sender' => $m->sender,
                'content' => $m->content,
                'suggested_links' => $m->suggested_links_json,
                'created_at' => $m->created_at->format('H:i'),
            ]),
        ]);
    }
}
