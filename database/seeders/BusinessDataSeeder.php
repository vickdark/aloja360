<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class BusinessDataSeeder extends Seeder
{
    /**
     * Legacy wrapper que delega a los seeders modulares correspondientes.
     */
    public function run(): void
    {
        $this->call([
            AmenitySeeder::class,
            ExpenseCategorySeeder::class,
        ]);
    }
}
