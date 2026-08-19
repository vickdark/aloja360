<?php

namespace Database\Seeders;

use App\Models\Amenity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AmenitySeeder extends Seeder
{
    public function run(): void
    {
        $amenities = [
            // Tecnología
            [
                'name' => 'WiFi de alta velocidad',
                'category' => 'Tecnología',
                'icon' => 'fa-solid fa-wifi',
                'description' => 'Conexión a internet inalámbrica de alta velocidad',
                'sort_order' => 1,
            ],
            [
                'name' => 'Smart TV',
                'category' => 'Tecnología',
                'icon' => 'fa-solid fa-tv',
                'description' => 'Televisor inteligente con aplicaciones de streaming',
                'sort_order' => 2,
            ],
            [
                'name' => 'Zona de trabajo',
                'category' => 'Tecnología',
                'icon' => 'fa-solid fa-laptop',
                'description' => 'Escritorio y espacio adecuado para teletrabajo',
                'sort_order' => 3,
            ],

            // Relax & Bienestar
            [
                'name' => 'Jacuzzi / Hidromasaje',
                'category' => 'Relax',
                'icon' => 'fa-solid fa-hot-tub-person',
                'description' => 'Tina de hidromasaje o jacuzzi privado',
                'sort_order' => 10,
            ],
            [
                'name' => 'Piscina',
                'category' => 'Relax',
                'icon' => 'fa-solid fa-person-swimming',
                'description' => 'Acceso a piscina privada o compartida',
                'sort_order' => 11,
            ],

            // Climatización
            [
                'name' => 'Aire acondicionado',
                'category' => 'Clima',
                'icon' => 'fa-solid fa-snowflake',
                'description' => 'Sistema de climatización frío para confort térmico',
                'sort_order' => 20,
            ],
            [
                'name' => 'Calefacción',
                'category' => 'Clima',
                'icon' => 'fa-solid fa-temperature-arrow-up',
                'description' => 'Sistema de calefacción para climas fríos',
                'sort_order' => 21,
            ],
            [
                'name' => 'Chimenea / Fogata',
                'category' => 'Clima',
                'icon' => 'fa-solid fa-fire',
                'description' => 'Chimenea de leña interior o zona de fogata exterior',
                'sort_order' => 22,
            ],

            // Cocina
            [
                'name' => 'Cocina equipada',
                'category' => 'Cocina',
                'icon' => 'fa-solid fa-kitchen-set',
                'description' => 'Cocina completa con estufa, utensilios y menaje',
                'sort_order' => 30,
            ],
            [
                'name' => 'Nevera / Minibar',
                'category' => 'Cocina',
                'icon' => 'fa-solid fa-temperature-arrow-down',
                'description' => 'Refrigerador o minibar para alimentos y bebidas',
                'sort_order' => 31,
            ],
            [
                'name' => 'Cafetera',
                'category' => 'Cocina',
                'icon' => 'fa-solid fa-mug-hot',
                'description' => 'Cafetera para preparación de café fresco',
                'sort_order' => 32,
            ],
            [
                'name' => 'Microondas',
                'category' => 'Cocina',
                'icon' => 'fa-solid fa-bowl-food',
                'description' => 'Horno microondas para calentar alimentos',
                'sort_order' => 33,
            ],

            // Exterior
            [
                'name' => 'Zona BBQ / Asador',
                'category' => 'Exterior',
                'icon' => 'fa-solid fa-utensils',
                'description' => 'Parrilla o asador para barbacoas al aire libre',
                'sort_order' => 40,
            ],
            [
                'name' => 'Terraza / Balcón',
                'category' => 'Exterior',
                'icon' => 'fa-solid fa-mountain-sun',
                'description' => 'Espacio exterior privado con mesas o sillas',
                'sort_order' => 41,
            ],
            [
                'name' => 'Jardín / Zonas verdes',
                'category' => 'Exterior',
                'icon' => 'fa-solid fa-tree',
                'description' => 'Amplias áreas verdes y senderos naturales',
                'sort_order' => 42,
            ],
            [
                'name' => 'Vista panorámica',
                'category' => 'Exterior',
                'icon' => 'fa-solid fa-mountain',
                'description' => 'Vista despejada a la montaña, valle o naturaleza',
                'sort_order' => 43,
            ],

            // Habitación & Baño
            [
                'name' => 'Ropa de cama',
                'category' => 'Habitación',
                'icon' => 'fa-solid fa-bed',
                'description' => 'Sábanas, almohadas y cobijas térmicas de calidad',
                'sort_order' => 50,
            ],
            [
                'name' => 'Toallas y aseo',
                'category' => 'Baño',
                'icon' => 'fa-solid fa-shower',
                'description' => 'Juegos de toallas y amenidades básicas de aseo',
                'sort_order' => 51,
            ],
            [
                'name' => 'Agua caliente',
                'category' => 'Baño',
                'icon' => 'fa-solid fa-droplet',
                'description' => 'Ducha con agua caliente continua',
                'sort_order' => 52,
            ],
            [
                'name' => 'Secador de cabello',
                'category' => 'Baño',
                'icon' => 'fa-solid fa-wind',
                'description' => 'Secador de pelo disponible en el baño',
                'sort_order' => 53,
            ],

            // General & Servicios
            [
                'name' => 'Estacionamiento gratuito',
                'category' => 'General',
                'icon' => 'fa-solid fa-square-parking',
                'description' => 'Parqueadero privado y vigilado dentro de las instalaciones',
                'sort_order' => 60,
            ],
            [
                'name' => 'Mascotas permitidas',
                'category' => 'General',
                'icon' => 'fa-solid fa-paw',
                'description' => 'Alojamiento Pet Friendly apto para mascotas',
                'sort_order' => 61,
            ],
            [
                'name' => 'Desayuno incluido',
                'category' => 'General',
                'icon' => 'fa-solid fa-coffee',
                'description' => 'Servicio de desayuno incluido en la tarifa',
                'sort_order' => 62,
            ],
            [
                'name' => 'Botiquín / Seguridad',
                'category' => 'Seguridad',
                'icon' => 'fa-solid fa-kit-medical',
                'description' => 'Botiquín de primeros auxilios y elementos de seguridad',
                'sort_order' => 70,
            ],
        ];

        foreach ($amenities as $item) {
            Amenity::updateOrCreate(
                ['name' => $item['name']],
                [
                    'slug' => Str::slug($item['name']),
                    'category' => $item['category'],
                    'icon' => $item['icon'],
                    'description' => $item['description'],
                    'is_default' => true,
                    'sort_order' => $item['sort_order'],
                ]
            );
        }
    }
}
