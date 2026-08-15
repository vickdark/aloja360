<?php

namespace App\Enums;

enum AccommodationType: string
{
    case Cabin = 'cabin';
    case Glamping = 'glamping';
    case Apartment = 'apartment';
    case House = 'house';
    case Villa = 'villa';
    case Room = 'room';
    case Farm = 'farm';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Cabin => 'Cabaña',
            self::Glamping => 'Glamping',
            self::Apartment => 'Apartamento',
            self::House => 'Casa',
            self::Villa => 'Villa',
            self::Room => 'Habitación',
            self::Farm => 'Finca',
            self::Other => 'Otro',
        };
    }
}
