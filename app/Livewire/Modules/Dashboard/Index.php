<?php

namespace App\Livewire\Modules\Dashboard;

use App\Models\Income;
use App\Models\Expense;
use Livewire\Component;

class Index extends Component
{
    public $totalIngresos;
    public $totalGastos;
    public $totalGeneral;

    public function mount()
    {
        // Datos financieros (totales generales)
        $this->totalIngresos = Income::sum('amount');
        $this->totalGastos = Expense::sum('amount');
        $this->totalGeneral = $this->totalIngresos - $this->totalGastos;
    }

    public function render()
    {
        return view('livewire.modules.dashboard.index', [
            'moneyFormatter' => \App\Services\MoneyFormatterService::class,
        ]);
    }
}
