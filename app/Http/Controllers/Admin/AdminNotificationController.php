<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SoulyNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminNotificationController extends Controller
{
    /**
     * Dispatch a manual notification or broadcast from Admin Panel.
     */
    public function sendManual(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'recipient' => ['required', 'string'], // 'all', 'clients', 'admins', or user ID
            'specific_user_id' => ['nullable', 'exists:users,id'],
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:2000'],
            'type' => ['required', 'string', 'in:info,wallet,system,booking,crm'],
            'action_url' => ['nullable', 'string', 'max:255'],
        ], [
            'title.required' => 'يرجى كتابة عنوان الإشعار.',
            'message.required' => 'يرجى كتابة نص الإشعار.',
            'type.required' => 'يرجى تحديد تصنيف الإشعار.',
        ]);

        $recipient = $validated['recipient'];
        $title = $validated['title'];
        $message = $validated['message'];
        $type = $validated['type'];
        $actionUrl = $validated['action_url'] ?? null;
        $sentCount = 0;

        if ($recipient === 'all') {
            SoulyNotificationService::broadcastToAll($type, $title, $message, $actionUrl);
            $targetLabel = "كافة مستخدمي المنظومة (بث عام)";
            $sentCount = User::where('is_active', true)->count();
        } elseif ($recipient === 'clients') {
            $sentCount = SoulyNotificationService::sendToAllClients($type, $title, $message, $actionUrl);
            $targetLabel = "جميع العملاء المسجلين ({$sentCount} عميل)";
        } elseif ($recipient === 'admins') {
            $admins = SoulyNotificationService::sendToAdmins($type, $title, $message, $actionUrl);
            $sentCount = count($admins);
            $targetLabel = "فريق الإدارة والدعم ({$sentCount} مهندس)";
        } else {
            // Specific User
            $userId = ($validated['specific_user_id'] ?? null) ?: (is_numeric($recipient) ? (int)$recipient : null);
            $targetUser = $userId ? User::find($userId) : null;
            if (!$targetUser) {
                return back()->with('citadel_toast', [
                    'type' => 'error',
                    'message' => 'تعذر تحديد المستخدم المستهدف بالإشعار.'
                ]);
            }

            SoulyNotificationService::send($targetUser->id, $type, $title, $message, $actionUrl);
            $targetLabel = "العميل: {$targetUser->name}";
            $sentCount = 1;
        }

        $successMsg = "تم إرسال وبث الإشعار بنجاح إلى: {$targetLabel}!";

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'sent_count' => $sentCount,
                'message' => $successMsg,
            ]);
        }

        return back()->with('citadel_toast', [
            'type' => 'success',
            'message' => $successMsg,
        ]);
    }
}
