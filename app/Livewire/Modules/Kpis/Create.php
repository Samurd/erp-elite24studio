<?php

namespace App\Livewire\Modules\Kpis;

use App\Livewire\Forms\Modules\Kpis\Form;
use App\Models\Role;
use Livewire\Component;

class Create extends Component
{
    public Form $form;

    public function mount()
    {
        $this->form->periodicity_days = 30; // Valor por defecto
    }

    public function save()
    {
        $this->form->save();

        session()->flash('success', 'KPI creado exitosamente.');

        return redirect()->route('kpis.index');
    }

    public function render()
    {
        $roles = Role::orderBy('name')->get();

        return view('livewire.modules.kpis.create', [
            'roles' => $roles,
            'kpi' => null,
        ]);
    }
}
