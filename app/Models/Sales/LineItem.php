<?php

namespace App\Models\Sales;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LineItem extends Model
{
    public $guarded = ['id'];
    protected $hidden = ['created_at', 'updated_at'];
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
    public function receipt(): BelongsTo
    {
        return $this->belongsTo(Receipt::class);
    }
    public function proforma(): BelongsTo
    {
        return $this->belongsTo(Proforma::class);
    }
    public function estimate(): BelongsTo
    {
        return $this->belongsTo(Estimate::class);
    }
    public function delivery(): BelongsTo
    {
        return $this->belongsTo(Delivery::class);
    }
    public function credit(): BelongsTo
    {
        return $this->belongsTo(Credit::class);
    }
    public function product(): BelongsTo
    {
        return $this->belongsTo(Products::class);
    }
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
