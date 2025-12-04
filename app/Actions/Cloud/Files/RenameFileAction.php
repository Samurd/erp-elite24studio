<?php

namespace App\Actions\Cloud\Files;

use App\Models\File;

class RenameFileAction
{
    public function execute(File $file, string $newName): File
    {
        if (empty($newName))
            throw new \InvalidArgumentException('Nombre inválido');

        // Mantener extensión
        $extension = pathinfo($file->name, PATHINFO_EXTENSION);
        if (!str_ends_with($newName, '.' . $extension)) {
            $newName .= '.' . $extension;
        }

        $file->update(['name' => $newName]);
        return $file;
    }
}