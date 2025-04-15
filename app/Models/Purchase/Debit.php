<?php

namespace App\Models\Purchase;

use App\Models\Address;
use App\Models\Business;
use App\Models\Sales\LineItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Debit extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia;
    protected $guarded =   ['id'];
    protected $hidden   = ['created_at', 'updated_at', 'deleted_at'];
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }
    public function billing():BelongsTo
    {
        return $this->belongsTo(Address::class, 'billing_id', 'id');
    }
    public function shipping():BelongsTo
    {
        return $this->belongsTo(Address::class, 'shipping_id', 'id');
    }
    public function items(): HasMany
    {
        return $this->hasMany(LineItem::class);
    }
}
