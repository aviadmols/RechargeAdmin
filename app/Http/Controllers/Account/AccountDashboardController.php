<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\RechargeSettings;
use App\Models\SubscriptionProduct;
use App\Services\RechargeService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AccountDashboardController extends Controller
{
    public const DASHBOARD_CACHE_TTL_SECONDS = 90;

    public function __construct(
        protected RechargeService $recharge
    ) {}

    public function index(): View
    {
        $user = auth()->guard('portal')->user();
        $customerId = $user->recharge_customer_id ?? '';
        $cacheKey = "account.dashboard.{$customerId}";

        $data = Cache::remember($cacheKey, self::DASHBOARD_CACHE_TTL_SECONDS, function () use ($customerId) {
            try {
                if ($customerId === '' || $customerId === null) {
                    return $this->defaultDashboardData();
                }
                return $this->loadDashboardDataFromRecharge((string) $customerId);
            } catch (\Throwable $e) {
                Log::warning('Account dashboard: Recharge data failed, using defaults.', [
                    'customer_id' => $customerId,
                    'message' => $e->getMessage(),
                ]);
                return $this->defaultDashboardData();
            }
        });

        $settings = RechargeSettings::first();
        $enableAddressUpdate = $settings ? $settings->isFeatureEnabled('enable_address_update') : true;
        $subscriptionProducts = SubscriptionProduct::active()->ordered()->get();

        return view('account.dashboard', [
            'activeSubscriptionsCount' => $data['activeSubscriptionsCount'],
            'nextChargeDate' => $data['nextChargeDate'],
            'ordersCount' => $data['ordersCount'],
            'lastOrder' => $data['lastOrder'],
            'subscriptionProducts' => $subscriptionProducts,
            'promotedProducts' => config('mills.promoted_products', []),
            'orders' => $data['orders'],
            'subscriptions' => $data['subs'],
            'address' => $data['address'],
            'addressId' => $data['addressId'],
            'enableAddressUpdate' => $enableAddressUpdate,
            'user' => $user,
        ]);
    }

    /**
     * Load Recharge-dependent data (cache miss). Called once per customer per TTL.
     */
    private function loadDashboardDataFromRecharge(string $customerId): array
    {
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
        if (! $nextCharge && ! empty($activeSubs)) {
            $first = is_array($activeSubs) ? array_values($activeSubs)[0] : $activeSubs[0];
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

        $address = null;
        $addressId = null;
        $firstSub = $activeSubs[0] ?? $subs[0] ?? null;
        if ($firstSub && ! empty($firstSub['address_id'])) {
            $addressId = (string) $firstSub['address_id'];
            $address = $this->recharge->getAddress($addressId);
        }

        return [
            'activeSubscriptionsCount' => $activeCount,
            'nextChargeDate' => $nextCharge,
            'ordersCount' => $ordersCount,
            'lastOrder' => $lastOrder,
            'orders' => $orders,
            'subs' => $subs,
            'address' => $address,
            'addressId' => $addressId,
        ];
    }

    /**
     * Default data when Recharge is unavailable or customer has no Recharge ID.
     */
    private function defaultDashboardData(): array
    {
        return [
            'activeSubscriptionsCount' => 0,
            'nextChargeDate' => null,
            'ordersCount' => 0,
            'lastOrder' => null,
            'orders' => [],
            'subs' => [],
            'address' => null,
            'addressId' => null,
        ];
    }

    /**
     * Clear dashboard cache for a customer (e.g. after adding product or updating address).
     */
    public static function forgetDashboardCache(string $customerId): void
    {
        Cache::forget("account.dashboard.{$customerId}");
    }
}
