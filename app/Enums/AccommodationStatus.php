<?php

namespace App\Enums;

enum AccommodationStatus: string
{
    case Available = 'available';
    case Reserved = 'reserved';
    case Occupied = 'occupied';
    case PendingCleaning = 'pending_cleaning';
    case Cleaning = 'cleaning';
    case Maintenance = 'maintenance';
    case Blocked = 'blocked';

    public function label(): string
    {
        return match ($this) {
            self::Available => 'Disponible',
            self::Reserved => 'Reservado',
            self::Occupied => 'Ocupado',
            self::PendingCleaning => 'Limpieza pendiente',
            self::Cleaning => 'En limpieza',
            self::Maintenance => 'Mantenimiento',
            self::Blocked => 'Bloqueado',
        };
    }
}
