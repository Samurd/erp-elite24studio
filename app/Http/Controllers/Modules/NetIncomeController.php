<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Models\Income;
use App\Models\Tag;
use App\Models\TagCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NetIncomeController extends Controller
{
    public function index(Request $request)
    {
        // Global filters
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', null);

        // Independent chart filters
        $monthlyChartYear = $request->get('monthlyChartYear', date('Y'));
        $categoryChartMonth = $request->get('categoryChartMonth', date('m'));
        $comparisonChartYear = $request->get('comparisonChartYear', date('Y'));

        // Prepare all chart data
        $data = $this->prepareChartData($year, $month, $monthlyChartYear, $categoryChartMonth, $comparisonChartYear);

        return Inertia::render('Net/Index', array_merge($data, [
            'filters' => [
                'year' => $year,
                'month' => $month,
                'monthlyChartYear' => $monthlyChartYear,
                'categoryChartMonth' => $categoryChartMonth,
                'comparisonChartYear' => $comparisonChartYear,
            ]
        ]));
    }

    private function prepareChartData($year, $month, $monthlyChartYear, $categoryChartMonth, $comparisonChartYear)
    {
        // 1. KPI Cards & Growth (Uses Global $year and $month)
        $globalQuery = Income::query()->whereYear('date', $year);

        if ($month) {
            $globalQuery->whereMonth('date', $month);
        }

        $globalIncomes = $globalQuery->get();
        $totalIncome = $globalIncomes->sum('amount');

        // Income by Type (Areas) - for KPI cards
        $incomeByType = $globalIncomes->groupBy('type.name')->map(function ($group) {
            return $group->sum('amount');
        });

        // Growth Percentage
        $previousPeriodIncome = 0;
        if ($month) {
            $previousDate = Carbon::createFromDate($year, $month, 1)->subMonth();
            $previousPeriodIncome = Income::whereYear('date', $previousDate->year)
                ->whereMonth('date', $previousDate->month)
                ->sum('amount');
        } else {
            $previousPeriodIncome = Income::whereYear('date', $year - 1)->sum('amount');
        }

        $growthPercentage = 0;
        if ($previousPeriodIncome > 0) {
            $growthPercentage = (($totalIncome - $previousPeriodIncome) / $previousPeriodIncome) * 100;
        }

        // 2. Net Income by Month (Bar Chart) - Uses $monthlyChartYear
        $monthlyChartIncomes = Income::whereYear('date', $monthlyChartYear)->get();
        $monthlyIncomesGrouped = $monthlyChartIncomes->groupBy(function ($date) {
            return Carbon::parse($date->date)->format('m');
        })->map(function ($row) {
            return $row->sum('amount');
        });

        $chartMonthlyData = [];
        for ($i = 1; $i <= 12; $i++) {
            $key = str_pad($i, 2, '0', STR_PAD_LEFT);
            $chartMonthlyData[] = isset($monthlyIncomesGrouped[$key]) ? $monthlyIncomesGrouped[$key] / 100 : 0;
        }

        // 3. Net Income by Category (Bar Chart) - Uses $categoryChartMonth
        $categoryChartQuery = Income::query()->whereYear('date', $year);
        if ($categoryChartMonth) {
            $categoryChartQuery->whereMonth('date', $categoryChartMonth);
        }
        $categoryChartIncomes = $categoryChartQuery->get();

        $incomeByCategory = $categoryChartIncomes->groupBy('category.name')->map(function ($group) {
            return $group->sum('amount') / 100;
        })->sortDesc();

        $chartCategoryLabels = $incomeByCategory->keys()->toArray();
        $chartCategoryData = $incomeByCategory->values()->toArray();

        // 4. Net Income by Area (Donut Chart) - Uses Global Filters
        $chartAreaLabels = $incomeByType->keys()->toArray();
        $chartAreaData = $incomeByType->map(fn($val) => $val / 100)->values()->toArray();

        // 5. Comparison (Year vs Year) - Uses $comparisonChartYear vs Previous Year
        $comparisonCurrentIncomes = Income::whereYear('date', $comparisonChartYear)->get();
        $comparisonLastIncomes = Income::whereYear('date', $comparisonChartYear - 1)->get();

        $compCurrentGrouped = $comparisonCurrentIncomes->groupBy(fn($d) => Carbon::parse($d->date)->format('m'))->map->sum('amount');
        $compLastGrouped = $comparisonLastIncomes->groupBy(fn($d) => Carbon::parse($d->date)->format('m'))->map->sum('amount');

        $chartComparisonCurrent = [];
        $chartComparisonLast = [];
        for ($i = 1; $i <= 12; $i++) {
            $key = str_pad($i, 2, '0', STR_PAD_LEFT);
            $chartComparisonCurrent[] = isset($compCurrentGrouped[$key]) ? $compCurrentGrouped[$key] / 100 : 0;
            $chartComparisonLast[] = isset($compLastGrouped[$key]) ? $compLastGrouped[$key] / 100 : 0;
        }

        // 6. Stacked Bar: Category by Month - Uses Global Year
        $stackedIncomes = $globalIncomes;
        $categories = Tag::whereHas('category', function ($q) {
            $q->where('slug', 'categoria_ingreso');
        })->pluck('name', 'id');

        $stackedChartData = [];
        foreach ($categories as $catId => $catName) {
            $data = [];
            for ($i = 1; $i <= 12; $i++) {
                $monthKey = str_pad($i, 2, '0', STR_PAD_LEFT);
                $val = $stackedIncomes->filter(function ($item) use ($catId, $monthKey) {
                    return $item->category_id == $catId && Carbon::parse($item->date)->format('m') == $monthKey;
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
