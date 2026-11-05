<?php

namespace App\Http\Controllers;

use App\Models\ErpStoreCatalog;
use App\Models\Service;
use App\Models\ServicePackage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ServiceController extends Controller
{
    /**
     * Display the Interactive Services Hub.
     */
    public function index(Request $request): View
    {
        $serviceIds = Cache::remember('souly_catalog_v1_ids', 3600, function () {
            return Service::where('is_active', true)
                ->orderBy('display_order')
                ->pluck('id')
                ->toArray();
        });

        if (!is_array($serviceIds) || empty($serviceIds)) {
            $serviceIds = Service::where('is_active', true)->orderBy('display_order')->pluck('id')->toArray();
        }

        $services = Service::with([
            'icons' => fn ($q) => $q->where('is_active', true)->orderBy('display_order'),
            'packages' => fn ($q) => $q->where('is_active', true)->orderBy('display_order'),
        ])
        ->whereIn('id', $serviceIds)
        ->orderBy('display_order')
        ->get();

        return view('pages.services.index', compact('services'));
    }

    /**
     * Display the dedicated deep landing page for a service.
     */
    public function show(Service $service): View
    {
        abort_unless($service->is_active, 404);

        $service->load([
            'rows' => fn ($q) => $q->where('is_active', true)->orderBy('display_order'),
            'icons' => fn ($q) => $q->where('is_active', true)->orderBy('display_order'),
            'packages' => fn ($q) => $q->where('is_active', true)->orderBy('display_order'),
        ]);

        $otherServices = Service::where('is_active', true)
            ->where('id', '!=', $service->id)
            ->orderBy('display_order')
            ->get();

        // Pass ERP store catalog to brand-erp service page
        $erpCatalog = null;
        if ($service->slug === 'brand-erp') {
            $erpCatalog = ErpStoreCatalog::active()
                ->orderBy('tier')
                ->orderBy('display_order')
                ->get()
                ->groupBy('tier');
        }

        return view('pages.services.show', compact('service', 'otherServices', 'erpCatalog'));
    }

    /**
     * Display a dedicated sub-page for a specific service package.
     */
    public function showPackage(Service $service, string $packageSlug): View
    {
        abort_unless($service->is_active, 404);

        $package = $service->packages()
            ->where('slug', $packageSlug)
            ->where('is_active', true)
            ->firstOrFail();

        $relatedPackages = $service->packages()
            ->where('id', '!=', $package->id)
            ->where('is_active', true)
            ->orderBy('display_order')
            ->get();

        return view('pages.services.package', compact('service', 'package', 'relatedPackages'));
    }

    /**
     * Display the standalone BrandERP Modular Store page.
     */
    public function storeBranderp(): View
    {
        $service = Service::where('slug', 'brand-erp')
            ->where('is_active', true)
            ->firstOrFail();

        $erpCatalog = ErpStoreCatalog::active()
            ->orderBy('tier')
            ->orderBy('display_order')
            ->get()
            ->groupBy('tier');

        return view('pages.store.branderp', compact('service', 'erpCatalog'));
    }
}
