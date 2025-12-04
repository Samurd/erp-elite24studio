<?php

namespace App\Policies;

use App\Models\File;
use App\Models\User;
use App\Models\Area;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class FilePolicy
{
    use HandlesAuthorization;

    /**
     * Helper: Convierte un area_id en el string del permiso (ej: 'finanzas.view')
     * Usa Cache para que sea ultra rápido y no golpee la BD en cada fila.
     */
    protected function getAreaPermissionName(int $areaId, string $action): ?string
    {
        // Cacheamos el slug del área por 24 horas para rendimiento extremo
        $areaSlugFull = Cache::remember("area_slug_{$areaId}", 60 * 60 * 24, function () use ($areaId) {
            $area = Area::find($areaId);
            if (!$area)
                return null;

            // Construye: "padre.hijo" o "hijo"
            return $area->parent ? "{$area->parent->slug}.{$area->slug}" : $area->slug;
        });

        if (!$areaSlugFull)
            return null;

        return "{$areaSlugFull}.{$action}";
    }

    /**
     * VER ARCHIVO
     */
    public function view(User $user, File $file): bool
    {
        // 1. NIVEL ADMIN CLOUD
        if ($user->can('cloud.view'))
            return true;

        // 2. NIVEL DUEÑO
        if ($user->id === $file->user_id)
            return true;

        // 3. NIVEL CONTEXTO GENÉRICO (La Solución)
        // Consultamos directamente la tabla intermedia. 
        // No nos importa SI es un Proyecto o un Lead, solo nos importa el ÁREA.

        $linkedAreaIds = DB::table('files_links')
            ->where('file_id', $file->id)
            ->whereNotNull('area_id')
            ->pluck('area_id') // Obtenemos solo los IDs: [1, 5, 8]
            ->unique();

        foreach ($linkedAreaIds as $areaId) {
            // Construimos el permiso: ej 'projects.view'
            $permission = $this->getAreaPermissionName($areaId, 'view');

            // Si el usuario tiene permiso en ESA área, entra.
            if ($permission && $user->can($permission)) {
                return true;
            }
        }

        // 4. NIVEL SHARES (Invitados)
        return $file->shares()
            ->active()
            ->where(function ($q) use ($user) {
                $q->where('shared_with_user_id', $user->id)
                    ->orWhereIn('shared_with_team_id', $user->teams->pluck('id'));
            })
            ->whereIn('permission', ['view', 'edit'])
            ->exists();
    }

    /**
     * EDITAR ARCHIVO
     */
    public function update(User $user, File $file): bool
    {
        // 1. Admin
        if ($user->can('cloud.update'))
            return true;

        // 2. Dueño
        if ($user->id === $file->user_id)
            return true;

        // 3. Shares (Solo edit)
        $hasShare = $file->shares()
            ->active()
            ->where(function ($q) use ($user) {
                $q->where('shared_with_user_id', $user->id)
                    ->orWhereIn('shared_with_team_id', $user->teams->pluck('id'));
            })
            ->where('permission', 'edit')
            ->exists();

        if ($hasShare)
            return true;

        // 4. CONTEXTO GENÉRICO (Edición)
        $linkedAreaIds = DB::table('files_links')
            ->where('file_id', $file->id)
            ->whereNotNull('area_id')
            ->pluck('area_id')
            ->unique();

        foreach ($linkedAreaIds as $areaId) {
            $permission = $this->getAreaPermissionName($areaId, 'update');
            if ($permission && $user->can($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * ELIMINAR (Solo Admin o Dueño)
     */
    public function delete(User $user, File $file): bool
    {
        if ($user->can('cloud.delete'))
            return true;
        return $user->id === $file->user_id;
    }
}