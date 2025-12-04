<?php

namespace App\Livewire\Modules\Finances\Norms;

use App\Models\Norm;
use Livewire\Component;

class Show extends Component
{
    public Norm $norm;

    public function mount(Norm $norm)
    {
        $this->norm = $norm;
    }

    public function render()
    {
        return view('livewire.modules.finances.norms.show', [
            'files' => $this->norm->files,
        ]);
    }
}
