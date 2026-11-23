<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\Access\AuthorizationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
            \App\Http\Middleware\LogAuditableChanges::class,
        ]);

        // Handle authorization exceptions in API
        $middleware->api(append: [
            \App\Http\Middleware\HandleAuthorizationException::class,
        ]);

        $middleware->alias([
            'role'          => \App\Http\Middleware\RoleMiddleware::class,
            'permission'    => \App\Http\Middleware\EnsurePermission::class,
            'api.token'     => \App\Http\Middleware\ApiTokenAuth::class,
            'throttle.media'=> \App\Http\Middleware\ThrottleMediaUpload::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle AuthorizationException for API responses
        $exceptions->render(function (AuthorizationException $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'error' => 'Unauthorized action',
                ], 403);
            }
        });
    })->create();
