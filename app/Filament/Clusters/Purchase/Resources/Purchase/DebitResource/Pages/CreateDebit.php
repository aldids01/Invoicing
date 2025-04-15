<?php

namespace App\Filament\Clusters\Purchase\Resources\Purchase\DebitResource\Pages;

use App\Filament\Clusters\Purchase\Resources\Purchase\DebitResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDebit extends CreateRecord
{
    protected static string $resource = DebitResource::class;
}
