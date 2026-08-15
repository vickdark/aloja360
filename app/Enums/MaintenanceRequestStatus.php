<?php

namespace App\Enums;

enum MaintenanceRequestStatus: string
{
    case Reported = 'reported';
    case Scheduled = 'scheduled';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Reported => 'Reportado',
            self::Scheduled => 'Programado',
            self::InProgress => 'En progreso',
            self::Completed => 'Completado',
            self::Cancelled => 'Cancelado',
        };
    }
}
