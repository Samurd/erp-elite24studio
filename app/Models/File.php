<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class File extends Model
{
    protected $fillable = [
        "name",
        "path",
        "disk",
        "mime_type",
        "size",
        "folder_id",
        "user_id",
        // 'fileable_id' y 'fileable_type' ELIMINADOS (ahora están en files_links)
    ];

    /**
     * Ciclo de vida del archivo
     */
    protected static function booted()
    {
        // 1. Al eliminar el registro de la BD, eliminar el archivo físico
        static::deleted(function ($file) {
            if ($file->path && Storage::disk($file->disk)->exists($file->path)) {
                Storage::disk($file->disk)->delete($file->path);
            }
        });

        // 2. Al actualizar, verificar cambios de nombre o carpeta
        static::updating(function ($file) {
            // A. Renombrar archivo físico si cambia el nombre
            if ($file->isDirty("name")) {
                $file->handlePhysicalRename();
            }

            // B. Mover archivo físico si cambia la carpeta
            if ($file->isDirty("folder_id")) {
                $file->moveToFolder($file->folder_id);
            }
        });
    }

    /* -----------------------------------------------------------------
     * RELACIONES PRINCIPALES
     * -----------------------------------------------------------------
     */

    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, "user_id");
    }

    /**
     * Archivos compartidos directamente con usuarios/equipos
     */
    public function shares()
    {
        return $this->morphMany(Share::class, "shareable");
    }

    /* -----------------------------------------------------------------
     * RELACIONES DEL ERP (INVERSAS - MUCHOS A MUCHOS)
     * Estas definen "En qué partes del ERP aparece este archivo".
     * IMPORTANTE: Todas deben apuntar a la tabla pivote 'files_links'.
     * -----------------------------------------------------------------
     */

    /**
     * Módulo Proyectos
     */
    public function projects(): MorphToMany
    {
        return $this->morphedByMany(Project::class, 'fileable', 'files_links');
    }


    public function caseRecords(): MorphToMany
    {
        return $this->morphedByMany(CaseRecord::class, 'fileable', 'files_links');
    }


    public function invoices()
    {
        return $this->morphedByMany(Invoice::class, 'fileable', 'files_links');
    }

    public function messages()
    {
        return $this->morphedByMany(Message::class, 'fileable', 'files_links');
    }

    /* -----------------------------------------------------------------
     * MÉTODOS DE LÓGICA DE NEGOCIO (Move / Rename)
     * -----------------------------------------------------------------
     */

    /**
     * Mueve el archivo a una nueva carpeta (Lógica física + BD)
     */
    public function moveToFolder($folderId)
    {
        if ($folderId === $this->folder_id) {
            return $this;
        }

        $oldPath = $this->path;
        $filename = basename($oldPath);

        if ($folderId) {
            $folder = Folder::find($folderId);
            if (!$folder)
                throw new \Exception("Carpeta destino no encontrada");
            $newPath = $folder->path . "/" . $filename;
        } else {
            $newPath = "cloud/root/" . $filename; // O tu ruta default
        }

        if ($oldPath !== $newPath) {
            $this->movePhysicalFile($oldPath, $newPath);
            $this->path = $newPath;
            $this->folder_id = $folderId;
        }

        return $this;
    }

    /**
     * Lógica interna para manejar el renombrado físico
     */
    protected function handlePhysicalRename()
    {
        $oldPath = $this->getOriginal("path");
        $directory = dirname($oldPath);
        $newPath = $directory . "/" . $this->name;

        if ($oldPath !== $newPath) {
            $disk = Storage::disk($this->disk);
            if ($disk->exists($newPath)) {
                throw new \Exception("Ya existe un archivo con ese nombre.");
            }

            $this->movePhysicalFile($oldPath, $newPath);
            $this->path = $newPath;
        }
    }

    /**
     * Helper privado para mover archivos en disco con manejo de errores
     */
    private function movePhysicalFile($oldPath, $newPath)
    {
        try {
            $disk = Storage::disk($this->disk);

            if ($disk->exists($oldPath)) {
                $newDir = dirname($newPath);
                if (!$disk->exists($newDir)) {
                    $disk->makeDirectory($newDir);
                }
                $disk->move($oldPath, $newPath);
            }
        } catch (\Exception $e) {
            logger()->error("Error moviendo archivo: " . $e->getMessage());
            throw new \Exception("Error IO: " . $e->getMessage());
        }
    }

    public function renameFile($newName)
    {
        if (empty($newName))
            throw new \Exception("Nombre inválido");
        $this->name = $newName;
        $this->save(); // Esto dispara el evento 'updating'
        return $this;
    }

    /* -----------------------------------------------------------------
     * ACCESORS
     * -----------------------------------------------------------------
     */

    public function getReadableSizeAttribute()
    {
        $size = $this->size;
        $units = ["B", "KB", "MB", "GB", "TB"];
        $i = 0;
        while ($size >= 1024 && $i < count($units) - 1) {
            $size /= 1024;
            $i++;
        }
        return round($size, 2) . " " . $units[$i];
    }

    public function getUrlAttribute()
    {
        $disk = Storage::disk($this->disk);
        // Soporte para S3 (url firmada) o Local
        return method_exists($disk, "url")
            ? $disk->url($this->path)
            : asset("storage/" . $this->path);
    }
}