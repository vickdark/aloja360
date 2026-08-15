<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Cash = 'cash';
    case BankTransfer = 'bank_transfer';
    case CreditCard = 'credit_card';
    case DebitCard = 'debit_card';
    case Nequi = 'nequi';
    case Daviplata = 'daviplata';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Cash => 'Efectivo',
            self::BankTransfer => 'Transferencia bancaria',
            self::CreditCard => 'Tarjeta crédito',
            self::DebitCard => 'Tarjeta débito',
            self::Nequi => 'Nequi',
            self::Daviplata => 'Daviplata',
            self::Other => 'Otro',
        };
    }
}
