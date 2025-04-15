<?php

namespace App\Models\Sales;

use App\Models\Address;
use App\Models\Business;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Credit extends Model implements HasMedia
{
    use softDeletes, InteractsWithMedia;
    public $guarded = ['id'];
    protected $hidden = ['created_at', 'updated_at'];
    public function billing():BelongsTo
    {
        return $this->belongsTo(Address::class, 'billing_id', 'id');
    }
    public function shipping():BelongsTo
    {
        return $this->belongsTo(Address::class, 'shipping_id', 'id');
    }
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
    public function items(): HasMany
    {
        return $this->hasMany(LineItem::class);
    }
}
