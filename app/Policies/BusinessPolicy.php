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
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Usuario $usuario, Business $business): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Usuario $usuario): bool
    {
        return $usuario->hasRole('Super Administrador');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Usuario $usuario, Business $business): bool
    {
        return $usuario->hasRole('Super Administrador') || $usuario->hasRole('Administrador de Negocio');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Usuario $usuario, Business $business): bool
    {
        return $usuario->hasRole('Super Administrador');
    }
}
