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
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
            'author' => \App\Http\Middleware\EnsureUserIsAuthor::class,
        ]);

        // // 🔹 Куда перенаправлять гостей (неавторизованных) fn () это просто стрелочная функция 
        // $middleware->redirectGuestsTo(fn () => route('User_login'));
        // // 🔹 Куда перенаправлять после успешного входа
        // $middleware->redirectUsersTo(fn () => route('dashboard'));

        $middleware->redirectGuestsTo(function () {
            return route('login');
        });
        $middleware->redirectUsersTo(function () {
            return auth()->user()?->isAdmin() ? route('admin.index') : route('dashboard');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
