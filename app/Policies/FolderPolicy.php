<?php

namespace App\Policies;

use App\Models\Folder;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FolderPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Folder $folder): bool
    {
        // 1. ADMIN CLOUD
        if ($user->can('cloud.view')) {
            return true;
        }

        // 2. DUEÑO
        if ($user->id === $folder->user_id) {
            return true;
        }

        // 3. SHARES DIRECTOS (O de equipo)
        // Verificamos si ESTA carpeta específica fue compartida
        $hasDirectShare = $folder->shares()
            ->active()
            ->where(function ($q) use ($user) {
                $q->where('shared_with_user_id', $user->id)
                    ->orWhereIn('shared_with_team_id', $user->teams->pluck('id'));
            })
            ->whereIn('permission', ['view', 'edit'])
            ->exists();

        if ($hasDirectShare) {
            return true;
        }

        // 4. HERENCIA DE PADRE (RECURSIVIDAD HACIA ARRIBA)
        // Si tengo permiso en la carpeta PADRE, tengo permiso en la HIJA.
        // Esto es lo "normal" en sistemas de archivos.
        if ($folder->parent_id) {
            // Nota: Esto hará llamadas recursivas a la DB.
            // Laravel cachea los modelos cargados, pero en estructuras profundas ten cuidado.
            // Para optimizar, podrías usar eager loading en la consulta inicial.
            if ($folder->parent && $this->view($user, $folder->parent)) {
                return true;
            }
        }

        return false;
    }

    public function create(User $user): bool
    {
        // Cualquiera puede crear carpetas en su espacio
        // O si quieres restringir: return $user->can('cloud.create');
        return true;
    }

    public function update(User $user, Folder $folder): bool
    {
        if ($user->can('cloud.update'))
            return true;
        if ($user->id === $folder->user_id)
            return true;

        // Verificar Share con permiso 'edit'
        $hasEditShare = $folder->shares()
            ->active()
            ->where(function ($q) use ($user) {
                $q->where('shared_with_user_id', $user->id)
                    ->orWhereIn('shared_with_team_id', $user->teams->pluck('id'));
            })
            ->where('permission', 'edit')
            ->exists();

        if ($hasEditShare)
            return true;

        // Herencia de edición del padre
        if ($folder->parent_id && $folder->parent) {
            return $this->update($user, $folder->parent);
        }

        return false;
    }

    public function delete(User $user, Folder $folder): bool
    {
        // Solo Admin o Dueño pueden BORRAR una estructura completa.
        // Los editores solo pueden editar/renombrar o subir archivos.
        if ($user->can('cloud.delete'))
            return true;
        return $user->id === $folder->user_id;
    }
}