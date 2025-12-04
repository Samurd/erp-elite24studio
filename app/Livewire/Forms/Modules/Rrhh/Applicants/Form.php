<?php

namespace App\Livewire\Forms\Modules\Rrhh\Applicants;

use Livewire\Attributes\Validate;
use Livewire\Form as LivewireForm;

class Form extends LivewireForm
{
    #[Validate('required|exists:vacancies,id')]
    public $vacancy_id = '';

    #[Validate('required|string|max:255')]
    public $full_name = '';

    #[Validate('required|email|max:255')]
    public $email = '';

    #[Validate('required|exists:tags,id')]
    public $status_id = '';

    #[Validate('nullable|string')]
    public $notes = '';

    public function rules()
    {
        return [
            'vacancy_id' => 'required|exists:vacancies,id',
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'status_id' => 'required|exists:tags,id',
            'notes' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'vacancy_id.required' => 'La vacante es obligatoria',
            'vacancy_id.exists' => 'La vacante seleccionada no es válida',
            'full_name.required' => 'El nombre completo es obligatorio',
            'full_name.max' => 'El nombre completo no puede superar los 255 caracteres',
            'email.required' => 'El email es obligatorio',
            'email.email' => 'El email debe ser una dirección válida',
            'email.max' => 'El email no puede superar los 255 caracteres',
            'status_id.required' => 'El estado es obligatorio',
            'status_id.exists' => 'El estado seleccionado no es válido',
        ];
    }

    public function setApplicant($applicant)
    {
        $this->vacancy_id = $applicant->vacancy_id;
        $this->full_name = $applicant->full_name;
        $this->email = $applicant->email;
        $this->status_id = $applicant->status_id;
        $this->notes = $applicant->notes;
    }

    public function resetForm()
    {
        $this->reset([
            'vacancy_id',
            'full_name',
            'email',
            'status_id',
            'notes',
        ]);
    }

    public function getDefaultValues()
    {
        // Establecer estado por defecto como "Pendiente"
        $pendingStatus = \App\Models\Tag::whereHas('category', function ($query) {
            $query->where('slug', 'estado_postulante');
        })->where('name', 'Pendiente')->first();

        if ($pendingStatus) {
            $this->status_id = $pendingStatus->id;
        }
    }

    public function getVacancies()
    {
        return \App\Models\Vacancy::orderBy('title')->get();
    }

    public function getApplicantStatuses()
    {
        $applicantStatusCategory = \App\Models\TagCategory::where('slug', 'estado_postulante')->first();
        return $applicantStatusCategory ? \App\Models\Tag::where('category_id', $applicantStatusCategory->id)->get() : collect();
    }
}