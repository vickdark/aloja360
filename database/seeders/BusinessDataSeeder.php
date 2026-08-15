<?php

namespace Database\Seeders;

use App\Enums\ExpenseCategory;
use App\Models\Business;
use App\Models\ExpenseCategory as ExpenseCategoryModel;
use App\Models\Service;
use App\Models\Amenity;
use App\Models\Usuarios\Usuario;
use App\Models\Roles\Role;
use Illuminate\Database\Seeder;

class BusinessDataSeeder extends Seeder
{
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

        $adminUser = Usuario::where('email', 'victormanjarres3mayo@gmail.com')->first();
        if ($adminUser) {
            // Usuario es admin
        }

        $amenities = [
            ['name' => 'WiFi', 'icon' => 'wifi', 'category' => 'Tecnología'],
            ['name' => 'TV', 'icon' => 'tv', 'category' => 'Entretenimiento'],
            ['name' => 'Piscina', 'icon' => 'pool', 'category' => 'Exterior'],
            ['name' => 'Cocina', 'icon' => 'kitchen', 'category' => 'Interior'],
            ['name' => 'Estacionamiento', 'icon' => 'car', 'category' => 'Exterior'],
            ['name' => 'Aire acondicionado', 'icon' => 'snowflake', 'category' => 'Clima'],
            ['name' => 'Calefacción', 'icon' => 'thermometer', 'category' => 'Clima'],
            ['name' => 'Jacuzzi', 'icon' => 'bath', 'category' => 'Relax'],
            ['name' => 'BBQ', 'icon' => 'fire', 'category' => 'Exterior'],
            ['name' => 'Mascotas permitidas', 'icon' => 'paw', 'category' => 'General'],
            ['name' => 'Desayuno incluido', 'icon' => 'coffee', 'category' => 'Alimentación'],
            ['name' => 'Ropa de cama', 'icon' => 'bed', 'category' => 'Interior'],
            ['name' => 'Toallas', 'icon' => 'shower', 'category' => 'Interior'],
            ['name' => 'Jardín', 'icon' => 'leaf', 'category' => 'Exterior'],
            ['name' => 'Terraza', 'icon' => 'sun', 'category' => 'Exterior'],
        ];

        foreach ($amenities as $amenity) {
            Amenity::firstOrCreate(
                ['name' => $amenity['name']],
                [
                    ...$amenity,
                    'is_default' => true,
                    'description' => $amenity['name'],
                ]
            );
        }

        $services = [
            ['name' => 'Desayuno', 'category' => 'Alimentación', 'price' => 25000, 'price_type' => 'per_person_per_night'],
            ['name' => 'Cena', 'category' => 'Alimentación', 'price' => 45000, 'price_type' => 'per_person_per_night'],
            ['name' => 'Transporte aeropuerto', 'category' => 'Transporte', 'price' => 80000, 'price_type' => 'per_trip'],
            ['name' => 'Tour guía', 'category' => 'Turismo', 'price' => 150000, 'price_type' => 'per_group'],
            ['name' => 'Decoración romántica', 'category' => 'Especial', 'price' => 120000, 'price_type' => 'per_stay'],
            ['name' => 'Leña para chimenea', 'category' => 'Extras', 'price' => 30000, 'price_type' => 'per_unit'],
            ['name' => 'Mascota adicional', 'category' => 'Extras', 'price' => 50000, 'price_type' => 'per_stay'],
            ['name' => 'Lavandería', 'category' => 'Servicios', 'price' => 20000, 'price_type' => 'per_load'],
        ];

        foreach ($services as $service) {
            Service::firstOrCreate(
                ['name' => $service['name']],
                [
                    ...$service,
                    'description' => 'Servicio de ' . strtolower($service['name']),
                    'is_taxable' => true,
                    'tax_rate' => 19,
                    'is_active' => true,
                ]
            );
        }

        $categories = ExpenseCategory::cases();
        foreach ($categories as $category) {
            ExpenseCategoryModel::firstOrCreate(
                ['slug' => $category->value],
                [
                    'name' => $category->label(),
                    'slug' => $category->value,
                    'description' => 'Gastos de ' . strtolower($category->label()),
                    'is_tax_deductible' => true,
                    'is_default' => true,
                ]
            );
        }
    }
}
