<?php

namespace App\Models\Usuarios;

use App\Models\AuditLog;
use App\Models\Business;
use App\Models\Roles\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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
        'name',
        'email',
        'password',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    /**
     * Check if the user has a specific role (by name or slug).
     */
    public function hasRole(string|array $roles): bool
    {
        $roles = (array) $roles;
        $canonicalRoles = array_map([Role::class, 'canonicalSlug'], $roles);

        return collect([$this->role, $this->roleForCurrentBusiness()])
            ->filter()
            ->contains(fn (Role $role) => in_array($role->nombre, $roles, true)
                || in_array(Role::canonicalSlug($role->slug), $canonicalRoles, true));
    }

    public function hasPermission(string $slug): bool
    {
        if ($this->isSystemAdministrator()) {
            return true;
        }

        return $this->roleForCurrentBusiness()?->permissions()->where('slug', $slug)->exists()
            ?? $this->role?->permissions()->where('slug', $slug)->exists()
            ?? false;
    }

    public function hasBusinessPermission(Business $business, string $slug): bool
    {
        $role = $this->roleForBusiness($business);

        if (! $role) {
            return false;
        }

        return $this->isSystemAdministrator() || $role->permissions()->where('slug', $slug)->exists();
    }

    public function roleForCurrentBusiness(): ?Role
    {
        return $this->current_business_id
            ? $this->roleForBusinessId((int) $this->current_business_id)
            : null;
    }

    public function roleForBusiness(Business $business): ?Role
    {
        return $this->roleForBusinessId($business->id);
    }

    public function isSystemAdministrator(): bool
    {
        return $this->role
            && Role::canonicalSlug($this->role->slug) === Role::ADMIN_SLUG;
    }

    private function roleForBusinessId(int $businessId): ?Role
    {
        $business = $this->businesses()->whereKey($businessId)->first();

        return $business?->pivot?->role_id
            ? Role::find($business->pivot->role_id)
            : null;
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
