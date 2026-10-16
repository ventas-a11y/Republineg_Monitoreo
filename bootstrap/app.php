<?php

use App\Http\Middleware\EnforceAbsoluteSessionTimeout;
use App\Http\Middleware\EnsureMonitoringPolicyAccepted;
use App\Http\Middleware\EnsurePasswordIsChanged;
use App\Http\Middleware\EnsureSecondFactor;
use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->encryptCookies(except: ['appearance', 'sidebar_state']);

        // Only the canonical host from APP_URL (no subdomains); inactive in local and tests.
        $middleware->trustHosts(
            at: fn (): array => ['^'.preg_quote((string) parse_url((string) config('app.url'), PHP_URL_HOST)).'$'],
            subdomains: false,
        );

        $middleware->web(append: [
            EnforceAbsoluteSessionTimeout::class,
            HandleAppearance::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->alias([
            'password.changed' => EnsurePasswordIsChanged::class,
            'consent' => EnsureMonitoringPolicyAccepted::class,
            'second-factor' => EnsureSecondFactor::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
