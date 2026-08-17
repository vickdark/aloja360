<?php

namespace App\Enums;

enum PricingType: string
{
    case PerAccommodation = 'per_accommodation';
    case PerPerson = 'per_person';

    public function label(): string
    {
        return match ($this) {
            self::PerAccommodation => 'Por alojamiento',
            self::PerPerson        => 'Por persona',
        };
    }

    public function shortLabel(): string
    {
        return match ($this) {
            self::PerAccommodation => 'Alojamiento',
            self::PerPerson        => 'Persona',
        };
    }
}
