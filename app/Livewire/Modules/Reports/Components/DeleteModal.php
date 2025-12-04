<?php

namespace App\Livewire\Modules\Reports\Components;

use App\Models\Report;
use Livewire\Component;

class DeleteModal extends Component
{

    protected $listeners = [
        "open-del-modal" => "openModal",
        "close-del-modal" => "closeModal"
    ];

    public $isDeleteModalOpen = false;

    public $selectedReport = null;

    public function openModal($id)
    {
        $this->selectedReport = Report::findOrFail($id);
        $this->isDeleteModalOpen = true;
    }

    public function closeModal()
    {
        $this->isDeleteModalOpen = false;
        $this->selectedReport = null;
    }

    public function deleteReport()
    {
        try {
            $this->selectedReport->delete();
        } catch (\Exception $e) {
            session()->flash('error', 'Error al eliminar el reporte: ' . $e->getMessage());
        }

        $this->closeModal();
        $this->dispatch('refresh');
    }
    public function render()
    {
        return view('livewire.modules.reports.components.delete-modal');
    }
}
