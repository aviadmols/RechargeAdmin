<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RechargeSettings;
use App\Services\AuditLogService;
use App\Services\RechargeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function __construct(
        protected RechargeService $recharge,
        protected AuditLogService $audit
    ) {}

    public function update(Request $request, string $id): JsonResponse
    {
        if (! RechargeSettings::first()?->isFeatureEnabled('enable_address_update')) {
            return response()->json(['message' => 'Not allowed.'], 403);
        }
        $user = auth()->guard('portal')->user();
        $payload = $request->validate([
            'address1' => 'sometimes|string',
            'address2' => 'sometimes|nullable|string',
            'city' => 'sometimes|string',
            'province' => 'sometimes|nullable|string',
            'country_code' => 'sometimes|string|size:2',
            'zip' => 'sometimes|string',
            'first_name' => 'sometimes|string',
            'last_name' => 'sometimes|string',
            'phone' => 'sometimes|nullable|string',
        ]);
        try {
            $this->recharge->updateShippingAddress($id, $payload);
            $this->audit->log('address.update', $user->email, $user->recharge_customer_id, 'address', $id, 'success', null, []);
            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            $this->audit->log('address.update', $user->email, $user->recharge_customer_id, 'address', $id, 'fail', $e->getMessage(), []);
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
