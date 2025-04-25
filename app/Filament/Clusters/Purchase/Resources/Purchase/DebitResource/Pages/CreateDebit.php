<?php

namespace App\Filament\Clusters\Purchase\Resources\Purchase\DebitResource\Pages;

use App\Filament\Clusters\Purchase\Resources\Purchase\DebitResource;
use Filament\Actions;
use Filament\Forms\Components\Wizard\Step;
use Filament\Resources\Pages\CreateRecord;

class CreateDebit extends CreateRecord
{
    protected static string $resource = DebitResource::class;
    use CreateRecord\Concerns\HasWizard;
    protected function getSteps(): array
    {
        return [
            Step::make('Debit Note Information')
                ->schema(DebitResource::getInformation())->columns(3),
            Step::make('Debit Note Items')
                ->schema([DebitResource::getItem()]),
            Step::make('Debit Note Summary')
                ->schema(DebitResource::getSummary()),
        ];
    }
}
