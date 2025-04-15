<?php

namespace App\Filament\Clusters\Purchase\Resources\Purchase\DebitResource\Pages;

use App\Filament\Clusters\Purchase\Resources\Purchase\DebitResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDebits extends ListRecords
{
    protected static string $resource = DebitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
