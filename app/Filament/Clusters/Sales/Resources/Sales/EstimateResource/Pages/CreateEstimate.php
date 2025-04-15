<?php

namespace App\Filament\Clusters\Sales\Resources\Sales\EstimateResource\Pages;

use App\Filament\Clusters\Sales\Resources\Sales\EstimateResource;
use App\Filament\Clusters\Sales\Resources\Sales\ProformaResource;
use Filament\Actions;
use Filament\Forms\Components\Wizard\Step;
use Filament\Resources\Pages\CreateRecord;

class CreateEstimate extends CreateRecord
{
    protected static string $resource = EstimateResource::class;
    use CreateRecord\Concerns\HasWizard;
    protected function getSteps(): array
    {
        return [
            Step::make('Estimate Information')
                ->schema(EstimateResource::getInformation())->columns(3),
            Step::make('Estimate Items')
                ->schema([EstimateResource::getItem()]),
            Step::make('Estimate Summary')
                ->schema(EstimateResource::getSummary()),
        ];
    }
}
