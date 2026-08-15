<?php

namespace App\Policies;

use App\Models\Guest;
use App\Models\Usuarios\Usuario;

class GuestPolicy
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
    public function view(Usuario $usuario, Guest $guest): bool
    {
        return $usuario->belongsToBusiness($guest->business_id);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Usuario $usuario): bool
    {
        return true; // Business scope check via FormRequest
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Usuario $usuario, Guest $guest): bool
    {
        return $usuario->belongsToBusiness($guest->business_id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Usuario $usuario, Guest $guest): bool
    {
        return $usuario->belongsToBusiness($guest->business_id);
    }
}
