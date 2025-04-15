<?php

namespace App\Filament\Clusters\Sales\Resources\Sales\ReceiptResource\Pages;

use App\Filament\Clusters\Sales\Resources\Sales\ReceiptResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReceipts extends ListRecords
{
    protected static string $resource = ReceiptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
