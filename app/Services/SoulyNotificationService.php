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

            if (class_exists(Notification::class) && Schema::hasTable('notifications')) {
                return (int) Notification::where('user_id', $targetUserId)
                    ->where(function ($query) {
                        $query->where('is_read', false)
                              ->orWhereNull('read_at');
                    })
                    ->count();
            }

            $user = Auth::user();
            if ($user && method_exists($user, 'unreadNotifications')) {
                return (int) $user->unreadNotifications()->count();
            }

            return 0;
        } catch (\Throwable $e) {
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
     * إرجاع أحدث الإشعارات للمستخدم مع دعم ترتيب المعاملات (userId, limit)
     */
    public static function getLatestForUser(?int $userId = null, int $limit = 30)
    {
        return self::getLatest($limit, $userId);
    }

    /**
     * تحديد الأيقونة المناسبة لنوع الإشعار
     */
    public static function resolveIcon(?string $type = null): string
    {
        return match (strtolower((string) $type)) {
            'wallet'   => '💳',
            'booking'  => '📅',
            'ticket'   => '🎫',
            'crm'      => '💼',
            'security' => '🛡️',
            'hardware' => '💻',
            default    => '🔔',
        };
    }

    /**
     * تحديد كافة إشعارات المستخدم كمقروءة دفعة واحدة
     */
    public static function markAllAsRead(?int $userId = null): int
    {
        try {
            $targetUserId = $userId ?: Auth::id();
            if (!$targetUserId) {
                return 0;
            }

            if (class_exists(Notification::class) && Schema::hasTable('notifications')) {
                return (int) Notification::where('user_id', $targetUserId)
                    ->where('is_read', false)
                    ->update([
                        'is_read' => true,
                        'read_at' => now(),
                    ]);
            }

            return 0;
        } catch (\Throwable $e) {
            return 0;
        }
    }

    /**
     * إرسال إشعار فوري وتخزينه في جدول الإشعارات
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
     * إرسال إشعار مرن يدعم الترتيب القياسي والتبادلي للبارامترات
     */
    public function notify($first, $second = null, $third = null, $fourth = null, $fifth = 'info'): bool
    {
        // إذا كان البارامتر الأول هو كائن User
        if ($first instanceof User) {
            return $this->send($first, (string)$second, (string)$third, (string)($fifth ?: 'info'), $fourth);
        }

        // إذا كان البارامتر الثالث هو كائن User: notify('title', 'message', $user)
        if ($third instanceof User) {
            return $this->send($third, (string)$first, (string)$second, (string)($fifth ?: 'info'), $fourth);
        }

        // المستخدم المسجل حالياً كخيار احتياطي
        $currentUser = Auth::user();
        if ($currentUser) {
            return $this->send($currentUser, (string)$first, (string)$second, (string)($fifth ?: 'info'), $third);
        }

        return false;
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