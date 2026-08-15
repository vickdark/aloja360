<?php

namespace App\Policies;

use App\Models\Business;
use App\Models\Usuarios\Usuario;

class BusinessPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Usuario $usuario): bool
    {
        return $usuario->businesses()->exists();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Usuario $usuario, Business $business): bool
    {
        return $usuario->belongsToBusiness($business->id);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Usuario $usuario): bool
    {
        // En un SaaS, solo superadmins o roles específicos pueden crear negocios.
        return $usuario->hasRole('Super Administrador');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Usuario $usuario, Business $business): bool
    {
        // Solo usuarios que pertenecen al negocio y tienen permisos pueden editar.
        // Aquí simplificaremos a pertenecer al negocio y tener un rol de Admin de negocio,
        // o si tienes el permiso específico (asumiendo que uses un trait de permisos por negocio luego).
        return $usuario->belongsToBusiness($business->id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Usuario $usuario, Business $business): bool
    {
        return $usuario->hasRole('Super Administrador');
    }
}
