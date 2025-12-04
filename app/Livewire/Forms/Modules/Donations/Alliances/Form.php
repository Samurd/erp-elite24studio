<?php

namespace App\Livewire\Forms\Modules\Donations\Alliances;

use Livewire\Attributes\Validate;
use Livewire\Form as LivewireForm;
use App\Models\Alliance;

class Form extends LivewireForm
{
    public ?Alliance $alliance = null;

    #[Validate('required|string|max:255')]
    public $name = '';

    #[Validate('nullable|exists:tags,id')]
    public $type_id = null;

    #[Validate('nullable|date')]
    public $start_date = '';

    #[Validate('nullable|integer|min:1')]
    public $validity = null;

    #[Validate('boolean')]
    public $certified = false;

    public function setAlliance(Alliance $alliance)
    {
        $this->alliance = $alliance;
        $this->name = $alliance->name;
        $this->type_id = $alliance->type_id;
        $this->start_date = $alliance->start_date ? $alliance->start_date->format('Y-m-d') : '';
        $this->validity = $alliance->validity;
        $this->certified = $alliance->certified;
    }

    public function store()
    {
        $this->validate();

        Alliance::create([
            'name' => $this->name,
            'type_id' => $this->type_id,
            'start_date' => $this->start_date ?: null,
            'validity' => $this->validity,
            'certified' => $this->certified,
        ]);
    }

    public function update()
    {
        $this->validate();

        $this->alliance->update([
            'name' => $this->name,
            'type_id' => $this->type_id,
            'start_date' => $this->start_date ?: null,
            'validity' => $this->validity,
            'certified' => $this->certified,
        ]);
    }
}
