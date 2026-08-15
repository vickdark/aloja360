<?php

namespace App\Http\Controllers\Roles;

use App\Http\Controllers\Controller;
use App\Models\Roles\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PermissionController extends Controller
{
    /**
     * Sincroniza las rutas con la tabla de permisos.
     */
    public function sync()
    {
        try {
            \Illuminate\Support\Facades\Artisan::call('permissions:sync');
            return redirect()->route('roles.index')->with('success', 'Permisos sincronizados correctamente con las rutas del sistema.');
        } catch (\Exception $e) {
            return redirect()->route('roles.index')->with('error', 'Error al sincronizar: ' . $e->getMessage());
        }
    }
}
