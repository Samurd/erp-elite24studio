<?php

namespace App\Livewire\Modules\Policies\Components;

use App\Models\Policy;
use Livewire\Component;

class DeleteModal extends Component
{

    protected $listeners = [
        "open-del-modal" => "openModal",
        "close-del-modal" => "closeModal"
    ];

    public $isDeleteModalOpen = false;

    public $selectedPolicy = null;

    public function openModal($id)
    {
        $this->selectedPolicy = Policy::findOrFail($id);
        $this->isDeleteModalOpen = true;
    }

    public function closeModal()
    {
        $this->isDeleteModalOpen = false;
        $this->selectedPolicy = null;
    }

    public function deletePolicy()
    {
        try {
            $this->selectedPolicy->delete();
        } catch (\Exception $e) {
            session()->flash('error', 'Error al eliminar la política: ' . $e->getMessage());
        }

        $this->closeModal();
        $this->redirectRoute("policies.index", navigate: true);
    }

    public function render()
    {
        return view('livewire.modules.policies.components.delete-modal');
    }
}
