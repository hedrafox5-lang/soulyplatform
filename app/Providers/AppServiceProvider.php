<?php

namespace App\Providers;

use App\Models\Service;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            if (Schema::hasTable('services')) {
                static $cachedServices = null;
                if ($cachedServices === null) {
                    $cachedServices = Service::with(['icons', 'packages', 'rows'])
                        ->where('is_active', true)
                        ->orderBy('display_order')
                        ->get();
                }

                // Provide lightweight starter precache for instant initial render
                $allIcons = $cachedServices->flatMap(function ($s) {
                    return $s->icons->take(4)->map(function ($icon) use ($s) {
                        return [
                            'id' => $icon->id,
                            'title' => $icon->title,
                            'badge_text' => $icon->badge_text,
                            'icon_name' => $icon->icon_name,
                            'popup_title' => $icon->popup_title,
                            'popup_summary' => $icon->popup_summary,
                            'popup_bullets_json' => $icon->popup_bullets_json,
                            'popup_cta_text' => $icon->popup_cta_text,
                            'service_name' => $s->name,
                            'service_slug' => $s->slug,
                        ];
                    });
                })->values();

                $view->with('citadelServices', $cachedServices);
                $view->with('citadelPrecacheJson', json_encode(['icons' => $allIcons], JSON_UNESCAPED_UNICODE));
            } else {
                $view->with('citadelServices', collect());
                $view->with('citadelPrecacheJson', json_encode(['icons' => []]));
            }

            // Quick Wallet Balance Pill (Auth user or demo account preview)
            $walletBalance = '3500.00';
            if (auth()->check()) {
                $wallet = auth()->user()->wallet ?? auth()->user()->walletOrCreate();
                $walletBalance = number_format($wallet->balance, 2, '.', '');
            }
            $view->with('citadelWalletBalance', $walletBalance);
        });
    }
}
