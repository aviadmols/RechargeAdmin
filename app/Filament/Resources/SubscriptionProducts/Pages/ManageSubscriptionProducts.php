<?php

namespace App\Filament\Resources\SubscriptionProducts\Pages;

use App\Filament\Resources\SubscriptionProducts\SubscriptionProductResource;
use App\Filament\Widgets\NextOrderHeroWidget;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageSubscriptionProducts extends ManageRecords
{
    protected static string $resource = SubscriptionProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getHeaderWidgets(): array
    {
        return [NextOrderHeroWidget::class];
    }
}
