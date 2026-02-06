<?php

namespace App\Filament\Widgets;

use App\Models\RechargeSettings;
use App\Services\RechargeService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat as StatWidget;

class RechargeHealthWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $settings = RechargeSettings::first();
        $lastSuccess = $settings?->last_api_success_at?->diffForHumans() ?? 'Never';

        return [
            StatWidget::make('Recharge API', $lastSuccess)
                ->description('Last successful call'),
        ];
    }
}
