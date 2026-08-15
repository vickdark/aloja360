<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Roles\Role;

return new class extends Migration
{
    /**
     * Consolida roles admin y administrator en uno solo (admin).
     * Cualquier usuario con 'administrator' será migrado a 'admin'.
     */
    public function up(): void
    {
        $adminRole = Role::where('slug', 'admin')->first();
        $administratorRole = Role::where('slug', 'administrator')->first();

        if ($adminRole && $administratorRole && $adminRole->id !== $administratorRole->id) {
            // Migrar usuarios de 'administrator' a 'admin'
            \App\Models\Usuarios\Usuario::where('role_id', $administratorRole->id)
                ->update(['role_id' => $adminRole->id]);

            // Migrar permisos: asignar todos los permisos de administrator a admin
            $adminPerms = $adminRole->permissions()->pluck('id')->toArray();
            $administerPerms = $administratorRole->permissions()->pluck('id')->toArray();
            $mergedPerms = array_unique(array_merge($adminPerms, $administerPerms));
            $adminRole->permissions()->sync($mergedPerms);

            // Eliminar el rol 'administrator'
            $administratorRole->delete();

            $this->command->info('Roles consolidados: "administrator" migrado a "admin".');
        }
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        // No reversible: la consolidación es una operación de limpieza de datos
        $this->command->warn('La consolidación de roles no puede revertirse. No se ejecutó down().');
    }
};
