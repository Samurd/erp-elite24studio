<?php

namespace App\Livewire\Forms\Modules\Certificates;

use App\Models\Certificate;
use App\Models\File;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Validate;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\Form as LivewireForm;

class Form extends LivewireForm
{

    public ?Certificate $certificate = null;

    #[Validate('required|string')]
    public $name;

    #[Validate('nullable|string')]
    public $description;

    #[Validate('required')]
    public $status_id;

    #[Validate('required')]
    public $type_id;

    #[Validate('required')]
    public $issued_at;

    #[Validate('required')]
    public $expires_at;

    #[Validate('required')]
    public $assigned_to_id;



    public function setCertificate(Certificate $certificate)
    {
        $this->certificate = $certificate;
        $this->name = $this->certificate->name;
        $this->description = $this->certificate->description;
        $this->type_id = $this->certificate->type_id;
        $this->status_id = $this->certificate->status_id;
        $this->issued_at = $this->certificate->issued_at;
        $this->expires_at = $this->certificate->expires_at;
        $this->assigned_to_id = $this->certificate->assigned_to_id;

        if (!$this->certificate) {
            throw new \Exception('No se ha establecido el certificado antes de actualizar.');
        }
    }

    public function update()
    {

        if (!$this->certificate) {
            throw new \Exception('No se ha establecido el certificado antes de actualizar.');
        }


        $data = $this->only([
            'name',
            'description',
            'type_id',
            'status_id',
            'issued_at',
            'expires_at',
            'assigned_to_id',
        ]);

        $this->certificate->update($data);


        $this->reset();
    }


    public function store()
    {
        $this->validate();

        $certificate = Certificate::create([
            'name' => $this->name,
            'description' => $this->description,
            'type_id' => $this->type_id,
            'status_id' => $this->status_id,
            'issued_at' => $this->issued_at,
            'expires_at' => $this->expires_at,
            'assigned_to_id' => $this->assigned_to_id,
        ]);

        return $certificate;


    }
}
