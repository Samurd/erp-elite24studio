<?php

namespace App\Livewire\Forms\Modules\Planner\Plans;

use App\Models\Plan;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Form as LivewireForm;

class Form extends LivewireForm
{
    public ?Plan $plan = null;

    #[Validate('required|string')]
    public $name;

    #[Validate('nullable|string')]
    public $description;

    #[Validate('nullable|integer')]
    public $team_id;

    #[Validate('nullable|integer')]
    public $project_id;


    public function setPlan(Plan $plan)
    {
        $this->plan = $plan;
        $this->name = $plan->name;
        $this->description = $plan->description;
        $this->team_id = $plan->team_id;
        $this->project_id = $plan->project_id;
    }


    public function update()
    {
        $this->validate();

        $this->plan->update([
            'name' => $this->name,
            'description' => $this->description,
            // team_id and project_id are not updatable - cannot change after plan creation
        ]);

        $this->reset();
    }


    public function store()
    {
        $this->validate();

        Plan::create([
            'name' => $this->name,
            'description' => $this->description,
            'owner_id' => Auth::user()->id,
            'team_id' => $this->team_id,
            'project_id' => $this->project_id,
        ]);

        $this->reset();
    }
}
