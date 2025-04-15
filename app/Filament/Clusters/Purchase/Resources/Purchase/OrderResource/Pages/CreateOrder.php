<?php

namespace App\Filament\Clusters\Purchase\Resources\Purchase\OrderResource\Pages;

use App\Filament\Clusters\Purchase\Resources\Purchase\OrderResource;
use Filament\Actions;
use Filament\Forms\Components\Wizard\Step;
use Filament\Resources\Pages\CreateRecord;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;
    use CreateRecord\Concerns\HasWizard;
    protected function getSteps(): array
    {
        return [
            Step::make('Purchase Order Information')
                ->schema(OrderResource::getInformation())->columns(3),
            Step::make('Purchase Order Items')
                ->schema([OrderResource::getItem()]),
            Step::make('Purchase Order Summary')
                ->schema(OrderResource::getSummary()),
        ];
    }
}
