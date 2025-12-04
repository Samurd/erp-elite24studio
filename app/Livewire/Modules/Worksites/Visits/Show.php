<?php

namespace App\Livewire\Modules\Worksites\Visits;

use Livewire\Component;
use App\Models\Worksite;
use App\Models\Visit;

class Show extends Component
{
    public Worksite $worksite;
    public Visit $visit;

    public function mount(Worksite $worksite, Visit $visit)
    {
        $this->worksite = $worksite;
        $this->visit = $visit;
    }

    public function delete()
    {
        $this->visit->delete();
        
        session()->flash('success', 'Visita eliminada exitosamente.');

        return redirect()->route('worksites.show', $this->worksite->id);
    }

    public function render()
    {
        return view('livewire.modules.worksites.visits.show', [
            'worksite' => $this->worksite,
            'visit' => $this->visit,
        ]);
    }
}