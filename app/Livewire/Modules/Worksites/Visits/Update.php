<?php

namespace App\Livewire\Modules\Worksites\Visits;

use Livewire\Component;
use App\Models\Worksite;
use App\Models\Visit;
use App\Livewire\Forms\Modules\Worksites\Visits\VisitForm;

class Update extends Component
{
    public Worksite $worksite;
    public Visit $visit;

    public VisitForm $form;

    public function mount(Worksite $worksite, Visit $visit)
    {
        $this->worksite = $worksite;
        $this->visit = $visit;
        
        $this->form->setVisit($visit);
    }

    public function save()
    {
        $this->form->update();

        session()->flash('success', 'Visita actualizada exitosamente.');

        return redirect()->route('worksites.show', $this->worksite->id);
    }

    public function render()
    {
        return view('livewire.modules.worksites.visits.update', [
            'worksite' => $this->worksite,
            'visit' => $this->visit,
            'visitStatusOptions' => $this->form::getVisitStatusOptions(),
            'visitorOptions' => $this->form::getVisitorOptions(),
        ]);
    }
}
