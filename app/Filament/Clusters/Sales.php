<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class Sales extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?int $navigationSort  = 100;
}
