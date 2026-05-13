<?php

namespace App\Providers;

use App\Services\CurrencyService;
use App\Support\AppLayoutShared;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Blade::directive('money', function ($expression) {
            return "<?php echo app('".CurrencyService::class."')->format($expression); ?>";
        });

        // Share theme color settings to all views once DB is ready
        View::composer('*', function ($view) {
            $view->with('themeColors', AppLayoutShared::themeColors());
        });

        // Share cart item count to layout views
        View::composer('layouts.app', function ($view) {
            $view->with(AppLayoutShared::navigation(request()));
        });
    }
}
