<?php

namespace App\Livewire\Forms\Modules\Taxes;

use Livewire\Attributes\Validate;
use Livewire\Form as LivewireForm;
use App\Models\TaxRecord;

class Form extends LivewireForm
{
    public ?TaxRecord $taxRecord = null;


    #[Validate('nullable|exists:tags,id')]
    public $type_id = null;

    #[Validate('nullable|exists:tags,id')]
    public $status_id = null;

    #[Validate('required|string|max:255')]
    public $entity = '';

    #[Validate('required|integer|min:0')]
    public $base = 0;

    #[Validate('required|integer|min:0|max:100')]
    public $porcentage = 0;

    #[Validate('required|integer|min:0')]
    public $amount = 0;

    #[Validate('required|date')]
    public $date = '';

    #[Validate('nullable|string')]
    public $observations = '';

    public function setTaxRecord(TaxRecord $taxRecord)
    {
        $this->taxRecord = $taxRecord;
        $this->type_id = $taxRecord->type_id;
        $this->status_id = $taxRecord->status_id;
        $this->entity = $taxRecord->entity;
        $this->base = $taxRecord->base;
        $this->porcentage = $taxRecord->porcentage;
        $this->amount = $taxRecord->amount;
        $this->date = $taxRecord->date ? $taxRecord->date->format('Y-m-d') : '';
        $this->observations = $taxRecord->observations;
    }

    public function store()
    {
        $this->validate();

        $taxRecord = TaxRecord::create([
            'type_id' => $this->type_id,
            'status_id' => $this->status_id,
            'entity' => $this->entity,
            'base' => $this->base,
            'porcentage' => $this->porcentage,
            'amount' => $this->amount,
            'date' => $this->date,
            'observations' => $this->observations,
        ]);

        return $taxRecord;
    }

    public function update()
    {
        $this->validate();

        $this->taxRecord->update([
            'type_id' => $this->type_id,
            'status_id' => $this->status_id,
            'entity' => $this->entity,
            'base' => $this->base,
            'porcentage' => $this->porcentage,
            'amount' => $this->amount,
            'date' => $this->date,
            'observations' => $this->observations,
        ]);
    }
}