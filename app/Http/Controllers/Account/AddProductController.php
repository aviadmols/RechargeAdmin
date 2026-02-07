<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionProduct;
use App\Services\RechargeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AddProductController extends Controller
{
    public function __construct(
        protected RechargeService $recharge
    ) {}

    /**
     * Add a subscription product to the customer's account (create a new subscription in Recharge).
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate(['product_id' => 'required|integer|exists:subscription_products,id']);

        $product = SubscriptionProduct::where('id', $request->product_id)->where('is_active', true)->first();
        if (! $product) {
            return redirect()->route('account.dashboard')->with('error', 'Product not available.');
        }

        $user = auth()->guard('portal')->user();
        $customerId = $user->recharge_customer_id;

        $subscriptionsData = $this->recharge->listSubscriptions($customerId, []);
        $subs = $subscriptionsData['subscriptions'] ?? [];
        $activeSubs = array_filter($subs, fn ($s) => ($s['status'] ?? '') === 'active');
        $firstSub = $activeSubs[0] ?? $subs[0] ?? null;

        if (! $firstSub || empty($firstSub['address_id'])) {
            return redirect()->route('account.dashboard')->with('error', 'You need an active subscription with a shipping address before adding another product.');
        }

        $addressId = (string) $firstSub['address_id'];
        $nextChargeAt = $firstSub['next_charge_scheduled_at'] ?? null;
        if (! $nextChargeAt) {
            return redirect()->route('account.dashboard')->with('error', 'Your active subscription has no next charge date set. Please contact support.');
        }

        $variantId = $product->shopify_variant_id;
        if (str_starts_with($variantId, 'gid://')) {
            $variantId = last(explode('/', trim($variantId)));
        }
        $variantId = (string) $variantId;

        $payload = [
            'address_id' => (int) $addressId,
            'quantity' => 1,
            'order_interval_frequency' => $product->order_interval_frequency,
            'order_interval_unit' => $product->order_interval_unit,
            'next_charge_scheduled_at' => $nextChargeAt,
            'external_variant_id' => [
                'ecommerce' => 'shopify',
                'external_variant_id' => $variantId,
            ],
        ];

        try {
            $this->recharge->createSubscription($payload);
        } catch (\Throwable $e) {
            return redirect()->route('account.dashboard')->with('error', 'Could not add product: ' . $e->getMessage());
        }

        return redirect()->route('account.dashboard')->with('success', __('Product added to your subscription.'));
    }
}
