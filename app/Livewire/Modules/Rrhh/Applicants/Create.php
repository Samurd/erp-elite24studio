<?php

namespace App\Livewire\Modules\Rrhh\Applicants;

use App\Livewire\Forms\Modules\Rrhh\Applicants\Form;
use App\Models\Applicant;
use Livewire\Component;

class Create extends Component
{
    public Form $form;

    public function mount()
    {
        $this->form->resetForm();
        $this->form->getDefaultValues();
    }

    public function save()
    {
        $this->form->validate();

        Applicant::create($this->form->all());

        session()->flash('message', 'Postulante creado exitosamente.');

        return $this->redirect(route('rrhh.vacancies.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.modules.rrhh.applicants.create');
    }
}
