<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\RechargeSettings;
use App\Models\SubscriptionProduct;
use App\Services\RechargeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AccountDashboardController extends Controller
{
    public function __construct(
        protected RechargeService $recharge
    ) {}

    public function index(): View|RedirectResponse
    {
        $user = auth()->guard('portal')->user();
        if (! $user) {
            return redirect()->route('login');
        }

        $customerId = $user->recharge_customer_id ?? '';
        $subs = [];
        $activeSubs = [];
        $activeCount = 0;
        $nextCharge = null;
        $orders = [];
        $ordersCount = 0;
        $lastOrder = null;
        $address = null;
        $addressId = null;
        $subscriptionProducts = collect();
        $enableAddressUpdate = true;

        try {
            $subscriptionsData = $this->recharge->listSubscriptions($customerId, []);
            $subs = $subscriptionsData['subscriptions'] ?? [];
            $activeSubs = array_values(array_filter($subs, fn ($s) => ($s['status'] ?? '') === 'active'));
            $activeCount = count($activeSubs);

            foreach ($activeSubs as $s) {
                $date = $s['next_charge_scheduled_at'] ?? null;
                if ($date && (! $nextCharge || $date < $nextCharge)) {
                    $nextCharge = $date;
                }
            }
            if (! $nextCharge && ! empty($activeSubs)) {
                $first = $activeSubs[0];
                $subId = $first['id'] ?? null;
                if ($subId) {
                    try {
                        $full = $this->recharge->getSubscription((string) $subId);
                        $nextCharge = $full['next_charge_scheduled_at'] ?? null;
                    } catch (\Throwable) {
                        // keep null
                    }
                }
            }

            $ordersData = $this->recharge->listOrders($customerId, ['limit' => 10]);
            $orders = $ordersData['orders'] ?? [];
            $ordersCount = $ordersData['count'] ?? count($orders);
            $lastOrder = $orders[0] ?? null;

            $firstSub = $activeSubs[0] ?? $subs[0] ?? null;
            if ($firstSub && ! empty($firstSub['address_id'])) {
                $addressId = (string) $firstSub['address_id'];
                try {
                    $address = $this->recharge->getAddress($addressId);
                } catch (\Throwable) {
                    // leave address null
                }
            }
        } catch (\Throwable $e) {
            report($e);
            // Continue with empty data so the page still loads
        }

        try {
            $settings = RechargeSettings::first();
            $enableAddressUpdate = $settings ? $settings->isFeatureEnabled('enable_address_update') : true;
            $subscriptionProducts = SubscriptionProduct::active()->ordered()->get();
        } catch (\Throwable $e) {
            report($e);
        }

        return view('account.dashboard', [
            'activeSubscriptionsCount' => $activeCount,
            'nextChargeDate' => $nextCharge,
            'ordersCount' => $ordersCount,
            'lastOrder' => $lastOrder,
            'subscriptionProducts' => $subscriptionProducts,
            'promotedProducts' => config('mills.promoted_products', []),
            'orders' => $orders,
            'subscriptions' => $subs,
            'address' => $address,
            'addressId' => $addressId,
            'enableAddressUpdate' => $enableAddressUpdate,
            'user' => $user,
        ]);
    }
}
