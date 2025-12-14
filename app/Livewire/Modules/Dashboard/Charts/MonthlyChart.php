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
        // ========== OPTIMIZADO: 2 queries en lugar de 24 ==========
        // Obtener todos los ingresos del año agrupados por mes
        $ingresosData = Income::whereYear('date', $this->selectedYear)
            ->selectRaw('MONTH(date) as month, SUM(amount) as total')
            ->groupBy('month')
            ->pluck('total', 'month');

        // Obtener todos los gastos del año agrupados por mes
        $gastosData = Expense::whereYear('date', $this->selectedYear)
            ->selectRaw('MONTH(date) as month, SUM(amount) as total')
            ->groupBy('month')
            ->pluck('total', 'month');

        $months = [];
        $ingresos = [];
        $gastos = [];

        // Generar datos para los 12 meses
        for ($month = 1; $month <= 12; $month++) {
            $months[] = \Carbon\Carbon::create($this->selectedYear, $month, 1)->translatedFormat('M');
            $ingresos[] = ($ingresosData->get($month, 0)) / 100;
            $gastos[] = ($gastosData->get($month, 0)) / 100;
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
