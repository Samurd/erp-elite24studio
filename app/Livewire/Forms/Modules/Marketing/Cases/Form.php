<?php

namespace App\Livewire\Forms\Modules\Marketing\Cases;

use Livewire\Attributes\Validate;
use Livewire\Form as LivewireForm;
use App\Models\CaseMarketing;

class Form extends LivewireForm
{
    #[Validate('required|string|max:255')]
    public $subject = '';

    #[Validate('nullable|exists:projects,id')]
    public $project_id = '';

    #[Validate('required|date')]
    public $date = '';

    #[Validate('required|string')]
    public $mediums = '';

    #[Validate('nullable|string')]
    public $description = '';

    #[Validate('nullable|exists:users,id')]
    public $responsible_id = '';

    #[Validate('nullable|exists:tags,id')]
    public $type_id = '';

    #[Validate('nullable|exists:tags,id')]
    public $status_id = '';

    public function store()
    {
        $this->validate();

        CaseMarketing::create([
            'subject' => $this->subject,
            'project_id' => $this->project_id ?: null,
            'date' => $this->date,
            'mediums' => $this->mediums,
            'description' => $this->description,
            'responsible_id' => $this->responsible_id ?: null,
            'type_id' => $this->type_id ?: null,
            'status_id' => $this->status_id ?: null,
        ]);

        $this->reset();
    }

    public function update(CaseMarketing $caseMarketing)
    {
        $this->validate();

        $caseMarketing->update([
            'subject' => $this->subject,
            'project_id' => $this->project_id ?: null,
            'date' => $this->date,
            'mediums' => $this->mediums,
            'description' => $this->description,
            'responsible_id' => $this->responsible_id ?: null,
            'type_id' => $this->type_id ?: null,
            'status_id' => $this->status_id ?: null,
        ]);
    }

    public function setCaseMarketing(CaseMarketing $caseMarketing)
    {
        $this->subject = $caseMarketing->subject;
        $this->project_id = $caseMarketing->project_id;
        $this->date = $caseMarketing->date->format('Y-m-d');
        $this->mediums = $caseMarketing->mediums;
        $this->description = $caseMarketing->description;
        $this->responsible_id = $caseMarketing->responsible_id;
        $this->type_id = $caseMarketing->type_id;
        $this->status_id = $caseMarketing->status_id;
    }
}
