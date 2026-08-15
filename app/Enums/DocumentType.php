<?php

namespace App\Enums;

enum DocumentType: string
{
    case CC = 'cc';
    case CE = 'ce';
    case TI = 'ti';
    case Passport = 'passport';
    case NIT = 'nit';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::CC => 'Cédula ciudadanía',
            self::CE => 'Cédula extranjería',
            self::TI => 'Tarjeta identidad',
            self::Passport => 'Pasaporte',
            self::NIT => 'NIT',
            self::Other => 'Otro',
        };
    }
}
