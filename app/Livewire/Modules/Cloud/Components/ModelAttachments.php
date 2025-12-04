<?php

namespace App\Livewire\Modules\Cloud\Components;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Database\Eloquent\Model;
use App\Actions\Cloud\Files\UploadFileAction;
use App\Actions\Cloud\Files\DeleteFileAction;
use App\Actions\Cloud\Folders\GetOrCreateFolderAction;
use App\Services\AreaPermissionService;
use App\Models\File;
use App\Models\Area; // <--- 1. NUEVO IMPORT

use Livewire\Attributes\On; // <--- 2. NUEVO IMPORT

class ModelAttachments extends Component
{
    use WithFileUploads;

    public $model;
    public $areaSlug;
    public $uploads = [];
    public $upload;

    #[On('file-linked')]
    public function refreshAttachments()
    {
        $this->model->refresh(); // Recargar relaciones
    }

    public function mount(Model $model, ?string $area = null)
    {
        $this->model = $model;
        $this->areaSlug = $area ?? $model->getTable();
    }

    protected function checkPermission(string $action)
    {
        if (!AreaPermissionService::canArea($action, $this->areaSlug)) {
            abort(403, "No tienes permiso.");
        }
    }

    public function updatedUpload()
    {
        $this->validate(['upload' => 'required|file|max:51200']);
        $this->uploads[] = $this->upload;
        $this->upload = null;
        $this->dispatch('clear-file-input');
    }

    public function removeUpload($index)
    {
        unset($this->uploads[$index]);
        $this->uploads = array_values($this->uploads);
    }

    public function save(UploadFileAction $uploader, GetOrCreateFolderAction $folderMaker)
    {
        if (empty($this->uploads))
            return;

        $this->checkPermission('update');

        // 2. NUEVO: Obtener el ID del Área
        $areaId = null;
        if ($this->areaSlug) {
            $areaId = Area::where('slug', $this->areaSlug)->value('id');
        }

        $folderId = null;
        if (isset($this->model->name)) {
            $folder = $folderMaker->execute($this->model->name, null);
            $folderId = $folder->id;
        }

        // 3. CAMBIO: Pasamos el $areaId como cuarto argumento
        $uploader->execute(
            $this->uploads,
            $this->model,
            $folderId,
            $areaId // <--- Enviamos el ID para guardarlo en la tabla pivote
        );

        $this->uploads = [];
        $this->dispatch('notify', 'Archivos guardados correctamente');
    }

    public function detach($fileId)
    {
        $this->checkPermission('update');
        $this->model->files()->detach($fileId);
        $this->dispatch('notify', 'Archivo desvinculado');
    }

    public function deleteFile($fileId, DeleteFileAction $deleter)
    {
        $this->checkPermission('update');

        $file = File::find($fileId);

        if (!$file)
            return;

        if ($file->user_id !== auth()->id() && !auth()->user()->can('cloud.delete')) {
            $this->dispatch('notify', 'No tienes permiso para eliminar este archivo del sistema. Solo puedes desvincularlo.', type: 'error');
            return;
        }

        $deleter->execute($file);
        $this->dispatch('notify', 'Archivo eliminado del sistema permanentemente');
    }

    public function openSelector()
    {
        $this->checkPermission('update');

        // 4. NUEVO: Obtenemos el ID del Área también aquí
        $areaId = Area::where('slug', $this->areaSlug)->value('id');

        $this->dispatch(
            'openFileSelector',
            type: get_class($this->model),
            id: $this->model->id,
            area_id: $areaId // <--- Pasamos el ID al modal para que LinkFileAction lo use
        );
    }

    public function render()
    {
        $areaId = Area::where('slug', $this->areaSlug)->value('id');

        return view('livewire.modules.cloud.components.model-attachments', [
            'files' => $this->model->files()->latest()->get(),
            'modelClass' => get_class($this->model),
            'modelId' => $this->model->id,
            'areaId' => $areaId
        ]);
    }
}