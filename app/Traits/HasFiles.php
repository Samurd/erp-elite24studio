<?php

namespace App\Traits;

use App\Models\File;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasFiles
{
    /**
     * Relación: Obtener todos los archivos vinculados a este modelo.
     */
    public function files(): MorphToMany
    {
        return $this->morphToMany(File::class, 'fileable', 'files_links')
            ->withPivot('area_id') // <--- Traemos el ID
            ->withTimestamps();
    }

    /**
     * Helper: Vincular un archivo (por ID o Modelo).
     * No duplica si ya existe.
     */
    public function attachFile($file)
    {
        $this->files()->syncWithoutDetaching($file);
    }

    /**
     * Helper: Desvincular un archivo.
     * Solo rompe el enlace, NO borra el archivo físico.
     */
    public function detachFile($file)
    {
        $this->files()->detach($file);
    }

    /**
     * Helper: Sincronizar archivos (útil para formularios de edición).
     * Reemplaza los archivos actuales con la nueva lista de IDs.
     */
    public function syncFiles(array $fileIds)
    {
        $this->files()->sync($fileIds);
    }
}