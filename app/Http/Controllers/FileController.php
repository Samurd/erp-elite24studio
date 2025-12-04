<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class FileController extends Controller
{
    use AuthorizesRequests;

    /**
     * Descargar archivo forzando la descarga.
     */
    public function download(File $file)
    {
        // 1. Verificar Permisos (Usa tu FilePolicy)
        $this->authorize('view', $file);

        // 2. Verificar existencia física
        if (!Storage::disk($file->disk)->exists($file->path)) {
            abort(404, 'El archivo no existe en el disco.');
        }

        // 3. Entregar archivo
        return Storage::disk($file->disk)->download($file->path, $file->name);
    }

    /**
     * Mostrar archivo en el navegador (ej: PDF o Imagen).
     */
    public function stream(File $file)
    {
        $this->authorize('view', $file);

        if (!Storage::disk($file->disk)->exists($file->path)) {
            abort(404);
        }

        return Storage::disk($file->disk)->response($file->path, $file->name);
    }
}