<?php

use App\Cli\Ui as Cli;
use App\Providers\AppServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;
use Maantje\XhprofBuggregatorLaravel\Middleware\XhprofProfiler;
use Maantje\XhprofBuggregatorLaravel\XhprofServiceProvider;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withProviders([
        XhprofServiceProvider::class,
        AppServiceProvider::class
    ])
    ->withExceptions()
    ->withCommands([
        __DIR__ . "/../app/Cli/Ui",
    ])
//    ->withKernels()
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(XhprofProfiler::class);
        $middleware->redirectTo(false);
    })
    ->create();
