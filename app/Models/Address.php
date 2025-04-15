<?php

namespace App\Models;

use Filament\Models\Contracts\HasName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model implements HasName
{
    use softDeletes;
    protected $guarded = ['id'];
    protected $hidden = ['created_at', 'updated_at'];
    public function getFilamentName(): string
    {
        return "{$this->street_1} {$this->street_2} {$this->zip} {$this->city} {$this->state} {$this->country}";
    }
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
}
