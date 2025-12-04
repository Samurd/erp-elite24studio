<?php

namespace App\Livewire\Modules\Finances\Payrolls;

use Livewire\Component;

use App\Models\Payroll;
use App\Models\TaxRecord;
use App\Models\Tag;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Stats extends Component
{
    public $yearStats;
    public $yearTaxes;
    public $yearPayrolls;
    public $yearDeductions;

    public $statusLabels = [];
    public $statusData = [];
    public $genderLabels = [];
    public $genderData = [];
    public $deductionsData = [];
    public $recentPayrolls;
    public $recentTaxes;

    public function mount()
    {
        $currentYear = date('Y');
        $this->yearStats = $currentYear;
        $this->yearTaxes = $currentYear;
        $this->yearPayrolls = $currentYear;
        $this->yearDeductions = $currentYear;

        $this->loadChartsStats();
        $this->loadTaxes();
        $this->loadPayrolls();
        $this->loadDeductions();
    }

    public function updatedYearStats()
    {
        $this->loadChartsStats();
        $this->dispatch('update-stats-charts', [
            'statusLabels' => $this->statusLabels,
            'statusData' => $this->statusData,
            'genderLabels' => $this->genderLabels,
            'genderData' => $this->genderData,
        ]);
    }

    public function updatedYearDeductions()
    {
        $this->loadDeductions();
        $this->dispatch('update-deductions-chart', [
            'deductionsData' => $this->deductionsData,
        ]);
    }

    public function updatedYearTaxes()
    {
        $this->loadTaxes();
    }

    public function updatedYearPayrolls()
    {
        $this->loadPayrolls();
    }

    public function loadPayrolls()
    {
        // 1. Recent Payrolls (Control Salarial)
        $this->recentPayrolls = Payroll::with(['employee', 'status'])
            ->whereYear('created_at', $this->yearPayrolls)
            ->latest()
            ->take(5)
            ->get();
    }

    public function loadTaxes()
    {
        // 2. Recent Taxes (Impuestos Laborales)
        $this->recentTaxes = TaxRecord::with(['type', 'status'])
            ->whereYear('date', $this->yearTaxes)
            ->latest('date')
            ->take(5)
            ->get();
    }

    public function loadChartsStats()
    {
        // 3. Status Stats (Pagados vs Pendientes)
        $statusStatsRaw = Payroll::join('tags', 'payrolls.status_id', '=', 'tags.id')
            ->whereYear('payrolls.created_at', $this->yearStats)
            ->select('tags.name', DB::raw('count(*) as count'))
            ->groupBy('tags.name')
            ->pluck('count', 'name')
            ->toArray();

        $this->statusLabels = array_keys($statusStatsRaw);
        $this->statusData = array_values($statusStatsRaw);

        // 4. Gender Stats
        $genderStatsRaw = Payroll::join('employees', 'payrolls.employee_id', '=', 'employees.id')
            ->join('tags', 'employees.gender_id', '=', 'tags.id')
            ->whereYear('payrolls.created_at', $this->yearStats)
            ->select('tags.name', DB::raw('count(*) as count'))
            ->groupBy('tags.name')
            ->pluck('count', 'name')
            ->toArray();

        $this->genderLabels = array_keys($genderStatsRaw);
        $this->genderData = array_values($genderStatsRaw);
    }

    public function loadDeductions()
    {
        // 5. Deductions per Month
        $deductionsRaw = TaxRecord::whereYear('date', $this->yearDeductions)
            ->select(
                DB::raw('MONTH(date) as month'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $this->deductionsData = [];
        for ($i = 1; $i <= 12; $i++) {
            $this->deductionsData[] = ($deductionsRaw[$i] ?? 0) / 100;
        }
    }

    public function render()
    {
        return view('livewire.modules.finances.payrolls.stats');
    }
}
