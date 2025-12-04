<?php

namespace App\Livewire\Modules\Rrhh\Applicants;

use App\Livewire\Forms\Modules\Rrhh\Applicants\Form;
use App\Models\Applicant;
use Livewire\Component;

class Update extends Component
{
    public Form $form;
    
    public Applicant $applicant;

    public function mount(Applicant $applicant)
    {
        $this->applicant = $applicant;
        $this->form->setApplicant($applicant);
    }

    public function update()
    {
        $this->form->validate();

        $this->applicant->update($this->form->all());

        session()->flash('message', 'Postulante actualizado exitosamente.');

        return $this->redirect(route('rrhh.vacancies.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.modules.rrhh.applicants.create', [
            'applicant' => $this->applicant,
        ]);
    }
}
