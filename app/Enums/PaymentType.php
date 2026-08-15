<?php

namespace App\Enums;

enum PaymentType: string
{
    case Payment = 'payment';
    case Refund = 'refund';
    case Deposit = 'deposit';
    case DepositReturn = 'deposit_return';

    public function label(): string
    {
        return match ($this) {
            self::Payment => 'Pago',
            self::Refund => 'Reembolso',
            self::Deposit => 'Depósito',
            self::DepositReturn => 'Devolución depósito',
        };
    }
}
