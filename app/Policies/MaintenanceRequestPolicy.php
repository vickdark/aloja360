<?php

namespace App\Policies;

use App\Models\MaintenanceRequest;
use App\Models\Usuarios\Usuario;

class MaintenanceRequestPolicy
{
    public function viewAny(Usuario $usuario): bool
    {
        return true;
    }

    public function view(Usuario $usuario, MaintenanceRequest $maintenanceRequest): bool
    {
        return true;
    }

    public function create(Usuario $usuario): bool
    {
        return true;
    }

    public function update(Usuario $usuario, MaintenanceRequest $maintenanceRequest): bool
    {
        return true;
    }

    public function delete(Usuario $usuario, MaintenanceRequest $maintenanceRequest): bool
    {
        return true;
    }
}
