<?php

namespace App\Enums;

enum OutboundMessageChannel: string
{
    case WhatsApp = 'whatsapp';
    case Email = 'email';
    case Sms = 'sms';

    public function label(): string
    {
        return match ($this) {
            self::WhatsApp => 'WhatsApp',
            self::Email => 'Correo electrónico',
            self::Sms => 'SMS',
        };
    }
}
