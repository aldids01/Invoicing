<?php

namespace App\Filament\Clusters\Sales\Resources\Sales\CustomerResource\Pages;

use App\Filament\Clusters\Sales\Resources\Sales\CustomerResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomer extends CreateRecord
{
    protected static string $resource = CustomerResource::class;
}
