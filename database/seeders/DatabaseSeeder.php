<?php

namespace Database\Seeders;

use App\Models\Usuarios\Usuario;
use App\Models\Roles\Role;
use App\Models\Roles\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $basePermissions = [
            ['nombre' => 'Ver Roles', 'slug' => 'roles.index', 'descripcion' => 'Permite ver la lista de roles', 'is_menu' => true, 'module' => 'Seguridad'],
            ['nombre' => 'Crear Roles', 'slug' => 'roles.create', 'descripcion' => 'Permite crear nuevos roles', 'is_menu' => false, 'module' => 'Seguridad'],
            ['nombre' => 'Editar Roles', 'slug' => 'roles.edit', 'descripcion' => 'Permite editar roles existentes', 'is_menu' => false, 'module' => 'Seguridad'],
            ['nombre' => 'Eliminar Roles', 'slug' => 'roles.destroy', 'descripcion' => 'Permite eliminar roles', 'is_menu' => false, 'module' => 'Seguridad'],

            ['nombre' => 'Ver Usuarios', 'slug' => 'usuarios.index', 'descripcion' => 'Permite ver la lista de usuarios', 'is_menu' => true, 'module' => 'Usuarios'],
            ['nombre' => 'Ver Permisos', 'slug' => 'permissions.index', 'descripcion' => 'Permite ver la lista de permisos', 'is_menu' => true, 'module' => 'Seguridad'],
            ['nombre' => 'Configuración', 'slug' => 'configuracion.index', 'descripcion' => 'Configuración del sistema', 'is_menu' => true, 'module' => 'Configuración'],
            ['nombre' => 'Ver Negocios', 'slug' => 'businesses.index', 'descripcion' => 'Lista de negocios', 'is_menu' => true, 'module' => 'Configuración'],
            ['nombre' => 'Crear Negocios', 'slug' => 'businesses.create', 'descripcion' => 'Crear un nuevo negocio', 'is_menu' => false, 'module' => 'Configuración'],

            ['nombre' => 'Dashboard', 'slug' => 'dashboard', 'descripcion' => 'Acceso al panel principal', 'is_menu' => true, 'module' => 'General'],

            ['nombre' => 'Ver Alojamientos', 'slug' => 'accommodations.index', 'descripcion' => 'Lista de alojamientos', 'is_menu' => true, 'module' => 'Alojamientos'],
            ['nombre' => 'Gestionar Alojamientos', 'slug' => 'accommodations.manage', 'descripcion' => 'Crear/editar alojamientos', 'is_menu' => false, 'module' => 'Alojamientos'],

            ['nombre' => 'Ver Huéspedes', 'slug' => 'guests.index', 'descripcion' => 'Lista de huéspedes', 'is_menu' => true, 'module' => 'Clientes'],
            ['nombre' => 'Gestionar Huéspedes', 'slug' => 'guests.manage', 'descripcion' => 'Crear/editar huéspedes', 'is_menu' => false, 'module' => 'Clientes'],

            ['nombre' => 'Ver Cotizaciones', 'slug' => 'quotes.index', 'descripcion' => 'Lista de cotizaciones', 'is_menu' => true, 'module' => 'Reservas'],
            ['nombre' => 'Gestionar Cotizaciones', 'slug' => 'quotes.manage', 'descripcion' => 'Crear/editar cotizaciones', 'is_menu' => false, 'module' => 'Reservas'],

            ['nombre' => 'Ver Reservas', 'slug' => 'reservations.index', 'descripcion' => 'Lista de reservas', 'is_menu' => true, 'module' => 'Reservas'],
            ['nombre' => 'Gestionar Reservas', 'slug' => 'reservations.manage', 'descripcion' => 'Crear/editar reservas', 'is_menu' => false, 'module' => 'Reservas'],
            ['nombre' => 'Check-in', 'slug' => 'reservations.checkin', 'descripcion' => 'Realizar check-in', 'is_menu' => false, 'module' => 'Reservas'],
            ['nombre' => 'Check-out', 'slug' => 'reservations.checkout', 'descripcion' => 'Realizar check-out', 'is_menu' => false, 'module' => 'Reservas'],

            ['nombre' => 'Ver Pagos', 'slug' => 'payments.index', 'descripcion' => 'Lista de pagos', 'is_menu' => true, 'module' => 'Finanzas'],
            ['nombre' => 'Gestionar Pagos', 'slug' => 'payments.manage', 'descripcion' => 'Registrar/editar pagos', 'is_menu' => false, 'module' => 'Finanzas'],

            ['nombre' => 'Ver Gastos', 'slug' => 'expenses.index', 'descripcion' => 'Lista de gastos', 'is_menu' => true, 'module' => 'Finanzas'],
            ['nombre' => 'Gestionar Gastos', 'slug' => 'expenses.manage', 'descripcion' => 'Registrar/editar gastos', 'is_menu' => false, 'module' => 'Finanzas'],

            ['nombre' => 'Ver Limpieza', 'slug' => 'cleaning.index', 'descripcion' => 'Tareas de limpieza', 'is_menu' => true, 'module' => 'Operación'],
            ['nombre' => 'Gestionar Limpieza', 'slug' => 'cleaning.manage', 'descripcion' => 'Asignar/completar limpieza', 'is_menu' => false, 'module' => 'Operación'],

            ['nombre' => 'Ver Mantenimiento', 'slug' => 'maintenance.index', 'descripcion' => 'Mantenimientos', 'is_menu' => true, 'module' => 'Operación'],
            ['nombre' => 'Gestionar Mantenimiento', 'slug' => 'maintenance.manage', 'descripcion' => 'Crear/editar mantenimiento', 'is_menu' => false, 'module' => 'Operación'],

            ['nombre' => 'Reportes', 'slug' => 'reports.index', 'descripcion' => 'Ver reportes', 'is_menu' => true, 'module' => 'Reportes'],
        ];

        foreach ($basePermissions as $permission) {
            Permission::firstOrCreate(['slug' => $permission['slug']], $permission);
        }

        $businessRoles = [
            ['nombre' => 'Propietario', 'slug' => 'owner', 'descripcion' => 'Acceso total al negocio'],
            ['nombre' => 'Administrador de Negocio', 'slug' => 'administrator', 'descripcion' => 'Gestiona alojamientos, reservas, clientes, pagos'],
            ['nombre' => 'Recepcionista', 'slug' => 'receptionist', 'descripcion' => 'Gestiona disponibilidad, cotizaciones, reservas, check-in/out'],
            ['nombre' => 'Contador', 'slug' => 'accountant', 'descripcion' => 'Gestiona pagos, ingresos, gastos, reportes financieros'],
            ['nombre' => 'Personal de Limpieza', 'slug' => 'cleaner', 'descripcion' => 'Solo tareas de limpieza asignadas'],
            ['nombre' => 'Personal de Mantenimiento', 'slug' => 'maintenance', 'descripcion' => 'Solo mantenimiento asignado'],
        ];

        foreach ($businessRoles as $roleData) {
            Role::firstOrCreate(['slug' => $roleData['slug']], $roleData);
        }

        $legacyRoles = [
            ['nombre' => 'Super Administrador', 'slug' => 'admin', 'descripcion' => 'Acceso total al sistema (multi-negocio)'],
            ['nombre' => 'Supervisor', 'slug' => 'supervisor', 'descripcion' => 'Acceso a gestión básica'],
            ['nombre' => 'Vendedor', 'slug' => 'vendedor', 'descripcion' => 'Acceso solo a ventas'],
        ];

        foreach ($legacyRoles as $roleData) {
            Role::firstOrCreate(['slug' => $roleData['slug']], $roleData);
        }

        $adminRole = Role::where('slug', 'admin')->first();
        if ($adminRole) {
            $adminRole->permissions()->sync(Permission::all());
        }

        $ownerRole = Role::where('slug', 'owner')->first();
        if ($ownerRole) {
            $ownerPerms = Permission::whereIn('slug', [
                'dashboard',
                'accommodations.index', 'accommodations.manage',
                'guests.index', 'guests.manage',
                'quotes.index', 'quotes.manage',
                'reservations.index', 'reservations.manage',
                'reservations.checkin', 'reservations.checkout',
                'payments.index', 'payments.manage',
                'expenses.index', 'expenses.manage',
                'cleaning.index', 'cleaning.manage',
                'maintenance.index', 'maintenance.manage',
                'reports.index',
            ])->pluck('id');
            $ownerRole->permissions()->syncWithoutDetaching($ownerPerms);
        }

        Usuario::firstOrCreate(
            ['email' => 'victormanjarres3mayo@gmail.com'],
            [
                'role_id'  => $adminRole?->id,
                'name'     => 'Administrador',
                'password' => Hash::make('admin123456789'),
                'email_verified_at' => now(),
            ]
        );

        $this->call([
            BusinessDataSeeder::class,
            DemoDataSeeder::class,
        ]);

        \Illuminate\Support\Facades\Artisan::call('permissions:sync');
    }
}
