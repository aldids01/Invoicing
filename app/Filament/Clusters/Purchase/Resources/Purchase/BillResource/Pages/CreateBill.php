<?php

namespace App\Filament\Clusters\Purchase\Resources\Purchase\BillResource\Pages;

use App\Filament\Clusters\Purchase\Resources\Purchase\BillResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBill extends CreateRecord
{
    protected static string $resource = BillResource::class;
}
