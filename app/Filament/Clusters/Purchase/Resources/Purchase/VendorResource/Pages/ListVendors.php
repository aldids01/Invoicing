<?php

namespace App\Filament\Clusters\Purchase\Resources\Purchase\VendorResource\Pages;

use App\Filament\Clusters\Purchase\Resources\Purchase\VendorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVendors extends ListRecords
{
    protected static string $resource = VendorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
