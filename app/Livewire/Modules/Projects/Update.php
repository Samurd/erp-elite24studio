<?php

namespace App\Livewire\Modules\Projects;

use App\Livewire\Forms\Modules\Projects\Form;
use App\Models\Project;
use App\Models\Contact;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\User;
use App\Models\Stage;
use App\Models\Team;
use Livewire\Component;

class Update extends Component
{
    public Form $form;
    public Project $project;
    public $showStageForm = false;
    public $stage_name;
    public $stage_description;
    public $stages;
    public $existingStages; // Etapas existentes en la base de datos

    protected $rules = [
        'stage_name' => 'required|string|max:255',
        'stage_description' => 'nullable|string',
    ];

    public function mount(Project $project)
    {
        $this->project = $project;
        $this->form->setProject($project);
        // Get all stages, including project's own stages
        $this->existingStages = Stage::orderBy('name')->get();
        $this->stages = $this->existingStages;
    }

    public function save()
    {
        return $this->saveProject();
    }

    public function saveProject()
    {
        $this->form->update($this->project);

        // Update stages list after project update
        $this->existingStages = Stage::orderBy('name')->get();
        $this->stages = $this->existingStages;

        session()->flash('success', 'Proyecto actualizado exitosamente.');

        return redirect()->route('projects.index');
    }

    public function toggleStageForm()
    {
        $this->showStageForm = !$this->showStageForm;
    }

    public function saveStage()
    {
        $this->validate();

        // Create stage directly in database since project already exists
        $stage = Stage::create([
            'project_id' => $this->project->id,
            'name' => $this->stage_name,
            'description' => $this->stage_description,
        ]);

        // Update stages list after adding new stage
        $this->existingStages = Stage::orderBy('name')->get();
        $this->stages = $this->existingStages;

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
        // Buscar en etapas existentes (no hay temporales en Update)
        $stage = $this->existingStages->where('id', $id)->first();

        if ($stage) {
            $this->stage_name = $stage->name;
            $this->stage_description = $stage->description;
            $this->showStageForm = true;
        }
    }

    public function deleteStage($id)
    {
        // Eliminar de etapas existentes (no hay temporales en Update)
        $stage = Stage::find($id);
        if ($stage) {
            $stage->delete();
            $this->existingStages = Stage::orderBy('name')->get();
            $this->stages = $this->existingStages;
        }

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

        // En Update solo mostramos etapas existentes (no hay temporales)
        $allStages = $this->existingStages;

        return view('livewire.modules.projects.create', [
            'statusOptions' => $statusOptions,
            'projectTypeOptions' => $projectTypeOptions,
            'contacts' => $contacts,
            'users' => $users,
            'teams' => $teams,
            'stages' => $allStages,
            'project' => $this->project,
            'existingStages' => $this->existingStages,
            'temporaryStages' => collect(), // Empty collection for consistency with view
        ]);
    }
}
