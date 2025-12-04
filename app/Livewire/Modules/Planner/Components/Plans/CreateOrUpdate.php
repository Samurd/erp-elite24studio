<?php

namespace App\Livewire\Modules\Planner\Components\Plans;

use App\Livewire\Forms\Modules\Planner\Plans\Form;
use App\Models\Plan;
use App\Models\Team;
use Livewire\Component;

class CreateOrUpdate extends Component
{

    protected $listeners = [
        'open-create-plan-modal' => 'openModal',
        'close-create-plan-modal' => 'closeModal',
        'open-edit-plan-modal' => 'openEditModal'
    ];

    public $isEdit = false;

    public $showModal = false;

    public Form $form;

    public $teams = [];


    public function mount()
    {
        $this->teams = Team::all();
    }

    public function openEditModal($planId)
    {
        $this->isEdit = true;
        $this->form->reset();
        $this->form->clearValidation();

        // Refresh teams list
        $this->teams = Team::all();

        $plan = Plan::find($planId);
        $this->form->setPlan($plan);
        $this->showModal = true;

    }

    public function openModal($projectId = null)
    {
        $this->isEdit = false;
        $this->form->reset();
        $this->form->clearValidation();

        // If projectId is provided, set it in the form and inherit team_id from project
        if ($projectId) {
            $this->form->project_id = $projectId;

            // Get the project and inherit its team_id
            $project = \App\Models\Project::find($projectId);
            if ($project && $project->team_id) {
                $this->form->team_id = $project->team_id;
            }
        }

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->form->reset();
    }


    public function save()
    {
        if ($this->isEdit) {
            $this->form->update();
        } else {
            $this->form->store();
        }

        $this->dispatch('refresh-plans');
        $this->closeModal();
    }

    public function render()
    {
        return view('livewire.modules.planner.components.plans.create-or-update');
    }
}
