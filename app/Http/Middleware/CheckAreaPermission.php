<?php

namespace App\Http\Middleware;

use App\Services\AreaPermissionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAreaPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $action, string $slug): Response
    {
        // 1. Verificar si el usuario está autenticado
        if (!$request->user()) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        // 2. Delegar la verificación (ahora cacheada en el servicio)
        if (!AreaPermissionService::canArea($action, $slug)) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        return $next($request);
    }
}
