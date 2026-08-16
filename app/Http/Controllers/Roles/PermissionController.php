<?php

namespace App\Http\Controllers\Roles;

use App\Http\Controllers\Controller;
use App\Console\Commands\SyncPermissions;
use App\Console\Commands\SyncRolesAndPermissions;
use App\Models\Roles\Permission;
use App\Models\Roles\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PermissionController extends Controller
{
    /**
     * Sincroniza las rutas con la tabla de permisos (método tradicional con redirect).
     */
    public function sync()
    {
        try {
            set_time_limit(600);
            ini_set('memory_limit', '1024M');

            \Illuminate\Support\Facades\Artisan::call('roles:sync-full');
            return redirect()->route('roles.index')->with('success', 'Sincronización completa: Permisos y roles actualizados.');
        } catch (\Exception $e) {
            return redirect()->route('roles.index')->with('error', 'Error al sincronizar: ' . $e->getMessage());
        }
    }

    /**
     * Endpoint de streaming para sincronización con progreso en tiempo real.
     * Incluye la asignación automática de permisos al rol Admin.
     */
    public function syncStream(Request $request): StreamedResponse
    {
        if (!auth()->user()->hasPermission('permissions.sync')) {
            abort(403, 'No autorizado para sincronizar permisos.');
        }

        set_time_limit(600);
        ini_set('memory_limit', '1024M');
        @ini_set('zlib.output_compression', 0);
        @ini_set('implicit_flush', 1);

        $response = new StreamedResponse(function () {
            $this->emit([
                'type' => 'start',
                'message' => 'Iniciando sincronización completa de permisos y roles...',
                'percent' => 0,
            ]);

            try {
                $result = SyncPermissions::runSync(function ($step, $total, $message, $type = 'info') {
                    $percent = $total > 0 ? (int) (($step / $total) * 80) : 0;
                    $this->emit([
                        'type' => $type,
                        'step' => $step,
                        'total' => $total,
                        'message' => '[1/2] ' . $message,
                        'percent' => $percent,
                    ]);
                });

                $this->emit([
                    'type' => 'info',
                    'message' => '[2/2] Asignando permisos al rol Admin...',
                    'percent' => 85,
                ]);

                $adminRole = Role::where('slug', 'admin')->first();
                if (!$adminRole) {
                    $adminRole = Role::create([
                        'nombre' => 'Admin',
                        'slug' => 'admin',
                        'descripcion' => 'Rol con acceso total al sistema',
                    ]);
                    $this->emit([
                        'type' => 'success',
                        'message' => 'Rol Admin creado automáticamente.',
                        'percent' => 88,
                    ]);
                }

                $allPerms = Permission::all();
                $totalPerms = $allPerms->count();
                $adminRole->permissions()->syncWithoutDetaching($allPerms->pluck('id')->all());

                $menuCount = $adminRole->permissions()->where('is_menu', true)->count();

                $this->emit([
                    'type' => 'complete',
                    'message' => "Sincronización completada. Rutas: {$result['processed']} · Nuevos: {$result['created']} · Eliminados: {$result['deleted']} · Admin tiene {$totalPerms} permisos ({$menuCount} en menú).",
                    'percent' => 100,
                    'stats' => [
                        'processed' => $result['processed'],
                        'created' => $result['created'],
                        'deleted' => $result['deleted'],
                        'role_permissions' => $totalPerms,
                        'role_menu_items' => $menuCount,
                    ],
                ]);
            } catch (\Throwable $e) {
                $this->emit([
                    'type' => 'error',
                    'message' => 'Error: ' . $e->getMessage() . ' en ' . class_basename($e->getFile()) . ':' . $e->getLine(),
                    'percent' => 0,
                ]);
            }
        });

        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
        $response->headers->set('X-Accel-Buffering', 'no');
        $response->headers->set('Connection', 'keep-alive');

        return $response;
    }

    /**
     * Emite un evento JSON por el stream.
     */
    protected function emit(array $data): void
    {
        echo json_encode($data, JSON_UNESCAPED_UNICODE) . "\n";
        ob_flush();
        flush();
    }
}

