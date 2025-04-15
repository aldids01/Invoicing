<?php

namespace App\Filament\Clusters\Sales\Resources\Sales\CreditResource\Pages;

use App\Filament\Clusters\Sales\Resources\Sales\CreditResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCredit extends ViewRecord
{
    protected static string $resource = CreditResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
