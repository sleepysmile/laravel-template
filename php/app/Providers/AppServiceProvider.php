<?php

namespace App\Providers;

use App\Http\Core\Exceptions\ApiHandler;
use App\Http\Core\Exceptions\TokenGenerator;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Exceptions\Handler;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
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
                $this->app->singleton(ExceptionHandler::class, Handler::class);
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
