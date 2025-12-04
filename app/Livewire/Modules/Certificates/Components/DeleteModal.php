<?php

namespace App\Livewire\Modules\Certificates\Components;

use App\Models\Certificate;
use Livewire\Component;

class DeleteModal extends Component
{

    protected $listeners = [
        "open-del-modal" => "openModal",
        "close-del-modal" => "closeModal"
    ];

    public $isDeleteModalOpen = false;

    public $selectedCert = null;

    public function openModal($id)
    {
        $cert = Certificate::find($id);

        $this->selectedCert = $cert;
        $this->isDeleteModalOpen = true;
    }

    public function closeModal()
    {
        $this->isDeleteModalOpen = false;
        $this->selectedCert = null;
    }

    public function deleteCert()
    {
        try {
            $this->selectedCert->delete();
        } catch (\Exception $e) {
            session()->flash('error', 'Error al eliminar la política: ' . $e->getMessage());
        }

        $this->closeModal();
        $this->redirectRoute("certificates.index", navigate: true);
    }


    public function render()
    {
        return view('livewire.modules.certificates.components.delete-modal');
    }
}
