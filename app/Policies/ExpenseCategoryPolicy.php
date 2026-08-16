<?php

namespace App\Policies;

use App\Models\ExpenseCategory;
use App\Models\Usuarios\Usuario;

class ExpenseCategoryPolicy
{
    public function viewAny(Usuario $usuario): bool
    {
        return true;
    }

    public function view(Usuario $usuario, ExpenseCategory $expenseCategory): bool
    {
        return true;
    }

    public function create(Usuario $usuario): bool
    {
        return true;
    }

    public function update(Usuario $usuario, ExpenseCategory $expenseCategory): bool
    {
        return true;
    }

    public function delete(Usuario $usuario, ExpenseCategory $expenseCategory): bool
    {
        return true;
    }
}
