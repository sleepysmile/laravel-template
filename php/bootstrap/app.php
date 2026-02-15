<?php

use App\Cli\Ui as Cli;
use App\Providers\MainServiceProvider;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;
use Maantje\XhprofBuggregatorLaravel\Middleware\XhprofProfiler;
use Maantje\XhprofBuggregatorLaravel\XhprofServiceProvider;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        commands: __DIR__ . '/../app/Console/console.php',
        health: '/up',
    )
    ->withProviders([
        XhprofServiceProvider::class,
        MainServiceProvider::class,
        RouteServiceProvider::class
    ])
    ->withExceptions()
    ->withCommands([
        __DIR__ . "/../Console/Commands",
    ])
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(XhprofProfiler::class);
        $middleware->redirectTo(false);
    })
    ->create();
