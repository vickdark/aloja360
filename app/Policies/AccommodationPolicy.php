<?php

namespace App\Policies;

use App\Models\Accommodation;
use App\Models\Usuarios\Usuario;

class AccommodationPolicy
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
    public function view(Usuario $usuario, Accommodation $accommodation): bool
    {
        return $usuario->belongsToBusiness($accommodation->business_id);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Usuario $usuario): bool
    {
        return true; // FormRequest will check if user belongs to the requested business_id
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Usuario $usuario, Accommodation $accommodation): bool
    {
        return $usuario->belongsToBusiness($accommodation->business_id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Usuario $usuario, Accommodation $accommodation): bool
    {
        return $usuario->belongsToBusiness($accommodation->business_id);
    }
}
