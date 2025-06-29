<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->redirectGuestsTo(function (Request $request): ?string {
            if ($request->is('admin') || $request->is('admin/*')) {
                return route('admin.login');
            }
            return route('login');
        });

        $middleware->redirectUsersTo(function (Request $request): ?string {
            if (Auth::guard('admin')->check()) {
                return route('admin.dashboard');
            }
            if (Auth::guard('web')->check()) { 
                return route('dashboard');
            }
            return '/';
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
