<?php

namespace App\Http\Middleware;

use App\Models\Area;
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
        $area = Area::where('slug', $slug)->with('parent')->firstOrFail();

        // Construir el permiso completo: padre.subarea.accion
        $permissionName = $area->parent
            ? $area->parent->slug . '.' . $area->slug . '.' . $action
            : $area->slug . '.' . $action;

        if (! $request->user() || ! $request->user()->can($permissionName)) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        return $next($request);
    }
}
