<?php

namespace App\Policies;

use App\Models\Usuarios\Usuario;

class ReportPolicy
{
    public function viewAny(Usuario $usuario): bool
    {
        return true;
    }
}
