<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\ClientDevice;
use App\Models\Service;
use App\Services\SoulyNotificationService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ClientDeviceController extends Controller
{
    /**
     * Display client registered devices and warranty status.
     */
    public function index(): View
    {
        $user = Auth::user();
        $devices = ClientDevice::where('user_id', $user->id)
            ->latest()
            ->get();

        return view('portal.devices', compact('user', 'devices'));
    }

    /**
     * Register a new device with technical specs and optional fault photos.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'model_number'  => ['required', 'string', 'max:190'],
            'serial_number' => ['nullable', 'string', 'max:100'],
            'details'       => ['required', 'string', 'max:3000'],
            'status'        => ['nullable', 'in:healthy,needs_maintenance'],
            'images'        => ['nullable', 'array', 'max:5'],
            'images.*'      => ['image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ], [
            'model_number.required' => 'يرجى إدخال اسم ورقم موديل الجهاز.',
            'details.required'      => 'يرجى كتابة تفاصيل ومواصفات الجهاز أو العطل.',
            'images.max'            => 'الحد الأقصى للصور هو 5 صور لكل جهاز.',
            'images.*.max'          => 'حجم الصورة لا يجب أن يتجاوز 5 ميجابايت.',
        ]);

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('client_devices/' . Auth::id(), 'public');
            }
        }

        ClientDevice::create([
            'user_id'       => Auth::id(),
            'model_number'  => strip_tags(trim($validated['model_number'])),
            'serial_number' => !empty($validated['serial_number']) ? strip_tags(trim($validated['serial_number'])) : null,
            'details'       => strip_tags(trim($validated['details'])),
            'status'        => $validated['status'] ?? 'needs_maintenance',
            'images_json'   => $imagePaths,
        ]);

        return redirect()->route('portal.devices')->with('citadel_toast', 'تم تسجيل وإضافة الجهاز بنجاح إلى خزينة أجهزتك.');
    }

    /**
     * Update existing registered device information.
     */
    public function update(Request $request, ClientDevice $device): RedirectResponse
    {
        abort_if((int)$device->user_id !== (int)Auth::id(), 403, 'غير مصرح لك بتعديل هذا الجهاز.');

        $validated = $request->validate([
            'model_number'  => ['required', 'string', 'max:190'],
            'serial_number' => ['nullable', 'string', 'max:100'],
            'details'       => ['required', 'string', 'max:3000'],
            'status'        => ['required', 'in:healthy,needs_maintenance'],
        ]);

        $device->update([
            'model_number'  => strip_tags(trim($validated['model_number'])),
            'serial_number' => !empty($validated['serial_number']) ? strip_tags(trim($validated['serial_number'])) : null,
            'details'       => strip_tags(trim($validated['details'])),
            'status'        => $validated['status'],
        ]);

        return redirect()->route('portal.devices')->with('citadel_toast', 'تم تحديث بيانات الجهاز بنجاح.');
    }

    /**
     * Remove device from client vault if not engaged in an active repair cycle.
     */
    public function destroy(ClientDevice $device): RedirectResponse
    {
        abort_if((int)$device->user_id !== (int)Auth::id(), 403, 'غير مصرح.');

        if (in_array($device->status, ['visit_requested', 'in_company_repair'], true)) {
            return back()->with('error', 'لا يمكن حذف جهاز قيد الصيانة الفعالة حالياً.');
        }

        if (is_array($device->images_json)) {
            foreach ($device->images_json as $path) {
                Storage::disk('public')->delete($path);
            }
        }

        $device->delete();

        return redirect()->route('portal.devices')->with('citadel_toast', 'تم حذف الجهاز من قائمتك بنجاح.');
    }

    /**
     * Dispatch an engineering maintenance booking request for this specific device.
     */
    public function requestMaintenance(Request $request, ClientDevice $device): RedirectResponse
    {
        abort_if((int)$device->user_id !== (int)Auth::id(), 403, 'غير مصرح.');

        $validated = $request->validate([
            'maintenance_type' => ['required', 'in:on_site_visit,in_company'],
            'scheduled_at'     => ['required', 'date', 'after_or_equal:today'],
            'hours'            => ['nullable', 'integer', 'min:3', 'max:12'],
            'delivery_method'  => ['nullable', 'string', 'max:100'],
            'address'          => ['required', 'string', 'max:500'],
            'issue_notes'      => ['required', 'string', 'max:2000'],
        ]);

        // Find primary engineering maintenance service safely
        $maintenanceService = Service::where('sector', 'engineering_maintenance')
            ->where('is_active', true)
            ->first() ?? Service::where('slug', 'maintenance')->first();

        if (!$maintenanceService) {
            return back()->with('error', 'خدمة الصيانة الهندسية غير متاحة حالياً، يرجى التواصل مع الدعم الفني.');
        }

        $isVisit = $validated['maintenance_type'] === 'on_site_visit';
        $hours = $isVisit ? max(3, (int)($validated['hours'] ?? 3)) : 3;
        $hourlyRate = (float)($maintenanceService->base_price ?? 400.00);
        $totalAmount = $hours * $hourlyRate;
        $bookingNumber = 'BK-' . date('ymd') . '-' . strtoupper(Str::random(5));

        $booking = Booking::create([
            'booking_number'      => $bookingNumber,
            'user_id'             => Auth::id(),
            'service_id'          => $maintenanceService->id,
            'is_field_visit'      => $isVisit,
            'quantity_or_hours'   => $hours,
            'total_amount'        => $totalAmount,
            'payment_method'      => 'on_site',
            'payment_status'      => 'unpaid',
            'status'              => 'new',
            'preferred_date'      => $validated['scheduled_at'],
            'preferred_time'      => $isVisit ? 'فترة محددة بالصيانة' : null,
            'address'             => strip_tags(trim($validated['address'])),
            'notes'               => sprintf(
                "[%s] موديل الجهاز: %s | سريال: %s | العطل: %s",
                $isVisit ? 'زيارة صيانة هندسية ميدانية' : 'صيانة بمقر الشركة (' . ($validated['delivery_method'] ?? 'تسليم شخصي') . ')',
                $device->model_number,
                $device->serial_number ?? 'غير مدخل',
                strip_tags(trim($validated['issue_notes']))
            ),
        ]);

        $device->update([
            'status'                => $isVisit ? 'visit_requested' : 'in_company_repair',
            'last_maintenance_type' => $validated['maintenance_type'],
        ]);

        SoulyNotificationService::sendToAdmins(
            'booking',
            'طلب صيانة جهاز جديد 🔧',
            "طلب صيانة رقم #{$booking->booking_number} للجهاز ({$device->model_number}) من العميل " . Auth::user()->name,
            route('admin.bookings.index'),
            'wrench'
        );

        return redirect()->route('portal.devices')->with('citadel_toast', "تم تسجيل طلب صيانة الجهاز بنجاح برقم حجز ({$bookingNumber}).");
    }
}
