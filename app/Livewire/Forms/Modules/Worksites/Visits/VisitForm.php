<?php

namespace App\Livewire\Forms\Modules\Worksites\Visits;

use Livewire\Form;
use App\Models\Visit;
use App\Models\Worksite;
use App\Models\User;
use App\Models\Tag;
use App\Models\TagCategory;
use Illuminate\Validation\Rule;

class VisitForm extends Form
{
    public ?Visit $visit = null;

    public $worksite_id = '';
    public $visit_date = '';
    public $performed_by = '';
    public $status_id = '';
    public $general_observations = '';
    public $internal_notes = '';

    protected function rules()
    {
        return [
            'worksite_id' => ['required', 'exists:worksites,id'],
            'visit_date' => ['required', 'date'],
            'performed_by' => ['nullable', 'exists:users,id'],
            'status_id' => ['nullable', 'exists:tags,id'],
            'general_observations' => ['nullable', 'string'],
            'internal_notes' => ['nullable', 'string'],
        ];
    }

    protected function messages()
    {
        return [
            'worksite_id.required' => 'La obra es obligatoria.',
            'worksite_id.exists' => 'La obra seleccionada no es válida.',
            'visit_date.required' => 'La fecha de visita es obligatoria.',
            'visit_date.date' => 'La fecha de visita debe ser una fecha válida.',
            'performed_by.exists' => 'El visitante seleccionado no es válido.',
            'status_id.exists' => 'El estado seleccionado no es válido.',
        ];
    }

    public function setVisit(Visit $visit)
    {
        $this->visit = $visit;
        
        $this->worksite_id = $visit->worksite_id;
        $this->visit_date = $visit->visit_date ? $visit->visit_date->format('Y-m-d') : '';
        $this->performed_by = $visit->performed_by;
        $this->status_id = $visit->status_id;
        $this->general_observations = $visit->general_observations;
        $this->internal_notes = $visit->internal_notes;
    }

    public function store()
    {
        $this->validate();

        Visit::create([
            'worksite_id' => $this->worksite_id,
            'visit_date' => $this->visit_date,
            'performed_by' => $this->performed_by,
            'status_id' => $this->status_id,
            'general_observations' => $this->general_observations,
            'internal_notes' => $this->internal_notes,
        ]);

        $this->reset();
    }

    public function update()
    {
        $this->validate();

        if (!$this->visit) {
            return;
        }

        $this->visit->update([
            'worksite_id' => $this->worksite_id,
            'visit_date' => $this->visit_date,
            'performed_by' => $this->performed_by,
            'status_id' => $this->status_id,
            'general_observations' => $this->general_observations,
            'internal_notes' => $this->internal_notes,
        ]);

        $this->reset();
    }

    public function resetForm()
    {
        $this->reset();
        $this->visit = null;
    }

    public function getWorksiteProperty()
    {
        return Worksite::find($this->worksite_id);
    }

    public function getVisitorProperty()
    {
        return User::find($this->performed_by);
    }

    public function getStatusProperty()
    {
        return Tag::find($this->status_id);
    }

    public function getPerformerProperty()
    {
        return User::find($this->performed_by);
    }

    public static function getVisitStatusOptions()
    {
        $category = TagCategory::where('slug', 'estado_visita')->first();
        return $category ? Tag::where('category_id', $category->id)->get() : collect();
    }

    public static function getVisitorOptions()
    {
        return User::orderBy('name')->get();
    }
}