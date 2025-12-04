<?php

namespace App\Livewire\Forms\Modules\Worksites\Changes;

use Livewire\Attributes\Validate;
use Livewire\Form as LivewireForm;
use App\Models\Change;
use App\Models\Worksite;

class Form extends LivewireForm
{
    public $worksite_id;
    public $change_date;
    public $change_type_id;
    public $requested_by;
    public $description;
    public $budget_impact_id;
    public $status_id;
    public $approved_by;
    public $internal_notes;

    protected $rules = [
        'worksite_id' => 'required|exists:worksites,id',
        'change_date' => 'required|date',
        'change_type_id' => 'required|exists:tags,id',
        'requested_by' => 'nullable|string|max:255',
        'description' => 'required|string',
        'budget_impact_id' => 'nullable|exists:tags,id',
        'status_id' => 'nullable|exists:tags,id',
        'approved_by' => 'nullable|exists:users,id',
        'internal_notes' => 'nullable|string',
    ];

    public function store()
    {
        $this->validate();

        $change = Change::create([
            'worksite_id' => $this->worksite_id,
            'change_date' => $this->change_date,
            'change_type_id' => $this->change_type_id,
            'requested_by' => $this->requested_by,
            'description' => $this->description,
            'budget_impact_id' => $this->budget_impact_id,
            'status_id' => $this->status_id,
            'approved_by' => $this->approved_by,
            'internal_notes' => $this->internal_notes,
        ]);

        session()->flash('success', 'Cambio creado exitosamente.');

        return redirect()->route('worksites.show', $this->worksite_id);
    }

    public function update(Change $change)
    {
        $this->validate();

        $change->update([
            'worksite_id' => $this->worksite_id,
            'change_date' => $this->change_date,
            'change_type_id' => $this->change_type_id,
            'requested_by' => $this->requested_by,
            'description' => $this->description,
            'budget_impact_id' => $this->budget_impact_id,
            'status_id' => $this->status_id,
            'approved_by' => $this->approved_by,
            'internal_notes' => $this->internal_notes,
        ]);

        session()->flash('success', 'Cambio actualizado exitosamente.');

        return redirect()->route('worksites.show', $this->worksite_id);
    }

    public function setChange(Change $change)
    {
        $this->worksite_id = $change->worksite_id;
        $this->change_date = $change->change_date?->format('Y-m-d');
        $this->change_type_id = $change->change_type_id;
        $this->requested_by = $change->requested_by;
        $this->description = $change->description;
        $this->budget_impact_id = $change->budget_impact_id;
        $this->status_id = $change->status_id;
        $this->approved_by = $change->approved_by;
        $this->internal_notes = $change->internal_notes;
    }

    public function setWorksite(Worksite $worksite)
    {
        $this->worksite_id = $worksite->id;
    }
}