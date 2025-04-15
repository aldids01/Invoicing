<?php

namespace App\Filament\Clusters\Sales\Resources\Sales\ReceiptResource\Pages;

use App\Filament\Clusters\Sales\Resources\Sales\InvoiceResource;
use App\Filament\Clusters\Sales\Resources\Sales\ReceiptResource;
use Filament\Actions;
use Filament\Forms\Components\Wizard\Step;
use Filament\Resources\Pages\CreateRecord;

class CreateReceipt extends CreateRecord
{
    protected static string $resource = ReceiptResource::class;
    use CreateRecord\Concerns\HasWizard;
    protected function getSteps(): array
    {
        return [
            Step::make('Receipt Information')
                ->schema(ReceiptResource::getInformation())->columns(3),
            Step::make('Receipt Items')
                ->schema([ReceiptResource::getItem()]),
            Step::make('Receipt Summary')
                ->schema(ReceiptResource::getSummary()),
        ];
    }
}
