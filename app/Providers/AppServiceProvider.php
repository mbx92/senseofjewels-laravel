<?php

namespace App\Providers;

use App\Models\Cart;
use App\Models\Setting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Share theme color settings to all views once DB is ready
        View::composer('*', function ($view) {
            static $themeColors = null;

            if ($themeColors === null) {
                try {
                    if (Schema::hasTable('settings')) {
                        $themeColors = Setting::query()
                            ->where('group', 'colors')
                            ->pluck('value', 'key')
                            ->toArray();
                    }
                } catch (\Throwable) {
                    // DB not ready yet (e.g., during migrations)
                }
                $themeColors = $themeColors ?? [];
            }

            $view->with('themeColors', $themeColors);
        });

        // Share cart item count to layout views
        View::composer('layouts.app', function ($view) {
            try {
                $sessionId = request()->session()->getId();
                $count = 0;
                if ($sessionId && Schema::hasTable('carts')) {
                    $cart = Cart::query()
                        ->where('session_id', $sessionId)
                        ->withCount('items')
                        ->first();
                    $count = $cart?->items_count ?? 0;
                }
            } catch (\Throwable) {
                $count = 0;
            }
            $view->with('navCartCount', $count);
        });
    }
}
