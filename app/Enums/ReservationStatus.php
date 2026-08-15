<?php

namespace App\Enums;

enum ReservationStatus: string
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case CheckedIn = 'checked_in';
    case CheckedOut = 'checked_out';
    case Cancelled = 'cancelled';
    case NoShow = 'no_show';

    public function blocksAvailability(): bool
    {
        return in_array($this, [
            self::Pending,
            self::Confirmed,
            self::CheckedIn,
        ]);
    }

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pendiente',
            self::Confirmed => 'Confirmada',
            self::CheckedIn => 'Check-in',
            self::CheckedOut => 'Check-out',
            self::Cancelled => 'Cancelada',
            self::NoShow => 'No asistió',
        };
    }
}
