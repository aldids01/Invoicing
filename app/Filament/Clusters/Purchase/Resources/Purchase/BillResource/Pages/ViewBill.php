<?php

namespace App\Filament\Clusters\Purchase\Resources\Purchase\BillResource\Pages;

use App\Filament\Clusters\Purchase\Resources\Purchase\BillResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBill extends ViewRecord
{
    protected static string $resource = BillResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
