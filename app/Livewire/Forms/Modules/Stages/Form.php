<?php

namespace App\Livewire\Forms\Modules\Stages;

use Livewire\Attributes\Validate;
use Livewire\Form as LivewireForm;
use App\Models\Stage;

class Form extends LivewireForm
{
    public $name;
    public $description;

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
    ];

    public function store()
    {
        $this->validate();

        $stage = Stage::create([
            'name' => $this->name,
            'description' => $this->description,
        ]);

        session()->flash('success', 'Etapa creada exitosamente.');

        return redirect()->route('stages.index');
    }

    public function update(Stage $stage)
    {
        $this->validate();

        $stage->update([
            'name' => $this->name,
            'description' => $this->description,
        ]);

        session()->flash('success', 'Etapa actualizada exitosamente.');

        return redirect()->route('stages.index');
    }

    public function setStage(Stage $stage)
    {
        $this->name = $stage->name;
        $this->description = $stage->description;
    }
}