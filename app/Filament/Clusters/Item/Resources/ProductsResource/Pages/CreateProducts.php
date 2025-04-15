<?php

namespace App\Filament\Clusters\Item\Resources\ProductsResource\Pages;

use App\Filament\Clusters\Item\Resources\ProductsResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProducts extends CreateRecord
{
    protected static string $resource = ProductsResource::class;
}
