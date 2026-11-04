<?php

use App\Http\Middleware\EnsureCompanyMembership;
use App\Http\Middleware\EnsureOnboardingCompleted;
use App\Http\Middleware\SetLocale;
use App\Http\Middleware\ThrottleAuthenticationRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustHosts(at: fn (): array => [
            '^'.preg_quote(config('app.host')).'$',
            '^[^.]+\.'.preg_quote(config('app.host')).'$',
        ], subdomains: false);

        $middleware->web(append: [
            SetLocale::class,
            ThrottleAuthenticationRequests::class,
        ]);

        $middleware->alias([
            'company' => EnsureCompanyMembership::class,
            'onboarded' => EnsureOnboardingCompleted::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
