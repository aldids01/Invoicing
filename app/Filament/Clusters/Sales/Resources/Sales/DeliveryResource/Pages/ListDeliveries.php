<?php

namespace App\Filament\Clusters\Sales\Resources\Sales\DeliveryResource\Pages;

use App\Filament\Clusters\Sales\Resources\Sales\DeliveryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDeliveries extends ListRecords
{
    protected static string $resource = DeliveryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
