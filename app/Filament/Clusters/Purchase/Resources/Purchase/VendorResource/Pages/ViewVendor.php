<?php

namespace App\Filament\Clusters\Purchase\Resources\Purchase\VendorResource\Pages;

use App\Filament\Clusters\Purchase\Resources\Purchase\VendorResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewVendor extends ViewRecord
{
    protected static string $resource = VendorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
