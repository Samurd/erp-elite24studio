<?php

namespace App\Livewire\Modules\Worksites\Visits;

use Livewire\Component;
use App\Models\Worksite;
use App\Livewire\Forms\Modules\Worksites\Visits\VisitForm;

class Create extends Component
{
    public Worksite $worksite;

    public VisitForm $form;

    public function mount(Worksite $worksite)
    {
        $this->worksite = $worksite;
        $this->form->worksite_id = $worksite->id;
    }

    public function save()
    {
        $this->form->store();

        session()->flash('success', 'Visita creada exitosamente.');

        return redirect()->route('worksites.show', $this->worksite->id);
    }

    public function render()
    {
        return view('livewire.modules.worksites.visits.create', [
            'worksite' => $this->worksite,
            'visitStatusOptions' => $this->form::getVisitStatusOptions(),
            'visitorOptions' => $this->form::getVisitorOptions(),
        ]);
    }
}
