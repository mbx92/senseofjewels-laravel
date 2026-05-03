<?php

namespace App\Providers;

use App\Models\Cart;
use App\Models\Setting;
use App\Services\CurrencyService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Blade::directive('money', function ($expression) {
            return "<?php echo app('" . CurrencyService::class . "')->format($expression); ?>";
        });

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
                $cartEnabled = Setting::cartEnabled();
                $whatsappUrl = Setting::whatsappUrl();
                $sessionId = request()->session()->getId();
                $count = 0;
                if ($cartEnabled && $sessionId && Schema::hasTable('carts')) {
                    $cartQuery = Cart::query()->withCount('items');
                    if (Auth::check()) {
                        $cartQuery->where('user_id', Auth::id());
                    } else {
                        $cartQuery->where('session_id', $sessionId)->whereNull('user_id');
                    }
                    $cart = $cartQuery->first();
                    $count = $cart?->items_count ?? 0;
                }
            } catch (\Throwable) {
                $cartEnabled = true;
                $whatsappUrl = null;
                $count = 0;
            }
            $view->with([
                'cartEnabled' => $cartEnabled,
                'navCartCount' => $count,
                'whatsappUrl' => $whatsappUrl,
            ]);
        });
    }
}
