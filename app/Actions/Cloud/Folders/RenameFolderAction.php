<?php

namespace App\Actions\Cloud\Folders;

use App\Models\Folder;

class RenameFolderAction
{
    public function execute(Folder $folder, string $newName): Folder
    {
        if (empty($newName))
            throw new \Exception('Nombre vacío');
        $folder->update(['name' => $newName]);
        return $folder;
    }
}