<?php

namespace App\Policies;

use App\Models\RatePeriod;
use App\Models\Usuarios\Usuario;

class RatePeriodPolicy
{
    public function viewAny(Usuario $usuario): bool
    {
        return true;
    }

    public function view(Usuario $usuario, RatePeriod $ratePeriod): bool
    {
        return true;
    }

    public function create(Usuario $usuario): bool
    {
        return true;
    }

    public function update(Usuario $usuario, RatePeriod $ratePeriod): bool
    {
        return true;
    }

    public function delete(Usuario $usuario, RatePeriod $ratePeriod): bool
    {
        return true;
    }
}
