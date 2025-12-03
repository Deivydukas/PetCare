<?php

use Illuminate\Foundation\Application;
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
        // Global middleware
        $middleware->append(\Illuminate\Http\Middleware\HandleCors::class);

        // Aliases (key => middleware class)
        $middleware->alias([
            'role'        => \App\Http\Middleware\RoleMiddleware::class,
            'jwt.refresh' => \App\Http\Middleware\RefreshJwtToken::class,
            'jwt.auth'    => \App\Http\Middleware\AuthenticateJwt::class,
            'jwt.cookie'  => \App\Http\Middleware\ParseJwtCookie::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // JWT expired
        $exceptions->render(function (
            Tymon\JWTAuth\Exceptions\TokenExpiredException $e,
            Illuminate\Http\Request $request
        ) {
            return response()->json([
                'error' => 'Token expired'
            ], 401);
        });

        // JWT invalid
        $exceptions->render(function (
            Tymon\JWTAuth\Exceptions\TokenInvalidException $e,
            Illuminate\Http\Request $request
        ) {
            return response()->json([
                'error' => 'Token invalid'
            ], 401);
        });

        // No token / malformed
        $exceptions->render(function (
            Tymon\JWTAuth\Exceptions\JWTException $e,
            Illuminate\Http\Request $request
        ) {
            return response()->json([
                'error' => 'Token missing or malformed'
            ], 401);
        });

        // Default auth failure
        $exceptions->render(function (
            Illuminate\Auth\AuthenticationException $e,
            Illuminate\Http\Request $request
        ) {
            return response()->json([
                'error' => 'Unauthenticated'
            ], 401);
        });

        // Fallback: UnauthorizedHttpException
        $exceptions->render(function (
            Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException $e,
            Illuminate\Http\Request $request
        ) {
            return response()->json([
                'error' => 'Invalid or expired token'
            ], 401);
        });
    })->create();
