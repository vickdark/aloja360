<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Usuarios\UsuarioController;
use App\Http\Controllers\Roles\RoleController;
use App\Http\Controllers\Roles\PermissionController;
use App\Http\Controllers\Profile\PasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WelcomeController;

use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\AccommodationController;
use App\Http\Controllers\BusinessController;

Route::redirect('/', '/login');

Route::get('/welcome', WelcomeController::class)->name('welcome');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/admin', [DashboardController::class, 'index'])->name('dashboard.admin');
    
    // Aloja360 MVP Core Routes (Web Views)
    // Negocios
    Route::get('businesses', [BusinessController::class, 'index'])->name('businesses.index');
    Route::get('businesses/create', [BusinessController::class, 'create'])->name('businesses.create');
    Route::post('businesses', [BusinessController::class, 'store'])->name('businesses.store');

    // Reservas
    Route::get('reservations', [ReservationController::class, 'index'])->name('reservations.index');
    Route::post('reservations', [ReservationController::class, 'store'])->name('reservations.store');
    Route::get('reservations/{reservation}', [ReservationController::class, 'show'])->name('reservations.show');
    Route::put('reservations/{reservation}', [ReservationController::class, 'update'])->name('reservations.update');
    Route::post('reservations/{reservation}/confirm', [ReservationController::class, 'confirm'])->name('reservations.confirm');
    Route::post('reservations/{reservation}/check-in', [ReservationController::class, 'checkIn'])->name('reservations.checkIn');
    Route::post('reservations/{reservation}/check-out', [ReservationController::class, 'checkOut'])->name('reservations.checkOut');
    Route::post('reservations/{reservation}/cancel', [ReservationController::class, 'cancel'])->name('reservations.cancel');

    // Alojamientos
    Route::get('accommodations', [AccommodationController::class, 'index'])->name('accommodations.index');
    Route::get('accommodations/available', [AccommodationController::class, 'available'])->name('accommodations.available');
    Route::get('accommodations/{accommodation}', [AccommodationController::class, 'show'])->name('accommodations.show');

    // API Routes (AJAX calls)
    Route::prefix('api/v1')->group(function () {
        // We can expose the JSON endpoints here if needed later, but for now we use the web routes for the UI.
    });

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
