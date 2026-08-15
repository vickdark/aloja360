<?php

namespace Database\Seeders;

use App\Enums\AccommodationStatus;
use App\Enums\AccommodationType;
use App\Models\Accommodation;
use App\Models\Business;
use App\Models\Amenity;
use App\Models\Guest;
use App\Models\InventoryItem;
use App\Models\Usuarios\Usuario;
use App\Models\Roles\Role;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $business = Business::first();
        if (!$business) {
            return;
        }

        $adminRole = Role::where('slug', 'admin')->first();
        $receptionistRole = Role::where('slug', 'receptionist')->first() ?? $adminRole;

        $staffEmails = [
            'recepcion@aloja360.com' => ['name' => 'Recepcionista Demo', 'roleSlug' => 'receptionist'],
            'limpieza@aloja360.com' => ['name' => 'Personal Limpieza', 'roleSlug' => 'cleaner'],
        ];

        foreach ($staffEmails as $email => $data) {
            $role = Role::where('slug', $data['roleSlug'])->first() ?? $adminRole;
            $user = Usuario::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $data['name'],
                    'role_id' => $role?->id,
                    'password' => bcrypt('password123'),
                    'current_business_id' => $business->id,
                    'email_verified_at' => now(),
                ]
            );
            $business->users()->syncWithoutDetaching([
                $user->id => ['role_id' => $role?->id],
            ]);
        }

        $cabinTypes = [
            [AccommodationType::Cabin, 'CAB-001', 'Cabaña La Esperanza', 4, 200000],
            [AccommodationType::Cabin, 'CAB-002', 'Cabaña El Mirador', 6, 300000],
            [AccommodationType::Cabin, 'CAB-003', 'Cabaña Los Pinos', 8, 400000],
            [AccommodationType::Glamping, 'GLA-001', 'Glamping Estrellas', 2, 250000],
            [AccommodationType::Glamping, 'GLA-002', 'Glamping Luna de Miel', 2, 350000],
            [AccommodationType::Apartment, 'APT-001', 'Apartamento Centro Histórico', 4, 180000],
            [AccommodationType::House, 'CAS-001', 'Casa Finca El Paraíso', 12, 800000],
            [AccommodationType::Villa, 'VIL-001', 'Villa Luxury Campestre', 10, 1200000],
        ];

        foreach ($cabinTypes as [$type, $code, $name, $maxGuests, $price]) {
            Accommodation::firstOrCreate(
                ['code' => $code],
                [
                    'business_id' => $business->id,
                    'name' => $name,
                    'slug' => \Str::slug($name),
                    'type' => $type,
                    'status' => AccommodationStatus::Available,
                    'description' => "Hermoso alojamiento tipo {$type->label()} con capacidad para {$maxGuests} personas. Perfecto para disfrutar de la naturaleza y la tranquilidad.",
                    'max_guests' => $maxGuests,
                    'min_nights' => 1,
                    'bedrooms' => ceil($maxGuests / 3),
                    'beds' => ceil($maxGuests / 2),
                    'bathrooms' => max(1, floor($maxGuests / 4)),
                    'base_price' => $price,
                    'cleaning_fee' => round($price * 0.2, -4),
                    'security_deposit' => round($price * 1.5, -5),
                    'check_in_time' => '15:00',
                    'check_out_time' => '11:00',
                ]
            );
        }

        $allAmenities = Amenity::where('business_id', $business->id)->get();
        Accommodation::where('business_id', $business->id)->each(function ($acc) use ($allAmenities) {
            $selectedAmenities = $allAmenities->random(min(8, $allAmenities->count()));
            $syncData = [];
            foreach ($selectedAmenities as $amenity) {
                $syncData[$amenity->id] = ['quantity' => rand(1, 3)];
            }
            $acc->amenities()->syncWithoutDetaching($syncData);
        });

        $defaultInventory = [
            ['Toallas', 'Baño', 6],
            ['Almohadas', 'Dormitorio', 4],
            ['Sábanas', 'Dormitorio', 3],
            ['Vasos', 'Cocina', 6],
            ['Platos', 'Cocina', 6],
            ['Cubiertos set', 'Cocina', 6],
            ['Control remoto TV', 'Sala', 1],
            ['Llaves habitación', 'General', 2],
            ['Cafetera', 'Cocina', 1],
            ['Nevera', 'Cocina', 1],
        ];

        Accommodation::where('business_id', $business->id)->each(function ($acc) use ($defaultInventory, $business) {
            foreach ($defaultInventory as [$name, $category, $qty]) {
                InventoryItem::firstOrCreate(
                    ['business_id' => $business->id, 'accommodation_id' => $acc->id, 'name' => $name],
                    [
                        'category' => $category,
                        'expected_quantity' => $qty,
                        'current_quantity' => $qty,
                        'unit' => 'unit',
                        'unit_value' => match($category) {
                            'Baño' => 15000,
                            'Dormitorio' => 45000,
                            'Cocina' => 8000,
                            'Sala' => 50000,
                            default => 20000,
                        },
                        'condition' => 'good',
                        'is_consumable' => false,
                    ]
                );
            }
        });

        if (Guest::where('business_id', $business->id)->count() < 5) {
            Guest::factory()->count(10)->create(['business_id' => $business->id]);
        }
    }
}
