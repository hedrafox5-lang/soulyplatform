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

                $allIcons = $cachedServices->flatMap(function ($s) {
                    return $s->icons->map(function ($icon) use ($s) {
                        $data = $icon->toArray();
                        $data['service_name'] = $s->name;
                        $data['service_slug'] = $s->slug;
                        return $data;
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
            if (auth()->check() && auth()->user()->wallet) {
                $walletBalance = number_format(auth()->user()->wallet->balance, 2);
            }
            $view->with('citadelWalletBalance', $walletBalance);
        });
    }
}
