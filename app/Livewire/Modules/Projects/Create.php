<?php

namespace App\Livewire\Modules\Projects;

use App\Livewire\Forms\Modules\Projects\Form;
use App\Models\Contact;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\User;
use App\Models\Stage;
use App\Models\Team;
use Livewire\Component;
use Illuminate\Support\Collection;

class Create extends Component
{
    public Form $form;
    public $showStageForm = false;
    public $stage_name;
    public $stage_description;
    public $temporaryStages; // Almacenará las etapas temporales
    public $existingStages; // Etapas existentes en la base de datos

    protected $listeners = [
        // 1. ESCUCHAR AL HIJO: Cuando termine de subir los archivos
        'attachments-committed' => 'finishCreation'
    ];

    protected $rules = [
        'stage_name' => 'required|string|max:255',
        'stage_description' => 'nullable|string',
    ];

    public function mount()
    {
        $this->temporaryStages = collect();
        $this->existingStages = Stage::orderBy('name')->get();
    }

    public function save()
    {
        // Save project first without redirecting
        $project = $this->form->storeWithoutRedirect();

        // Array to store mapping of temporary IDs to real IDs
        $tempIdMapping = [];

        // Save all temporary stages with project_id and collect their IDs
        foreach ($this->temporaryStages as $stage) {
            $newStage = Stage::create([
                'project_id' => $project->id,
                'name' => $stage['name'],
                'description' => $stage['description'],
            ]);

            // Store mapping from temporary ID to real ID
            $tempIdMapping[$stage['id']] = $newStage->id;
        }

        // Check if an initial stage was selected
        if ($this->form->initial_stage_id) {
            // If it's a temporary stage, map it to the real ID
            if (strpos($this->form->initial_stage_id, 'temp_') === 0) {
                $realStageId = $tempIdMapping[$this->form->initial_stage_id] ?? null;
                if ($realStageId) {
                    $project->update(['current_stage_id' => $realStageId]);
                }
            } else {
                // It's an existing stage
                $project->update(['current_stage_id' => $this->form->initial_stage_id]);
            }
        }

        if ($this->temporaryStages->count() > 0) {
            session()->flash('success', 'Proyecto y etapas creados exitosamente.');
        } else {
            session()->flash('success', 'Proyecto creado exitosamente.');
        }



        $this->dispatch('commit-attachments', [
            'id' => $project->id,
            'name' => $project->name
        ]);


    }


    /**
     * Este método se ejecuta automáticamente cuando el hijo termina
     */
    public function finishCreation()
    {
        // 4. Ahora sí, redirigimos o mostramos éxito
        session()->flash('success', 'Registro creado y archivos adjuntados correctamente.');

        return redirect()->route('projects.index');
    }


    public function toggleStageForm()
    {
        $this->showStageForm = !$this->showStageForm;
    }

    public function saveStage()
    {
        // Validar solo los campos de la etapa
        $this->validate([
            'stage_name' => 'required|string|max:255',
            'stage_description' => 'nullable|string',
        ]);

        // Create a temporary ID for stage
        $tempId = 'temp_' . uniqid();

        // Add to temporary stages collection
        $this->temporaryStages->push([
            'id' => $tempId,
            'name' => $this->stage_name,
            'description' => $this->stage_description,
        ]);

        $this->reset(['stage_name', 'stage_description']);
        $this->showStageForm = false;

        session()->flash('success', 'Etapa agregada exitosamente.');
    }

    public function cancelStage()
    {
        $this->reset(['stage_name', 'stage_description']);
        $this->showStageForm = false;
    }

    public function editStage($id)
    {
        // Buscar en etapas temporales primero
        $stage = $this->temporaryStages->where('id', $id)->first();

        if (!$stage) {
            // Si no está en temporales, buscar en existentes
            $stage = $this->existingStages->where('id', $id)->first();
        }

        if ($stage) {
            $this->stage_name = $stage['name'];
            $this->stage_description = $stage['description'];
            $this->showStageForm = true;
        }
    }

    public function deleteStage($id)
    {
        // Eliminar de etapas temporales
        $this->temporaryStages = $this->temporaryStages->reject(function ($stage) use ($id) {
            return $stage['id'] === $id;
        });

        session()->flash('success', 'Etapa eliminada exitosamente.');
    }

    public function render()
    {
        // Obtener opciones para los filtros usando TagCategory
        $statusCategory = TagCategory::where('slug', 'estado_proyecto')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        $projectTypeCategory = TagCategory::where('slug', 'tipo_proyecto')->first();
        $projectTypeOptions = $projectTypeCategory ? Tag::where('category_id', $projectTypeCategory->id)->get() : collect();

        // Obtener contactos
        $contacts = Contact::orderBy('name')->get();

        // Obtener usuarios responsables
        $users = User::orderBy('name')->get();

        // Obtener equipos
        $teams = Team::orderBy('name')->get();

        // Combinar etapas existentes y temporales para mostrar en el selector
        $allStages = $this->existingStages->concat($this->temporaryStages);

        return view('livewire.modules.projects.create', [
            'statusOptions' => $statusOptions,
            'projectTypeOptions' => $projectTypeOptions,
            'contacts' => $contacts,
            'users' => $users,
            'teams' => $teams,
            'stages' => $allStages,
        ]);
    }
}
