<?php

namespace App\Actions\Cloud\Folders;

use App\Models\Folder;

class MoveFolderAction
{
    public function execute(Folder $folder, ?int $targetFolderId): Folder
    {
        if ($folder->parent_id === $targetFolderId)
            return $folder;
        if ($folder->id === $targetFolderId)
            throw new \Exception('No puedes moverte a ti mismo.');

        if ($targetFolderId) {
            $target = Folder::find($targetFolderId);
            if ($this->isDescendant($target, $folder->id)) {
                throw new \Exception('No puedes mover una carpeta dentro de su hija.');
            }
        }

        $folder->update(['parent_id' => $targetFolderId]);
        return $folder;
    }

    private function isDescendant(Folder $target, int $forbiddenId): bool
    {
        if ($target->parent_id === $forbiddenId)
            return true;
        if ($target->parent)
            return $this->isDescendant($target->parent, $forbiddenId);
        return false;
    }
}
