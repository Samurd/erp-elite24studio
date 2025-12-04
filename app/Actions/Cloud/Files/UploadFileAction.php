<?php

namespace App\Actions\Cloud\Files;

use App\Models\File;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class UploadFileAction
{
    /**
     * Sube archivos y los vincula al contexto dado.
     * * @param mixed $files Archivo(s) a subir
     * @param Model|null $contextModel Modelo padre (Project, Lead, etc.)
     * @param int|null $folderId ID de la carpeta visual
     * @param int|null $areaId ID del área para permisos (Nuevo)
     */
    public function execute($files, ?Model $contextModel = null, ?int $folderId = null, ?int $areaId = null): void
    {
        // Normalizar a array
        $files = is_array($files) ? $files : [$files];

        DB::transaction(function () use ($files, $contextModel, $folderId, $areaId) {
            foreach ($files as $uploadedFile) {

                // 1. Configuración
                $disk = config('filesystems.default', 'public');
                $uuidName = Str::uuid() . '.' . $uploadedFile->getClientOriginalExtension();

                // Ruta física organizada por fecha (Mejor práctica)
                // Nota: Puedes dejar 'cloud/root/' si prefieres, pero 'cloud-files/' es más limpio.
                $storagePath = 'cloud-files/' . date('Y/m');

                // 2. GUARDAR EN DISCO
                $path = $uploadedFile->storeAs(
                    $storagePath,  // Carpeta destino
                    $uuidName,     // Nombre archivo
                    ['disk' => $disk]
                );

                // 3. Crear Registro en BD
                // Si viene con areaId, es un archivo del sistema/área (user_id = null)
                // Si NO viene con areaId, es un archivo personal del usuario
                $userId = $areaId ? null : auth()->id();

                $file = File::create([
                    'name' => $uploadedFile->getClientOriginalName(),
                    'path' => $path,
                    'disk' => $disk,
                    'mime_type' => $uploadedFile->getMimeType(),
                    'size' => $uploadedFile->getSize(),
                    'user_id' => $userId,
                    'folder_id' => $folderId,
                ]);

                // 4. VINCULAR AL CONTEXTO CON AREA_ID
                if ($contextModel && method_exists($contextModel, 'files')) {
                    // Usamos la sintaxis de array para pasar datos extra a la tabla pivote
                    $contextModel->files()->syncWithoutDetaching([
                        $file->id => [
                            'area_id' => $areaId // <--- AQUÍ SE GUARDA EL CONTEXTO DE SEGURIDAD
                        ]
                    ]);
                }
            }
        });
    }
}