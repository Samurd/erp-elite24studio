<?php

namespace App\Livewire\Modules\Cloud\Components;

use Livewire\Component;
use Illuminate\Database\Eloquent\Model;
use App\Models\File;
use App\Actions\Cloud\Files\DeleteFileAction;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ModelFiles extends Component
{
    use AuthorizesRequests;

    public $model;

    public function mount(Model $model)
    {
        $this->model = $model;
    }

    public function detach($fileId)
    {
        $file = File::find($fileId);

        if (!$file) {
            return;
        }

        // Check if user can update the file (which implies they can manage it)
        // OR check if they can update the model itself (contextual permission)
        // For now, we'll rely on the policy 'update' check or a specific check if needed.
        // But typically, detaching is about the PARENT model.
        // However, the user request specifically mentioned "dependiendo de los permisos... si muestra tal file".
        // Let's stick to the FilePolicy 'update' for now as a safe default for "managing" the file connection,
        // but often detaching is a property of the container.
        // Let's use a custom check: can they update the file OR can they update the model?
        // Actually, the prompt said "dependiendo de los permisos... tambien los botones".

        $this->authorize('update', $file);

        $this->model->files()->detach($fileId);
        $this->dispatch('notify', 'Archivo desvinculado correctamente');
    }

    public function delete($fileId, DeleteFileAction $deleter)
    {
        $file = File::find($fileId);

        if (!$file) {
            return;
        }

        $this->authorize('delete', $file);

        $deleter->execute($file);
        $this->dispatch('notify', 'Archivo eliminado del sistema permanentemente');
    }

    public function render()
    {
        // Filter files based on 'view' policy
        $files = $this->model->files()->latest()->get()->filter(function ($file) {
            return auth()->user()->can('view', $file);
        });

        return view('livewire.modules.cloud.components.model-files', [
            'files' => $files
        ]);
    }
}
