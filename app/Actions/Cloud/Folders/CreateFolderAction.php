<?php

namespace App\Actions\Cloud\Folders;

use App\Models\Folder;

class CreateFolderAction
{
    public function execute(string $name, ?int $parentId = null): Folder
    {
        return Folder::create([
            'name' => $name,
            'parent_id' => $parentId,
            'user_id' => auth()->id(),
        ]);
    }
}