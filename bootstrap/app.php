<?php

use Illuminate\Foundation\Application;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\ApiKeyMiddleware;
use App\Http\Middleware\AccessTokenMiddleware;
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
        //
    })
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'api.key' => ApiKeyMiddleware::class,
            'access_token'=>AccessTokenMiddleware::class,
            'verify.forwarder.token'=> VerifyForwarderToken::class
        ]);
    })

    ->withExceptions(function (Exceptions $exceptions) {

    })->create();
