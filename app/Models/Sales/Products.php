<?php

namespace App\Models\Sales;

use App\Models\Business;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Products extends Model implements HasMedia
{
    use softDeletes;
    use InteractsWithMedia;
    public $guarded = ['id'];
    protected $hidden = ['created_at', 'updated_at'];
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_product', 'product_id', 'category_id')->withTimestamps();
    }
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function invoices():HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
