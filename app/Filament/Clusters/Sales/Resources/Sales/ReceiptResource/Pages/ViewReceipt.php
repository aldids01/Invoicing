<?php

namespace App\Filament\Clusters\Sales\Resources\Sales\ReceiptResource\Pages;

use App\Filament\Clusters\Sales\Resources\Sales\ReceiptResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewReceipt extends ViewRecord
{
    protected static string $resource = ReceiptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
