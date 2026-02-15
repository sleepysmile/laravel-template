<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Route::middleware("web")
            ->group(base_path("app/Web/routes.php"));

        Route::middleware("api")
            ->prefix("/api")
            ->group(base_path("app/Api/routes.php"));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
    }
}
