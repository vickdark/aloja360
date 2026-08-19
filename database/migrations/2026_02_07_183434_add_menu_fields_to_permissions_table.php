<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Facades\Artisan;
use App\Models\Roles\Role;
use App\Models\Roles\Permission;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            if (!Schema::hasColumn('permissions', 'is_menu')) {
                $table->boolean('is_menu')->default(false)->after('slug');
            }
            if (!Schema::hasColumn('permissions', 'icon')) {
                $table->string('icon')->nullable()->after('is_menu');
            }
            if (!Schema::hasColumn('permissions', 'module')) {
                $table->string('module')->nullable()->after('icon');
            }
            if (!Schema::hasColumn('permissions', 'order')) {
                $table->integer('order')->default(0)->after('module');
            }
        });

        // Ejecutar la sincronización de permisos (escanea rutas y crea registros en BD)
        Artisan::call('permissions:sync');

        // Asignar absolutamente todos los permisos al rol administrador para asegurar acceso total desde el inicio
        $adminRole = Role::where('slug', 'admin')->first();
        if ($adminRole) {
            $allPermissions = Permission::all();
            $adminRole->permissions()->sync($allPermissions->pluck('id'));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn(['is_menu', 'icon', 'module', 'order']);
        });
    }
};
