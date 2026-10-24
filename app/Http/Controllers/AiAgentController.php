<?php

namespace App\Http\Controllers;

use App\Models\AiConversation;
use App\Services\SoulyAiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AiAgentController extends Controller
{
    /**
     * Process message from interactive AI widget.
     */
    public function chat(Request $request, SoulyAiService $aiService): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
            'conversation_id' => ['nullable', 'string', 'max:64'],
            'visitor_name' => ['nullable', 'string', 'max:100'],
            'visitor_phone' => ['nullable', 'string', 'max:50'],
        ]);

        $result = $aiService->reply(
            message: $validated['message'],
            sessionUuid: $validated['conversation_id'] ?? null,
            visitorName: $validated['visitor_name'] ?? null,
            visitorPhone: $validated['visitor_phone'] ?? null
        );

        return response()->json($result);
    }

    /**
     * Retrieve previous messages for a conversation session.
     */
    public function history(string $sessionUuid): JsonResponse
    {
        $conversation = AiConversation::with('messages')
            ->where('session_uuid', $sessionUuid)
            ->first();

        if (!$conversation) {
            return response()->json(['messages' => []]);
        }

        return response()->json([
            'session_uuid' => $conversation->session_uuid,
            'messages' => $conversation->messages->map(fn ($m) => [
                'sender' => $m->sender,
                'content' => $m->content,
                'suggested_links' => $m->suggested_links_json,
                'created_at' => $m->created_at->format('H:i'),
            ]),
        ]);
    }
}
