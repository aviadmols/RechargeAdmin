<?php

namespace App\Filament\Resources\PortalCustomers\Pages;

use App\Filament\Resources\PortalCustomers\PortalCustomerResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManagePortalCustomers extends ManageRecords
{
    protected static string $resource = PortalCustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
