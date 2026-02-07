<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\RechargeSettings;
use App\Services\RechargeService;
use Illuminate\View\View;

class AccountDashboardController extends Controller
{
    public function __construct(
        protected RechargeService $recharge
    ) {}

    public function index(): View
    {
        $user = auth()->guard('portal')->user();
        $customerId = $user->recharge_customer_id;

        $subscriptionsData = $this->recharge->listSubscriptions($customerId, []);
        $subs = $subscriptionsData['subscriptions'] ?? [];
        $activeSubs = array_filter($subs, fn ($s) => ($s['status'] ?? '') === 'active');
        $activeCount = count($activeSubs);

        $nextCharge = null;
        foreach ($activeSubs as $s) {
            $date = $s['next_charge_scheduled_at'] ?? null;
            if ($date && (! $nextCharge || $date < $nextCharge)) {
                $nextCharge = $date;
            }
        }

        $ordersData = $this->recharge->listOrders($customerId, ['limit' => 10]);
        $orders = $ordersData['orders'] ?? [];
        $ordersCount = $ordersData['count'] ?? count($orders);
        $lastOrder = $orders[0] ?? null;

        $address = null;
        $addressId = null;
        $firstSub = $activeSubs[0] ?? $subs[0] ?? null;
        if ($firstSub && ! empty($firstSub['address_id'])) {
            $addressId = (string) $firstSub['address_id'];
            $address = $this->recharge->getAddress($addressId);
        }

        $settings = RechargeSettings::first();
        $enableAddressUpdate = $settings ? $settings->isFeatureEnabled('enable_address_update') : true;

        return view('account.dashboard', [
            'activeSubscriptionsCount' => $activeCount,
            'nextChargeDate' => $nextCharge,
            'ordersCount' => $ordersCount,
            'lastOrder' => $lastOrder,
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
