<?php

namespace App\Console\Commands;

use App\Models\Roles\Permission;
use App\Models\Roles\Role;
use Illuminate\Console\Command;

class SyncRolesAndPermissions extends Command
{
    protected $signature = 'roles:sync-full {--role=admin : Slug del rol al que asignar TODOS los permisos}';
    protected $description = 'Sincroniza permisos con rutas y asegura que el rol especificado (por defecto admin) tenga TODOS los permisos asignados';

    public function handle()
    {
        $slug = $this->option('role');
        $this->info("=== Sincronización Completa de Roles y Permisos ===");
        $this->line("");

        $this->info("1) Ejecutando permissions:sync...");
        \Illuminate\Support\Facades\Artisan::call('permissions:sync');
        $syncOutput = \Illuminate\Support\Facades\Artisan::output();
        $this->line(trim($syncOutput));
        $this->line("");

        $this->info("2) Asegurando existencia del rol con slug: {$slug}");

        $role = Role::where('slug', $slug)->first();
        if (!$role) {
            $role = Role::create([
                'nombre' => ucfirst($slug),
                'slug' => $slug,
                'descripcion' => 'Rol con acceso total al sistema (generado automáticamente)',
            ]);
            $this->line(" <info>✔</info> Rol {$role->nombre} creado correctamente.");
        } else {
            $this->line(" <info>✔</info> Rol {$role->nombre} ya existe.");
        }

        $permissions = Permission::all();
        $total = $permissions->count();

        if ($total === 0) {
            $this->warn("No hay permisos en la base de datos.");
            return 1;
        }

        $this->line("");
        $this->info("3) Asignando los {$total} permisos al rol: {$role->nombre}");

        $existingIds = $role->permissions()->pluck('permission_id')->all();
        $allIds = $permissions->pluck('id')->all();
        $toAttach = array_diff($allIds, $existingIds);
        $alreadyCount = count($existingIds);

        if (count($toAttach) > 0) {
            $role->permissions()->syncWithoutDetaching($allIds);
            $this->line(" <info>✔</info> Se asignaron " . count($toAttach) . " permisos NUEVOS (ya tenía {$alreadyCount}).");
        } else {
            $role->permissions()->syncWithoutDetaching($allIds);
            $this->line(" <info>✔</info> El rol ya tenía los {$total} permisos asignados. Todo OK.");
        }

        $finalCount = $role->permissions()->count();
        $menuCount = $role->permissions()->where('is_menu', true)->count();

        $this->line("");
        $this->info("=== RESUMEN ===");
        $this->table(['Rol', 'Total Permisos', 'Opciones en Menú'], [
            [$role->nombre, $finalCount, $menuCount]
        ]);

        $this->line("");
        $this->info("✅ Sincronización finalizada. Refresca la página web para ver los cambios en el menú.");

        return 0;
    }
}
