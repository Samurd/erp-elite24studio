<?php

namespace App\Livewire\Modules\Finances\Norms;

use App\Livewire\Forms\Modules\Finances\Norms\Form;
use App\Models\Norm;
use App\Services\FileUploadManager;
use Livewire\Component;
use Livewire\WithFileUploads;

class Update extends Component
{
    use WithFileUploads;

    public Form $form;
    public Norm $norm;

    // File Management Properties
    public $tempFiles = [];
    public $tempFile;
    public $iteration = 1;
    public $linkedFileIds = [];

    protected $listeners = [
        'file-selected' => 'handleFileSelected',
    ];

    public function mount(Norm $norm)
    {
        $this->norm = $norm;
        $this->form->setNorm($norm);
    }

    public function updatedTempFile()
    {
        $this->validate([
            'tempFile' => 'file|max:10240', // 10MB
        ]);

        $this->tempFiles[] = $this->tempFile;
        $this->tempFile = null;
        $this->iteration++;
    }

    public function removeTempFile($index)
    {
        if (isset($this->tempFiles[$index])) {
            unset($this->tempFiles[$index]);
            $this->tempFiles = array_values($this->tempFiles);
        }
    }

    public function openFileSelector()
    {
        $this->dispatch('open-folder-selector', ['mode' => 'file']);
    }

    public function handleFileSelected($data)
    {
        if (!in_array($data['id'], $this->linkedFileIds)) {
            $this->linkedFileIds[] = $data['id'];
            $this->dispatch(
                'notify',
                type: 'success',
                message: "Archivo '{$data['name']}' vinculado"
            );
        }
    }

    public function removeLinkedFile($fileId)
    {
        $this->linkedFileIds = array_values(
            array_filter($this->linkedFileIds, fn($id) => $id != $fileId)
        );
    }

    public function removeFile($fileId)
    {
        $fileManager = app(FileUploadManager::class);
        if ($fileManager->deleteFile($fileId, $this->norm)) {
            $this->norm->refresh(); // Reload relation
            session()->flash('success', 'Archivo eliminado correctamente');
        } else {
            session()->flash('error', 'Error al eliminar el archivo');
        }
    }

    public function save()
    {
        // Pass files to form
        $this->form->files = $this->tempFiles;
        $this->form->linked_file_ids = $this->linkedFileIds;

        $this->form->update();

        session()->flash('success', 'Norma actualizada exitosamente');

        return redirect()->route('finances.norms.index');
    }

    public function render()
    {
        return view('livewire.modules.finances.norms.create', [
            'isEdit' => true,
        ]);
    }
}
