<?php

namespace App\Livewire\Forms\Modules\Rrhh\Vacancies;

use Livewire\Attributes\Validate;
use Livewire\Form as LivewireForm;

class Form extends LivewireForm
{
    #[Validate('required|string|max:255')]
    public $title = '';

    #[Validate('required|string|max:255')]
    public $area = '';

    #[Validate('required|exists:tags,id')]
    public $contract_type_id = '';

    #[Validate('nullable|date')]
    public $published_at = '';

    #[Validate('required|exists:tags,id')]
    public $status_id = '';

    #[Validate('nullable|string')]
    public $description = '';

    #[Validate('required|exists:users,id')]
    public $user_id = '';

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'area' => 'required|string|max:255',
            'contract_type_id' => 'required|exists:tags,id',
            'published_at' => 'nullable|date',
            'status_id' => 'required|exists:tags,id',
            'user_id' => 'required|exists:users,id',
            'description' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'El título es obligatorio',
            'title.max' => 'El título no puede superar los 255 caracteres',
            'area.required' => 'El área es obligatoria',
            'area.max' => 'El área no puede superar los 255 caracteres',
            'contract_type_id.required' => 'El tipo de contrato es obligatorio',
            'contract_type_id.exists' => 'El tipo de contrato seleccionado no es válido',
            'published_at.date' => 'La fecha de publicación debe ser una fecha válida',
            'status_id.required' => 'El estado es obligatorio',
            'status_id.exists' => 'El estado seleccionado no es válido',
            'user_id.required' => 'El responsable es obligatorio',
            'user_id.exists' => 'El responsable seleccionado no es válido',
        ];
    }

    public function setVacancy($vacancy)
    {
        $this->title = $vacancy->title;
        $this->area = $vacancy->area;
        $this->contract_type_id = $vacancy->contract_type_id;

        // Manejar published_at para que siempre sea string en formato Y-m-d
        if ($vacancy->published_at) {
            if (is_string($vacancy->published_at)) {
                $this->published_at = $vacancy->published_at;
            } else {
                $this->published_at = $vacancy->published_at->format('Y-m-d');
            }
        } else {
            $this->published_at = null;
        }

        $this->status_id = $vacancy->status_id;
        $this->user_id = $vacancy->user_id;
        $this->description = $vacancy->description;
    }

    public function resetForm()
    {
        $this->reset([
            'title',
            'area',
            'contract_type_id',
            'published_at',
            'status_id',
            'user_id',
            'description',
        ]);
    }

    public function getDefaultValues()
    {
        $this->published_at = now()->format('Y-m-d');


        // Establecer el usuario actual como responsable por defecto
        $this->user_id = auth()->id();
    }

    public function getRrhhUsers()
    {
        return \App\Services\PermissionCacheService::getUsersByArea('rrhh');
    }
}
