<?php

namespace App\Models\Roles;

use App\Models\Usuarios\Usuario;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    public const ADMIN_SLUG = 'admin';

    /** Legacy alias kept only while existing data is consolidated. */
    public const ADMINISTRATOR_ALIAS = 'administrator';

    protected $fillable = [
        'nombre',
        'slug',
        'descripcion',
    ];

    /**
     * Obtener los usuarios asociados a este rol.
     */
    public function users(): HasMany
    {
        return $this->hasMany(Usuario::class, 'role_id');
    }

    /**
     * Obtener los permisos asociados a este rol.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }

    public static function canonicalSlug(string $slug): string
    {
        return $slug === self::ADMINISTRATOR_ALIAS ? self::ADMIN_SLUG : $slug;
    }

    public function setSlugAttribute(string $value): void
    {
        $this->attributes['slug'] = self::canonicalSlug($value);
    }
}
