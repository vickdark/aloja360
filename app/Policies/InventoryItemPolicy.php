<?php

namespace App\Policies;

use App\Models\InventoryItem;
use App\Models\Usuarios\Usuario;

class InventoryItemPolicy
{
    public function viewAny(Usuario $usuario): bool
    {
        return true;
    }

    public function view(Usuario $usuario, InventoryItem $inventoryItem): bool
    {
        return true;
    }

    public function create(Usuario $usuario): bool
    {
        return true;
    }

    public function update(Usuario $usuario, InventoryItem $inventoryItem): bool
    {
        return true;
    }

    public function delete(Usuario $usuario, InventoryItem $inventoryItem): bool
    {
        return true;
    }
}
