<?php

namespace Database\Seeders;

use App\Models\Roles\Role;
use App\Models\Usuarios\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('slug', 'admin')->first();

        $adminEmail = env('ADMIN_EMAIL', 'victormanjarres3mayo@gmail.com');
        $adminName = env('ADMIN_NAME', 'Administrador');
        $adminPassword = env('ADMIN_PASSWORD', 'admin123456789');

        $adminUser = Usuario::firstOrNew(['email' => $adminEmail]);

        $adminUser->name = $adminName;
        $adminUser->role_id = $adminRole?->id;
        if (!$adminUser->exists) {
            $adminUser->password = Hash::make($adminPassword);
        }
        $adminUser->email_verified_at = $adminUser->email_verified_at ?? now();
        $adminUser->save();
    }
}
