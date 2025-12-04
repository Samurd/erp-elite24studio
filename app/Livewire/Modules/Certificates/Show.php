<?php

namespace App\Livewire\Modules\Certificates;

use App\Models\Certificate;
use Livewire\Component;

class Show extends Component
{
    public Certificate $certificate;

    public function mount(Certificate $certificate)
    {
        $this->certificate = $certificate;
    }

    public function render()
    {
        return view('livewire.modules.certificates.show', [
            'certificate' => $this->certificate,
        ]);
    }
}