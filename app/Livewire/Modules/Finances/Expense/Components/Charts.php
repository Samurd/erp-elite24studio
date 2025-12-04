<?php

namespace App\Livewire\Modules\Finances\Expense\Components;

use App\Models\Expense;
use Livewire\Attributes\On;
use Livewire\Component;

class Charts extends Component
{
    public $yearChart1;
    public $monthChart2;
    public $yearTable;

    public $expenseByMonth = [];
    public $expenseByCategory = [];
    public $topProjects = [];

    public function mount()
    {
        $this->yearChart1 = now()->year;
        $this->monthChart2 = now()->month;
        $this->yearTable = now()->year;

        // // 🔍 DEBUG: Verifica que hay datos
        // $totalIncomes = Income::count();
        // $incomesThisYear = Income::whereYear("date", now()->year)->count();

        // // dd([
        // //     "total_incomes" => $totalIncomes,
        // //     "incomes_this_year" => $incomesThisYear,
        // //     "year_chart1" => $this->yearChart1,
        // //     "sample_dates" => Income::limit(5)
        // //         ->pluck("date", "amount")
        // //         ->toArray(),
        // // ]);

        $this->loadChart1();
        $this->loadChart2();
        $this->loadTable();
    }

    #[On("expenseCreated")]
    public function refreshCharts()
    {
        $this->loadChart1();
        $this->loadChart2();
        $this->loadTable();
    }

    public function updatedYearChart1()
    {
        $this->loadChart1();
    }

    public function updatedMonthChart2()
    {
        $this->loadChart2();
    }

    public function updatedYearTable()
    {
        $this->loadTable();
    }
    // ===== CHART 1 =====
    public function loadChart1()
    {
        // Obtener ingresos agrupados por mes
        $incomes = Expense::whereYear("date", $this->yearChart1)->get();

        // Inicializar los 12 meses con 0
        $this->expenseByMonth = collect(range(1, 12))
            ->mapWithKeys(function ($month) use ($incomes) {
                $total = $incomes
                    ->filter(function ($income) use ($month) {
                        return \Carbon\Carbon::parse($income->date)->month ==
                            $month;
                    })
                    ->sum("amount") / 100;

                return [$month => $total];
            })
            ->toArray();

        $this->dispatch("updateChart1", $this->expenseByMonth);
    }

    // ===== CHART 2 =====
    public function loadChart2()
    {
        // Obtener ingresos con categorías del mes seleccionado
        $incomes = Expense::with("category")
            ->whereMonth("date", $this->monthChart2)
            ->whereYear("date", $this->yearChart1)
            ->get();

        // Agrupar por categoría
        $this->expenseByCategory = $incomes
            ->groupBy(function ($income) {
                return $income->category->name ?? "Sin categoría";
            })
            ->map(function ($group) {
                return $group->sum("amount") / 100;
            })
            ->sortDesc()
            ->toArray();

        $this->dispatch("updateChart2", $this->expenseByCategory);
    }

    // ===== TABLA =====
    public function loadTable()
    {
        $this->topProjects = Expense::with(["category", "createdBy"])
            ->whereYear("date", $this->yearTable)
            ->orderByDesc("amount")
            ->limit(5)
            ->get();
    }

    public function render()
    {
        return view("livewire.modules.finances.expense.components.charts");
    }
}
