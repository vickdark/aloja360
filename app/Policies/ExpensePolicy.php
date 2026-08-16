<?php

namespace App\Policies;

use App\Models\Expense;
use App\Models\Usuarios\Usuario;

class ExpensePolicy
{
    public function viewAny(Usuario $usuario): bool
    {
        return true;
    }

    public function view(Usuario $usuario, Expense $expense): bool
    {
        return true;
    }

    public function create(Usuario $usuario): bool
    {
        return true;
    }

    public function update(Usuario $usuario, Expense $expense): bool
    {
        return true;
    }

    public function delete(Usuario $usuario, Expense $expense): bool
    {
        return true;
    }
}
