<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database for production.
     * Solo inicializa los datos maestros y esenciales del sistema:
     * - Roles y Permisos sincronizados
     * - Usuario Administrador principal
     * - Catálogo maestro de Amenidades
     * - Configuraciones básicas del sistema
     * - Categorías maestras de Gastos
     */
    public function run(): void
    {
        $this->call([
            RoleAndPermissionSeeder::class,
            AdminUserSeeder::class,
            AmenitySeeder::class,
            ConfiguracionSeeder::class,
            ExpenseCategorySeeder::class,
        ]);
    }
}
