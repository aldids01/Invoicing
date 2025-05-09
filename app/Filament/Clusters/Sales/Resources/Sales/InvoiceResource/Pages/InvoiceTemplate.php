<?php

namespace App\Filament\Clusters\Sales\Resources\Sales\InvoiceResource\Pages;

use App\Filament\Clusters\Sales\Resources\Sales\InvoiceResource;
use Filament\Resources\Pages\Page;

class InvoiceTemplate extends Page
{
    protected static string $resource = InvoiceResource::class;

    protected static string $view = 'filament.clusters.sales.resources.sales.invoice-resource.pages.invoice-template';
}
