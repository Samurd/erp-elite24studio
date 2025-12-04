<?php

namespace App\Actions\Cloud\Files;

use App\Models\File;
use Illuminate\Support\Facades\DB;

class DeleteFileAction
{
    public function execute(File $file): void
    {
        DB::transaction(function () use ($file) {
            // El borrado en cascada de BD limpia files_links y shares
            // El evento 'deleted' del modelo limpia el Storage
            $file->delete();
        });
    }
}