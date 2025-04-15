<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use softDeletes;
    protected $guarded = ['id'];
    protected $hidden = ['created_at', 'updated_at'];
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
}
