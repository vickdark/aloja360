<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\Usuarios\Usuario;

class PaymentPolicy
{
    public function viewAny(Usuario $usuario): bool
    {
        return true;
    }

    public function view(Usuario $usuario, Payment $payment): bool
    {
        return true;
    }

    public function create(Usuario $usuario): bool
    {
        return true;
    }

    public function update(Usuario $usuario, Payment $payment): bool
    {
        return true;
    }

    public function delete(Usuario $usuario, Payment $payment): bool
    {
        return true;
    }
}
