<?php

namespace App\Filament\Clusters\Purchase\Resources\Purchase\DebitResource\Pages;

use App\Filament\Clusters\Purchase\Resources\Purchase\DebitResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDebit extends ViewRecord
{
    protected static string $resource = DebitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
