<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiActionSnapshot;
use App\Models\AiApiKey;
use App\Models\Setting;
use App\Services\AdminAiToolExecutor;
use App\Services\AdminSovereignAgentService;
use App\Services\SoulyAiKeyPoolService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminAiCopilotController extends Controller
{
    protected AdminSovereignAgentService $agentService;
    protected AdminAiToolExecutor $toolExecutor;
    protected SoulyAiKeyPoolService $keyPoolService;

    public function __construct(
        AdminSovereignAgentService $agentService,
        AdminAiToolExecutor $toolExecutor,
        SoulyAiKeyPoolService $keyPoolService
    ) {
        $this->agentService = $agentService;
        $this->toolExecutor = $toolExecutor;
        $this->keyPoolService = $keyPoolService;
    }

    /**
     * Show Sovereign Admin AI Copilot Full View.
     */
    public function index(Request $request): View
    {
        $briefing = $this->toolExecutor->getMorningBriefing();
        $adminModel = $this->keyPoolService->getAdminModel();
        $visitorModel = $this->keyPoolService->getVisitorModel();
        $keys = AiApiKey::orderBy('priority')->get();
        $recentSnapshots = AiActionSnapshot::with('admin')->latest()->take(10)->get();

        return view('admin.ai-copilot.index', compact(
            'briefing',
            'adminModel',
            'visitorModel',
            'keys',
            'recentSnapshots'
        ));
    }

    /**
     * Send command / chat to Sovereign Admin AI Agent.
     */
    public function chat(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:5000',
        ]);

        $adminId = auth()->id();
        $result = $this->agentService->handleCommand($request->input('message'), $adminId);

        return response()->json($result);
    }

    /**
     * Parse raw WhatsApp / Facebook chat transcript to create 360 dossier & booking.
     */
    public function parseChat(Request $request): JsonResponse
    {
        $request->validate([
            'chat_transcript' => 'required|string|max:20000',
        ]);

        $adminId = auth()->id();
        $result = $this->agentService->parseChatAndCreateDossier($request->input('chat_transcript'), $adminId);

        return response()->json($result);
    }

    /**
     * 1-Tap Undo for any database action.
     */
    public function undo(int $snapshotId): JsonResponse
    {
        $result = $this->toolExecutor->revertSnapshot($snapshotId);

        return response()->json($result);
    }

    /**
     * Get Morning Executive Briefing as JSON.
     */
    public function briefing(): JsonResponse
    {
        $briefing = $this->toolExecutor->getMorningBriefing();

        return response()->json($briefing);
    }

    /**
     * Change Active Dynamic Model.
     */
    public function changeModel(Request $request): JsonResponse
    {
        $request->validate([
            'model' => 'required|string|max:100',
            'scope' => 'nullable|in:admin,visitor,both',
        ]);

        $model = trim($request->input('model'));
        $scope = $request->input('scope', 'admin');

        if (in_array($scope, ['admin', 'both'])) {
            Setting::set('ai_admin_model', $model, 'system', 'Dynamic AI Model for Admin Sovereign Agent');
        }
        if (in_array($scope, ['visitor', 'both'])) {
            Setting::set('ai_visitor_model', $model, 'system', 'Dynamic AI Model for Visitor Chat');
        }

        return response()->json([
            'success' => true,
            'message' => "تم تحديث الموديل النشط بنجاح إلى: {$model}",
            'model' => $model,
        ]);
    }

    /**
     * Ping test for an API Key.
     */
    public function pingKey(AiApiKey $key): JsonResponse
    {
        $res = $this->keyPoolService->pingKey($key);

        return response()->json($res);
    }

    /**
     * Store new API Key in pool.
     */
    public function storeKey(Request $request): JsonResponse
    {
        $request->validate([
            'label' => 'required|string|max:150',
            'account_email' => 'nullable|email|max:150',
            'provider' => 'required|in:gemini,pinecone',
            'api_key' => 'required|string|max:500',
            'scope' => 'required|in:all,admin_only,visitor_only',
            'priority' => 'nullable|integer|min:1|max:10',
        ]);

        $key = AiApiKey::create([
            'label' => $request->input('label'),
            'account_email' => $request->input('account_email'),
            'provider' => $request->input('provider'),
            'api_key' => $request->input('api_key'),
            'scope' => $request->input('scope', 'all'),
            'priority' => $request->input('priority', 1),
            'status' => 'active',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تمت إضافة المفتاح الجديد إلى خزينة الـ AI بنجاح.',
            'key' => $key,
        ]);
    }

    /**
     * Toggle Key active / disabled.
     */
    public function toggleKey(AiApiKey $key): JsonResponse
    {
        $newStatus = ($key->status === 'disabled') ? 'active' : 'disabled';
        $key->update([
            'status' => $newStatus,
            'cooldown_until' => null,
        ]);

        return response()->json([
            'success' => true,
            'status' => $newStatus,
            'message' => $newStatus === 'active' ? 'تم تفعيل المفتاح.' : 'تم تعطيل المفتاح.',
        ]);
    }

    /**
     * Delete API Key.
     */
    public function destroyKey(AiApiKey $key): JsonResponse
    {
        $key->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف المفتاح من الخزينة.',
        ]);
    }
}
