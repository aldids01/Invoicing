<?php

namespace App\Filament\Clusters\Purchase\Resources\Purchase\DebitResource\Pages;

use App\Filament\Clusters\Purchase\Resources\Purchase\DebitResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDebit extends EditRecord
{
    protected static string $resource = DebitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
