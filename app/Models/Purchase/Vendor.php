<?php

namespace App\Models\Purchase;

use App\Models\Address;
use App\Models\Business;
use App\Models\Company;
use Filament\Models\Contracts\HasName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model implements HasName
{
    use SoftDeletes;
    public $guarded = ['id'];
    protected $hidden = ['created_at', 'updated_at'];
    public function getFilamentName(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
    public function company():BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
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
    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class);
    }
}
