<?php

namespace App\Filament\Clusters\Purchase\Resources\Purchase\VendorResource\Pages;

use App\Filament\Clusters\Purchase\Resources\Purchase\VendorResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateVendor extends CreateRecord
{
    protected static string $resource = VendorResource::class;
}
