<?php

namespace App\Livewire\Modules\Worksites\PunchItems;

use Livewire\Component;
use App\Models\PunchItem;
use App\Models\Worksite;

class Show extends Component
{
    public Worksite $worksite;
    public PunchItem $punchItem;

    public function mount(Worksite $worksite, PunchItem $punchItem)
    {
        $this->worksite = $worksite;
        $this->punchItem = $punchItem;
    }

    public function render()
    {
        return view('livewire.modules.worksites.punch-items.show', [
            'worksite' => $this->worksite,
            'punchItem' => $this->punchItem,
        ]);
    }
}