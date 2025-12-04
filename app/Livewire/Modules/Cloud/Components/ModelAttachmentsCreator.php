<?php

namespace App\Livewire\Modules\Cloud\Components;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use App\Actions\Cloud\Files\UploadFileAction;
use App\Actions\Cloud\Files\LinkFileAction;
use App\Actions\Cloud\Folders\GetOrCreateFolderAction;
use App\Services\AreaPermissionService;
use App\Models\Area;
use App\Models\File;

class ModelAttachmentsCreator extends Component
{
    use WithFileUploads;

    // Configuración
    public $modelClass; // Ej: App\Models\Project (String)
    public $areaSlug;   // Ej: 'projects'

    // Estado de Subida (Temporales)
    public $uploads = []; // Array de TemporaryUploadedFile
    public $upload;       // Archivo individual en proceso (bypass S3 limit)

    // Estado de Vínculos Cloud (IDs seleccionados)
    public $pendingLinkIds = [];
    public $pendingLinkFiles = []; // Modelos File para mostrar info visual (nombre, tamaño)

    protected $listeners = [
        'commit-attachments' => 'commit',           // Orden del padre para guardar
        'file-selected-for-creation' => 'addCloudFile' // Respuesta del Modal Selector
    ];

    public function mount(string $modelClass, string $areaSlug)
    {
        $this->modelClass = $modelClass;
        $this->areaSlug = $areaSlug;
    }

    /**
     * Hook que se ejecuta tras subir un archivo temporalmente.
     */
    public function updatedUpload()
    {
        $this->validate(['upload' => 'required|file|max:51200']); // 50MB

        // Agregar a la cola
        $this->uploads[] = $this->upload;

        // Limpiar para el siguiente
        $this->upload = null;
        $this->dispatch('clear-file-input');
    }

    /**
     * Quitar un archivo de la cola de subida.
     */
    public function deleteUpload(int $index)
    {
        array_splice($this->uploads, $index, 1);
    }

    /**
     * Abrir el modal para seleccionar archivos existentes.
     */
    public function openSelector()
    {
        // Obtener ID del área para pasarlo al modal (contexto)
        $areaId = Area::where('slug', $this->areaSlug)->value('id');

        $this->dispatch(
            'openFileSelector',
            type: $this->modelClass,
            id: null, // NULL indica "Modo Creación"
            area_id: $areaId
        );
    }

    /**
     * Callback cuando se selecciona un archivo en el modal.
     */
    public function addCloudFile($fileId)
    {
        // Evitar duplicados visuales
        if (!in_array($fileId, $this->pendingLinkIds)) {
            $this->pendingLinkIds[] = $fileId;

            // Cargar el modelo para mostrar nombre/icono en la lista
            $file = File::find($fileId);
            if ($file) {
                $this->pendingLinkFiles[] = $file;
            }
        }
    }

    /**
     * Quitar un archivo del Cloud de la lista de pendientes.
     */
    public function removeCloudFile($index)
    {
        // Necesitamos el ID para quitarlo de ambos arrays
        unset($this->pendingLinkIds[$index]);
        unset($this->pendingLinkFiles[$index]);

        // Reindexar
        $this->pendingLinkIds = array_values($this->pendingLinkIds);
        $this->pendingLinkFiles = array_values($this->pendingLinkFiles);
    }

    /**
     * EJECUCIÓN FINAL: Guardar todo en la BD.
     * Llamado por el padre después de crear el registro principal.
     */
    public function commit($data)
    {
        // 1. Datos del nuevo registro creado
        $newModelId = $data['id'];
        $newModelName = $data['name'] ?? null;

        // 2. Instanciar el modelo real
        if (!class_exists($this->modelClass))
            return;
        $model = $this->modelClass::find($newModelId);

        if (!$model)
            return;

        // 3. Preparar dependencias
        $uploader = app(UploadFileAction::class);
        $linker = app(LinkFileAction::class);
        $folderMaker = app(GetOrCreateFolderAction::class);

        // Obtener el ID del Área para los permisos
        $areaId = Area::where('slug', $this->areaSlug)->value('id');

        // 4. Gestión de Carpeta Automática (Opcional)
        $folderId = null;
        if ($newModelName) {
            $folder = $folderMaker->execute($newModelName, null);
            $folderId = $folder->id;
        }

        // 5. Procesar UPLOADS NUEVOS (Físicos)
        if (!empty($this->uploads)) {
            $uploader->execute(
                $this->uploads,
                $model,
                $folderId,
                $areaId // Guardar contexto de área
            );
        }

        // 6. Procesar VÍNCULOS DE CLOUD (Existentes)
        if (!empty($this->pendingLinkIds)) {
            foreach ($this->pendingLinkIds as $fileId) {
                $file = File::find($fileId);
                if ($file) {
                    $linker->execute($file, $model, $areaId);
                }
            }
        }

        // 7. Avisar al padre que terminamos
        $this->dispatch('attachments-committed');
    }

    public function render()
    {
        $areaId = Area::where('slug', $this->areaSlug)->value('id');

        return view('livewire.modules.cloud.components.model-attachments-creator', [
            'modelClass' => $this->modelClass,
            'areaId' => $areaId
        ]);
    }
}