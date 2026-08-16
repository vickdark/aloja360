<?php

use App\Models\Business;
use App\Models\Roles\Permission;
use App\Models\Roles\Role;
use App\Models\Usuarios\Usuario;
use App\Policies\ReservationPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('uses the role assigned to the active business for permissions', function () {
    $globalRole = Role::create(['nombre' => 'Rol global', 'slug' => 'global-role']);
    $allowedRole = Role::create(['nombre' => 'Rol permitido', 'slug' => 'allowed-role']);
    $deniedRole = Role::create(['nombre' => 'Rol denegado', 'slug' => 'denied-role']);
    $permission = Permission::firstOrCreate(
        ['slug' => 'reservations.index'],
        ['nombre' => 'Ver reservas'],
    );
    $allowedRole->permissions()->attach($permission);

    $allowedBusiness = Business::create(['name' => 'Negocio permitido']);
    $deniedBusiness = Business::create(['name' => 'Negocio denegado']);
    $user = Usuario::create([
        'name' => 'Usuario con permiso',
        'email' => 'permission-user@example.test',
        'password' => 'password',
        'role_id' => $globalRole->id,
        'current_business_id' => $allowedBusiness->id,
    ]);

    $user->businesses()->attach([
        $allowedBusiness->id => ['role_id' => $allowedRole->id],
        $deniedBusiness->id => ['role_id' => $deniedRole->id],
    ]);

    expect($user->hasPermission('reservations.index'))->toBeTrue();

    $userWithoutBusiness = Usuario::create([
        'name' => 'Usuario autorizado sin relación',
        'email' => 'authorized-user@example.test',
        'password' => 'password',
        'role_id' => $allowedRole->id,
    ]);

    expect((new ReservationPolicy)->viewAny($userWithoutBusiness))->toBeTrue();

    $user->update(['current_business_id' => $deniedBusiness->id]);
    expect($user->fresh()->hasPermission('reservations.index'))->toBeFalse();
});

it('consolidates administrator into admin for users and business assignments', function () {
    $admin = Role::create(['nombre' => 'Super Administrador', 'slug' => 'admin']);
    DB::table('roles')->insert([
        'nombre' => 'Administrator',
        'slug' => 'administrator',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    $legacy = Role::where('slug', 'administrator')->firstOrFail();
    $permission = Permission::firstOrCreate(
        ['slug' => 'usuarios.index'],
        ['nombre' => 'Administrar usuarios'],
    );
    $legacy->permissions()->attach($permission);

    $business = Business::create(['name' => 'Negocio de prueba']);
    $user = Usuario::create([
        'name' => 'Usuario legado',
        'email' => 'legacy-user@example.test',
        'password' => 'password',
        'role_id' => $legacy->id,
    ]);
    $user->businesses()->attach($business->id, ['role_id' => $legacy->id]);

    $migration = require database_path('migrations/2026_08_15_000021_consolidate_administrator_role.php');
    $migration->up();

    expect($user->fresh()->role_id)->toBe($admin->id)
        ->and($user->businesses()->first()->pivot->role_id)->toBe($admin->id)
        ->and(Role::where('slug', 'administrator')->exists())->toBeFalse()
        ->and($admin->fresh()->permissions()->whereKey($permission->id)->exists())->toBeTrue();
});
