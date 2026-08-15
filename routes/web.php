<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Usuarios\UsuarioController;
use App\Http\Controllers\Roles\RoleController;
use App\Http\Controllers\Roles\PermissionController;
use App\Http\Controllers\Profile\PasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WelcomeController;

use App\Http\Controllers\ConfiguracionController;

Route::redirect('/', '/login');

Route::get('/welcome', WelcomeController::class)->name('welcome');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/admin', [DashboardController::class, 'index'])->name('dashboard.admin');
    
    Route::resources([
        'usuarios' => UsuarioController::class,
        'roles' => RoleController::class,
    ]);

    // Configuración del Sistema
    Route::get('/configuracion', [ConfiguracionController::class, 'index'])->name('configuracion.index');
    Route::post('/configuracion', [ConfiguracionController::class, 'update'])->name('configuracion.update');
    
    // Gestión de Roles y Seguridad (Rutas adicionales)
    Route::get('roles/{role}/permisos', [RoleController::class, 'permissions'])->name('roles.edit_permissions');
    Route::put('roles/{role}/permisos', [RoleController::class, 'updateRolePermissions'])->name('roles.update_permissions');
    
    // Gestión de Permisos (Sincronización)
    Route::post('permissions/sync', [PermissionController::class, 'sync'])->name('permissions.sync');
    // Perfil y Seguridad
    Route::put('/password', [PasswordController::class, 'update'])->name('password.update.ajax');
});

require __DIR__.'/auth.php';
