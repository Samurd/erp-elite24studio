<?php

namespace App\Livewire\Components;

use App\Services\FileUploadManager;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;
use Livewire\WithFileUploads;

class FileUpload extends Component
{
    use WithFileUploads;

    // Configuración del componente
    public ?Model $model = null; // Modelo al que vincular archivos (null en creación)
    public string $modelClass; // Clase del modelo (string)
    public string $defaultFolderName; // Carpeta por defecto
    public bool $allowMultiple = true; // Permitir múltiples archivos
    public int $maxFileSize = 2048; // Tamaño máximo en KB
    public ?string $acceptedTypes = null; // Tipos aceptados (ej: "image/*,application/pdf")

    // Estado del componente
    public ?int $selectedFolderId = null; // Carpeta específica seleccionada
    public ?string $selectedFolderPath = null;
    public array $tempFiles = []; // Archivos temporales cargados
    public $existingFiles = []; // Archivos ya guardados en BD

    protected $listeners = [
        'folder-selected' => 'handleFolderSelected',
        'file-selected' => 'handleFileSelected',
    ];

    public function mount()
    {
        // Si hay un modelo, cargar sus archivos existentes
        if ($this->model && $this->model->exists) {
            $this->loadExistingFiles();
        }

        // En modo creación, inicializar arrays vacíos
        if (!$this->model || !$this->model->exists) {
            $this->tempFiles = [];
            $this->existingFiles = collect();
        }
    }

    /**
     * Cargar archivos existentes del modelo
     */
    public function loadExistingFiles()
    {
        if ($this->model && method_exists($this->model, 'getFilesWithRelations')) {
            $this->existingFiles = $this->model->getFilesWithRelations();
        } elseif ($this->model && method_exists($this->model, 'files')) {
            $this->existingFiles = $this->model->files()->with(['folder', 'user'])->get();
        }
    }

    /**
     * Procesar archivos subidos
     */
    public function updatedTempFiles()
    {
        // Validar archivos
        $this->validate([
            'tempFiles.*' => "file|max:{$this->maxFileSize}",
        ]);

        // Si el modelo ya existe, subir inmediatamente
        if ($this->model && $this->model->exists) {
            $this->uploadFiles();
        }
        // Si no existe, los archivos se quedan en tempFiles hasta que se cree el modelo
    }

    /**
     * Subir archivos pendientes después de crear el modelo
     * Este método debe ser llamado desde el componente padre después de crear el registro
     */
    public function uploadPendingFiles($model)
    {
        if (empty($this->tempFiles)) {
            return 0;
        }

        $this->model = $model;
        $fileManager = app(FileUploadManager::class);
        $uploadedCount = 0;

        foreach ($this->tempFiles as $tempFile) {
            if ($tempFile && $tempFile instanceof \Illuminate\Http\UploadedFile) {
                $file = $fileManager->uploadFile(
                    $tempFile,
                    $this->model,
                    $this->selectedFolderId,
                    $this->defaultFolderName
                );

                if ($file) {
                    $uploadedCount++;
                }
            }
        }

        // Limpiar archivos temporales
        $this->tempFiles = [];

        // Recargar archivos existentes
        $this->loadExistingFiles();

        return $uploadedCount;
    }

    /**
     * Subir archivos al servidor
     */
    public function uploadFiles()
    {
        if (empty($this->tempFiles)) {
            return;
        }

        if (!$this->model || !$this->model->exists) {
            session()->flash('error', 'Debe guardar el registro antes de subir archivos.');
            return;
        }

        $fileManager = app(FileUploadManager::class);
        $uploadedCount = 0;

        foreach ($this->tempFiles as $tempFile) {
            if ($tempFile && $tempFile instanceof \Illuminate\Http\UploadedFile) {
                $file = $fileManager->uploadFile(
                    $tempFile,
                    $this->model,
                    $this->selectedFolderId,
                    $this->defaultFolderName
                );

                if ($file) {
                    $uploadedCount++;
                }
            }
        }

        // Limpiar archivos temporales
        $this->tempFiles = [];

        // Recargar archivos existentes
        $this->loadExistingFiles();

        // Emitir evento
        $this->dispatch('files-uploaded', ['count' => $uploadedCount]);

        session()->flash('success', "{$uploadedCount} archivo(s) subido(s) correctamente.");
    }

    /**
     * Eliminar archivo temporal
     */
    public function removeTempFile($index)
    {
        if (isset($this->tempFiles[$index])) {
            // Si es un archivo temporal de Livewire, eliminarlo
            if ($this->tempFiles[$index] instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                $this->tempFiles[$index]->delete();
            }

            unset($this->tempFiles[$index]);
            $this->tempFiles = array_values($this->tempFiles); // Reindexar
        }
    }

    /**
     * Eliminar archivo guardado
     */
    public function deleteStoredFile($fileId)
    {
        if (!$this->model || !$this->model->exists) {
            session()->flash('error', 'No se puede eliminar el archivo.');
            return;
        }

        $fileManager = app(FileUploadManager::class);
        $success = $fileManager->deleteFile($fileId, $this->model);

        if ($success) {
            // Recargar archivos
            $this->loadExistingFiles();

            // Emitir evento
            $this->dispatch('file-deleted', ['fileId' => $fileId]);

            session()->flash('success', 'Archivo eliminado correctamente.');
        } else {
            session()->flash('error', 'No se pudo eliminar el archivo.');
        }
    }

    /**
     * Abrir modal de selección de carpeta
     */
    public function openFolderSelector()
    {
        $this->dispatch('open-folder-selector', ['allow_files' => false]);
    }

    /**
     * Abrir modal para seleccionar archivo existente del Cloud
     */
    public function openFileSelector()
    {
        $this->dispatch('open-folder-selector', ['allow_files' => true]);
    }

    /**
     * Manejar selección de carpeta
     */
    public function handleFolderSelected($data)
    {
        $this->selectedFolderId = $data['id'];
        $this->selectedFolderPath = $data['path'];

        $this->dispatch('show-notification', [
            'type' => 'success',
            'message' => "Carpeta seleccionada: {$data['name']}",
        ]);
    }

    /**
     * Manejar selección de archivo existente
     */
    public function handleFileSelected($data)
    {
        if (!$this->model || !$this->model->exists) {
            session()->flash('error', 'Debe guardar el registro antes de vincular archivos.');
            return;
        }

        $fileManager = app(FileUploadManager::class);
        $success = $fileManager->attachExistingFile($data['id'], $this->model);

        if ($success) {
            $this->loadExistingFiles();

            $this->dispatch('show-notification', [
                'type' => 'success',
                'message' => "Archivo '{$data['name']}' vinculado correctamente.",
            ]);
        } else {
            $this->dispatch('show-notification', [
                'type' => 'error',
                'message' => 'No se pudo vincular el archivo.',
            ]);
        }
    }

    /**
     * Limpiar carpeta seleccionada
     */
    public function clearSelectedFolder()
    {
        $this->selectedFolderId = null;
        $this->selectedFolderPath = null;
    }

    /**
     * Obtener archivos temporales para la vista
     */
    public function getTempFilesForView()
    {
        return collect($this->tempFiles)->map(function ($file, $index) {
            if ($file instanceof \Illuminate\Http\UploadedFile) {
                return [
                    'index' => $index,
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                ];
            }
            return null;
        })->filter();
    }

    public function render()
    {
        return view('livewire.components.file-upload', [
            'tempFilesView' => $this->getTempFilesForView(),
        ]);
    }
}
