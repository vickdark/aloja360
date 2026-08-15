<?php

namespace App\Enums;

enum BlockedPeriodType: string
{
    case OwnerUse = 'owner_use';
    case Maintenance = 'maintenance';
    case Administrative = 'administrative';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::OwnerUse => 'Uso del propietario',
            self::Maintenance => 'Mantenimiento',
            self::Administrative => 'Administrativo',
            self::Other => 'Otro',
        };
    }
}
