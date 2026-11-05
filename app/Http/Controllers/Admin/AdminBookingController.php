<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\User;
use App\Models\Warranty;
use App\Services\SoulyNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AdminBookingController extends Controller
{
    /**
     * Display list of service bookings.
     */
    public function index(Request $request)
    {
        $query = Booking::with(['service', 'package', 'user', 'assignedEngineer']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('sector')) {
            $query->whereHas('service', fn($q) => $q->where('sector', $request->sector));
        }

        $bookings = $query->latest()->paginate(15);
        $engineers = User::whereIn('role', ['admin', 'technician'])->get();

        return view('admin.bookings.index', compact('bookings', 'engineers'));
    }

    /**
     * Update booking general details.
     */
    public function update(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'status'         => ['nullable', 'string', 'max:50'],
            'stage'          => ['nullable', 'string', 'max:50'],
            'preferred_date' => ['nullable', 'date'],
            'preferred_time' => ['nullable', 'string', 'max:100'],
            'total_amount'   => ['nullable', 'numeric', 'min:0'],
            'admin_notes'    => ['nullable', 'string', 'max:2000'],
        ]);

        $booking->update(array_filter($validated, fn($val) => !is_null($val)));

        return back()->with('citadel_toast', 'تم تحديث بيانات الحجز بنجاح.');
    }

    /**
     * Assign engineer or field technician to booking.
     */
    public function assignEngineer(Request $request, Booking $booking, SoulyNotificationService $notifier)
    {
        $validated = $request->validate([
            'engineer_id' => ['required', 'exists:users,id'],
        ]);

        $engineer = User::findOrFail($validated['engineer_id']);

        $booking->update([
            'assigned_to' => $engineer->id,
            'status'      => 'assigned',
        ]);

        $notifier->notify(
            $engineer,
            'تكليف بزيارة ميدانية جديدة',
            "تم تكليفك بالزيارة الميدانية رقم #{$booking->booking_number} للعميل {$booking->name}",
            route('admin.bookings.index')
        );

        return back()->with('citadel_toast', 'تم تكليف المهندس المختص بنجاح.');
    }

    /**
     * Toggle phone confirmation status.
     */
    public function toggleCallConfirmed(Booking $booking)
    {
        $booking->update([
            'call_confirmed' => !$booking->call_confirmed,
        ]);

        return back()->with('citadel_toast', 'تم تحديث حالة تأكيد الاتصال الهاتفي.');
    }

    /**
     * Update booking pipeline stage.
     */
    public function updateStage(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'stage' => ['required', 'string', 'max:50'],
        ]);

        $booking->update(['stage' => $validated['stage']]);

        return back()->with('citadel_toast', 'تم تحديث مرحلة الحجز الميداني بنجاح.');
    }

    /**
     * Start field visit and start tracking timer.
     */
    public function startVisit(Booking $booking)
    {
        $booking->update([
            'status'           => 'in_progress',
            'visit_started_at' => now(),
        ]);

        return back()->with('citadel_toast', 'تم تسجيل بدء الزيارة الميدانية وتشغيل العداد.');
    }

    /**
     * Complete visit, attach spare parts, and issue invoice with warranty.
     */
    public function completeVisit(Request $request, Booking $booking, SoulyNotificationService $notifier)
    {
        $validated = $request->validate([
            'parts_cost'       => ['nullable', 'numeric', 'min:0'],
            'parts_notes'      => ['nullable', 'string', 'max:500'],
            'technician_notes' => ['nullable', 'string', 'max:1000'],
            'warranty_months'  => ['nullable', 'integer', 'min:0'],
        ]);

        try {
            DB::transaction(function () use ($booking, $validated, $notifier) {
                $partsCost = (float)($validated['parts_cost'] ?? 0.00);
                $serviceCost = (float)($booking->total_amount ?? 0.00);
                $grandTotal = $serviceCost + $partsCost;

                $booking->update([
                    'status'             => 'completed',
                    'visit_completed_at' => now(),
                    'technician_notes'   => $validated['technician_notes'] ?? null,
                    'parts_cost'         => $partsCost,
                ]);

                $invoice = Invoice::create([
                    'invoice_number' => 'INV-' . date('ymd') . '-' . strtoupper(Str::random(4)),
                    'user_id'        => $booking->user_id ?? 1,
                    'client_name'    => $booking->name ?? 'عميل سولي',
                    'client_phone'   => $booking->phone ?? '01000000000',
                    'client_email'   => $booking->email,
                    'subtotal'       => $serviceCost,
                    'total_amount'   => $grandTotal,
                    'payment_status' => ($booking->payment_status === 'paid' && $partsCost <= 0) ? 'paid' : 'unpaid',
                    'payment_method' => $booking->payment_method ?? 'cash',
                    'source_type'    => 'field_service',
                    'source_id'      => $booking->id,
                    'hash_id'        => (string)Str::uuid(),
                ]);

                InvoiceItem::create([
                    'invoice_id'      => $invoice->id,
                    'item_name'       => $booking->service?->name_ar ?? 'زيارة صيانة ميدانية معتمدة',
                    'quantity'        => 1,
                    'unit_price'      => $serviceCost,
                    'total_price'     => $serviceCost,
                    'warranty_days'   => ((int)($validated['warranty_months'] ?? 3)) * 30,
                    'warranty_policy' => 'صيانة وضمان مجاني ضد عيوب التركيب والتشغيل',
                ]);

                if ($partsCost > 0) {
                    InvoiceItem::create([
                        'invoice_id'      => $invoice->id,
                        'item_name'       => 'قطع غيار ومستلزمات صيانة: ' . ($validated['parts_notes'] ?? 'قطع غيار أصلية'),
                        'quantity'        => 1,
                        'unit_price'      => $partsCost,
                        'total_price'     => $partsCost,
                        'warranty_days'   => ((int)($validated['warranty_months'] ?? 6)) * 30,
                        'warranty_policy' => 'ضمان استبدال قطع الغيار المعتمدة',
                    ]);
                }

                $warrantyMonths = (int)($validated['warranty_months'] ?? 3);
                if ($warrantyMonths > 0) {
                    Warranty::create([
                        'certificate_number'      => 'WAR-' . date('ymd') . '-' . strtoupper(Str::random(4)),
                        'invoice_id'              => $invoice->id,
                        'user_id'                 => $booking->user_id ?? 1,
                        'product_or_service_name' => $booking->service?->name_ar ?? 'صيانة ميدانية معتمدة',
                        'coverage_type'           => 'repair',
                        'starts_at'               => now(),
                        'expires_at'              => now()->addMonths($warrantyMonths),
                        'status'                  => 'active',
                        'terms_and_conditions'    => 'شهادة ضمان رقمية معتمدة من منصة سولي للتكنولوجيا.',
                    ]);
                }

                if ($booking->user) {
                    $notifier->notify(
                        $booking->user,
                        'اكتمال الزيارة الميدانية',
                        "تم اكتمال الزيارة الميدانية رقم #{$booking->booking_number} وإصدار الفاتورة الإلكترونية والضمان.",
                        route('portal.bookings.index')
                    );
                }
            });

            return back()->with('citadel_toast', 'تم إنهاء الزيارة وإصدار الفاتورة الإلكترونية والضمان بنجاح.');

        } catch (\Throwable $e) {
            Log::error('Complete Visit Error: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء حفظ بيانات إنهاء الزيارة: ' . $e->getMessage());
        }
    }
}