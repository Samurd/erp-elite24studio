<?php

namespace App\Livewire\Modules\Dashboard\Charts;

use App\Models\Income;
use App\Models\Expense;
use Livewire\Component;

class WeeklyChart extends Component
{
    public $selectedYear;
    public $selectedQuarter;
    public $availableYears = [];
    public $availableQuarters = [
        1 => 'T1 (Ene-Mar)',
        2 => 'T2 (Abr-Jun)',
        3 => 'T3 (Jul-Sep)',
        4 => 'T4 (Oct-Dic)',
    ];
    public $weeklyData = [];

    public function mount()
    {
        // Generar años disponibles (últimos 5 años)
        $currentYear = now()->year;
        for ($i = 0; $i < 5; $i++) {
            $this->availableYears[] = $currentYear - $i;
        }

        // Valores por defecto: año actual y trimestre actual
        $this->selectedYear = $currentYear;
        $this->selectedQuarter = ceil(now()->month / 3);

        $this->loadData();
    }

    public function updatedSelectedYear()
    {
        $this->loadData();
    }

    public function updatedSelectedQuarter()
    {
        $this->loadData();
    }

    private function loadData()
    {
        $weeks = [];
        $ingresos = [];
        $gastos = [];

        // Calcular el rango de meses del trimestre seleccionado
        $startMonth = ($this->selectedQuarter - 1) * 3 + 1;
        $endMonth = $this->selectedQuarter * 3;

        // Obtener la primera fecha del trimestre
        $startDate = \Carbon\Carbon::create($this->selectedYear, $startMonth, 1)->startOfMonth();
        $endDate = \Carbon\Carbon::create($this->selectedYear, $endMonth, 1)->endOfMonth();

        // Calcular semanas del trimestre
        $weekNumber = 1;
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            $weekStart = $currentDate->copy();
            $weekEnd = $currentDate->copy()->addDays(6);

            if ($weekEnd->gt($endDate)) {
                $weekEnd = $endDate->copy();
            }

            $weeks[] = "Sem {$weekNumber}";

            // Ingresos de la semana
            $ingresosSemanales = Income::whereBetween('date', [$weekStart, $weekEnd])
                ->sum('amount') / 100;
            $ingresos[] = $ingresosSemanales;

            // Gastos de la semana
            $gastosSemanales = Expense::whereBetween('date', [$weekStart, $weekEnd])
                ->sum('amount') / 100;
            $gastos[] = $gastosSemanales;

            $currentDate->addDays(7);
            $weekNumber++;
        }

        $this->weeklyData = [
            'labels' => $weeks,
            'ingresos' => $ingresos,
            'gastos' => $gastos,
        ];
    }

    public function render()
    {
        return view('livewire.modules.dashboard.charts.weekly-chart');
    }
}
