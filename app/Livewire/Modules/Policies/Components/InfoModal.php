<?php

namespace App\Livewire\Modules\Policies\Components;

use App\Models\Policy;
use Livewire\Component;

class InfoModal extends Component
{


    protected $listeners = [
        "open-info-modal" => "openModal",
        "close-info-modal" => "closeModal"
    ];

    public $showModal = false;
    public ?Policy $policy = null;


    public function openModal($policyId)
    {
        // Busca la política usando el ID recibido
        $this->policy = Policy::find($policyId);
        $this->showModal = true;
    }
    public function closeModal()
    {
        $this->showModal = false;
        $this->policy = null;
    }


    public function render()
    {
        return view('livewire.modules.policies.components.info-modal');
    }
}
