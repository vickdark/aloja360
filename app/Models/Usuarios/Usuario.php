<?php

namespace App\Models\Usuarios;

use App\Models\AuditLog;
use App\Models\Business;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Roles\Role;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Usuario extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\Auth\ResetPassword($token));
    }

    protected $table = 'users';

    protected $fillable = [
        'role_id',
        'current_business_id',
        'name',
        'email',
        'password',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function currentBusiness(): BelongsTo
    {
        return $this->belongsTo(Business::class, 'current_business_id');
    }

    public function businesses(): BelongsToMany
    {
        return $this->belongsToMany(Business::class, 'business_user', 'user_id', 'business_id')
            ->withPivot('role_id')
            ->withTimestamps();
    }

    /**
     * Check if the user belongs to a specific business.
     */
    public function belongsToBusiness(int $businessId): bool
    {
        return $this->businesses()->where('business_id', $businessId)->exists();
    }

    /**
     * Check if the user has a specific role (by name or slug).
     */
    public function hasRole(string|array $roles): bool
    {
        if (!$this->role) {
            return false;
        }

        if (is_array($roles)) {
            return in_array($this->role->nombre, $roles) || in_array($this->role->slug, $roles);
        }

        return $this->role->nombre === $roles || $this->role->slug === $roles;
    }

    public function hasPermission(string $slug): bool
    {
        if (!$this->role) {
            return false;
        }

        return $this->role->permissions()->where('slug', $slug)->exists();
    }

    public function hasBusinessPermission(Business $business, string $slug): bool
    {
        $pivot = $this->businesses()->where('business_id', $business->id)->first();

        if (!$pivot || !$pivot->pivot?->role_id) {
            return false;
        }

        $role = Role::find($pivot->pivot->role_id);

        if (!$role) {
            return false;
        }

        return $role->permissions()->where('slug', $slug)->exists();
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class, 'user_id');
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
