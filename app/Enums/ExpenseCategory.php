<?php

namespace App\Enums;

enum ExpenseCategory: string
{
    case Utilities = 'utilities';
    case Maintenance = 'maintenance';
    case Payroll = 'payroll';
    case Cleaning = 'cleaning';
    case Supplies = 'supplies';
    case Advertising = 'advertising';
    case Transport = 'transport';
    case Taxes = 'taxes';
    case Commissions = 'commissions';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Utilities => 'Servicios públicos',
            self::Maintenance => 'Mantenimiento',
            self::Payroll => 'Nómina',
            self::Cleaning => 'Limpieza',
            self::Supplies => 'Suministros',
            self::Advertising => 'Publicidad',
            self::Transport => 'Transporte',
            self::Taxes => 'Impuestos',
            self::Commissions => 'Comisiones',
            self::Other => 'Otro',
        };
    }
}
