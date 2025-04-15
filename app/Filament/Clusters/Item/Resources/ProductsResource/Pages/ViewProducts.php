<?php

namespace App\Filament\Clusters\Item\Resources\ProductsResource\Pages;

use App\Filament\Clusters\Item\Resources\ProductsResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewProducts extends ViewRecord
{
    protected static string $resource = ProductsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
