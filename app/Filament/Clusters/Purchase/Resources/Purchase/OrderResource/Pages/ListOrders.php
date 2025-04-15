<?php

namespace App\Filament\Clusters\Purchase\Resources\Purchase\OrderResource\Pages;

use App\Filament\Clusters\Purchase\Resources\Purchase\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
