<?php

namespace App\Filament\Clusters\Purchase\Resources\Purchase\BillResource\Pages;

use App\Filament\Clusters\Purchase\Resources\Purchase\BillResource;
use Filament\Forms\Components\Wizard\Step;
use Filament\Resources\Pages\CreateRecord;

class CreateBill extends CreateRecord
{
    protected static string $resource = BillResource::class;
    use CreateRecord\Concerns\HasWizard;
    protected function getSteps(): array
    {
        return [
            Step::make('Bill Information')
                ->schema(BillResource::getInformation())->columns(3),
            Step::make('Bill Items')
                ->schema([BillResource::getItem()]),
            Step::make('Bill Summary')
                ->schema(BillResource::getSummary()),
        ];
    }
}
