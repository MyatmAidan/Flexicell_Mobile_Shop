<?php

use App\Http\Middleware\AdminAuth;
use App\Http\Middleware\AuthCheck;
use App\Http\Middleware\CheckPermission;
use App\Http\Middleware\EnsureUserHasRole;
use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\RedirectIfAuthenticated;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Alias middleware for easy use in routes
        $middleware->alias([
            'guest'      => RedirectIfAuthenticated::class,
            'authCheck'  => AuthCheck::class,
            'adminAuth'  => AdminAuth::class,
            'admin'      => EnsureUserIsAdmin::class,
            'role'       => EnsureUserHasRole::class,
            'permission' => CheckPermission::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
