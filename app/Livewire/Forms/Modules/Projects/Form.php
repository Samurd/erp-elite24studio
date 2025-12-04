<?php

namespace App\Livewire\Forms\Modules\Projects;

use Livewire\Attributes\Validate;
use Livewire\Form as LivewireForm;
use App\Models\Project;
use App\Models\Tag;
use App\Models\TagCategory;
use Illuminate\Support\Facades\Auth;

class Form extends LivewireForm
{
    public $name;
    public $description;
    public $direction;
    public $contact_id;
    public $status_id;
    public $project_type_id;
    public $current_stage_id;
    public $initial_stage_id;
    public $responsible_id;
    public $team_id;

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'direction' => 'nullable|string|max:255',
        'contact_id' => 'nullable|exists:contacts,id',
        'status_id' => 'nullable|exists:tags,id',
        'project_type_id' => 'nullable|exists:tags,id',
        'current_stage_id' => 'nullable',
        'initial_stage_id' => 'nullable',
        'responsible_id' => 'nullable|exists:users,id',
        'team_id' => 'nullable|exists:teams,id',
    ];

    public function store()
    {
        $this->validate();

        $project = Project::create([
            'name' => $this->name,
            'description' => $this->description,
            'direction' => $this->direction,
            'contact_id' => $this->contact_id ?: null,
            'status_id' => $this->status_id ?: null,
            'project_type_id' => $this->project_type_id ?: null,
            'current_stage_id' => $this->current_stage_id ?: null,
            'responsible_id' => $this->responsible_id ?: null,
            'team_id' => $this->team_id ?: null,
        ]);

        session()->flash('success', 'Proyecto creado exitosamente.');

        return redirect()->route('projects.index');
    }

    public function storeWithoutRedirect()
    {
        $this->validate();

        $project = Project::create([
            'name' => $this->name,
            'description' => $this->description,
            'direction' => $this->direction,
            'contact_id' => $this->contact_id ?: null,
            'status_id' => $this->status_id ?: null,
            'project_type_id' => $this->project_type_id ?: null,
            'current_stage_id' => $this->current_stage_id ?: null,
            'responsible_id' => $this->responsible_id ?: null,
            'team_id' => $this->team_id ?: null,
        ]);

        return $project;
    }

    public function update(Project $project)
    {
        $this->validate();

        $project->update([
            'name' => $this->name,
            'description' => $this->description,
            'direction' => $this->direction,
            'contact_id' => $this->contact_id ?: null,
            'status_id' => $this->status_id ?: null,
            'project_type_id' => $this->project_type_id ?: null,
            'current_stage_id' => $this->current_stage_id ?: null,
            'responsible_id' => $this->responsible_id ?: null,
            'team_id' => $this->team_id ?: null,
        ]);

        // Update all plans associated with this project to have the same team_id
        $project->plans()->update(['team_id' => $this->team_id]);

        session()->flash('success', 'Proyecto actualizado exitosamente.');

        return redirect()->route('projects.index');
    }

    public function setProject(Project $project)
    {
        $this->name = $project->name;
        $this->description = $project->description;
        $this->direction = $project->direction;
        $this->contact_id = $project->contact_id;
        $this->status_id = $project->status_id;
        $this->project_type_id = $project->project_type_id;
        $this->current_stage_id = $project->current_stage_id;
        $this->initial_stage_id = null; // Reset initial stage for edit mode
        $this->responsible_id = $project->responsible_id;
        $this->team_id = $project->team_id;
    }
}
