<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\RechargeSettings;
use App\Services\RechargeService;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    public function __construct(
        protected RechargeService $recharge
    ) {}

    public function index(): View
    {
        $user = auth()->guard('portal')->user();
        $customerId = $user->recharge_customer_id;

        $data = $this->recharge->listSubscriptions($customerId);
        $subscriptions = $data['subscriptions'] ?? [];

        $settings = RechargeSettings::first();

        return view('account.subscriptions.index', [
            'subscriptions' => $subscriptions,
            'enabledFeatures' => $settings?->enabled_features ?? [],
        ]);
    }

    public function show(string $id): View
    {
        $user = auth()->guard('portal')->user();
        $subscription = $this->recharge->getSubscription($id);
        if (! $subscription) {
            abort(404);
        }
        $subCustomerId = (string) ($subscription['customer_id'] ?? $subscription['customer']['id'] ?? '');
        if ($subCustomerId !== (string) $user->recharge_customer_id) {
            abort(403);
        }

        $settings = RechargeSettings::first();

        return view('account.subscriptions.show', [
            'subscription' => $subscription,
            'enabledFeatures' => $settings?->enabled_features ?? [],
        ]);
    }
}
