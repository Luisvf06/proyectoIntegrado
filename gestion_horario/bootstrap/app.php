<?php
#Este archivo es el antiguo kernel.php, los middlewares se definen en withmiddleware, dentro del array
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\LoginMiddleware;
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->statefulApi();
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
            LoginMiddleware::class, #Creado por mí, supuestamente para controlar el acceso a usuarios no autenticados

        ]);

        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

