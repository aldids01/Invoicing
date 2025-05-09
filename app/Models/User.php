<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasDefaultTenant;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Jeffgreco13\FilamentBreezy\Traits\TwoFactorAuthenticatable;
use Laravelcm\Subscriptions\Traits\HasPlanSubscriptions;
use Spatie\Permission\Traits\HasRoles;
use TomatoPHP\FilamentSubscriptions\Facades\FilamentSubscriptions;

class User extends Authenticatable implements FilamentUser, HasTenants, HasAvatar, HasDefaultTenant
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, SoftDeletes, TwoFactorAuthenticatable, HasPlanSubscriptions;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar_url',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function getDefaultTenant(Panel $panel): ?Model
    {
        return $this->latestTeam;
    }

    public function latestTeam(): BelongsTo
    {
        return $this->belongsTo(Business::class, 'latest_team_id');
    }
    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url ? Storage::url($this->avatar_url) : null ;
    }
    public function businesses(): BelongsToMany
    {
        return $this->belongsToMany(Business::class);
    }

    public function getTenants(Panel $panel): Collection
    {
        return $this->businesses;
    }

    public function canAccessTenant(Model $tenant): bool
    {
        return $this->businesses()->whereKey($tenant)->exists();
    }
    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
}
