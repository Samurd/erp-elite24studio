<?php

namespace App\Livewire\Forms\Modules\Worksites;

use Livewire\Attributes\Validate;
use Livewire\Form as LivewireForm;
use App\Models\Worksite;
use App\Models\Tag;
use App\Models\TagCategory;
use Illuminate\Support\Facades\Auth;

class Form extends LivewireForm
{
    public $project_id;
    public $name;
    public $type_id;
    public $status_id;
    public $responsible_id;
    public $address;
    public $start_date;
    public $end_date;

    protected $rules = [
        'project_id' => 'required|exists:projects,id',
        'name' => 'required|string|max:255',
        'type_id' => 'nullable|exists:tags,id',
        'status_id' => 'nullable|exists:tags,id',
        'responsible_id' => 'nullable|exists:users,id',
        'address' => 'nullable|string|max:500',
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date|after_or_equal:start_date',
    ];

    public function store()
    {
        $this->validate();

        $worksite = Worksite::create([
            'project_id' => $this->project_id,
            'name' => $this->name,
            'type_id' => $this->type_id,
            'status_id' => $this->status_id,
            'responsible_id' => $this->responsible_id,
            'address' => $this->address,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
        ]);

        session()->flash('success', 'Obra creada exitosamente.');

        return redirect()->route('worksites.index');
    }

    public function update(Worksite $worksite)
    {
        $this->validate();

        $worksite->update([
            'project_id' => $this->project_id,
            'name' => $this->name,
            'type_id' => $this->type_id,
            'status_id' => $this->status_id,
            'responsible_id' => $this->responsible_id,
            'address' => $this->address,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
        ]);

        session()->flash('success', 'Obra actualizada exitosamente.');

        return redirect()->route('worksites.index');
    }

    public function setWorksite(Worksite $worksite)
    {
        $this->project_id = $worksite->project_id;
        $this->name = $worksite->name;
        $this->type_id = $worksite->type_id;
        $this->status_id = $worksite->status_id;
        $this->responsible_id = $worksite->responsible_id;
        $this->address = $worksite->address;
        $this->start_date = $worksite->start_date?->format('Y-m-d');
        $this->end_date = $worksite->end_date?->format('Y-m-d');
    }
}
