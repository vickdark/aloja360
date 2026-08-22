<?php

namespace App\Policies;

use App\Models\Commission;
use App\Models\Usuarios\Usuario;

class CommissionPolicy
{
    public function viewAny(Usuario $usuario): bool
    {
        return true;
    }

    public function view(Usuario $usuario, Commission $commission): bool
    {
        return true;
    }

    public function create(Usuario $usuario): bool
    {
        return true;
    }

    public function update(Usuario $usuario, Commission $commission): bool
    {
        return true;
    }

    public function delete(Usuario $usuario, Commission $commission): bool
    {
        return true;
    }
}
