<?php

namespace App\Providers;

use App\Shared\Handlers\Api\ErrorHandler as ApiHandler;
use App\Shared\Services\Token\TokenGenerator;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class MainServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->environment() !== 'production') {
            $this->app->register(\Barryvdh\Debugbar\ServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->singleton(TokenGenerator::class, function (Application $application) {
            return new TokenGenerator();
        });

        $this->app->singleton(ExceptionHandler::class, ApiHandler::class);
    }
}
