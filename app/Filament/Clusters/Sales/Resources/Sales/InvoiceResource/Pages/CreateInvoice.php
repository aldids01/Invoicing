<?php

namespace App\Filament\Clusters\Sales\Resources\Sales\InvoiceResource\Pages;

use App\Filament\Clusters\Sales\Resources\Sales\InvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Components\Wizard\Step;
class CreateInvoice extends CreateRecord
{

    protected static string $resource = InvoiceResource::class;
    use CreateRecord\Concerns\HasWizard;
    protected function getSteps(): array
    {
        return [
            Step::make('Invoice Information')
                ->schema(InvoiceResource::getInformation())->columns(3),
            Step::make('Invoice Items')
                ->schema([InvoiceResource::getItem()]),
            Step::make('Invoice Summary')
                ->schema(InvoiceResource::getSummary()),
        ];
    }
}
