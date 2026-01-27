<?php

use App\Cli\Ui as Cli;
use App\Http\Core\Exceptions\ApiExceptionFormatter;
use App\Http\Core\Exceptions\TokenGenerator;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Log;
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
    ])
    ->withCommands([
        Cli\TestCommand::class,
        Cli\UserMakeCli::class,

    ])
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(XhprofProfiler::class);
        $middleware->redirectTo(false);
    })
    ->withExceptions(using: function (Exceptions $exceptions): void {
        $tokenGenerator = new TokenGenerator();

        $apiFormatter = new ApiExceptionFormatter($exceptions, $tokenGenerator);
        $apiFormatter->configure();

        $exceptions->context(fn () => [
            "token" => $tokenGenerator->token()
        ]);
//        $exceptions->report(function (Throwable $throwable) use ($tokenGenerator) {
//            $logger = Log::channel();
//
//
//        });
    })
    ->create();
