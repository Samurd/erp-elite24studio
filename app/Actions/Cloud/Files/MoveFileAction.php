<?php

namespace App\Actions\Cloud\Files;

use App\Models\File;

class MoveFileAction
{
    public function execute(File $file, ?int $targetFolderId): File
    {
        if ($file->folder_id === $targetFolderId)
            return $file;

        $file->update(['folder_id' => $targetFolderId]);
        return $file;
    }
}