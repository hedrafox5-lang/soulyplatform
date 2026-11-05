<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Services\SoulyNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Get live notifications list and unread count for current user.
     */
    public function list(Request $request): JsonResponse
    {
        $userId = Auth::id();
        $notifications = SoulyNotificationService::getLatestForUser($userId, 30);
        $unreadCount = SoulyNotificationService::getUnreadCount($userId);

        $mapped = $notifications->map(function ($item) {
            return [
                'id' => $item->id,
                'type' => $item->type,
                'title' => $item->title,
                'message' => $item->message,
                'icon' => $item->icon ?: SoulyNotificationService::resolveIcon($item->type),
                'action_url' => $item->action_url,
                'is_read' => (bool) $item->is_read,
                'created_at' => $item->created_at->diffForHumans(),
                'created_at_formatted' => $item->created_at->format('Y-m-d H:i'),
            ];
        });

        return response()->json([
            'success' => true,
            'unread_count' => $unreadCount,
            'notifications' => $mapped,
        ]);
    }

    /**
     * Mark a single notification as read.
     */
    public function markAsRead(Request $request, Notification $notification): JsonResponse
    {
        $userId = Auth::id();

        // If private notification, verify ownership
        if ($notification->user_id && $notification->user_id !== $userId && !in_array(Auth::user()->role, ['admin', 'staff'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'unread_count' => SoulyNotificationService::getUnreadCount($userId),
        ]);
    }

    /**
     * Mark all notifications as read for current user.
     */
    public function markAllRead(Request $request): JsonResponse
    {
        $userId = Auth::id();
        $updated = SoulyNotificationService::markAllAsRead($userId);

        return response()->json([
            'success' => true,
            'updated_count' => $updated,
            'unread_count' => 0,
            'message' => 'تم تحديد جميع الإشعارات كمقروءة بنجاح.',
        ]);
    }
}
