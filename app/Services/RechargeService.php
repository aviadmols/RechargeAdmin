<?php

namespace App\Services;

use App\Models\RechargeSettings;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class RechargeService
{
    protected ?string $token = null;

    protected ?string $baseUrl = null;

    protected ?string $version = null;

    protected int $timeout;

    protected int $retryTimes;

    public function __construct()
    {
        $settings = RechargeSettings::first();
        if ($settings) {
            $this->token = $settings->getDecryptedToken();
            $this->baseUrl = rtrim($settings->base_url ?? config('recharge.base_url'), '/');
            $this->version = $settings->api_version ?? config('recharge.api_version');
        }
        $this->timeout = config('recharge.timeout', 15);
        $this->retryTimes = config('recharge.retry_times', 2);
    }

    protected function client(?string $version = null): \Illuminate\Http\Client\PendingRequest
    {
        if (! $this->token || ! $this->baseUrl) {
            throw new \RuntimeException('Recharge API not configured.');
        }
        $apiVersion = $version ?? $this->version ?? config('recharge.api_version', '2021-11');
        $apiVersion = $apiVersion !== '' && $apiVersion !== null ? $apiVersion : '2021-11';

        return Http::withHeaders([
            'X-Recharge-Access-Token' => $this->token,
            'X-Recharge-Version' => $apiVersion,
            'X-Request-ID' => Str::uuid()->toString(),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->timeout($this->timeout)->retry($this->retryTimes, 500);
    }

    public function findCustomerByEmail(string $email): ?array
    {
        $response = $this->client()->get("{$this->baseUrl}/customers", [
            'email' => $email,
            'limit' => 1,
        ]);
        if (! $response->successful()) {
            return null;
        }
        $this->markSuccess();
        $data = $response->json();
        $customers = $data['customers'] ?? [];
        return $customers[0] ?? null;
    }

    public function listOrders(string $customerId, array $params = []): array
    {
        $ttl = RechargeSettings::first()?->cache_ttl_orders ?? 120;
        $key = "recharge.orders.{$customerId}." . md5(json_encode($params));
        return Cache::remember($key, $ttl, function () use ($customerId, $params) {
            $query = array_merge(['customer_id' => $customerId, 'limit' => 50], $params);
            $response = $this->client()->get("{$this->baseUrl}/orders", $query);
            if (! $response->successful()) {
                throw new \RuntimeException('Recharge orders list failed: ' . $response->body());
            }
            $this->markSuccess();
            return $response->json();
        });
    }

    public function getOrder(string $orderId): ?array
    {
        $response = $this->client()->get("{$this->baseUrl}/orders/{$orderId}");
        if (! $response->successful()) {
            return null;
        }
        $this->markSuccess();
        $data = $response->json();
        return $data['order'] ?? $data;
    }

    public function listSubscriptions(string $customerId, array $params = []): array
    {
        $ttl = RechargeSettings::first()?->cache_ttl_subscriptions ?? 60;
        $key = "recharge.subscriptions.{$customerId}." . md5(json_encode($params));
        return Cache::remember($key, $ttl, function () use ($customerId, $params) {
            $query = array_merge(['customer_id' => $customerId, 'limit' => 250], $params);
            $response = $this->client()->get("{$this->baseUrl}/subscriptions", $query);
            if (! $response->successful()) {
                throw new \RuntimeException('Recharge subscriptions list failed: ' . $response->body());
            }
            $this->markSuccess();
            return $response->json();
        });
    }

    public function getSubscription(string $subscriptionId): ?array
    {
        $ttl = RechargeSettings::first()?->cache_ttl_subscriptions ?? 60;
        $key = "recharge.subscription.{$subscriptionId}";
        $cached = Cache::get($key);
        if ($cached !== null) {
            return $cached;
        }
        $response = $this->client()->get("{$this->baseUrl}/subscriptions/{$subscriptionId}");
        if (! $response->successful()) {
            return null;
        }
        $this->markSuccess();
        $data = $response->json();
        $sub = $data['subscription'] ?? $data;
        Cache::put($key, $sub, $ttl);
        return $sub;
    }

    public function invalidateCustomerCache(string $customerId): void
    {
        Cache::forget("recharge.subscriptions.{$customerId}");
        Cache::forget("recharge.orders.{$customerId}");
    }

    public function updateNextChargeDate(string $subscriptionId, Carbon $date): array
    {
        $this->invalidateSubscriptionCache($subscriptionId, null);
        $response = $this->client()->put("{$this->baseUrl}/subscriptions/{$subscriptionId}", [
            'next_charge_scheduled_at' => $date->toIso8601String(),
        ]);
        if (! $response->successful()) {
            throw new \RuntimeException('Recharge update next charge failed: ' . $response->body());
        }
        $this->markSuccess();
        return $response->json();
    }

    public function cancelSubscription(string $subscriptionId, array $payload = []): array
    {
        $this->invalidateSubscriptionCache($subscriptionId, null);
        $response = $this->client()->post("{$this->baseUrl}/subscriptions/{$subscriptionId}/cancel", $payload);
        if (! $response->successful()) {
            throw new \RuntimeException('Recharge cancel subscription failed: ' . $response->body());
        }
        $this->markSuccess();
        return $response->json();
    }

    public function pauseSubscription(string $subscriptionId): array
    {
        $this->invalidateSubscriptionCache($subscriptionId, null);
        $response = $this->client()->post("{$this->baseUrl}/subscriptions/{$subscriptionId}/pause");
        if (! $response->successful()) {
            throw new \RuntimeException('Recharge pause subscription failed: ' . $response->body());
        }
        $this->markSuccess();
        return $response->json();
    }

    public function resumeSubscription(string $subscriptionId): array
    {
        $this->invalidateSubscriptionCache($subscriptionId, null);
        $response = $this->client()->post("{$this->baseUrl}/subscriptions/{$subscriptionId}/activate");
        if (! $response->successful()) {
            throw new \RuntimeException('Recharge resume subscription failed: ' . $response->body());
        }
        $this->markSuccess();
        return $response->json();
    }

    public function swapSubscriptionVariant(string $subscriptionId, string $externalVariantId): array
    {
        $this->invalidateSubscriptionCache($subscriptionId, null);
        $response = $this->client()->put("{$this->baseUrl}/subscriptions/{$subscriptionId}", [
            'external_variant_id' => [
                'ecommerce' => (string) $externalVariantId,
            ],
        ]);
        if (! $response->successful()) {
            throw new \RuntimeException('Recharge swap variant failed: ' . $response->body());
        }
        $this->markSuccess();
        return $response->json();
    }

    public function updateSubscriptionQuantity(string $subscriptionId, int $quantity): array
    {
        $this->invalidateSubscriptionCache($subscriptionId, null);
        $response = $this->client()->put("{$this->baseUrl}/subscriptions/{$subscriptionId}", [
            'quantity' => $quantity,
        ]);
        if (! $response->successful()) {
            throw new \RuntimeException('Recharge update quantity failed: ' . $response->body());
        }
        $this->markSuccess();
        return $response->json();
    }

    public function getAddress(string $addressId): ?array
    {
        $response = $this->client()->get("{$this->baseUrl}/addresses/{$addressId}");
        if (! $response->successful()) {
            return null;
        }
        $this->markSuccess();
        $data = $response->json();
        return $data['address'] ?? $data;
    }

    public function updateShippingAddress(string $addressId, array $payload): array
    {
        $response = $this->client()->put("{$this->baseUrl}/addresses/{$addressId}", $payload);
        if (! $response->successful()) {
            throw new \RuntimeException('Recharge update address failed: ' . $response->body());
        }
        $this->markSuccess();
        return $response->json();
    }

    /**
     * List products configured for subscription in Recharge (linked to Shopify).
     * Required scope: read_products.
     */
    public function listProducts(array $params = []): array
    {
        $query = array_merge(['limit' => 250], $params);
        $response = $this->client()->get("{$this->baseUrl}/products", $query);
        if (! $response->successful()) {
            throw new \RuntimeException('Recharge products list failed: ' . $response->body());
        }
        $this->markSuccess();
        return $response->json();
    }

    /**
     * Create a new subscription (add a product to the customer – same address).
     * Required scope: write_subscriptions.
     * Uses Recharge API version 2021-11 and X-Recharge-Version header.
     * Normalizes external_variant_id and external_product_id to { ecommerce: "<id>" } before sending.
     *
     * @see https://developer.rechargepayments.com/2021-11/subscriptions/subscriptions_create
     */
    public function createSubscription(array $payload): array
    {
        if (isset($payload['external_variant_id'])) {
            $payload['external_variant_id'] = [
                'ecommerce' => (string) $payload['external_variant_id'],
            ];
        }

        if (isset($payload['external_product_id'])) {
            $payload['external_product_id'] = [
                'ecommerce' => (string) $payload['external_product_id'],
            ];
        }

        $response = $this->client('2021-11')->post("{$this->baseUrl}/subscriptions", $payload);

        if (! $response->successful()) {
            throw new \RuntimeException('Recharge create subscription failed: ' . $response->body());
        }

        $this->markSuccess();

        $data = $response->json();
        $subscription = $data['subscription'] ?? $data;

        if (! empty($subscription['address_id'])) {
            $this->invalidateSubscriptionCache(null, (string) ($subscription['customer_id'] ?? ''));
        }

        return $data;
    }

    public function testConnection(): bool
    {
        try {
            $response = $this->client()->get("{$this->baseUrl}/customers", ['limit' => 1]);
            if ($response->successful()) {
                $this->markSuccess();
                return true;
            }
            return false;
        } catch (\Throwable) {
            return false;
        }
    }

    protected function markSuccess(): void
    {
        RechargeSettings::query()->update(['last_api_success_at' => now()]);
    }

    protected function invalidateSubscriptionCache(string $subscriptionId, ?string $customerId = null): void
    {
        Cache::forget("recharge.subscription.{$subscriptionId}");
        if ($customerId) {
            Cache::forget("recharge.subscriptions.{$customerId}");
        }
    }
}
