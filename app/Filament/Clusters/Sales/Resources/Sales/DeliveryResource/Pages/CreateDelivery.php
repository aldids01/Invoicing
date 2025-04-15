<?php

namespace App\Filament\Clusters\Sales\Resources\Sales\DeliveryResource\Pages;

use App\Filament\Clusters\Sales\Resources\Sales\DeliveryResource;
use App\Filament\Clusters\Sales\Resources\Sales\EstimateResource;
use Filament\Actions;
use Filament\Forms\Components\Wizard\Step;
use Filament\Resources\Pages\CreateRecord;

class CreateDelivery extends CreateRecord
{
    protected static string $resource = DeliveryResource::class;
    use CreateRecord\Concerns\HasWizard;
    protected function getSteps(): array
    {
        return [
            Step::make('Delivery Challan Information')
                ->schema(DeliveryResource::getInformation())->columns(3),
            Step::make('Delivery Challan Items')
                ->schema([DeliveryResource::getItem()]),
            Step::make('Delivery Challan Summary')
                ->schema(DeliveryResource::getSummary()),
        ];
    }
}
