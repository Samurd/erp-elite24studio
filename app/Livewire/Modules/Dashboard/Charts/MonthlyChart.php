<?php

namespace App\Livewire\Modules\Dashboard\Charts;

use App\Models\Income;
use App\Models\Expense;
use Livewire\Component;

class MonthlyChart extends Component
{
    public $selectedYear;
    public $availableYears = [];
    public $monthlyData = [];

    public function mount()
    {
        // Generar años disponibles (últimos 5 años)
        $currentYear = now()->year;
        for ($i = 0; $i < 5; $i++) {
            $this->availableYears[] = $currentYear - $i;
        }

        $this->selectedYear = $currentYear;
        $this->loadData();
    }

    public function updatedSelectedYear()
    {
        $this->loadData();
    }

    private function loadData()
    {
        $months = [];
        $ingresos = [];
        $gastos = [];

        // Generar datos para los 12 meses del año seleccionado
        for ($month = 1; $month <= 12; $month++) {
            $months[] = \Carbon\Carbon::create($this->selectedYear, $month, 1)->translatedFormat('M');

            // Ingresos del mes
            $ingresos[] = Income::whereYear('date', $this->selectedYear)
                ->whereMonth('date', $month)
                ->sum('amount') / 100;

            // Gastos del mes
            $gastos[] = Expense::whereYear('date', $this->selectedYear)
                ->whereMonth('date', $month)
                ->sum('amount') / 100;
        }

        $this->monthlyData = [
            'labels' => $months,
            'ingresos' => $ingresos,
            'gastos' => $gastos,
        ];
    }

    public function render()
    {
        return view('livewire.modules.dashboard.charts.monthly-chart');
    }
}
