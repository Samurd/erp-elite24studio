<?php

namespace App\Livewire\Forms\Modules\Finances\Audits;

use App\Models\Audit;
use App\Models\Folder;
use App\Models\File;
use Livewire\Attributes\Validate;
use Livewire\Form as LivewireForm;
use Illuminate\Support\Facades\Storage;

class Form extends LivewireForm
{
    public ?Audit $audit = null;

    #[Validate('required|date')]
    public $date_register = '';

    #[Validate('required|date')]
    public $date_audit = '';

    #[Validate('required|integer|min:0')]
    public $objective = 0;

    #[Validate('required|exists:tags,id')]
    public $type_id = '';

    #[Validate('nullable|string|max:255')]
    public $place = '';

    #[Validate('required|exists:tags,id')]
    public $status_id = '';

    #[Validate('nullable|string')]
    public $observations = '';


    public function setAudit(Audit $audit)
    {
        $this->audit = $audit;
        $this->date_register = $audit->date_register->format('Y-m-d');
        $this->date_audit = $audit->date_audit->format('Y-m-d');
        $this->objective = $audit->objective;
        $this->type_id = $audit->type_id;
        $this->place = $audit->place;
        $this->status_id = $audit->status_id;
        $this->observations = $audit->observations;
    }

    public function store()
    {
        $this->validate();

        $audit = Audit::create([
            'date_register' => $this->date_register,
            'date_audit' => $this->date_audit,
            'objective' => $this->objective,
            'type_id' => $this->type_id,
            'place' => $this->place,
            'status_id' => $this->status_id,
            'observations' => $this->observations,
        ]);


        return $audit;
    }

    public function update()
    {
        $this->validate();

        $this->audit->update([
            'date_register' => $this->date_register,
            'date_audit' => $this->date_audit,
            'objective' => $this->objective,
            'type_id' => $this->type_id,
            'place' => $this->place,
            'status_id' => $this->status_id,
            'observations' => $this->observations,
        ]);
    }

}
