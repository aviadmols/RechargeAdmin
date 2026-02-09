<?php

namespace App\Http\Middleware;

use App\Services\AuditLogService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogPortalActivity
{
    public function __construct(
        protected AuditLogService $audit
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $user = $request->user('portal');
        if (! $user) {
            return $response;
        }

        try {
            $route = $request->route();
            $this->audit->log(
                'portal.page_view',
                $user->email,
                $user->recharge_customer_id ?? null,
                'page',
                $route?->getName() ?? $request->path(),
                'success',
                null,
                [
                    'path' => $request->path(),
                    'method' => $request->method(),
                ]
            );
        } catch (\Throwable) {
            // Don't break the request if logging fails
        }

        return $response;
    }
}
