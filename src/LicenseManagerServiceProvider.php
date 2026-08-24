<?php

namespace Nerdtech\LicenseManager;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use Nerdtech\LicenseManager\Http\Middleware\VerifyLicense;

class LicenseManagerServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/nerdtech-license.php', 'nerdtech-license'
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // a. Publish the config file
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/nerdtech-license.php' => config_path('nerdtech-license.php'),
            ], 'nerdtech-license-config');
        }

        // b. Auto-inject the VerifyLicense middleware globally
        $kernel = $this->app->make(\Illuminate\Contracts\Http\Kernel::class);
        $kernel->pushMiddleware(VerifyLicense::class);
    }
}
