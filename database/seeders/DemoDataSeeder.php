<?php

namespace Database\Seeders;

use App\Enums\AccommodationStatus;
use App\Enums\AccommodationType;
use App\Models\Accommodation;
use App\Models\Business;
use App\Models\Amenity;
use App\Models\Guest;
use App\Models\InventoryItem;
use App\Models\Service;
use App\Models\Usuarios\Usuario;
use App\Models\Roles\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    /**
     * Run demo data seeds (for development and staging testing only).
     */
    public function run(): void
    {
        $business = Business::firstOrCreate(
            ['name' => 'Aloja360 Demo'],
            [
                'legal_name' => 'Aloja360 SAS',
                'tax_id' => '901234567-8',
                'email' => 'info@aloja360.com',
                'phone' => '+57 1 234 5678',
                'whatsapp' => '+57 300 123 4567',
                'address' => 'Calle 123 # 45-67',
                'city' => 'Medellín',
                'country' => 'CO',
                'timezone' => 'America/Bogota',
                'currency' => 'COP',
                'status' => 'active',
            ]
        );

        $adminRole = Role::where('slug', 'admin')->first();
        $receptionistRole = Role::where('slug', 'receptionist')->first() ?? $adminRole;
        $cleanerRole = Role::where('slug', 'cleaner')->first() ?? $adminRole;

        $staffEmails = [
            'recepcion@aloja360.com' => ['name' => 'Recepcionista Demo', 'role' => $receptionistRole],
            'limpieza@aloja360.com' => ['name' => 'Personal Limpieza', 'role' => $cleanerRole],
        ];

        foreach ($staffEmails as $email => $data) {
            Usuario::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $data['name'],
                    'role_id' => $data['role']?->id,
                    'password' => bcrypt('password123'),
                    'email_verified_at' => now(),
                ]
            );
        }

        $services = [
            ['name' => 'Desayuno Campestre', 'category' => 'Alimentación', 'price' => 25000, 'price_type' => 'per_person_per_night'],
            ['name' => 'Cena Gourmet', 'category' => 'Alimentación', 'price' => 45000, 'price_type' => 'per_person_per_night'],
            ['name' => 'Transporte aeropuerto', 'category' => 'Transporte', 'price' => 80000, 'price_type' => 'per_trip'],
            ['name' => 'Tour guiado ecológico', 'category' => 'Turismo', 'price' => 150000, 'price_type' => 'per_group'],
            ['name' => 'Decoración romántica', 'category' => 'Especial', 'price' => 120000, 'price_type' => 'per_stay'],
            ['name' => 'Leña para chimenea', 'category' => 'Extras', 'price' => 30000, 'price_type' => 'per_unit'],
            ['name' => 'Mascota adicional', 'category' => 'Extras', 'price' => 50000, 'price_type' => 'per_stay'],
            ['name' => 'Servicio de lavandería', 'category' => 'Servicios', 'price' => 20000, 'price_type' => 'per_load'],
        ];

        foreach ($services as $service) {
            Service::firstOrCreate(
                ['name' => $service['name']],
                [
                    ...$service,
                    'description' => 'Servicio demo de ' . strtolower($service['name']),
                    'is_taxable' => true,
                    'tax_rate' => 19,
                    'is_active' => true,
                ]
            );
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
                    'name' => $name,
                    'slug' => Str::slug($name),
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

        $allAmenities = Amenity::all();
        if ($allAmenities->isNotEmpty()) {
            Accommodation::all()->each(function ($acc) use ($allAmenities) {
                $selectedAmenities = $allAmenities->random(min(8, $allAmenities->count()));
                $syncData = [];
                foreach ($selectedAmenities as $amenity) {
                    $syncData[$amenity->id] = ['quantity' => rand(1, 3)];
                }
                $acc->amenities()->syncWithoutDetaching($syncData);
            });
        }

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

        Accommodation::all()->each(function ($acc) use ($defaultInventory) {
            foreach ($defaultInventory as [$name, $category, $qty]) {
                InventoryItem::firstOrCreate(
                    ['accommodation_id' => $acc->id, 'name' => $name],
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

        if (Guest::count() < 5) {
            Guest::factory()->count(10)->create();
        }
    }
}
