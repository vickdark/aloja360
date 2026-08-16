<?php

use App\Models\Roles\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /** Consolidate the legacy administrator slug into the canonical admin role. */
    public function up(): void
    {
        DB::transaction(function (): void {
            $adminRole = Role::where('slug', Role::ADMIN_SLUG)->lockForUpdate()->first();
            $legacyRole = Role::where('slug', Role::ADMINISTRATOR_ALIAS)->lockForUpdate()->first();

            if (! $legacyRole) {
                return;
            }

            $adminRole ??= Role::create([
                'nombre' => 'Super Administrador',
                'slug' => Role::ADMIN_SLUG,
                'descripcion' => 'Acceso total al sistema (multi-negocio)',
            ]);

            $adminRole->permissions()->syncWithoutDetaching($legacyRole->permissions()->pluck('permissions.id'));

            DB::table('users')->where('role_id', $legacyRole->id)->update(['role_id' => $adminRole->id]);
            DB::table('business_user')->where('role_id', $legacyRole->id)->update(['role_id' => $adminRole->id]);

            $legacyRole->delete();
        });
    }

    public function down(): void
    {
        // This data consolidation is intentionally irreversible.
    }
};
