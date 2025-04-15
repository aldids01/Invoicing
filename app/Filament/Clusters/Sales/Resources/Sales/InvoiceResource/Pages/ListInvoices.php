<?php

namespace App\Filament\Clusters\Sales\Resources\Sales\InvoiceResource\Pages;

use App\Filament\Clusters\Sales\Resources\Sales\InvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInvoices extends ListRecords
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
