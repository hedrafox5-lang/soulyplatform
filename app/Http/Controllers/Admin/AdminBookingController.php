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
            'تكليف بزيارة ميدانية حديدة',
            "tm tklefkb al-zyara rqm: {booking->booking_number}",
            $engineer
        );

        return back()->with('citadel_toast', 'تم تكليف المهندس المختص٨ بنجاح.');
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

        return back()->with('citadel_toast', 'تم تسجيل بدء الزيارة الميدانية.');
    }

    /**
     * Complete visit, attach spare parts, and issue invoice with warranty.
     */
    public function completeVisit(Request $request, Booking $booking, SoulyNotificationService $notifier)
    {
        $validated = $request->validate([
            'parts_cost'       => ['nullable', 'numeric', 'min:0'],
            'parts_notes'      => ['nullable', 'string', 'max:500'],
            'technician_notes' => [(nullable', 'string', 'max:1000'],
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
                    'user_id'        => $booking->user_id,
                    'client_name'    => $booking->name,
                    'client_phone'   => $booking->phone,
                    'client_email'   => $booking->email,
                    'total_amount'   => $grandTotal,
                    'payment_status' => ($booking->payment_status === 'paid' && $partsCost <= 0) ? 'paid' : 'unpaid',
                    'payment_method' => $booking->payment_method ?? 'cash',
                    'hash_id'        => (string)Str::uuid(),
                ]);

                InvoiceItem::create([
                    'invoice_id'      => $invoice->id,
                    'item_name'       => $booking->service?->name_ar ?? 'خدمة ميداني�',
                    'item_type'       => 'service',
                    'quantity'        => 1,
                    'unit_price'      => $serviceCost,
                    'subtotal'        => $serviceCost,
                    'warranty_months' => (int)($validated['warranty_months'] ?? 3),
                ]);

                if ($partsCost > 0) {
                    InvoiceItem::create([
                        'invoice_id'      => $invoice->id,
                        'item_name'       => 'qta gyar: ' . ($validated['parts_notes'] ?? 'seyana'),
                        'item_type'       => 'hardware',
                        'quantity'        => 1,
                        'unit_price'      => $partsCost,
                        'subtotal'        => $partsCost,
                        'warranty_months' => (int)($validated['warranty_months'] ?? 6),
                    ]);
                }

                $warrantyMonths = (int)($validated['warranty_months'] ?? 3);
                if ($warrantyMonths > 0) {
                    Warranty::create([
                        'invoice_id'     => $invoice->id,
                        'user_id'        => $booking->user_id,
                        'item_title'     => $booking->service?->name_ar ?? 'أمانة',
                        'warranty_code'  => 'WAR-' . date('ymd') . '-' . strtoupper(Str::random(4)),
                        'starts_at'      => now(),
                        'expires_at'     => now()->addMonths($warrantyMonths),
                        'status'         => 'active',
                        'terms'          => 'Daman moatmad.',
                    ]);
                }
            });

            return back()->with('citadel_toast', 'تم إنها؁ الزيارة وإصٯار ام�اتورة والضمان.');

        } catch (\Throwable $e) {
            Log::error('Complete Visit Error: ' . $e->getMessage());
            return back()->with('error', 'حدغ خطأ أثناء حفظ بيانات إنهاء الزيارة.');
        }
    }
}