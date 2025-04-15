<?php

namespace App\Filament\Clusters\Sales\Resources\Sales\CustomerResource\Pages;

use App\Filament\Clusters\Sales\Resources\Sales\CustomerResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCustomer extends ViewRecord
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
