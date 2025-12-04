<?php

namespace App\Livewire\Modules\Licenses;

use App\Livewire\Forms\Modules\Licenses\Form;
use App\Models\License;
use App\Models\Project;
use App\Models\Tag;
use App\Models\TagCategory;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;

class Update extends Component
{
    use WithFileUploads;

    public Form $form;
    public License $license;

    // File Management Properties
    public $tempFiles = [];
    public $tempFile;
    public $iteration = 1;
    public $linkedFileIds = [];
    public $selectedFolderId = null;
    public $selectingFolderMode = false;

    // Propiedades para el formulario de actualización de estado
    public $update_date;
    public $responsible_id;
    public $new_status_id;
    public $description;
    public $internal_notes;
    public $showStatusForm = false;

    protected $listeners = [
        'folder-selected' => 'handleFolderSelected',
        'file-selected' => 'handleFileSelected',
    ];

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

    public function mount(License $license)
    {
        $this->license = $license;
        $this->form->setLicense($license);
    }

    public function save()
    {
        // Pasar archivos al Form Object
        $this->form->files = $this->tempFiles;
        $this->form->selected_folder_id = $this->selectedFolderId;
        $this->form->linked_file_ids = $this->linkedFileIds;

        $this->form->update($this->license);
    }

    /* ----------------- File Management Methods ----------------- */

    public function openFolderSelector()
    {
        $this->selectingFolderMode = true;
        $this->dispatch('open-folder-selector');
    }

    public function openFileSelector()
    {
        $this->selectingFolderMode = false;
        $this->dispatch('open-folder-selector', mode: 'file');
    }

    public function handleFolderSelected($data)
    {
        // Si estamos seleccionando carpeta destino para nuevos archivos
        if ($this->selectingFolderMode) {
            $this->selectedFolderId = $data['id'];
            $this->dispatch(
                'notify',
                type: 'success',
                message: 'Carpeta de destino seleccionada: ' . $data['name']
            );
        }
    }

    public function handleFileSelected($data)
    {
        // Si estamos vinculando un archivo existente
        if (!$this->selectingFolderMode) {
            if (!in_array($data['id'], $this->linkedFileIds)) {
                $this->linkedFileIds[] = $data['id'];
                $this->dispatch(
                    'notify',
                    type: 'success',
                    message: 'Archivo vinculado: ' . $data['name']
                );
            } else {
                $this->dispatch(
                    'notify',
                    type: 'warning',
                    message: 'Este archivo ya está vinculado'
                );
            }
        }
    }

    public function removeLinkedFile($fileId)
    {
        $this->linkedFileIds = array_diff($this->linkedFileIds, [$fileId]);
    }

    /* ----------------- Status Updates Methods ----------------- */

    public function addStatusUpdate()
    {
        $this->validate([
            'update_date' => 'required|date',
            'responsible_id' => 'required|exists:users,id',
            'new_status_id' => 'required|exists:tags,id',
            'description' => 'required|string',
            'internal_notes' => 'nullable|string',
        ]);

        // Guardar el estado anterior antes de actualizar
        $oldStatusId = $this->license->status_id;

        // Crear la actualización de estado
        \App\Models\LicenseStatusUpdate::create([
            'license_id' => $this->license->id,
            'date' => $this->update_date,
            'responsible_id' => $this->responsible_id,
            'status_id' => $this->new_status_id,
            'description' => $this->description,
            'internal_notes' => $this->internal_notes,
            'created_by' => Auth::id(),
        ]);

        // Actualizar el estado de la licencia
        $this->license->update([
            'status_id' => $this->new_status_id,
        ]);

        // Actualizar el formulario principal con el nuevo estado
        $this->form->status_id = $this->new_status_id;

        // Resetear el formulario
        $this->reset(['update_date', 'responsible_id', 'new_status_id', 'description', 'internal_notes']);
        $this->showStatusForm = false;

        // Recargar las actualizaciones de estado
        $this->license->load(['statusUpdates.responsible', 'statusUpdates.status']);

        session()->flash('success', 'Actualización de estado agregada exitosamente.');
    }

    public function cancelStatusUpdate()
    {
        $this->reset(['update_date', 'responsible_id', 'new_status_id', 'description', 'internal_notes']);
        $this->showStatusForm = false;
    }

    public function toggleStatusForm()
    {
        $this->showStatusForm = !$this->showStatusForm;
    }

    public function getUsers()
    {
        // Obtener permisos del área RRHH
        $rrhhPermissionIds = \App\Models\Permission::whereHas("area", function ($query) {
            $query->where("slug", "licencias");
        })->pluck("id");

        return \App\Models\User::whereHas("roles.permissions", function ($query) use ($rrhhPermissionIds) {
            $query->whereIn("permissions.id", $rrhhPermissionIds);
        })
            ->orderBy("name")
            ->get();
    }

    public function render()
    {
        // Obtener opciones para los filtros usando TagCategory
        $licenseTypeCategory = TagCategory::where('slug', 'tipo_licencia')->first();
        $licenseTypeOptions = $licenseTypeCategory ? Tag::where('category_id', $licenseTypeCategory->id)->get() : collect();

        $statusCategory = TagCategory::where('slug', 'estado_licencia')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        // Obtener proyectos
        $projects = Project::orderBy('name')->get();

        // Obtener usuarios para el selector de responsables
        $users = $this->getUsers();

        // Cargar la licencia con sus relaciones
        $this->license->load(['statusUpdates.responsible', 'statusUpdates.status']);

        return view('livewire.modules.licenses.update', [
            'licenseTypeOptions' => $licenseTypeOptions,
            'statusOptions' => $statusOptions,
            'projects' => $projects,
            'license' => $this->license,
            'users' => $users,
            'isEdit' => true,
        ]);
    }
}
