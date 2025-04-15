<?php

namespace App\Filament\Clusters\Sales\Resources\Sales\EstimateResource\Pages;

use App\Filament\Clusters\Sales\Resources\Sales\EstimateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEstimate extends EditRecord
{
    protected static string $resource = EstimateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
