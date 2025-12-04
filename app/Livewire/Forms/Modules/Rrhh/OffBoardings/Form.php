<?php

namespace App\Livewire\Forms\Modules\Rrhh\OffBoardings;

use Livewire\Attributes\Validate;
use Livewire\Form as LivewireForm;
use App\Models\OffBoarding;

class Form extends LivewireForm
{
    public ?OffBoarding $offBoarding = null;

    #[Validate('required|exists:employees,id')]
    public $employee_id = null;

    #[Validate('nullable|exists:projects,id')]
    public $project_id = null;

    #[Validate('nullable|string')]
    public $reason = '';

    #[Validate('required|date')]
    public $exit_date = '';

    #[Validate('nullable|exists:tags,id')]
    public $status_id = null;

    #[Validate('nullable|exists:users,id')]
    public $responsible_id = null;

    public function setOffBoarding(OffBoarding $offBoarding)
    {
        $this->offBoarding = $offBoarding;
        $this->employee_id = $offBoarding->employee_id;
        $this->project_id = $offBoarding->project_id;
        $this->reason = $offBoarding->reason;
        $this->exit_date = $offBoarding->exit_date ? $offBoarding->exit_date->format('Y-m-d') : '';
        $this->status_id = $offBoarding->status_id;
        $this->responsible_id = $offBoarding->responsible_id;
    }

    public function store()
    {
        $this->validate();

        OffBoarding::create([
            'employee_id' => $this->employee_id,
            'project_id' => $this->project_id,
            'reason' => $this->reason,
            'exit_date' => $this->exit_date,
            'status_id' => $this->status_id,
            'responsible_id' => $this->responsible_id,
        ]);
    }

    public function update()
    {
        $this->validate();

        $this->offBoarding->update([
            'employee_id' => $this->employee_id,
            'project_id' => $this->project_id,
            'reason' => $this->reason,
            'exit_date' => $this->exit_date,
            'status_id' => $this->status_id,
            'responsible_id' => $this->responsible_id,
        ]);
    }
}
