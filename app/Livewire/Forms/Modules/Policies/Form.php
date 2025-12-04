<?php

namespace App\Livewire\Forms\Modules\Policies;

use App\Models\Policy;
use Livewire\Attributes\Validate;
use Livewire\Form as LivewireForm;

class Form extends LivewireForm
{
    public ?Policy $policy = null;

    #[Validate('required|string|max:255')]
    public $name = '';

    #[Validate('nullable|string')]
    public $description = '';

    #[Validate('required|exists:tags,id')]
    public $type_id = null;

    #[Validate('nullable|exists:tags,id')]
    public $status_id = null;

    #[Validate('required|date')]
    public $issued_at = '';

    #[Validate('nullable|date')]
    public $reviewed_at = '';

    #[Validate('nullable|exists:users,id')]
    public $assigned_to_id = null;

    // Propiedades para manejo de archivos
    #[Validate(['nullable', 'array'])]
    #[Validate(['files.*' => 'file|max:10240'])]
    public $files = [];

    public $selected_folder_id = null;
    public $linked_file_ids = [];

    public function setPolicy(Policy $policy)
    {
        $this->policy = $policy;
        $this->name = $policy->name;
        $this->description = $policy->description;
        $this->type_id = $policy->type_id;
        $this->status_id = $policy->status_id;
        $this->issued_at = $policy->issued_at ? $policy->issued_at->format('Y-m-d') : '';
        $this->reviewed_at = $policy->reviewed_at ? $policy->reviewed_at->format('Y-m-d') : '';
        $this->assigned_to_id = $policy->assigned_to_id;
    }

    public function store()
    {
        $this->validate();

        $policy = Policy::create([
            'name' => $this->name,
            'description' => $this->description,
            'type_id' => $this->type_id,
            'status_id' => $this->status_id,
            'issued_at' => $this->issued_at,
            'reviewed_at' => $this->reviewed_at,
            'assigned_to_id' => $this->assigned_to_id,
        ]);

        // Procesar archivos si hay
        if (!empty($this->files) || !empty($this->linked_file_ids)) {
            $this->processFiles($policy);
        }

        $this->reset();
        return $policy;
    }

    public function update()
    {
        $this->validate();

        $this->policy->update([
            'name' => $this->name,
            'description' => $this->description,
            'type_id' => $this->type_id,
            'status_id' => $this->status_id,
            'issued_at' => $this->issued_at,
            'reviewed_at' => $this->reviewed_at,
            'assigned_to_id' => $this->assigned_to_id,
        ]);

        // Procesar archivos si hay
        if (!empty($this->files) || !empty($this->linked_file_ids)) {
            $this->processFiles($this->policy);
        }

        $this->reset();
    }

    /**
     * Procesar archivos nuevos y vinculados
     */
    private function processFiles(Policy $policy)
    {
        $fileManager = app(\App\Services\FileUploadManager::class);

        // Subir archivos nuevos
        if (!empty($this->files)) {
            foreach ($this->files as $file) {
                if ($file && $file instanceof \Illuminate\Http\UploadedFile) {
                    $fileManager->uploadFile(
                        $file,
                        $policy,
                        $this->selected_folder_id,
                        'policies'
                    );
                }
            }
        }

        // Vincular archivos existentes
        if (!empty($this->linked_file_ids)) {
            foreach ($this->linked_file_ids as $fileId) {
                $fileManager->attachExistingFile($fileId, $policy);
            }
        }
    }
}
