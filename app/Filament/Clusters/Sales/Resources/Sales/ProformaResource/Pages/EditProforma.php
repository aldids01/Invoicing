<?php

namespace App\Filament\Clusters\Sales\Resources\Sales\ProformaResource\Pages;

use App\Filament\Clusters\Sales\Resources\Sales\ProformaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProforma extends EditRecord
{
    protected static string $resource = ProformaResource::class;

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
