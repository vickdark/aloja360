<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Redirige al usuario a su dashboard específico basado en permisos.
     */
    public function index()
    {
        /** @var \App\Models\Usuarios\Usuario \$user */
        $user = Auth::user();

        // Validamos que el usuario y su rol existan para evitar errores
        if (!$user || !$user->role) {
            return view('dashboard');
        }

        // 1. Si el usuario tiene un permiso específico de dashboard (ej: dashboard.admin, dashboard.vendedor)
        // Buscamos si tiene algún permiso que empiece por 'dashboard.' y no sea el genérico
        $roleDashboard = $user->role->permissions()
            ->where('slug', 'like', 'dashboard.%')
            ->where('slug', '!=', 'dashboard') // Ajustado para comparar con el slug base
            ->first();

        if ($roleDashboard) {
            // Si tiene un dashboard específico, intentamos cargar esa vista
            // Por convención: dashboard.{rol} -> views/dashboards/{rol}.blade.php
            $viewName = str_replace('dashboard.', 'dashboards.', $roleDashboard->slug);
            
            if (view()->exists($viewName)) {
                return view($viewName);
            }
        }

        // 2. Dashboard genérico por defecto
        return view('dashboard');
    }
}
