<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class Item extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?int $navigationSort  = 200;
}
