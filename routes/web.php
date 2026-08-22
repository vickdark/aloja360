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
use App\Http\Controllers\GuestController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CleaningTaskController;
use App\Http\Controllers\MaintenanceRequestController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\CommissionController;

Route::redirect('/', '/login');

Route::get('/welcome', WelcomeController::class)->name('welcome');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/admin', [DashboardController::class, 'index'])->name('dashboard.admin');
    
    // Aloja360 MVP Core Routes (Web Views)
    // Negocios
    Route::resource('businesses', BusinessController::class);

    // Reservas
    Route::get('reservations/calendar', [ReservationController::class, 'calendar'])->name('reservations.calendar');
    Route::get('reservations/calendar-data', [ReservationController::class, 'calendarData'])->name('reservations.calendarData');
    Route::post('reservations/estimate', [ReservationController::class, 'estimate'])->name('reservations.estimate');
    Route::get('reservations/{reservation}/pdf', [ReservationController::class, 'pdf'])->name('reservations.pdf');
    Route::post('reservations/{reservation}/send-email', [ReservationController::class, 'sendEmail'])->name('reservations.sendEmail');
    Route::post('reservations/{reservation}/confirm', [ReservationController::class, 'confirm'])->name('reservations.confirm');
    Route::post('reservations/{reservation}/check-in', [ReservationController::class, 'checkIn'])->name('reservations.checkIn');
    Route::post('reservations/{reservation}/check-out', [ReservationController::class, 'checkOut'])->name('reservations.checkOut');
    Route::post('reservations/{reservation}/cancel', [ReservationController::class, 'cancel'])->name('reservations.cancel');
    Route::resource('reservations', ReservationController::class); // Reemplaza las rutas manuales y agrega create/edit/destroy

    // Alojamientos
    Route::get('accommodations/available', [AccommodationController::class, 'available'])->name('accommodations.available');
    Route::delete('accommodations/{accommodation}/images/{image}', [AccommodationController::class, 'destroyImage'])->name('accommodations.destroyImage');
    Route::resource('accommodations', AccommodationController::class);

    // Huéspedes
    Route::resource('guests', GuestController::class);

    // Pagos
    Route::resource('payments', PaymentController::class);

    // Limpieza
    Route::resource('cleaning', CleaningTaskController::class)->parameters([
        'cleaning' => 'cleaning' // to match $cleaning in controller
    ]);

    // Mantenimiento
    Route::resource('maintenance', MaintenanceRequestController::class)->parameters([
        'maintenance' => 'maintenance' // to match $maintenance in controller
    ]);

    // Gastos
    Route::resource('expenses', ExpenseController::class);

    // Comisiones
    Route::post('commissions/{commission}/mark-paid', [CommissionController::class, 'markPaid'])->name('commissions.markPaid');
    Route::resource('commissions', CommissionController::class);

    // MANTENIMIENTO Y OPERACIONES (NUEVOS MODULOS)
    // Amenidades (Servicios del Alojamiento)
    Route::resource('amenities', \App\Http\Controllers\AmenityController::class);

    // Temporadas / Tarifas
    Route::resource('rate_periods', \App\Http\Controllers\RatePeriodController::class);

    // Servicios Extras (Vendibles en Reservas)
    Route::resource('services', \App\Http\Controllers\ServiceController::class);

    // Bloqueos de Disponibilidad
    Route::resource('blocked_periods', \App\Http\Controllers\BlockedPeriodController::class);

    // Inventario
    Route::resource('inventory', \App\Http\Controllers\InventoryItemController::class)->parameters([
        'inventory' => 'inventory_item' 
    ]);

    // Categorías de Gastos
    Route::resource('expense_categories', \App\Http\Controllers\ExpenseCategoryController::class);

    // Cotizaciones
    Route::post('quotes/estimate', [\App\Http\Controllers\QuoteController::class, 'estimate'])->name('quotes.estimate');
    Route::get('quotes/{quote}/pdf', [\App\Http\Controllers\QuoteController::class, 'pdf'])->name('quotes.pdf');
    Route::post('quotes/{quote}/send-email', [\App\Http\Controllers\QuoteController::class, 'sendEmail'])->name('quotes.sendEmail');
    Route::post('quotes/{quote}/convert', [\App\Http\Controllers\QuoteController::class, 'convertToReservation'])->name('quotes.convert');
    Route::resource('quotes', \App\Http\Controllers\QuoteController::class);

    // Reportes
    Route::get('reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/data', [\App\Http\Controllers\ReportController::class, 'data'])->name('reports.data');

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
    Route::get('permissions/sync-stream', [PermissionController::class, 'syncStream'])->name('permissions.sync_stream');
    // Perfil y Seguridad
    Route::get('/profile/password', [PasswordController::class, 'show'])->name('profile.password');
    Route::put('/profile/password', [PasswordController::class, 'update'])->name('profile.password.update');
});

require __DIR__.'/auth.php';
