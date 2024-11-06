<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class GlobalServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $modules = config('modules');

        foreach ($modules as $module) {

            // Load helper functions
            require_once base_path("app/Http/Controllers/$module/Helpers/{$module}Helper.php");

            // Load constants
            require_once base_path("app/Http/Controllers/$module/Constants/{$module}Constants.php");

            // Load routes
            $routesFile = base_path("app/Http/Controllers/$module/Routes/{$module}Routes.php");

            Route::middleware(['web'])
                ->namespace('App\\Http\\Controllers')
                ->group(function () use ($routesFile) {
                    require $routesFile;
                });
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
