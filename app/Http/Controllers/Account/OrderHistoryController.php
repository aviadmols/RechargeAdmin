<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Services\RechargeService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderHistoryController extends Controller
{
    public function __construct(
        protected RechargeService $recharge
    ) {}

    public function index(Request $request): View
    {
        $user = auth()->guard('portal')->user();
        $customerId = $user->recharge_customer_id;

        $params = ['limit' => 20];
        if ($request->filled('status')) {
            $params['status'] = $request->input('status');
        }
        if ($request->filled('created_at_min')) {
            $params['created_at_min'] = $request->input('created_at_min');
        }
        if ($request->filled('created_at_max')) {
            $params['created_at_max'] = $request->input('created_at_max');
        }
        if ($request->filled('cursor')) {
            $params['cursor'] = $request->input('cursor');
        }

        $data = $this->recharge->listOrders($customerId, $params);
        $orders = $data['orders'] ?? [];
        $nextCursor = $data['next_cursor'] ?? null;
        $prevCursor = $data['previous_cursor'] ?? null;

        return view('account.orders.index', [
            'orders' => $orders,
            'nextCursor' => $nextCursor,
            'prevCursor' => $prevCursor,
        ]);
    }

    public function show(string $id): View
    {
        $user = auth()->guard('portal')->user();
        $order = $this->recharge->getOrder($id);
        if (! $order) {
            abort(404);
        }
        $orderCustomerId = (string) ($order['customer_id'] ?? $order['customer']['id'] ?? '');
        if ($orderCustomerId !== (string) $user->recharge_customer_id) {
            abort(403);
        }
        return view('account.orders.show', ['order' => $order]);
    }
}
