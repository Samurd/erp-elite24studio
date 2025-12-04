<?php

namespace App\Services;

use App\Models\File;
use App\Models\Folder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FileUploadManager
{
    /**
     * Subir un archivo y vincularlo a un modelo
     *
     * @param UploadedFile $uploadedFile Archivo subido
     * @param Model $model Modelo al que vincular el archivo
     * @param int|null $folderId ID de carpeta específica (opcional)
     * @param string|null $defaultFolderName Nombre de carpeta por defecto
     * @return File|null
     */
    public function uploadFile(
        UploadedFile $uploadedFile,
        Model $model,
        ?int $folderId = null,
        ?string $defaultFolderName = null
    ): ?File {
        try {
            $filename = time() . '_' . $uploadedFile->getClientOriginalName();
            $disk = config('filesystems.default', 'public');

            // Determinar la carpeta a usar
            if ($folderId) {
                // Usar carpeta específica seleccionada
                $folder = Folder::find($folderId);
                if (!$folder) {
                    logger()->error("Carpeta seleccionada no encontrada: {$folderId}");
                    return null;
                }
            } else {
                // Usar carpeta por defecto del modelo
                $folderName = $defaultFolderName ?? $this->getModelDefaultFolder($model);
                $folder = $this->getOrCreateDefaultFolder($folderName);
            }

            $folderPath = $folder->path;

            // Guardar archivo en storage
            $path = $uploadedFile->storeAs($folderPath, $filename, $disk);

            if (!$path) {
                logger()->error("Error al guardar archivo: {$uploadedFile->getClientOriginalName()}");
                return null;
            }

            // Crear registro en base de datos
            $file = File::create([
                'fileable_id' => $model->id,
                'fileable_type' => get_class($model),
                'disk' => $disk,
                'path' => $path,
                'name' => $uploadedFile->getClientOriginalName(),
                'mime_type' => $uploadedFile->getMimeType(),
                'size' => $uploadedFile->getSize(),
                'folder_id' => $folder->id,
                'user_id' => Auth::id(),
            ]);

            logger()->info("Archivo guardado correctamente: {$uploadedFile->getClientOriginalName()} en {$path}");

            return $file;
        } catch (\Exception $e) {
            logger()->error("Error al subir archivo: {$e->getMessage()}");
            logger()->error("Stack trace: {$e->getTraceAsString()}");
            return null;
        }
    }

    /**
     * Obtener o crear carpeta por defecto
     *
     * @param string $folderName Nombre de la carpeta
     * @return Folder
     */
    public function getOrCreateDefaultFolder(string $folderName): Folder
    {
        $user = Auth::user();

        // Buscar si ya existe la carpeta en el root
        $folder = Folder::where('name', $folderName)
            ->whereNull('parent_id')
            ->first();

        if (!$folder) {
            // Crear la carpeta si no existe
            $folder = Folder::create([
                'name' => $folderName,
                'parent_id' => null,
                'user_id' => $user->id,
                'path' => "cloud/root/{$folderName}",
            ]);

            logger()->info("Carpeta por defecto creada: {$folderName}");
        }

        return $folder;
    }

    /**
     * Vincular un archivo existente del Cloud a un modelo
     *
     * @param int $fileId ID del archivo existente
     * @param Model $model Modelo al que vincular
     * @return bool
     */
    public function attachExistingFile(int $fileId, Model $model): bool
    {
        try {
            $file = File::find($fileId);

            if (!$file) {
                logger()->error("Archivo no encontrado: {$fileId}");
                return false;
            }

            // Verificar permisos
            if (!$file->hasPermission(Auth::user(), 'view')) {
                logger()->error("Sin permisos para vincular archivo: {$fileId}");
                return false;
            }

            // Actualizar la relación polimórfica
            $file->update([
                'fileable_id' => $model->id,
                'fileable_type' => get_class($model),
            ]);

            logger()->info("Archivo existente vinculado: {$fileId} a {$model->id}");

            return true;
        } catch (\Exception $e) {
            logger()->error("Error al vincular archivo existente: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Eliminar un archivo con validación de permisos
     *
     * @param int $fileId ID del archivo
     * @param Model $model Modelo dueño del archivo
     * @return bool
     */
    public function deleteFile(int $fileId, Model $model): bool
    {
        try {
            $file = File::find($fileId);

            if (!$file) {
                logger()->error("Archivo no encontrado: {$fileId}");
                return false;
            }

            // Verificar que el archivo pertenece al modelo
            if ($file->fileable_id !== $model->id || $file->fileable_type !== get_class($model)) {
                logger()->error("El archivo no pertenece al modelo especificado");
                return false;
            }

            // Eliminar del storage
            if ($file->path && Storage::disk($file->disk)->exists($file->path)) {
                Storage::disk($file->disk)->delete($file->path);
            }

            // Eliminar de la base de datos
            $file->delete();

            logger()->info("Archivo eliminado correctamente: {$fileId}");

            return true;
        } catch (\Exception $e) {
            logger()->error("Error al eliminar archivo: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Mover un archivo a otra carpeta
     *
     * @param int $fileId ID del archivo
     * @param int $folderId ID de la carpeta destino
     * @return bool
     */
    public function moveFile(int $fileId, int $folderId): bool
    {
        try {
            $file = File::find($fileId);

            if (!$file) {
                logger()->error("Archivo no encontrado: {$fileId}");
                return false;
            }

            $file->moveToFolder($folderId);

            logger()->info("Archivo movido correctamente: {$fileId} a carpeta {$folderId}");

            return true;
        } catch (\Exception $e) {
            logger()->error("Error al mover archivo: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Obtener el nombre de carpeta por defecto de un modelo
     *
     * @param Model $model
     * @return string
     */
    private function getModelDefaultFolder(Model $model): string
    {
        if (method_exists($model, 'getDefaultFolderName')) {
            return $model->getDefaultFolderName();
        }

        // Fallback: usar el nombre de la tabla
        return $model->getTable();
    }
}
