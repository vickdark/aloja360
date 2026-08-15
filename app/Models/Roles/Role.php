<?php

namespace App\Models\Roles;

use App\Models\Usuarios\Usuario;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasFactory;

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
}
