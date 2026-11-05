<?php

namespace App\Services;

use App\Models\AiActionSnapshot;
use App\Models\Booking;
use App\Models\ErpStoreCatalog;
use App\Models\HardwareProduct;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\Notification;
use App\Models\Service;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminAiToolExecutor
{
    /**
     * Record a snapshot before performing any database write/update/delete.
     */
    protected function recordSnapshot(?int $adminId, string $actionType, string $targetTable, int $targetId, ?array $beforeState, ?array $afterState, string $descriptionAr): AiActionSnapshot
    {
        return AiActionSnapshot::create([
            'admin_id' => $adminId,
            'action_type' => $actionType,
            'target_table' => $targetTable,
            'target_id' => $targetId,
            'before_state_json' => $beforeState,
            'after_state_json' => $afterState,
            'description_ar' => $descriptionAr,
            'is_reverted' => false,
        ]);
    }

    /**
     * Revert any previously executed action snapshot in 1-tap.
     */
    public function revertSnapshot(int $snapshotId): array
    {
        $snapshot = AiActionSnapshot::find($snapshotId);
        if (!$snapshot) {
            return ['success' => false, 'message' => 'لم يتم العثور على لقطة العملية المحددة.'];
        }

        if ($snapshot->is_reverted) {
            return ['success' => false, 'message' => 'تم التراجع عن هذه العملية بالفعل مسبقاً.'];
        }

        $table = $snapshot->target_table;
        $id = $snapshot->target_id;
        $type = $snapshot->action_type;

        DB::beginTransaction();
        try {
            if ($type === 'create') {
                // Delete the created record
                DB::table($table)->where('id', $id)->delete();
                $snapshot->update(['is_reverted' => true]);
                DB::commit();
                return [
                    'success' => true,
                    'message' => "تم التراجع بنجاح: تم حذف السجل المنشأ من جدول ({$table}) برقم #{$id}.",
                    'snapshot' => $snapshot,
                ];
            }

            if ($type === 'update') {
                // Restore before state
                if (!empty($snapshot->before_state_json)) {
                    $restoreData = $snapshot->before_state_json;
                    unset($restoreData['id'], $restoreData['created_at'], $restoreData['updated_at']);
                    DB::table($table)->where('id', $id)->update($restoreData);
                }
                $snapshot->update(['is_reverted' => true]);
                DB::commit();
                return [
                    'success' => true,
                    'message' => "تم التراجع بنجاح: تمت استعادة الحالة الأصلية للسجل #{$id} في جدول ({$table}).",
                    'snapshot' => $snapshot,
                ];
            }

            if ($type === 'delete') {
                // Re-insert deleted record
                if (!empty($snapshot->before_state_json)) {
                    DB::table($table)->insert($snapshot->before_state_json);
                }
                $snapshot->update(['is_reverted' => true]);
                DB::commit();
                return [
                    'success' => true,
                    'message' => "تم التراجع بنجاح: تمت استعادة السجل المحذوف #{$id} في جدول ({$table}).",
                    'snapshot' => $snapshot,
                ];
            }

            DB::rollBack();
            return ['success' => false, 'message' => 'نوع العملية غير مدعوم للتراجع.'];
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("[AdminAiToolExecutor] Failed to revert snapshot #{$snapshotId}: " . $e->getMessage());
            return ['success' => false, 'message' => 'حدث خطأ أثناء محاولة التراجع: ' . $e->getMessage()];
        }
    }

    /**
     * Executive Morning Briefing
     */
    public function getMorningBriefing(): array
    {
        $today = Carbon::today();

        $todayBookings = Booking::with(['user', 'service'])
            ->whereDate('scheduled_at', $today)
            ->get();

        $unconfirmedCalls = Booking::where('call_confirmed', false)
            ->whereIn('status', ['new', 'confirmed'])
            ->count();

        $pendingWallets = WalletTransaction::where('type', 'deposit')
            ->where('status', 'pending')
            ->count();

        $openTickets = Ticket::whereIn('status', ['open', 'in_progress'])->count();

        $todayRevenue = Booking::whereDate('created_at', $today)
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        $activeLeadsCount = Lead::where('status', 'new')->count();

        return [
            'date_arabic' => Carbon::now()->locale('ar')->isoFormat('dddd، D MMMM YYYY'),
            'today_bookings_count' => $todayBookings->count(),
            'today_bookings' => $todayBookings->take(5)->map(function ($b) {
                return [
                    'id' => $b->id,
                    'booking_number' => $b->booking_number,
                    'client_name' => $b->user->name ?? 'عميل',
                    'client_phone' => $b->user->phone ?? '',
                    'service' => $b->service->name ?? '',
                    'time' => $b->scheduled_at ? $b->scheduled_at->format('h:i A') : 'غير محدد',
                    'call_confirmed' => (bool) $b->call_confirmed,
                    'stage' => $b->pipeline_stage,
                ];
            })->values()->all(),
            'unconfirmed_calls_count' => $unconfirmedCalls,
            'pending_wallets_count' => $pendingWallets,
            'open_tickets_count' => $openTickets,
            'today_revenue' => (float) $todayRevenue,
            'active_leads_count' => $activeLeadsCount,
            'workload_percentage' => min(100, round(($todayBookings->count() / 8) * 100)), // Based on 8 slots capacity
        ];
    }

    /**
     * Update call confirmation status on a booking.
     */
    public function toggleBookingCallConfirmed(int $bookingId, bool $confirmed, ?int $adminId = null): array
    {
        $booking = Booking::find($bookingId);
        if (!$booking) {
            return ['success' => false, 'message' => "الحجز رقم #{$bookingId} غير موجود."];
        }

        $before = $booking->toArray();
        $booking->call_confirmed = $confirmed;
        $booking->call_confirmed_at = $confirmed ? Carbon::now() : null;
        if ($confirmed && $booking->pipeline_stage === 'awaiting_call') {
            $booking->pipeline_stage = 'confirmed_scheduled';
        }
        $booking->save();

        $snapshot = $this->recordSnapshot(
            $adminId,
            'update',
            'bookings',
            $booking->id,
            $before,
            $booking->fresh()->toArray(),
            $confirmed ? "تأكيد المكالمة الهاتفية للحجز {$booking->booking_number}" : "إلغاء تأكيد المكالمة للحجز {$booking->booking_number}"
        );

        return [
            'success' => true,
            'message' => $confirmed ? "تم تأكيد المكالمة الهاتفية للعميل بنجاح 📞" : "تم إلغاء حالة تأكيد المكالمة.",
            'booking' => $booking->fresh(),
            'snapshot_id' => $snapshot->id,
        ];
    }

    /**
     * Update booking maintenance pipeline stage.
     */
    public function updateBookingStage(int $bookingId, string $stage, ?int $adminId = null): array
    {
        $validStages = [
            'initial_contact' => 'تواصل أولي',
            'awaiting_call' => 'بانتظار مكالمة التأكيد',
            'awaiting_hardware_topup' => 'بانتظار عربون/شحن الهاردوير',
            'confirmed_scheduled' => 'مؤكد ومجدول للزيارة',
            'visit_in_progress' => 'الزيارة الميدانية جارية الآن',
            'completed_warranty' => 'مكتمل وساري الضمان الهندسي',
        ];

        if (!array_key_exists($stage, $validStages)) {
            return ['success' => false, 'message' => 'المرحلة المحددة غير صالحة.'];
        }

        $booking = Booking::find($bookingId);
        if (!$booking) {
            return ['success' => false, 'message' => "الحجز رقم #{$bookingId} غير موجود."];
        }

        $before = $booking->toArray();
        $booking->pipeline_stage = $stage;
        if ($stage === 'visit_in_progress' && !$booking->visit_started_at) {
            $booking->visit_started_at = Carbon::now();
        }
        if ($stage === 'completed_warranty') {
            $booking->status = 'completed';
        }
        $booking->save();

        $snapshot = $this->recordSnapshot(
            $adminId,
            'update',
            'bookings',
            $booking->id,
            $before,
            $booking->fresh()->toArray(),
            "تحديث مرحلة الصيانة للحجز {$booking->booking_number} إلى [{$validStages[$stage]}]"
        );

        return [
            'success' => true,
            'message' => "تم تحديث مرحلة الصيانة بنجاح إلى: {$validStages[$stage]}",
            'booking' => $booking->fresh(),
            'snapshot_id' => $snapshot->id,
        ];
    }

    /**
     * Update Hardware product price, stock, or status.
     */
    public function updateHardwareProduct(int $productId, array $data, ?int $adminId = null): array
    {
        $product = HardwareProduct::find($productId);
        if (!$product) {
            return ['success' => false, 'message' => "منتج الهاردوير رقم #{$productId} غير موجود."];
        }

        $before = $product->toArray();
        if (isset($data['stock_qty'])) {
            $product->stock_qty = $data['stock_qty'];
        } elseif (isset($data['stock_quantity'])) {
            $product->stock_qty = $data['stock_quantity'];
        }
        if (isset($data['price'])) {
            $product->price = $data['price'];
        }
        if (isset($data['old_price'])) {
            $product->old_price = $data['old_price'];
        } elseif (isset($data['original_price'])) {
            $product->old_price = $data['original_price'];
        }
        if (isset($data['name_ar'])) {
            $product->name_ar = $data['name_ar'];
        }
        if (isset($data['is_active'])) {
            $product->is_active = $data['is_active'];
        }
        $product->save();

        $snapshot = $this->recordSnapshot(
            $adminId,
            'update',
            'hardware_products',
            $product->id,
            $before,
            $product->fresh()->toArray(),
            "تعديل منتج الهاردوير: {$product->name}"
        );

        return [
            'success' => true,
            'message' => "تم تحديث منتج الهاردوير ({$product->name}) بنجاح! السعر: {$product->price} ج.م | المخزون: {$product->stock_quantity}",
            'product' => $product->fresh(),
            'snapshot_id' => $snapshot->id,
        ];
    }

    /**
     * Update BrandERP catalog feature price, popular status, or active state.
     */
    public function updateErpFeature(int $featureId, array $data, ?int $adminId = null): array
    {
        $feature = ErpStoreCatalog::find($featureId);
        if (!$feature) {
            return ['success' => false, 'message' => "ميزة BrandERP رقم #{$featureId} غير موجودة."];
        }

        $before = $feature->toArray();
        $feature->fill(array_intersect_key($data, array_flip([
            'name_ar', 'price', 'is_active', 'is_popular', 'is_included_in_core', 'module_group'
        ])));
        $feature->save();

        $snapshot = $this->recordSnapshot(
            $adminId,
            'update',
            'erp_store_catalog',
            $feature->id,
            $before,
            $feature->fresh()->toArray(),
            "تعديل ميزة BrandERP: {$feature->name_ar}"
        );

        return [
            'success' => true,
            'message' => "تم تحديث ميزة BrandERP ({$feature->name_ar}) بنجاح! السعر: {$feature->price} ج.م",
            'feature' => $feature->fresh(),
            'snapshot_id' => $snapshot->id,
        ];
    }

    /**
     * Approve wallet deposit transaction and credit balance.
     */
    public function approveWalletDeposit(int $transactionId, ?int $adminId = null): array
    {
        $tx = WalletTransaction::with('wallet.user')->find($transactionId);
        if (!$tx || $tx->type !== 'deposit') {
            return ['success' => false, 'message' => "معاملة الإيداع رقم #{$transactionId} غير موجودة."];
        }

        if ($tx->status !== 'pending') {
            return ['success' => false, 'message' => "هذه المعاملة تم معالجتها مسبقاً (الحالة: {$tx->status})."];
        }

        $wallet = $tx->wallet;
        if (!$wallet) {
            return ['success' => false, 'message' => 'المحفظة المرتبطة غير موجودة.'];
        }

        $beforeWallet = $wallet->toArray();
        $beforeTx = $tx->toArray();

        DB::beginTransaction();
        try {
            $tx->update(['status' => 'approved']);
            $wallet->increment('balance', $tx->amount);

            $snapshot = $this->recordSnapshot(
                $adminId,
                'update',
                'wallet_transactions',
                $tx->id,
                ['tx' => $beforeTx, 'wallet' => $beforeWallet],
                ['tx' => $tx->fresh()->toArray(), 'wallet' => $wallet->fresh()->toArray()],
                "اعتماد إيداع محفظة بقيمة {$tx->amount} ج.م للعميل {$wallet->user->name}"
            );

            DB::commit();

            return [
                'success' => true,
                'message' => "تم اعتماد إيداع بقيمة {$tx->amount} ج.م وإضافتها لمحفظة ({$wallet->user->name}) بنجاح! الرصيد الجديد: {$wallet->fresh()->balance} ج.م",
                'snapshot_id' => $snapshot->id,
            ];
        } catch (\Throwable $e) {
            DB::rollBack();
            return ['success' => false, 'message' => 'فشل اعتماد الإيداع: ' . $e->getMessage()];
        }
    }

    /**
     * Direct credit/debit client wallet.
     */
    public function adjustClientWallet(int $userId, float $amount, string $reason, ?int $adminId = null): array
    {
        $user = User::with('wallet')->find($userId);
        if (!$user) {
            return ['success' => false, 'message' => "المستخدم رقم #{$userId} غير موجود."];
        }

        $wallet = $user->getOrCreateWallet();
        $before = $wallet->toArray();

        if ($amount < 0 && $wallet->balance < abs($amount)) {
            return ['success' => false, 'message' => "رصيد المحفظة الحالي ({$wallet->balance} ج.م) لا يكفي لخصم " . abs($amount) . " ج.م."];
        }

        $wallet->balance += $amount;
        $wallet->save();

        WalletTransaction::create([
            'wallet_id' => $wallet->id,
            'type' => $amount >= 0 ? 'deposit' : 'withdrawal',
            'amount' => abs($amount),
            'status' => 'approved',
            'payment_method' => 'admin_adjustment',
            'reference_id' => 'ADMIN-ADJ-' . strtoupper(substr(uniqid(), -6)),
            'notes' => $reason ?: 'تعديل يدوي بواسطة وكيل الإدارة التنفيذي',
        ]);

        $snapshot = $this->recordSnapshot(
            $adminId,
            'update',
            'wallets',
            $wallet->id,
            $before,
            $wallet->fresh()->toArray(),
            "تعديل رصيد محفظة العميل {$user->name} بمقدار {$amount} ج.م ({$reason})"
        );

        return [
            'success' => true,
            'message' => "تم تعديل رصيد محفظة العميل ({$user->name}) بنجاح! الرصيد الحالي: {$wallet->balance} ج.م",
            'snapshot_id' => $snapshot->id,
        ];
    }

    /**
     * Create client and booking from parsed raw chat.
     */
    public function createDossierFromChat(array $parsedData, ?int $adminId = null): array
    {
        $phone = preg_replace('/[^0-9]/', '', $parsedData['phone'] ?? '');
        if (strlen($phone) >= 10 && !str_starts_with($phone, '20') && !str_starts_with($phone, '0')) {
            $phone = '0' . $phone;
        }

        $name = trim($parsedData['name'] ?? '') ?: 'عميل سولي المحتمل';
        $email = $parsedData['email'] ?? null;
        if (!$email) {
            $email = 'client_' . ($phone ?: uniqid()) . '@souly.eg';
        }

        DB::beginTransaction();
        try {
            // Find or create user
            $user = null;
            if (!empty($phone)) {
                $user = User::where('phone', $phone)->first();
            }
            if (!$user) {
                $user = User::create([
                    'name' => $name,
                    'phone' => $phone ?: '01000000000',
                    'email' => $email,
                    'password' => bcrypt('Souly@2026'),
                    'role' => 'client',
                ]);
            }

            // Find matching service
            $service = Service::where('slug', 'hardware-maintenance')->first()
                ?: Service::first();

            $bookingNumber = 'SOULY-BK-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

            $requirements = implode("\n", array_filter([
                !empty($parsedData['device_model']) ? "الجهاز: " . $parsedData['device_model'] : null,
                !empty($parsedData['district']) ? "العنوان/المنطقة: " . $parsedData['district'] : null,
                !empty($parsedData['issue_description']) ? "وصف العطل: " . $parsedData['issue_description'] : null,
                !empty($parsedData['required_hardware']) ? "القطع المطلوبة: " . $parsedData['required_hardware'] : null,
            ]));

            $stage = $parsedData['recommended_stage'] ?? 'awaiting_call';
            $scheduledAt = !empty($parsedData['scheduled_at']) ? Carbon::parse($parsedData['scheduled_at']) : Carbon::tomorrow()->setHour(14)->setMinute(0);

            $booking = Booking::create([
                'booking_number' => $bookingNumber,
                'user_id' => $user->id,
                'service_id' => $service ? $service->id : 1,
                'quantity_or_hours' => 1,
                'total_amount' => 1200.00, // Standard 3-hour field maintenance visit base
                'payment_method' => 'on_site',
                'payment_status' => 'unpaid',
                'status' => 'confirmed',
                'pipeline_stage' => $stage,
                'call_confirmed' => false,
                'client_requirements' => $requirements,
                'chat_transcript' => $parsedData['raw_chat'] ?? null,
                'scheduled_at' => $scheduledAt,
            ]);

            // Also create or update CRM Lead for 360-degree visibility
            $lead = Lead::updateOrCreate(
                ['phone' => $user->phone],
                [
                    'name' => $user->name,
                    'email' => $user->email,
                    'business_activity' => $parsedData['device_model'] ?? 'صيانة ميدانية',
                    'interested_service_id' => $service ? $service->id : null,
                    'source' => 'whatsapp',
                    'status' => 'qualified',
                    'pipeline_stage' => $stage,
                    'call_confirmed' => false,
                    'chat_transcript' => $parsedData['raw_chat'] ?? null,
                    'ai_summary' => "تم استخراج الملف تلقائياً عبر AI Chat Parser: " . ($parsedData['issue_description'] ?? ''),
                ]
            );

            LeadActivity::create([
                'lead_id' => $lead->id,
                'user_id' => $adminId,
                'type' => 'ai_capture',
                'description' => "تم بناء ملف العميل 360 وحجز الزيارة رقم {$booking->booking_number} آلياً من نص المحادثة.",
            ]);

            $snapshot = $this->recordSnapshot(
                $adminId,
                'create',
                'bookings',
                $booking->id,
                null,
                $booking->toArray(),
                "إنشاء ملف عميل وحجز بالذكاء الاصطناعي {$booking->booking_number} للعميل {$user->name}"
            );

            DB::commit();

            return [
                'success' => true,
                'message' => "تم استخراج ملف العميل بنجاح! العميل: ({$user->name}) | الهاتف: ({$user->phone}) | الحجز رقم: ({$booking->booking_number})",
                'user' => $user,
                'booking' => $booking,
                'lead' => $lead,
                'snapshot_id' => $snapshot->id,
            ];
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("[AdminAiToolExecutor] Failed to create dossier from chat: " . $e->getMessage());
            return ['success' => false, 'message' => 'فشل إنشاء ملف العميل: ' . $e->getMessage()];
        }
    }
}
