<?php

use Illuminate\Foundation\Application;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\ApiKeyMiddleware;
use App\Http\Middleware\AccessTokenMiddleware;
use App\Http\Middleware\DailySearchLimit;
use App\Http\Middleware\VerifyForwarderToken;
use App\Http\Middleware\ParentMiddleware;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Register custom middleware aliases
        $middleware->alias([
            'api.key' => ApiKeyMiddleware::class,
            'access_token' => AccessTokenMiddleware::class,
              'daily_search_limit' => DailySearchLimit::class,
            'verify.forwarder.token' => VerifyForwarderToken::class
        ]);

        // Ensure CORS middleware runs for API routes
        $middleware->api(prepend: [
            \Illuminate\Http\Middleware\HandleCors::class,
        ]);

        // Disable CSRF for API routes
        $middleware->validateCsrfTokens(except: [
            'api/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
