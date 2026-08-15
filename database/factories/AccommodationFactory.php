<?php

namespace Database\Factories;

use App\Enums\AccommodationStatus;
use App\Enums\AccommodationType;
use App\Models\Accommodation;
use App\Models\Business;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccommodationFactory extends Factory
{
    protected $model = Accommodation::class;

    public function definition(): array
    {
        $types = AccommodationType::cases();
        $type = $types[array_rand($types)];

        $namesByType = [
            'cabin' => ['Cabaña del Bosque', 'Cabaña La Esperanza', 'Cabaña El Mirador', 'Cabaña Los Pinos'],
            'glamping' => ['Glamping Estrellas', 'Glamping Luna', 'Glamping Sol Naciente'],
            'apartment' => ['Apartamento Centro', 'Apartamento Vista', 'Apto Lujoso 501'],
            'house' => ['Casa Grande', 'Casa Finca El Paraíso', 'Casa Vacacional'],
            'villa' => ['Villa Luxury', 'Villa del Sol', 'Villa Campestre'],
            'room' => ['Habitación Premium', 'Habitación Suite', 'Habitación Estándar'],
            'farm' => ['Finca La Hacienda', 'Finca El Refugio'],
            'other' => ['Alojamiento Especial'],
        ];

        $nameList = $namesByType[$type->value] ?? $namesByType['cabin'];

        return [
            'code' => strtoupper(substr($type->value, 0, 3)) . '-' . fake()->unique()->numerify('###'),
            'name' => $nameList[array_rand($nameList)],
            'slug' => fake()->slug(),
            'type' => $type,
            'status' => AccommodationStatus::Available,
            'description' => fake()->paragraphs(3, true),
            'max_guests' => fake()->numberBetween(2, 12),
            'min_nights' => fake()->numberBetween(1, 3),
            'bedrooms' => fake()->numberBetween(1, 5),
            'beds' => fake()->numberBetween(1, 8),
            'bathrooms' => fake()->numberBetween(1, 4),
            'base_price' => fake()->randomElement([150000, 200000, 250000, 300000, 450000, 600000, 800000]),
            'cleaning_fee' => fake()->randomElement([50000, 80000, 100000, 120000]),
            'security_deposit' => fake()->randomElement([200000, 300000, 500000]),
            'check_in_time' => '15:00',
            'check_out_time' => '11:00',
            'house_rules' => "Normas de la casa:\n- No fumar dentro\n- No fiestas\n- Horario de silencio después de 10pm",
            'address' => fake()->address(),
            'sort_order' => fake()->numberBetween(0, 50),
        ];
    }
}
