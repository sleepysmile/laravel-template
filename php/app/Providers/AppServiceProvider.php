<?php

namespace App\Providers;

use App\Http\Core\Exceptions\ApiHandler;
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


        $this->app->bind(ExceptionHandler::class, function (Application $application) {
            /** @var Request $request */
            $request = $application->make(Request::class);
            $isApi = $request->is("api/*") && $request->expectsJson();

            if ($isApi) {
                return $application->make(ApiHandler::class);
            }

            return $application->make(Handler::class);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
    }
}
