<?php

namespace App\Providers;

use App\Shared\Handlers\Api\Handler as ApiHandler;
use App\Shared\Services\TokenGenerator;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Exceptions\Handler as WebHandler;
use Illuminate\Http\Request;
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

        $this->app->singleton(TokenGenerator::class, function (Application $application) {
            return new TokenGenerator();
        });

        if (! $this->app->runningInConsole()) {
            $request = $this->app->make(Request::class);
            $isApi = $request->is("api/*");

            if ($isApi) {
                $this->app->singleton(ExceptionHandler::class, ApiHandler::class);
            } else {
                $this->app->singleton(ExceptionHandler::class, WebHandler::class);
            }
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
    }
}
