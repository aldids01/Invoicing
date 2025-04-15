<?php

namespace App\Filament\Clusters\Sales\Resources\Sales\EstimateResource\Pages;

use App\Filament\Clusters\Sales\Resources\Sales\EstimateResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewEstimate extends ViewRecord
{
    protected static string $resource = EstimateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
