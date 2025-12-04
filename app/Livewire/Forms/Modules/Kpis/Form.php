<?php

namespace App\Livewire\Forms\Modules\Kpis;

use App\Models\Kpi;
use Livewire\Attributes\Validate;
use Livewire\Form as LivewireForm;

class Form extends LivewireForm
{
    public $protocol_code = '';
    public $indicator_name = '';
    public $target_value = null;
    public $periodicity_days = 30;
    public $role_id = null;


    protected $rules = [
        'indicator_name' => 'required|string|max:255',
        'target_value' => 'nullable|numeric',
        'periodicity_days' => 'required|integer|min:1',
        'role_id' => 'required|exists:roles,id',
    ];

    public function save()
    {
        $this->validate();


        // Generar código de protocolo automático
        $latestKpi = Kpi::latest('id')->first();
        $nextId = $latestKpi ? $latestKpi->id + 1 : 1;
        $this->protocol_code = 'KPI-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);

        // Crear nuevo KPI
        Kpi::create([
            'protocol_code' => $this->protocol_code,
            'indicator_name' => $this->indicator_name,
            'target_value' => $this->target_value,
            'periodicity_days' => $this->periodicity_days,
            'role_id' => $this->role_id,
        ]);

        session()->flash('success', 'KPI guardado exitosamente.');

        return redirect()->route('kpis.index');
    }

    public function update(Kpi $kpi)
    {
        $this->validate();
        $kpi->update([
            'protocol_code' => $this->protocol_code,
            'indicator_name' => $this->indicator_name,
            'target_value' => $this->target_value,
            'periodicity_days' => $this->periodicity_days,
            'role_id' => $this->role_id,
        ]);

        session()->flash('success', 'KPI actualizado exitosamente.');

        return redirect()->route('kpis.index');
    }



    public function setKpi(Kpi $kpi)
    {
        $this->protocol_code = $kpi->protocol_code;
        $this->indicator_name = $kpi->indicator_name;
        $this->target_value = $kpi->target_value;
        $this->periodicity_days = $kpi->periodicity_days;
        $this->role_id = $kpi->role_id;
    }
}
