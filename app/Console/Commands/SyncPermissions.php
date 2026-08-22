<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;

class SyncPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:sync {--clean : Elimina permisos que ya no existen en las rutas}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza las rutas del sistema con la tabla de permisos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando sincronización de permisos...');

        $result = self::runSync(function ($step, $total, $message, $type = 'info') {
            if ($type === 'line') {
                $this->line($message);
            } elseif ($type === 'warn') {
                $this->warn($message);
            } else {
                $this->info($message);
            }
        });

        $this->info("Sincronización completada. Total nuevos: {$result['created']}. Eliminados: {$result['deleted']}.");
    }

    /**
     * Lógica central de sincronización, reutilizable desde CLI y Web.
     * Acepta un callback de progreso: function($step, $total, $message, $type)
     */
    public static function runSync(?callable $onProgress = null): array
    {
        $routes = Route::getRoutes();
        $routeNames = [];

        foreach ($routes as $route) {
            $name = $route->getName();
            if ($name && self::shouldSyncStatic($name)) {
                $routeNames[] = [
                    'name' => $name,
                    'route' => $route,
                ];
            }
        }

        $total = count($routeNames);
        $permissionsCreated = 0;
        $activeSlugs = [];
        $step = 0;

        foreach ($routeNames as $item) {
            $step++;
            $name = $item['name'];
            $route = $item['route'];
            $activeSlugs[] = $name;

            $permission = \App\Models\Roles\Permission::updateOrCreate(
                ['slug' => $name],
                [
                    'nombre' => self::generateNameStatic($name),
                    'descripcion' => self::generateDescriptionStatic($name, $route),
                    'is_menu' => self::isMenuStatic($name),
                    'icon' => self::generateIconStatic($name),
                    'module' => self::generateModuleNameStatic($name),
                    'order' => self::generateOrderStatic($name),
                ]
            );

            if ($permission->wasRecentlyCreated) {
                $permissionsCreated++;
                if ($onProgress) {
                    $onProgress($step, $total, "✔ Nuevo permiso: {$name}", 'line');
                }
            } else {
                if ($onProgress && ($step % 5 === 0 || $step === $total)) {
                    $onProgress($step, $total, "Procesando: {$name}", 'info');
                }
            }
        }

        $deleted = \App\Models\Roles\Permission::whereNotIn('slug', $activeSlugs)->delete();

        if ($deleted > 0 && $onProgress) {
            $onProgress($total, $total, "Se eliminaron {$deleted} permisos antiguos.", 'warn');
        }

        if ($onProgress) {
            $onProgress($total, $total, "Sincronización completada. Nuevos: {$permissionsCreated}. Eliminados: {$deleted}.", 'success');
        }

        return [
            'created' => $permissionsCreated,
            'deleted' => $deleted,
            'processed' => $total,
        ];
    }

    /**
     * Wrappers estáticos de los helpers protegidos.
     */
    protected static function shouldSyncStatic($name)
    {
        $excludedPrefixes = [
            'sanctum.', 'ignition.', 'livewire.', 'verification.',
            'password.', 'login', 'logout', 'register',
            'profile.', 'storage.'
        ];

        foreach ($excludedPrefixes as $prefix) {
            if (str_starts_with($name, $prefix)) {
                return false;
            }
        }

        if ($name === 'dashboard' || str_contains($name, '.')) {
            return true;
        }

        return false;
    }

    protected static function generateNameStatic($slug)
    {
        if (str_starts_with($slug, 'dashboard.')) {
            $role = ucfirst(str_replace('dashboard.', '', $slug));
            return "Dashboard {$role}";
        }

        $parts = explode('.', $slug);
        $action = end($parts);
        $module = count($parts) > 1 ? $parts[count($parts) - 2] : 'General';

        $translations = self::getTranslationsStatic();
        $actionName = $translations[$action] ?? ucfirst($action);
        $moduleName = ucfirst($module);

        return "{$actionName} {$moduleName}";
    }

    protected static function getTranslationsStatic()
    {
        return [
            'index'   => 'Ver lista de',
            'show'    => 'Ver detalle de',
            'create'  => 'Crear',
            'store'   => 'Guardar',
            'edit'    => 'Editar',
            'update'  => 'Actualizar',
            'destroy' => 'Eliminar',
            'sync'    => 'Sincronizar',
            'export'  => 'Exportar',
            'import'  => 'Importar',
            'edit_permissions'   => 'Gestionar permisos de',
            'update_permissions' => 'Actualizar permisos de',
        ];
    }

    protected static function generateDescriptionStatic($slug, $route)
    {
        if (str_starts_with($slug, 'dashboard.')) {
            $role = str_replace('dashboard.', '', $slug);
            return "Vista de panel principal personalizada para el rol {$role}";
        }

        $parts = explode('.', $slug);
        $action = end($parts);
        $module = count($parts) > 1 ? $parts[count($parts) - 2] : 'General';

        $translations = self::getTranslationsStatic();
        $actionName = $translations[$action] ?? ucfirst($action);
        $moduleName = ucfirst($module);

        return "Permite {$actionName} {$moduleName} en el sistema";
    }

    protected static function isMenuStatic($slug)
    {
        if ($slug === 'dashboard') return true;
        if (str_starts_with($slug, 'dashboard.')) return false;

        $parts = explode('.', $slug);
        $action = end($parts);

        return $action === 'index';
    }

    protected static function generateIconStatic($slug)
    {
        if ($slug === 'dashboard') return 'fa-solid fa-chart-line';

        $parts = explode('.', $slug);
        $module = count($parts) > 1 ? $parts[count($parts) - 2] : 'General';

        $icons = [
            'usuarios' => 'fa-solid fa-users',
            'roles'    => 'fa-solid fa-user-shield',
            'permissions' => 'fa-solid fa-key',
            'configuracion' => 'fa-solid fa-gears',
            'businesses' => 'fa-solid fa-building',
            'accommodations' => 'fa-solid fa-house',
            'amenities' => 'fa-solid fa-sparkles',
            'rate_periods' => 'fa-solid fa-calendar-days',
            'guests' => 'fa-solid fa-user-group',
            'quotes' => 'fa-solid fa-file-invoice-dollar',
            'reservations' => 'fa-solid fa-calendar-check',
            'services' => 'fa-solid fa-bell-concierge',
            'payments' => 'fa-solid fa-money-bill-wave',
            'expenses' => 'fa-solid fa-receipt',
            'commissions' => 'fa-solid fa-hand-holding-dollar',
            'expense_categories' => 'fa-solid fa-tags',
            'cleaning' => 'fa-solid fa-broom',
            'maintenance' => 'fa-solid fa-wrench',
            'blocked_periods' => 'fa-solid fa-ban',
            'inventory' => 'fa-solid fa-boxes-stacked',
            'reports' => 'fa-solid fa-chart-pie',
        ];

        return $icons[strtolower($module)] ?? 'fa-solid fa-circle-dot';
    }

    protected static function generateModuleNameStatic($slug)
    {
        if ($slug === 'dashboard') return 'General';

        $parts = explode('.', $slug);
        if (count($parts) <= 1) return 'General';

        $module = strtolower($parts[count($parts) - 2]);

        $modules = [
            'usuarios' => 'Usuarios',
            'roles' => 'Seguridad',
            'permissions' => 'Seguridad',
            'configuracion' => 'Configuración',
            'businesses' => 'Configuración',
            'accommodations' => 'Alojamientos',
            'amenities' => 'Alojamientos',
            'rate_periods' => 'Alojamientos',
            'blocked_periods' => 'Alojamientos',
            'guests' => 'Clientes',
            'inventory' => 'Operación',
            'quotes' => 'Cotizaciones',
            'reservations' => 'Reservas',
            'services' => 'Reservas',
            'payments' => 'Finanzas',
            'expenses' => 'Finanzas',
            'commissions' => 'Finanzas',
            'expense_categories' => 'Finanzas',
            'cleaning' => 'Operación',
            'maintenance' => 'Operación',
            'reports' => 'Reportes',
        ];

        return $modules[$module] ?? ucfirst($module);
    }

    protected static function generateOrderStatic($slug)
    {
        if ($slug === 'dashboard') return 1;
        if (str_contains($slug, 'accommodations')) return 10;
        if (str_contains($slug, 'amenities')) return 11;
        if (str_contains($slug, 'rate_periods')) return 12;
        if (str_contains($slug, 'blocked_periods')) return 13;
        if (str_contains($slug, 'guests')) return 20;
        if (str_contains($slug, 'quotes')) return 30;
        if (str_contains($slug, 'reservations')) return 35;
        if (str_contains($slug, 'services')) return 36;
        if (str_contains($slug, 'payments')) return 40;
        if (str_contains($slug, 'expense_categories')) return 44;
        if (str_contains($slug, 'expenses')) return 45;
        if (str_contains($slug, 'commissions')) return 46;
        if (str_contains($slug, 'cleaning')) return 50;
        if (str_contains($slug, 'maintenance')) return 55;
        if (str_contains($slug, 'inventory')) return 57;
        if (str_contains($slug, 'reports')) return 60;
        if (str_contains($slug, 'usuarios')) return 80;
        if (str_contains($slug, 'roles') || str_contains($slug, 'permissions')) return 90;
        if (str_contains($slug, 'configuracion') || str_contains($slug, 'businesses')) return 100;

        return 50;
    }
}
