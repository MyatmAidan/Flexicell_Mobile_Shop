<?php

namespace App\Providers;

use App\Models\Device;
use App\Observers\DeviceObserver;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
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
        Device::observe(DeviceObserver::class);

        // Share the authenticated user to all views for permission checks
        View::composer('*', function ($view) {
            $view->with('authUser', Auth::user());
        });

        // @canDo('permission.name') ... @endcanDo
        Blade::directive('canDo', function (string $expression) {
            return "<?php if(Auth::check() && Auth::user()->hasPermission({$expression})): ?>";
        });

        Blade::directive('endcanDo', function () {
            return '<?php endif; ?>';
        });

        // @cannotDo('permission.name') ... @endcannotDo
        Blade::directive('cannotDo', function (string $expression) {
            return "<?php if(!Auth::check() || !Auth::user()->hasPermission({$expression})): ?>";
        });

        Blade::directive('endcannotDo', function () {
            return '<?php endif; ?>';
        });
    }
}

