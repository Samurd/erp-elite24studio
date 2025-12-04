<?php

namespace App\Livewire\Modules\Worksites\Changes;

use Livewire\Component;
use App\Models\Change;
use App\Models\Worksite;

class Show extends Component
{
    public Worksite $worksite;
    public Change $change;

    public function mount(Worksite $worksite, Change $change)
    {
        $this->worksite = $worksite;
        $this->change = $change;
    }

    public function render()
    {
        return view('livewire.modules.worksites.changes.show', [
            'worksite' => $this->worksite,
            'change' => $this->change,
        ]);
    }
}