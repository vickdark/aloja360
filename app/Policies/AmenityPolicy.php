<?php

namespace App\Policies;

use App\Models\Amenity;
use App\Models\Usuarios\Usuario;

class AmenityPolicy
{
    public function viewAny(Usuario $usuario): bool
    {
        return true;
    }

    public function view(Usuario $usuario, Amenity $amenity): bool
    {
        return true;
    }

    public function create(Usuario $usuario): bool
    {
        return true;
    }

    public function update(Usuario $usuario, Amenity $amenity): bool
    {
        return true;
    }

    public function delete(Usuario $usuario, Amenity $amenity): bool
    {
        return true;
    }
}
