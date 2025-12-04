<?php

namespace App\Livewire\Modules\Kpis;

use App\Livewire\Forms\Modules\Kpis\Form;
use App\Models\Kpi;
use App\Models\Role;
use Livewire\Component;

class Update extends Component
{
    public Form $form;
    public $kpi;

    public function mount(Kpi $kpi)
    {
        $this->kpi = $kpi;
        $this->form->setKpi($kpi);
        
        // Debug: Log the KPI data to ensure it's loaded
        logger('KPI loaded for edit:', [
            'kpi_id' => $kpi->id,
            'protocol_code' => $kpi->protocol_code,
            'form_protocol_code' => $this->form->protocol_code,
        ]);
    }

    public function save()
    {
        $this->form->update($this->kpi);

        session()->flash('success', 'KPI actualizado exitosamente.');

        return redirect()->route('kpis.index');
    }

    public function render()
    {
        $roles = Role::orderBy('name')->get();

        return view('livewire.modules.kpis.create', [
            'roles' => $roles,
            'kpi' => $this->kpi,
        ]);
    }
}
