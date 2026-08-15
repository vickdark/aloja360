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
