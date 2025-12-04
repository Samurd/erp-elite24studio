<?php

namespace App\Actions\Cloud\Folders;

use App\Models\Folder;
use Illuminate\Support\Facades\DB;

class DeleteFolderAction
{
    public function execute(Folder $folder): void
    {
        DB::transaction(function () use ($folder) {
            $this->deleteRecursively($folder);
        });
    }

    private function deleteRecursively(Folder $folder)
    {
        // 1. Borrar archivos (dispara borrado físico)
        $folder->files()->each(function ($file) {
            $file->delete();
        });

        // 2. Borrar subcarpetas
        $folder->children()->each(function ($childFolder) {
            $this->deleteRecursively($childFolder);
        });

        // 3. Borrar carpeta
        $folder->delete();
    }
}