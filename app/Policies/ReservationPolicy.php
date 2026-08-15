<?php

namespace App\Policies;

use App\Models\Reservation;
use App\Models\Usuarios\Usuario;

class ReservationPolicy
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
    public function view(Usuario $usuario, Reservation $reservation): bool
    {
        return $usuario->belongsToBusiness($reservation->business_id);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Usuario $usuario): bool
    {
        return true; // Validated in FormRequest (business_id)
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Usuario $usuario, Reservation $reservation): bool
    {
        return $usuario->belongsToBusiness($reservation->business_id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Usuario $usuario, Reservation $reservation): bool
    {
        return $usuario->belongsToBusiness($reservation->business_id);
    }
}
