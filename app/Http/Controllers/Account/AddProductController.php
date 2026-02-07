<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionProduct;
use App\Services\AuditLogService;
use App\Services\RechargeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AddProductController extends Controller
{
    public function __construct(
        protected RechargeService $recharge,
        protected AuditLogService $audit
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
        $activeSubs = array_values(array_filter($subs, fn ($s) => ($s['status'] ?? '') === 'active'));
        $firstSub = $activeSubs[0] ?? $subs[0] ?? null;

        if (! $firstSub || empty($firstSub['address_id'])) {
            return redirect()->route('account.dashboard')->with('error', 'You need an active subscription with a shipping address before adding another product.');
        }

        $addressId = (string) $firstSub['address_id'];
        $nextChargeAt = $firstSub['next_charge_scheduled_at'] ?? null;
        $fullSub = null;

        // List API may omit next_charge_scheduled_at; fetch single subscription to get it
        if (! $nextChargeAt && ! empty($firstSub['id'])) {
            $fullSub = $this->recharge->getSubscription((string) $firstSub['id']);
            $nextChargeAt = $fullSub['next_charge_scheduled_at'] ?? null;
        }

        $debugMeta = $this->buildAddProductDebugMeta(
            $customerId,
            $user->email ?? null,
            (int) $request->product_id,
            $subs,
            $activeSubs,
            $firstSub,
            $fullSub,
            $nextChargeAt,
            'before_check'
        );

        if (! $nextChargeAt) {
            $this->audit->log(
                'add_product.debug',
                $user->email ?? null,
                $customerId,
                'subscription',
                (string) ($firstSub['id'] ?? ''),
                'fail',
                'No next_charge_scheduled_at found for chosen subscription.',
                $debugMeta
            );
            return redirect()->route('account.dashboard')->with('error', 'Your active subscription has no next charge date set. Please contact support.');
        }

        $variantId = $product->shopify_variant_id;
        if (str_starts_with($variantId, 'gid://')) {
            $variantId = last(explode('/', trim($variantId)));
        }
        $variantId = (string) $variantId;

        $payload = [
            'address_id' => (int) $addressId,
            'quantity' => (int) ($product->default_quantity ?? 1),
            'order_interval_frequency' => $product->order_interval_frequency,
            'order_interval_unit' => $product->order_interval_unit,
            'charge_interval_frequency' => $product->order_interval_frequency,
            'charge_interval_unit' => $product->order_interval_unit,
            'next_charge_scheduled_at' => $nextChargeAt,
            'external_variant_id' => $variantId,
        ];

        // מחיר להזמנה הראשונה (OTP) – נשלח ל-Recharge אם הוגדר במוצר
        $price = $product->first_order_price !== null
            ? (float) $product->first_order_price
            : $product->discounted_price;
        if ($price !== null && $price > 0) {
            $payload['price'] = number_format((float) $price, 2, '.', '');
        }

        try {
            $this->recharge->createSubscription($payload);
            $this->audit->log(
                'add_product.success',
                $user->email ?? null,
                $customerId,
                'subscription',
                (string) ($firstSub['id'] ?? ''),
                'success',
                null,
                $this->buildAddProductDebugMeta($customerId, $user->email ?? null, (int) $request->product_id, $subs, $activeSubs, $firstSub, $fullSub, $nextChargeAt, 'created')
            );
        } catch (\Throwable $e) {
            $this->audit->log(
                'add_product.fail',
                $user->email ?? null,
                $customerId,
                'subscription',
                (string) ($firstSub['id'] ?? ''),
                'fail',
                $e->getMessage(),
                $this->buildAddProductDebugMeta($customerId, $user->email ?? null, (int) $request->product_id, $subs, $activeSubs, $firstSub, $fullSub, $nextChargeAt, 'exception') + ['exception' => get_class($e), 'payload_sent' => $payload]
            );
            return redirect()->route('account.dashboard')->with('error', 'Could not add product: ' . $e->getMessage());
        }

        AccountDashboardController::forgetDashboardCache($customerId);
        return redirect()->route('account.dashboard')->with('success', __('Product added to your subscription.'));
    }

    /**
     * Add product as a one-time purchase (not subscription) via Recharge Onetimes.
     */
    public function buyOnce(Request $request): RedirectResponse
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
        $activeSubs = array_values(array_filter($subs, fn ($s) => ($s['status'] ?? '') === 'active'));
        $firstSub = $activeSubs[0] ?? $subs[0] ?? null;

        if (! $firstSub || empty($firstSub['address_id'])) {
            return redirect()->route('account.dashboard')->with('error', 'You need an address on file (e.g. from a subscription) to place a one-time order.');
        }

        $addressId = (string) $firstSub['address_id'];
        $nextChargeAt = $firstSub['next_charge_scheduled_at'] ?? null;
        if (! $nextChargeAt && ! empty($firstSub['id'])) {
            $fullSub = $this->recharge->getSubscription((string) $firstSub['id']);
            $nextChargeAt = $fullSub['next_charge_scheduled_at'] ?? null;
        }
        if (! $nextChargeAt) {
            return redirect()->route('account.dashboard')->with('error', 'Could not determine next charge date for the one-time order. Please try again or add to subscription.');
        }

        $variantId = $product->shopify_variant_id;
        if (str_starts_with($variantId, 'gid://')) {
            $variantId = last(explode('/', trim($variantId)));
        }
        $variantId = (string) $variantId;

        $payload = [
            'address_id' => (int) $addressId,
            'quantity' => (int) ($product->default_quantity ?? 1),
            'next_charge_scheduled_at' => $nextChargeAt,
            'external_variant_id' => $variantId,
        ];

        $price = $product->first_order_price !== null ? (float) $product->first_order_price : $product->discounted_price;
        if ($price !== null && $price > 0) {
            $payload['price'] = number_format((float) $price, 2, '.', '');
        }

        try {
            $this->recharge->createOnetime($payload);
        } catch (\Throwable $e) {
            return redirect()->route('account.dashboard')->with('error', 'Could not add one-time product: ' . $e->getMessage());
        }
        AccountDashboardController::forgetDashboardCache($customerId);
        return redirect()->route('account.dashboard')->with('success', __('One-time product added. It will be included in your next order.'));
    }

    /**
     * Build metadata for add_product audit logs (visible in Admin → Audit logs).
     */
    private function buildAddProductDebugMeta(
        string $customerId,
        ?string $email,
        int $productId,
        array $subs,
        array $activeSubs,
        ?array $firstSub,
        ?array $fullSub,
        ?string $nextChargeAt,
        string $step
    ): array {
        $subsSummary = [];
        foreach ($subs as $i => $s) {
            $subsSummary[] = [
                'index' => $i,
                'id' => $s['id'] ?? null,
                'status' => $s['status'] ?? null,
                'address_id' => $s['address_id'] ?? null,
                'next_charge_scheduled_at' => $s['next_charge_scheduled_at'] ?? null,
                'keys_in_response' => array_keys($s),
            ];
        }
        $firstSubSnapshot = $firstSub ? [
            'id' => $firstSub['id'] ?? null,
            'status' => $firstSub['status'] ?? null,
            'address_id' => $firstSub['address_id'] ?? null,
            'next_charge_scheduled_at' => $firstSub['next_charge_scheduled_at'] ?? null,
            'keys_in_response' => array_keys($firstSub),
        ] : null;
        $fullSubSnapshot = $fullSub ? [
            'id' => $fullSub['id'] ?? null,
            'next_charge_scheduled_at' => $fullSub['next_charge_scheduled_at'] ?? null,
            'keys_in_response' => array_keys($fullSub),
        ] : null;

        return [
            'step' => $step,
            'customer_id' => $customerId,
            'actor_email' => $email,
            'product_id' => $productId,
            'subscriptions_count' => count($subs),
            'active_subscriptions_count' => count($activeSubs),
            'subs_summary_from_list' => $subsSummary,
            'first_sub_from_list' => $firstSubSnapshot,
            'get_subscription_called' => $fullSub !== null,
            'full_sub_from_get' => $fullSubSnapshot,
            'final_next_charge_used' => $nextChargeAt,
        ];
    }
}
