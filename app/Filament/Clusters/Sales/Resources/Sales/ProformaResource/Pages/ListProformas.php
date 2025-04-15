<?php

namespace App\Filament\Clusters\Sales\Resources\Sales\ProformaResource\Pages;

use App\Filament\Clusters\Sales\Resources\Sales\ProformaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProformas extends ListRecords
{
    protected static string $resource = ProformaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
