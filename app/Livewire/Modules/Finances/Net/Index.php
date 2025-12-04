<?php

namespace App\Livewire\Modules\Finances\Net;

use Livewire\Component;

class Index extends Component
{
    // Global Filters (for KPIs and Growth)
    public $year;
    public $month;

    // Independent Chart Filters
    public $monthlyChartYear;
    public $categoryChartMonth;
    public $comparisonChartYear;

    public function mount()
    {
        $this->year = date('Y');
        $this->monthlyChartYear = date('Y');
        $this->categoryChartMonth = date('m'); // Default to current month? Or null for all year? Image shows "Marzo"
        $this->comparisonChartYear = date('Y');
    }

    public function getListeners()
    {
        return [
            'update-filters' => '$refresh',
        ];
    }

    public function dehydrate()
    {
        $data = $this->prepareChartData();
        $this->dispatch('charts-updated', $data);
    }

    private function prepareChartData()
    {
        // 1. KPI Cards & Growth (Uses Global $year and $month)
        $globalQuery = \App\Models\Income::query()
            ->whereYear('date', $this->year);

        if ($this->month) {
            $globalQuery->whereMonth('date', $this->month);
        }

        $globalIncomes = $globalQuery->get();
        $totalIncome = $globalIncomes->sum('amount');

        // Income by Type (Areas) - for KPI cards
        $incomeByType = $globalIncomes->groupBy('type.name')->map(function ($group) {
            return $group->sum('amount');
        });

        // Growth Percentage
        $previousPeriodIncome = 0;
        if ($this->month) {
            $previousDate = \Carbon\Carbon::createFromDate($this->year, $this->month, 1)->subMonth();
            $previousPeriodIncome = \App\Models\Income::whereYear('date', $previousDate->year)
                ->whereMonth('date', $previousDate->month)
                ->sum('amount');
        } else {
            $previousPeriodIncome = \App\Models\Income::whereYear('date', $this->year - 1)
                ->sum('amount');
        }

        $growthPercentage = 0;
        if ($previousPeriodIncome > 0) {
            $growthPercentage = (($totalIncome - $previousPeriodIncome) / $previousPeriodIncome) * 100;
        }


        // 2. Net Income by Month (Bar Chart) - Uses $monthlyChartYear
        $monthlyChartIncomes = \App\Models\Income::whereYear('date', $this->monthlyChartYear)->get();
        $monthlyIncomesGrouped = $monthlyChartIncomes->groupBy(function ($date) {
            return \Carbon\Carbon::parse($date->date)->format('m');
        })->map(function ($row) {
            return $row->sum('amount');
        });

        $chartMonthlyData = [];
        for ($i = 1; $i <= 12; $i++) {
            $key = str_pad($i, 2, '0', STR_PAD_LEFT);
            $chartMonthlyData[] = isset($monthlyIncomesGrouped[$key]) ? $monthlyIncomesGrouped[$key] / 100 : 0;
        }


        // 3. Net Income by Category (Bar Chart) - Uses $categoryChartMonth (and Global Year? Or just Month of current year?)
        // Assuming Month of the Global Year or Independent Year? Let's use Global Year + Independent Month for now.
        $categoryChartQuery = \App\Models\Income::query()->whereYear('date', $this->year);
        if ($this->categoryChartMonth) {
            $categoryChartQuery->whereMonth('date', $this->categoryChartMonth);
        }
        $categoryChartIncomes = $categoryChartQuery->get();

        $incomeByCategory = $categoryChartIncomes->groupBy('category.name')->map(function ($group) {
            return $group->sum('amount') / 100;
        })->sortDesc();

        $chartCategoryLabels = $incomeByCategory->keys()->toArray();
        $chartCategoryData = $incomeByCategory->values()->toArray();


        // 4. Net Income by Area (Donut Chart) - Uses Global Filters (matches KPI context)
        // Already calculated in $incomeByType
        $chartAreaLabels = $incomeByType->keys()->toArray();
        $chartAreaData = $incomeByType->map(fn($val) => $val / 100)->values()->toArray();


        // 5. Comparison (Year vs Year) - Uses $comparisonChartYear vs Previous Year
        $comparisonCurrentIncomes = \App\Models\Income::whereYear('date', $this->comparisonChartYear)->get();
        $comparisonLastIncomes = \App\Models\Income::whereYear('date', $this->comparisonChartYear - 1)->get();

        $compCurrentGrouped = $comparisonCurrentIncomes->groupBy(fn($d) => \Carbon\Carbon::parse($d->date)->format('m'))->map->sum('amount');
        $compLastGrouped = $comparisonLastIncomes->groupBy(fn($d) => \Carbon\Carbon::parse($d->date)->format('m'))->map->sum('amount');

        $chartComparisonCurrent = [];
        $chartComparisonLast = [];
        for ($i = 1; $i <= 12; $i++) {
            $key = str_pad($i, 2, '0', STR_PAD_LEFT);
            $chartComparisonCurrent[] = isset($compCurrentGrouped[$key]) ? $compCurrentGrouped[$key] / 100 : 0;
            $chartComparisonLast[] = isset($compLastGrouped[$key]) ? $compLastGrouped[$key] / 100 : 0;
        }


        // 6. Stacked Bar: Category by Month - Uses Global Year
        // (Keeping this simple for now, using global year)
        $stackedIncomes = $globalIncomes; // Already filtered by global year
        $categories = \App\Models\Tag::whereHas('category', function ($q) {
            $q->where('slug', 'categoria_ingreso');
        })->pluck('name', 'id');

        $stackedChartData = [];
        foreach ($categories as $catId => $catName) {
            $data = [];
            for ($i = 1; $i <= 12; $i++) {
                $monthKey = str_pad($i, 2, '0', STR_PAD_LEFT);
                $val = $stackedIncomes->filter(function ($item) use ($catId, $monthKey) {
                    return $item->category_id == $catId && \Carbon\Carbon::parse($item->date)->format('m') == $monthKey;
                })->sum('amount');
                $data[] = $val / 100;
            }
            // Only add if there is data
            if (array_sum($data) > 0) {
                $stackedChartData[] = [
                    'label' => $catName,
                    'data' => $data,
                    'backgroundColor' => $this->getColorForCategory($catName),
                ];
            }
        }

        return [
            'totalIncome' => $totalIncome / 100,
            'incomeByType' => $incomeByType->map(fn($v) => $v / 100),
            'growthPercentage' => $growthPercentage,
            'previousPeriodIncome' => $previousPeriodIncome / 100,

            'chartMonthlyData' => $chartMonthlyData,

            'chartCategoryLabels' => $chartCategoryLabels,
            'chartCategoryData' => $chartCategoryData,

            'chartAreaLabels' => $chartAreaLabels,
            'chartAreaData' => $chartAreaData,

            'chartComparisonCurrent' => $chartComparisonCurrent,
            'chartComparisonLast' => $chartComparisonLast,

            'stackedChartData' => $stackedChartData,
        ];
    }

    public function render()
    {
        return view('livewire.modules.finances.net.index', $this->prepareChartData());
    }

    private function getColorForCategory($categoryName)
    {
        $colors = [
            'LEED y EDGE' => '#818cf8',
            'Diseño Arq.Ejecutivo' => '#c084fc',
            'Interiorismo' => '#f472b6',
            'Inmobiliaria' => '#fb7185',
            'Ejecucion de obra' => '#38bdf8',
            'Arquitectura espacial' => '#2dd4bf',
        ];
        return $colors[$categoryName] ?? '#94a3b8';
    }
}
