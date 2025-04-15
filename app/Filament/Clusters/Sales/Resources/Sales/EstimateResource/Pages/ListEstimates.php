<?php

namespace App\Filament\Clusters\Sales\Resources\Sales\EstimateResource\Pages;

use App\Filament\Clusters\Sales\Resources\Sales\EstimateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEstimates extends ListRecords
{
    protected static string $resource = EstimateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
