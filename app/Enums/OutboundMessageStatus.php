<?php

namespace App\Enums;

enum OutboundMessageStatus: string
{
    case Pending = 'pending';
    case Scheduled = 'scheduled';
    case Sent = 'sent';
    case Failed = 'failed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pendiente',
            self::Scheduled => 'Programado',
            self::Sent => 'Enviado',
            self::Failed => 'Fallido',
            self::Cancelled => 'Cancelado',
        };
    }
}
