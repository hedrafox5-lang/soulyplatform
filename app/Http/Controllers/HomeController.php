<?php

namespace App\Http\Controllers;

use App\Models\ErpStoreCatalog;
use App\Models\Service;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    /**
     * Display the Souly Digital Citadel Homepage.
     */
    public function index(Request $request): View
    {
        // Cache active catalog service IDs for 1 hour to keep CPU usage < 1% on Lonex
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
            'rows' => fn ($q) => $q->where('is_active', true)->orderBy('display_order'),
            'icons' => fn ($q) => $q->where('is_active', true)->orderBy('display_order'),
            'packages' => fn ($q) => $q->where('is_active', true)->orderBy('display_order'),
        ])
        ->whereIn('id', $serviceIds)
        ->orderBy('display_order')
        ->get();

        // Extract lightweight hero slides array to avoid serializing 130 icons into the page script
        $heroSlides = $services->map(fn ($s) => [
            'id' => $s->id,
            'slug' => $s->slug,
            'name' => $s->name,
            'tagline' => $s->tagline,
            'hero_badge' => $s->hero_badge,
            'short_description' => $s->short_description,
            'accent_color' => $s->accent_color,
            'secondary_color' => $s->secondary_color,
            'hero_slides_json' => $s->hero_slides_json,
            'stats_json' => $s->stats_json,
        ]);

        $erpCatalog = ErpStoreCatalog::active()
            ->orderBy('tier')
            ->orderBy('display_order')
            ->get()
            ->groupBy('tier');

        return view('pages.home', compact('services', 'heroSlides', 'erpCatalog'));
    }
}
