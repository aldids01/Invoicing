<?php

namespace App\Filament\Clusters\Sales\Resources\Sales\CreditResource\Pages;

use App\Filament\Clusters\Sales\Resources\Sales\CreditResource;
use App\Filament\Clusters\Sales\Resources\Sales\DeliveryResource;
use Filament\Actions;
use Filament\Forms\Components\Wizard\Step;
use Filament\Resources\Pages\CreateRecord;

class CreateCredit extends CreateRecord
{
    protected static string $resource = CreditResource::class;
    use CreateRecord\Concerns\HasWizard;
    protected function getSteps(): array
    {
        return [
            Step::make('Credit Note Information')
                ->schema(CreditResource::getInformation())->columns(3),
            Step::make('Credit Note Items')
                ->schema([CreditResource::getItem()]),
            Step::make('Credit Note Summary')
                ->schema(CreditResource::getSummary()),
        ];
    }
}
