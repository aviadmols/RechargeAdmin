<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePortalSession
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->guard('portal')->check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('login')->with('intended', $request->url());
        }

        return $next($request);
    }
}
