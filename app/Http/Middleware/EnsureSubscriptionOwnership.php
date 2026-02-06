<?php

namespace App\Http\Middleware;

use App\Services\RechargeService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSubscriptionOwnership
{
    public function __construct(
        protected RechargeService $recharge
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $subscriptionId = $request->route('id') ?? $request->route('subscription');
        if (! $subscriptionId) {
            return response()->json(['message' => 'Subscription ID required.'], 400);
        }

        $user = auth()->guard('portal')->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $rechargeCustomerId = $user->recharge_customer_id ?? null;
        if (! $rechargeCustomerId) {
            return response()->json(['message' => 'Invalid session.'], 403);
        }

        $subscription = $this->recharge->getSubscription((string) $subscriptionId);
        if (! $subscription) {
            return response()->json(['message' => 'Subscription not found.'], 404);
        }

        $subCustomerId = (string) ($subscription['customer_id'] ?? $subscription['customer']['id'] ?? '');
        if ($subCustomerId !== (string) $rechargeCustomerId) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $request->attributes->set('subscription', $subscription);

        return $next($request);
    }
}
