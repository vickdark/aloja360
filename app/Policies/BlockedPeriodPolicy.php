<?php

namespace App\Policies;

use App\Models\BlockedPeriod;
use App\Models\Usuarios\Usuario;

class BlockedPeriodPolicy
{
    public function viewAny(Usuario $usuario): bool
    {
        return true;
    }

    public function view(Usuario $usuario, BlockedPeriod $blockedPeriod): bool
    {
        return true;
    }

    public function create(Usuario $usuario): bool
    {
        return true;
    }

    public function update(Usuario $usuario, BlockedPeriod $blockedPeriod): bool
    {
        return true;
    }

    public function delete(Usuario $usuario, BlockedPeriod $blockedPeriod): bool
    {
        return true;
    }
}
