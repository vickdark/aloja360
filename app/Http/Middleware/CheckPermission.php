<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ?string $permission = null): Response
    {
        // Si no se especifica un permiso, usamos el nombre de la ruta actual
        $permissionSlug = $permission ?: $request->route()->getName();

        // Si la ruta no tiene nombre, permitimos el acceso (rutas de sistema sin nombre)
        if (!$permissionSlug) {
            return $next($request);
        }

        if (!$request->user() || !$request->user()->hasPermission($permissionSlug)) {
            // Si es una petición AJAX o espera JSON, devolvemos error en JSON
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No tienes permiso para realizar esta acción.'], 403);
            }
            
            abort(403, 'No tienes permiso para acceder a esta sección [' . $permissionSlug . '].');
        }

        return $next($request);
    }
}
