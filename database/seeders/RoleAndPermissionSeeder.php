<?php

namespace Database\Seeders;

use App\Console\Commands\SyncPermissions;
use App\Models\Roles\Permission;
use App\Models\Roles\Role;
use Illuminate\Database\Seeder;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Sincronizar todos los permisos basados en las rutas actuales del sistema
        SyncPermissions::runSync();

        // 2. Definir los roles canónicos del sistema
        $roles = [
            [
                'nombre' => 'Super Administrador',
                'slug' => 'admin',
                'descripcion' => 'Acceso total y configuración del sistema',
            ],
            [
                'nombre' => 'Propietario',
                'slug' => 'owner',
                'descripcion' => 'Acceso total a la administración del negocio',
            ],
            [
                'nombre' => 'Recepcionista',
                'slug' => 'receptionist',
                'descripcion' => 'Gestión de disponibilidad, cotizaciones, reservas, huéspedes y check-in/out',
            ],
            [
                'nombre' => 'Contador',
                'slug' => 'accountant',
                'descripcion' => 'Gestión de pagos, ingresos, gastos y reportes financieros',
            ],
            [
                'nombre' => 'Personal de Limpieza',
                'slug' => 'cleaner',
                'descripcion' => 'Gestión de tareas de limpieza asignadas',
            ],
            [
                'nombre' => 'Personal de Mantenimiento',
                'slug' => 'maintenance',
                'descripcion' => 'Gestión de solicitudes y tareas de mantenimiento',
            ],
            [
                'nombre' => 'Supervisor',
                'slug' => 'supervisor',
                'descripcion' => 'Supervisión y control operativo general',
            ],
            [
                'nombre' => 'Vendedor',
                'slug' => 'vendedor',
                'descripcion' => 'Gestión comercial de cotizaciones y reservas',
            ],
        ];

        $createdRoles = [];
        foreach ($roles as $roleData) {
            $createdRoles[$roleData['slug']] = Role::firstOrCreate(
                ['slug' => $roleData['slug']],
                $roleData
            );
        }

        // 3. Asignar el 100% de los permisos al rol de Super Administrador
        $allPermissions = Permission::all();
        if (isset($createdRoles['admin'])) {
            $createdRoles['admin']->permissions()->sync($allPermissions->pluck('id'));
        }

        // 4. Asignar permisos por defecto a otros roles
        if (isset($createdRoles['owner'])) {
            $ownerPerms = Permission::where(function ($query) {
                $query->where('slug', 'not like', 'roles.destroy')
                      ->where('slug', 'not like', 'permissions.%');
            })->pluck('id');
            $createdRoles['owner']->permissions()->syncWithoutDetaching($ownerPerms);
        }

        if (isset($createdRoles['receptionist'])) {
            $receptionistPerms = Permission::where(function ($query) {
                $query->whereIn('module', ['Alojamientos', 'Clientes', 'Cotizaciones', 'Reservas', 'General'])
                      ->orWhereIn('slug', [
                          'payments.index',
                          'payments.show',
                          'payments.create',
                          'payments.store',
                          'cleaning.index',
                          'maintenance.index',
                      ]);
            })->pluck('id');
            $createdRoles['receptionist']->permissions()->syncWithoutDetaching($receptionistPerms);
        }

        if (isset($createdRoles['accountant'])) {
            $accountantPerms = Permission::where(function ($query) {
                $query->whereIn('module', ['Finanzas', 'Reportes', 'General'])
                      ->orWhereIn('slug', [
                          'reservations.index',
                          'reservations.show',
                      ]);
            })->pluck('id');
            $createdRoles['accountant']->permissions()->syncWithoutDetaching($accountantPerms);
        }

        if (isset($createdRoles['cleaner'])) {
            $cleanerPerms = Permission::where(function ($query) {
                $query->where('slug', 'dashboard')
                      ->orWhere('module', 'Operación')
                      ->where('slug', 'like', 'cleaning%');
            })->pluck('id');
            $createdRoles['cleaner']->permissions()->syncWithoutDetaching($cleanerPerms);
        }

        if (isset($createdRoles['maintenance'])) {
            $maintenancePerms = Permission::where(function ($query) {
                $query->where('slug', 'dashboard')
                      ->orWhere('module', 'Operación')
                      ->where('slug', 'like', 'maintenance%');
            })->pluck('id');
            $createdRoles['maintenance']->permissions()->syncWithoutDetaching($maintenancePerms);
        }
    }
}
