<?php

namespace App\Filament\Clusters\Sales\Resources\Sales\ReceiptResource\Pages;

use App\Filament\Clusters\Sales\Resources\Sales\ReceiptResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReceipt extends EditRecord
{
    protected static string $resource = ReceiptResource::class;

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
