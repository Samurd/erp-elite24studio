<?php

namespace App\Livewire\Modules\Finances\Audits;

use App\Models\Audit;
use Livewire\Component;

class Show extends Component
{
    public Audit $audit;

    public function mount(Audit $audit)
    {
        $this->audit = $audit;
    }

    public function render()
    {
        return view('livewire.modules.finances.audits.show', [
            'files' => $this->audit->files,
        ]);
    }
}
