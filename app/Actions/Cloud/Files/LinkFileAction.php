<?php

namespace App\Actions\Cloud\Files;

use App\Models\File;
use Illuminate\Database\Eloquent\Model;

class LinkFileAction
{
    /**
     * Vincula un archivo existente del Cloud a un contexto (Project, Lead, etc).
     * * @param File $file El archivo a vincular
     * @param Model $contextModel El modelo al que se vincula (Project, Contract, etc)
     * @param int|null $areaId El ID del área para permisos (opcional)
     */
    public function execute(File $file, Model $contextModel, ?int $areaId = null): void
    {
        if (!method_exists($contextModel, 'files')) {
            throw new \Exception('El modelo no soporta archivos.');
        }

        // Usamos la sintaxis de array asociativo para guardar datos extra en la tabla pivote
        $contextModel->files()->syncWithoutDetaching([
            $file->id => [
                'area_id' => $areaId // <--- Guardamos el contexto de seguridad
            ]
        ]);
    }
}