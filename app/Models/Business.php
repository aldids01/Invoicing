<?php

namespace App\Models;

use App\Models\Purchase\Bill;
use App\Models\Purchase\Vendor;
use App\Models\Sales\Category;
use App\Models\Sales\Credit;
use App\Models\Sales\Customer;
use App\Models\Sales\Delivery;
use App\Models\Sales\Estimate;
use App\Models\Sales\Invoice;
use App\Models\Sales\Products;
use App\Models\Sales\Proforma;
use App\Models\Sales\Receipt;
use App\Models\Sales\Service;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasCurrentTenantLabel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Business extends Model implements HasAvatar, HasCurrentTenantLabel
{
    use SoftDeletes;
    protected $guarded = ['id'];
    // add hidden
    protected $hidden = ['created_at', 'updated_at'];

    public function members():BelongsToMany
    {
        return $this->belongsToMany(User::class, 'business_user', 'business_id', 'user_id');
    }
    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url ? Storage::url($this->avatar_url) : null ;
    }
    public function getCurrentTenantLabel(): string
    {
        return 'Active Business';
    }
    public function roles(): HasMany
    {
        return $this->hasMany(\Spatie\Permission\Models\Role::class);
    }
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }
    public function vendors(): HasMany
    {
        return $this->hasMany(Vendor::class);
    }
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
    public function proformas(): HasMany
    {
        return $this->hasMany(Proforma::class);
    }
    public function estimates(): HasMany
    {
        return $this->hasMany(Estimate::class);
    }
    public function receipts(): HasMany
    {
        return $this->hasMany(Receipt::class);
    }
    public function deliveries(): HasMany
    {
        return $this->hasMany(Delivery::class);
    }
    public function credits(): HasMany
    {
        return $this->hasMany(Credit::class);
    }
    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }
    public function products(): HasMany
    {
        return $this->hasMany(Products::class);
    }
    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class);
    }
    public function orders(): HasMany
    {
        return $this->hasMany(Bill::class);
    }
    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }
}
