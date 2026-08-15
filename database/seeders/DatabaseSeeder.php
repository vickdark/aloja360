<?php

namespace Database\Seeders;

use App\Models\Usuarios\Usuario;
use App\Models\Roles\Role;
use App\Models\Roles\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crear permisos iniciales
        $permissions = [
            ['nombre' => 'Ver Roles', 'slug' => 'roles.index', 'descripcion' => 'Permite ver la lista de roles', 'is_menu' => true, 'module' => 'Seguridad'],
            ['nombre' => 'Crear Roles', 'slug' => 'roles.create', 'descripcion' => 'Permite crear nuevos roles', 'is_menu' => false, 'module' => 'Seguridad'],
            ['nombre' => 'Editar Roles', 'slug' => 'roles.edit', 'descripcion' => 'Permite editar roles existentes', 'is_menu' => false, 'module' => 'Seguridad'],
            ['nombre' => 'Eliminar Roles', 'slug' => 'roles.destroy', 'descripcion' => 'Permite eliminar roles', 'is_menu' => false, 'module' => 'Seguridad'],
            
            ['nombre' => 'Ver Usuarios', 'slug' => 'usuarios.index', 'descripcion' => 'Permite ver la lista de usuarios', 'is_menu' => true, 'module' => 'Usuarios'],
            ['nombre' => 'Ver Permisos', 'slug' => 'permissions.index', 'descripcion' => 'Permite ver la lista de permisos', 'is_menu' => true, 'module' => 'Seguridad'],
            ['nombre' => 'Configuración', 'slug' => 'configuracion.index', 'descripcion' => 'Configuración del sistema', 'is_menu' => true, 'module' => 'Configuración'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['slug' => $permission['slug']], $permission);
        }

        // Crear roles iniciales
        $roles = [
            ['nombre' => 'Administrador', 'slug' => 'admin', 'descripcion' => 'Acceso total al sistema'],
            ['nombre' => 'Supervisor', 'slug' => 'supervisor', 'descripcion' => 'Acceso a gestión básica'],
            ['nombre' => 'Vendedor', 'slug' => 'vendedor', 'descripcion' => 'Acceso solo a ventas'],
        ];

        foreach ($roles as $roleData) {
            $role = Role::firstOrCreate(['slug' => $roleData['slug']], $roleData);

            // Asignar algunos permisos al supervisor por defecto
            if ($role->slug === 'supervisor') {
                $pms = Permission::whereIn('slug', ['roles.index', 'roles.edit'])->pluck('id');
                $role->permissions()->sync($pms);
            }
        }

        // Obtener el rol de administrador
        $adminRole = Role::where('slug', 'admin')->first();
        
        // Asignar todos los permisos al administrador
        $adminRole->permissions()->sync(Permission::all());

        // Crear el usuario administrador único
        Usuario::firstOrCreate(
            ['email' => 'victormanjarres3mayo@gmail.com'],
            [
                'role_id'  => $adminRole->id,
                'name'     => 'Administrador',
                'password' => Hash::make('admin123456789'),
            ]
        );
    }
}
