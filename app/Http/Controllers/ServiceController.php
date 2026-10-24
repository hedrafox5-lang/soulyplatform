<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServicePackage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Display the Interactive Services Hub.
     */
    public function index(Request $request): View
    {
        $services = Service::with([
            'icons' => fn ($q) => $q->where('is_active', true)->orderBy('display_order'),
            'packages' => fn ($q) => $q->where('is_active', true)->orderBy('display_order'),
        ])
        ->where('is_active', true)
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

        return view('pages.services.show', compact('service', 'otherServices'));
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
}
