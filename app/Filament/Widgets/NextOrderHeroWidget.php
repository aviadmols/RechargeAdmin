<?php

namespace App\Filament\Widgets;

use App\Models\SubscriptionProduct;
use App\Services\RechargeService;
use Carbon\Carbon;
use Filament\Widgets\Widget;

class NextOrderHeroWidget extends Widget
{
    protected static bool $isDiscovered = false;

    protected int $columnSpan = 'full';

    /**
     * @var view-string
     */
    protected string $view = 'filament.widgets.next-order-hero-widget';

    protected static ?int $sort = 0;

    protected function getViewData(): array
    {
        $nextOrderDate = null;
        try {
            $recharge = app(RechargeService::class);
            $response = $recharge->listCharges([
                'scheduled_at_min' => now()->toIso8601String(),
                'sort_by' => 'scheduled_at-asc',
                'limit' => 1,
            ]);
            $charges = $response['charges'] ?? [];
            $first = $charges[0] ?? null;
            if ($first && ! empty($first['scheduled_at'])) {
                $nextOrderDate = Carbon::parse($first['scheduled_at']);
            }
        } catch (\Throwable) {
            // Leave nextOrderDate null
        }

        $products = SubscriptionProduct::active()->ordered()->get();

        return [
            'nextOrderDate' => $nextOrderDate,
            'products' => $products,
        ];
    }
}
