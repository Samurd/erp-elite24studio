<?php

namespace App\Actions\Cloud\Folders;

use App\Models\Folder;

class GetOrCreateFolderAction
{
    protected $createAction;

    // Inyectamos la acción de crear para reutilizar su lógica
    public function __construct(CreateFolderAction $createAction)
    {
        $this->createAction = $createAction;
    }

    /**
     * Busca una carpeta por nombre y padre, si no existe, la crea.
     *
     * @param string $name Nombre de la carpeta
     * @param int|null $parentId ID de la carpeta padre (null = Raíz)
     * @return Folder
     */
    public function execute(string $name, ?int $parentId = null): Folder
    {
        // 1. Intentar encontrarla
        $folder = Folder::where('name', $name)
            ->where('parent_id', $parentId)
            ->first();

        if ($folder) {
            return $folder;
        }

        // 2. Si no existe, usar la acción de crear que ya tienes
        return $this->createAction->execute($name, $parentId);
    }
}