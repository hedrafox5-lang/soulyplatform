<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display the Souly Digital Citadel Homepage.
     */
    public function index(Request $request): View
    {
        $services = Service::with([
            'rows' => fn ($q) => $q->where('is_active', true)->orderBy('display_order'),
            'icons' => fn ($q) => $q->where('is_active', true)->orderBy('display_order'),
            'packages' => fn ($q) => $q->where('is_active', true)->orderBy('display_order'),
        ])
        ->where('is_active', true)
        ->orderBy('display_order')
        ->get();

        return view('pages.home', compact('services'));
    }
}
