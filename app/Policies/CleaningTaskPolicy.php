<?php

namespace App\Policies;

use App\Models\CleaningTask;
use App\Models\Usuarios\Usuario;

class CleaningTaskPolicy
{
    public function viewAny(Usuario $usuario): bool
    {
        return true;
    }

    public function view(Usuario $usuario, CleaningTask $cleaningTask): bool
    {
        return true;
    }

    public function create(Usuario $usuario): bool
    {
        return true;
    }

    public function update(Usuario $usuario, CleaningTask $cleaningTask): bool
    {
        return true;
    }

    public function delete(Usuario $usuario, CleaningTask $cleaningTask): bool
    {
        return true;
    }
}
