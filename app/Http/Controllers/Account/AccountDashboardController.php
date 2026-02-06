<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
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

        $subscriptions = $this->recharge->listSubscriptions($customerId, ['status' => 'active']);
        $subs = $subscriptions['subscriptions'] ?? [];
        $activeCount = count($subs);

        $nextCharge = null;
        foreach ($subs as $s) {
            $date = $s['next_charge_scheduled_at'] ?? null;
            if ($date && (! $nextCharge || $date < $nextCharge)) {
                $nextCharge = $date;
            }
        }

        $ordersData = $this->recharge->listOrders($customerId, ['limit' => 1]);
        $orders = $ordersData['orders'] ?? [];
        $ordersCount = $ordersData['count'] ?? count($orders);
        $lastOrder = $orders[0] ?? null;

        return view('account.dashboard', [
            'activeSubscriptionsCount' => $activeCount,
            'nextChargeDate' => $nextCharge,
            'ordersCount' => $ordersCount,
            'lastOrder' => $lastOrder,
        ]);
    }
}
