<?php

namespace App\Enums;

enum CleaningTaskStatus: string
{
    case Pending = 'pending';
    case Assigned = 'assigned';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pendiente',
            self::Assigned => 'Asignada',
            self::InProgress => 'En progreso',
            self::Completed => 'Completada',
            self::Cancelled => 'Cancelada',
        };
    }
}
