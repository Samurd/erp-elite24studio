<?php

namespace App\Livewire\Components;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Services\FileUploadManager;
use App\Models\File;
use Illuminate\Support\Facades\Storage;

class FileManager extends Component
{
    use WithFileUploads;

    // Props públicas (pasadas desde el padre)
    public $model = null;           // Instancia del modelo (null en creación)
    public $modelClass;             // Clase del modelo (ej: "App\Models\Payroll")
    public $defaultFolder;          // Carpeta por defecto (ej: "payrolls")
    public $isEdit = false;         // Boolean para modo edición

    // Estado interno del componente
    public $tempFiles = [];         // Archivos temporales a subir
    public $linkedFileIds = [];     // IDs de archivos existentes a vincular
    public $selectedFolderId = null; // Carpeta específica seleccionada
    public $selectingFolderMode = false; // Flag para diferenciar selección vs navegación

    protected $listeners = [
        'folder-selected' => 'handleFolderSelected',
        'file-selected' => 'handleFileSelected',
        'save-files' => 'saveFiles', // Evento para guardar archivos después de crear/editar modelo
    ];

    /**
     * Abre el modal de selección de carpeta
     */
    public function openFolderSelector()
    {
        $this->selectingFolderMode = true;
        $this->dispatch('open-folder-selector', ['mode' => 'folder']);
    }

    /**
     * Abre el modal de selección de archivo
     */
    public function openFileSelector()
    {
        $this->selectingFolderMode = false;
        $this->dispatch('open-folder-selector', ['mode' => 'file']);
    }

    /**
     * Maneja la selección de una carpeta
     */
    public function handleFolderSelected($data)
    {
        // Solo procesar si estamos en modo de selección de carpeta de destino
        if (!$this->selectingFolderMode) {
            return;
        }

        $this->selectedFolderId = $data['id'];

        $this->dispatch('show-notification', [
            'type' => 'success',
            'message' => "Carpeta de destino seleccionada: {$data['name']}",
        ]);

        $this->selectingFolderMode = false;
    }

    /**
     * Maneja la selección de un archivo existente
     */
    public function handleFileSelected($data)
    {
        if (!in_array($data['id'], $this->linkedFileIds)) {
            $this->linkedFileIds[] = $data['id'];

            $this->dispatch('show-notification', [
                'type' => 'success',
                'message' => "Archivo '{$data['name']}' agregado para vincular",
            ]);
        }
    }

    /**
     * Remueve un archivo de la lista de archivos a vincular
     */
    public function removeLinkedFile($fileId)
    {
        $this->linkedFileIds = array_values(array_filter($this->linkedFileIds, fn($id) => $id != $fileId));
    }

    /**
     * Guarda los archivos al modelo
     * Este método se llama mediante evento desde el componente padre
     */
    public function saveFiles($modelId)
    {
        // Obtener la instancia del modelo
        $modelClass = $this->modelClass;
        $model = $modelClass::find($modelId);

        if (!$model) {
            $this->dispatch('show-notification', [
                'type' => 'error',
                'message' => 'No se pudo encontrar el registro para guardar archivos',
            ]);
            return;
        }

        $fileManager = app(FileUploadManager::class);
        $filesUploaded = 0;
        $filesLinked = 0;

        // Subir archivos nuevos
        if (!empty($this->tempFiles)) {
            foreach ($this->tempFiles as $file) {
                if ($file && $file instanceof \Illuminate\Http\UploadedFile) {
                    $fileManager->uploadFile(
                        $file,
                        $model,
                        $this->selectedFolderId,
                        $this->defaultFolder
                    );
                    $filesUploaded++;
                }
            }
        }

        // Vincular archivos existentes
        if (!empty($this->linkedFileIds)) {
            foreach ($this->linkedFileIds as $fileId) {
                $fileManager->attachExistingFile($fileId, $model);
                $filesLinked++;
            }
        }

        // Limpiar estado
        $this->tempFiles = [];
        $this->linkedFileIds = [];
        $this->selectedFolderId = null;

        // Notificar éxito
        if ($filesUploaded > 0 || $filesLinked > 0) {
            $message = [];
            if ($filesUploaded > 0) {
                $message[] = "{$filesUploaded} archivo(s) subido(s)";
            }
            if ($filesLinked > 0) {
                $message[] = "{$filesLinked} archivo(s) vinculado(s)";
            }

            $this->dispatch('show-notification', [
                'type' => 'success',
                'message' => implode(' y ', $message),
            ]);
        }
    }

    /**
     * Obtiene los archivos ya guardados del modelo (solo en edición)
     */
    public function getExistingFilesProperty()
    {
        if (!$this->isEdit || !$this->model) {
            return collect();
        }

        return $this->model->getFilesWithRelations();
    }

    public function render()
    {
        return view('livewire.components.file-manager');
    }
}
