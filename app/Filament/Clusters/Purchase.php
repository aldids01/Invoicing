<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class Purchase extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?int $navigationSort = 300;
}
