<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceIcon;
use App\Models\ServicePackage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminServiceController extends Controller
{
    /**
     * Display all Services with their packages and icons count.
     */
    public function index(): View
    {
        $services = Service::with([
            'packages' => fn ($q) => $q->orderBy('display_order'),
            'icons' => fn ($q) => $q->orderBy('display_order'),
        ])->orderBy('display_order')->get();

        return view('admin.services.index', compact('services'));
    }

    /**
     * Store new Service Package.
     */
    public function storePackage(Request $request, Service $service): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:100'],
            'subtitle' => ['nullable', 'string', 'max:500'],
            'badge_text' => ['nullable', 'string', 'max:100'],
            'billing_type' => ['required', 'in:monthly,yearly,one_time,hourly'],
            'original_price' => ['nullable', 'numeric'],
            'price' => ['required', 'numeric', 'min:0'],
            'min_units' => ['nullable', 'integer', 'min:1'],
            'unit_label' => ['nullable', 'string', 'max:50'],
            'warranty_days' => ['required', 'integer', 'min:0'],
            'features' => ['nullable', 'string'],
        ]);

        $featuresArray = [];
        if (!empty($validated['features'])) {
            $featuresArray = array_filter(array_map('trim', explode("\n", str_replace("\r", "", $validated['features']))));
        }

        $service->packages()->create([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'subtitle' => $validated['subtitle'] ?? null,
            'badge_text' => $validated['badge_text'] ?? null,
            'billing_type' => $validated['billing_type'],
            'original_price' => $validated['original_price'] ?? null,
            'price' => $validated['price'],
            'min_units' => $validated['min_units'] ?? 1,
            'unit_label' => $validated['unit_label'] ?? 'شهر',
            'warranty_days' => $validated['warranty_days'],
            'features_json' => $featuresArray,
            'is_active' => true,
        ]);

        return back()->with('citadel_toast', [
            'type' => 'success',
            'message' => 'تم إضافة الباقة الجديدة بنجاح!'
        ]);
    }

    /**
     * Update existing Service Package.
     */
    public function updatePackage(Request $request, ServicePackage $package): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:500'],
            'badge_text' => ['nullable', 'string', 'max:100'],
            'billing_type' => ['required', 'in:monthly,yearly,one_time,hourly'],
            'original_price' => ['nullable', 'numeric'],
            'price' => ['required', 'numeric', 'min:0'],
            'min_units' => ['nullable', 'integer', 'min:1'],
            'unit_label' => ['nullable', 'string', 'max:50'],
            'warranty_days' => ['required', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $package->update($validated);

        return back()->with('citadel_toast', [
            'type' => 'success',
            'message' => "تم تحديث بيانات باقة ({$package->name}) بنجاح."
        ]);
    }

    /**
     * Delete Service Package.
     */
    public function deletePackage(ServicePackage $package): RedirectResponse
    {
        $package->delete();

        return back()->with('citadel_toast', [
            'type' => 'info',
            'message' => 'تم حذف الباقة بنجاح.'
        ]);
    }

    /**
     * Store new Interactive Icon.
     */
    public function storeIcon(Request $request, Service $service): RedirectResponse
    {
        $validated = $request->validate([
            'group_type' => ['required', 'in:industry,feature'],
            'title' => ['required', 'string', 'max:255'],
            'icon_svg_or_name' => ['required', 'string', 'max:255'],
            'badge_text' => ['nullable', 'string', 'max:100'],
            'popup_summary' => ['required', 'string', 'max:2000'],
            'popup_cta_text' => ['nullable', 'string', 'max:100'],
            'popup_cta_link' => ['nullable', 'string', 'max:255'],
        ]);

        $service->icons()->create([
            'group_type' => $validated['group_type'],
            'title' => $validated['title'],
            'icon_svg_or_name' => $validated['icon_svg_or_name'],
            'badge_text' => $validated['badge_text'] ?? null,
            'popup_summary' => $validated['popup_summary'],
            'popup_bullets_json' => [],
            'popup_cta_text' => $validated['popup_cta_text'] ?? 'احجز الآن',
            'popup_cta_link' => $validated['popup_cta_link'] ?? '/portal/bookings',
            'is_active' => true,
        ]);

        return back()->with('citadel_toast', [
            'type' => 'success',
            'message' => 'تم إضافة الأيقونة التفاعلية بنجاح!'
        ]);
    }

    /**
     * Delete Service Icon.
     */
    public function deleteIcon(ServiceIcon $icon): RedirectResponse
    {
        $icon->delete();

        return back()->with('citadel_toast', [
            'type' => 'info',
            'message' => 'تم حذف الأيقونة التفاعلية.'
        ]);
    }
}
