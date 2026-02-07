<?php

use App\Http\Middleware\EnsurePortalSession;
use App\Http\Middleware\EnsureSubscriptionOwnership;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'portal' => EnsurePortalSession::class,
            'subscription.owner' => EnsureSubscriptionOwnership::class,
        ]);
        $middleware->append(SecurityHeaders::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (QueryException $e, Request $request): ?Response {
            if (! $request->expectsJson() && str_contains($e->getMessage(), 'connection')) {
                return response()->view('errors.db-unavailable', [], 503);
            }
            return null;
        });
    })->create();
