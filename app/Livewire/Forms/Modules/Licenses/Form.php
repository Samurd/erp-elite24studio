<?php

namespace App\Livewire\Forms\Modules\Licenses;

use Livewire\Attributes\Validate;
use Livewire\Form as LivewireForm;
use App\Models\License;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Services\FileUploadManager;
use Illuminate\Support\Facades\Auth;

class Form extends LivewireForm
{
    public $project_id;
    public $license_type_id;
    public $status_id;
    public $entity;
    public $company;
    public $eradicated_number;
    public $eradicatd_date;
    public $estimated_approval_date;
    public $expiration_date;
    public $requires_extension;
    public $observations;

    protected $rules = [
        'project_id' => 'nullable|exists:projects,id',
        'license_type_id' => 'required|exists:tags,id',
        'status_id' => 'required|exists:tags,id',
        'entity' => 'required|string|max:255',
        'company' => 'required|string|max:255',
        'eradicated_number' => 'nullable|string|max:255',
        'eradicatd_date' => 'nullable|date',
        'estimated_approval_date' => 'nullable|date|after_or_equal:eradicatd_date',
        'expiration_date' => 'nullable|date|after:estimated_approval_date',
        'requires_extension' => 'boolean',
        'observations' => 'nullable|string',
    ];

    public function store()
    {
        $this->validate();

        $license = License::create([
            'project_id' => $this->project_id,
            'license_type_id' => $this->license_type_id,
            'status_id' => $this->status_id,
            'entity' => $this->entity,
            'company' => $this->company,
            'eradicated_number' => $this->eradicated_number,
            'eradicatd_date' => $this->eradicatd_date,
            'estimated_approval_date' => $this->estimated_approval_date,
            'expiration_date' => $this->expiration_date,
            'requires_extension' => $this->requires_extension,
            'observations' => $this->observations,
        ]);


        return $license;
    }

    public function update(License $license)
    {
        $this->validate();

        $license->update([
            'project_id' => $this->project_id,
            'license_type_id' => $this->license_type_id,
            'status_id' => $this->status_id,
            'entity' => $this->entity,
            'company' => $this->company,
            'eradicated_number' => $this->eradicated_number,
            'eradicatd_date' => $this->eradicatd_date,
            'estimated_approval_date' => $this->estimated_approval_date,
            'expiration_date' => $this->expiration_date,
            'requires_extension' => $this->requires_extension,
            'observations' => $this->observations,
        ]);

        session()->flash('success', 'Licencia actualizada exitosamente.');

        return redirect()->route('licenses.index');
    }

    public function setLicense(License $license)
    {
        $this->project_id = $license->project_id;
        $this->license_type_id = $license->license_type_id;
        $this->status_id = $license->status_id;
        $this->entity = $license->entity;
        $this->company = $license->company;
        $this->eradicated_number = $license->eradicated_number;
        $this->eradicatd_date = $license->eradicatd_date?->format('Y-m-d');
        $this->estimated_approval_date = $license->estimated_approval_date?->format('Y-m-d');
        $this->expiration_date = $license->expiration_date?->format('Y-m-d');
        $this->requires_extension = $license->requires_extension;
        $this->observations = $license->observations;
    }
}