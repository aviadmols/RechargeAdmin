<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RechargeSettings;
use App\Services\AuditLogService;
use App\Services\RechargeService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionActionController extends Controller
{
    public function __construct(
        protected RechargeService $recharge,
        protected AuditLogService $audit
    ) {}

    protected function customerId(): string
    {
        return (string) auth()->guard('portal')->user()->recharge_customer_id;
    }

    protected function email(): string
    {
        return (string) auth()->guard('portal')->user()->email;
    }

    protected function settings(): ?RechargeSettings
    {
        return RechargeSettings::first();
    }

    public function updateNextChargeDate(Request $request, string $id): JsonResponse
    {
        $request->validate(['date' => 'required|date|after:today']);
        try {
            $this->recharge->updateNextChargeDate($id, Carbon::parse($request->input('date')));
            $this->recharge->invalidateCustomerCache($this->customerId());
            $this->audit->log('subscription.next_charge_date', $this->email(), $this->customerId(), 'subscription', $id, 'success', null, []);
            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            $this->audit->log('subscription.next_charge_date', $this->email(), $this->customerId(), 'subscription', $id, 'fail', $e->getMessage(), []);
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function cancel(Request $request, string $id): JsonResponse
    {
        if (! $this->settings()?->isFeatureEnabled('enable_cancel')) {
            return response()->json(['message' => 'Not allowed.'], 403);
        }
        $payload = $request->only(['cancellation_reason', 'comment']);
        try {
            $this->recharge->cancelSubscription($id, $payload);
            $this->recharge->invalidateCustomerCache($this->customerId());
            $this->audit->log('subscription.cancel', $this->email(), $this->customerId(), 'subscription', $id, 'success', null, []);
            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            $this->audit->log('subscription.cancel', $this->email(), $this->customerId(), 'subscription', $id, 'fail', $e->getMessage(), []);
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function pause(string $id): JsonResponse
    {
        if (! $this->settings()?->isFeatureEnabled('enable_pause')) {
            return response()->json(['message' => 'Not allowed.'], 403);
        }
        try {
            $this->recharge->pauseSubscription($id);
            $this->recharge->invalidateCustomerCache($this->customerId());
            $this->audit->log('subscription.pause', $this->email(), $this->customerId(), 'subscription', $id, 'success', null, []);
            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            $this->audit->log('subscription.pause', $this->email(), $this->customerId(), 'subscription', $id, 'fail', $e->getMessage(), []);
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function resume(string $id): JsonResponse
    {
        if (! $this->settings()?->isFeatureEnabled('enable_pause')) {
            return response()->json(['message' => 'Not allowed.'], 403);
        }
        try {
            $this->recharge->resumeSubscription($id);
            $this->recharge->invalidateCustomerCache($this->customerId());
            $this->audit->log('subscription.resume', $this->email(), $this->customerId(), 'subscription', $id, 'success', null, []);
            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            $this->audit->log('subscription.resume', $this->email(), $this->customerId(), 'subscription', $id, 'fail', $e->getMessage(), []);
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function swap(Request $request, string $id): JsonResponse
    {
        if (! $this->settings()?->isFeatureEnabled('enable_swap')) {
            return response()->json(['message' => 'Not allowed.'], 403);
        }
        $request->validate(['external_variant_id' => 'required|string']);
        try {
            $this->recharge->swapSubscriptionVariant($id, $request->input('external_variant_id'));
            $this->recharge->invalidateCustomerCache($this->customerId());
            $this->audit->log('subscription.swap', $this->email(), $this->customerId(), 'subscription', $id, 'success', null, []);
            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            $this->audit->log('subscription.swap', $this->email(), $this->customerId(), 'subscription', $id, 'fail', $e->getMessage(), []);
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function updateQuantity(Request $request, string $id): JsonResponse
    {
        $request->validate(['quantity' => 'required|integer|min:1']);
        try {
            $this->recharge->updateSubscriptionQuantity($id, (int) $request->input('quantity'));
            $this->recharge->invalidateCustomerCache($this->customerId());
            $this->audit->log('subscription.quantity', $this->email(), $this->customerId(), 'subscription', $id, 'success', null, []);
            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            $this->audit->log('subscription.quantity', $this->email(), $this->customerId(), 'subscription', $id, 'fail', $e->getMessage(), []);
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
