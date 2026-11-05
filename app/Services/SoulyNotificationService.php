<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class SoulyNotificationService
{
    /**
     * إرجاع عدد الإشعارات غير المقروءة للمستخدم الحالي بأمان تام
     * يمنع الانهيار في layouts/master.blade.php
     */
    public static function getUnreadCount(?int $userId = null): int
    {
        try {
            $targetUserId = $userId ?: Auth::id();

            if (!$targetUserId) {
                return 0;
            }

            // التأكد أولاً من وجود جدول notifications أو إشعارات المستخدمين لتفادي أي خطأ بقاعدة البيانات
            if (class_exists(Notification::class) && Schema::hasTable('notifications')) {
                return (int) Notification::where('user_id', $targetUserId)
                    ->where(function ($query) {
                        $query->where('is_read', false)
                              ->orWhereNull('read_at');
                    })
                    ->count();
            }

            // فحص جدول إشعارات لارافيل الافتراضي إذا كان مستخدماً
            $user = Auth::user();
            if ($user && method_exists($user, 'unreadNotifications')) {
                return (int) $user->unreadNotifications()->count();
            }

            return 0;
        } catch (\Throwable $e) {
            // صامت تماماً حتى لا تنهار واجهة الموقع
            return 0;
        }
    }

    /**
     * إرجاع أحدث الإشعارات للمستخدم الحالي
     */
    public static function getLatest(int $limit = 5, ?int $userId = null)
    {
        try {
            $targetUserId = $userId ?: Auth::id();

            if (!$targetUserId) {
                return collect();
            }

            if (class_exists(Notification::class) && Schema::hasTable('notifications')) {
                return Notification::where('user_id', $targetUserId)
                    ->latest()
                    ->take($limit)
                    ->get();
            }

            $user = Auth::user();
            if ($user && method_exists($user, 'notifications')) {
                return $user->notifications()->take($limit)->get();
            }

            return collect();
        } catch (\Throwable $e) {
            return collect();
        }
    }

    /**
     * إرسال إشعار فوري وتخزينه
     */
    public function send(User $user, string $title, string $message, string $type = 'info', ?string $actionUrl = null): bool
    {
        try {
            if (class_exists(Notification::class) && Schema::hasTable('notifications')) {
                Notification::create([
                    'user_id'    => $user->id,
                    'title'      => $title,
                    'message'    => $message,
                    'type'       => $type,
                    'action_url' => $actionUrl,
                    'is_read'    => false,
                ]);
            }

            return true;
        } catch (\Throwable $e) {
            Log::error('SoulyNotificationService Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * تحديد إشعار كمقروء
     */
    public function markAsRead(int $notificationId, ?int $userId = null): bool
    {
        try {
            $targetUserId = $userId ?: Auth::id();

            if (class_exists(Notification::class) && Schema::hasTable('notifications')) {
                Notification::where('id', $notificationId)
                    ->where('user_id', $targetUserId)
                    ->update([
                        'is_read' => true,
                        'read_at' => now(),
                    ]);
            }

            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }
}