<?php

namespace Database\Factories;

use App\Enums\DocumentType;
use App\Models\Business;
use App\Models\Guest;
use Illuminate\Database\Eloquent\Factories\Factory;

class GuestFactory extends Factory
{
    protected $model = Guest::class;

    public function definition(): array
    {
        $docTypes = DocumentType::cases();

        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'document_type' => $docTypes[array_rand($docTypes)],
            'document_number' => fake()->unique()->numerify('########'),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'whatsapp' => fake()->phoneNumber(),
            'birth_date' => fake()->dateTimeBetween('-70 years', '-18 years'),
            'country' => 'CO',
            'city' => fake()->city(),
            'address' => fake()->address(),
            'nationality' => fake()->country(),
            'occupation' => fake()->jobTitle(),
            'marketing_consent' => fake()->boolean(70),
            'total_stays' => fake()->numberBetween(0, 20),
            'total_nights' => fake()->numberBetween(0, 100),
            'lifetime_value' => fake()->numberBetween(0, 50000000) / 100,
        ];
    }
}
