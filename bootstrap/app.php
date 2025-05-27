<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // We don't redirect when unauthenticated
        $middleware->redirectGuestsTo(function () {
            return null;
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // We always render as json
        $exceptions->shouldRenderJsonWhen(function () {
            return true;
        });
    })->create();
