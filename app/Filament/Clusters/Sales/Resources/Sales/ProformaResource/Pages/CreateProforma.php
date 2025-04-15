<?php

namespace App\Filament\Clusters\Sales\Resources\Sales\ProformaResource\Pages;

use App\Filament\Clusters\Sales\Resources\Sales\ProformaResource;
use App\Filament\Clusters\Sales\Resources\Sales\ReceiptResource;
use Filament\Actions;
use Filament\Forms\Components\Wizard\Step;
use Filament\Resources\Pages\CreateRecord;

class CreateProforma extends CreateRecord
{
    protected static string $resource = ProformaResource::class;
    use CreateRecord\Concerns\HasWizard;
    protected function getSteps(): array
    {
        return [
            Step::make('Proforma Invoice Information')
                ->schema(ProformaResource::getInformation())->columns(3),
            Step::make('Proforma Invoice Items')
                ->schema([ProformaResource::getItem()]),
            Step::make('Proforma Invoice Summary')
                ->schema(ProformaResource::getSummary()),
        ];
    }
}
