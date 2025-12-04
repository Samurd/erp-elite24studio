<?php

namespace App\Livewire\Modules\Certificates\Components;

use App\Models\Certificate;
use Livewire\Component;

class InfoModal extends Component
{

    protected $listeners = [
        "open-info-modal" => "openModal",
        "close-info-modal" => "closeModal"
    ];

    public $showModal = false;
    public ?Certificate $certificate = null;


    public function openModal($certificateId)
    {
        // Busca la política usando el ID recibido
        $this->certificate = Certificate::find($certificateId);
        $this->showModal = true;
    }
    public function closeModal()
    {
        $this->showModal = false;
        $this->certificate = null;
    }

    public function render()
    {
        return view('livewire.modules.certificates.components.info-modal');
    }
}
