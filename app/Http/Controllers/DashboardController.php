<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Income;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $totalIngresos = (int) Income::sum('amount');
        $totalGastos = (int) Expense::sum('amount');
        $totalGeneral = $totalIngresos - $totalGastos;

        return Inertia::render('Dashboard/Index', [
            'totalIngresos' => $totalIngresos,
            'totalGastos' => $totalGastos,
            'totalGeneral' => $totalGeneral,
            'weeklyChartData' => $this->getWeeklyData(),
            'monthlyChartData' => $this->getMonthlyData(),
        ]);
    }

    private function getWeeklyData()
    {
        $currentYear = now()->year;
        $currentQuarter = ceil(now()->month / 3);

        // Calcular el rango de meses del trimestre actual
        $startMonth = ($currentQuarter - 1) * 3 + 1;
        $endMonth = $currentQuarter * 3;

        // Obtener la primera fecha del trimestre
        $startDate = \Carbon\Carbon::create($currentYear, $startMonth, 1)->startOfMonth();
        $endDate = \Carbon\Carbon::create($currentYear, $endMonth, 1)->endOfMonth();

        // Obtener todos los ingresos del trimestre
        $ingresosData = Income::whereBetween('date', [$startDate, $endDate])
            ->selectRaw('date, amount')
            ->get()
            ->groupBy(function ($item) {
                return $item->date; // Assumes date is stored as YYYY-MM-DD or casted
            });

        // Obtener todos los gastos del trimestre
        $gastosData = Expense::whereBetween('date', [$startDate, $endDate])
            ->selectRaw('date, amount')
            ->get()
            ->groupBy(function ($item) {
                return $item->date;
            });

        $weeks = [];
        $ingresos = [];
        $gastos = [];
        $weekNumber = 1;
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            $weekStart = $currentDate->copy();
            $weekEnd = $currentDate->copy()->addDays(6);

            if ($weekEnd->gt($endDate)) {
                $weekEnd = $endDate->copy();
            }

            $weeks[] = "Sem {$weekNumber}";

            // Sumar ingresos de la semana
            $ingresosSemanales = 0;
            foreach ($ingresosData as $date => $items) {
                $itemDate = \Carbon\Carbon::parse($date);
                if ($itemDate->between($weekStart, $weekEnd)) {
                    $ingresosSemanales += $items->sum('amount');
                }
            }
            $ingresos[] = $ingresosSemanales / 100;

            // Sumar gastos de la semana
            $gastosSemanales = 0;
            foreach ($gastosData as $date => $items) {
                $itemDate = \Carbon\Carbon::parse($date);
                if ($itemDate->between($weekStart, $weekEnd)) {
                    $gastosSemanales += $items->sum('amount');
                }
            }
            $gastos[] = $gastosSemanales / 100;

            $currentDate->addDays(7);
            $weekNumber++;
        }

        return [
            'labels' => $weeks,
            'ingresos' => $ingresos,
            'gastos' => $gastos,
        ];
    }

    private function getMonthlyData()
    {
        $currentYear = now()->year;

        // Obtener todos los ingresos del año agrupados por mes
        $ingresosData = Income::whereYear('date', $currentYear)
            ->selectRaw('MONTH(date) as month, SUM(amount) as total')
            ->groupBy('month')
            ->pluck('total', 'month');

        // Obtener todos los gastos del año agrupados por mes
        $gastosData = Expense::whereYear('date', $currentYear)
            ->selectRaw('MONTH(date) as month, SUM(amount) as total')
            ->groupBy('month')
            ->pluck('total', 'month');

        $months = [];
        $ingresos = [];
        $gastos = [];

        // Generar datos para los 12 meses
        for ($month = 1; $month <= 12; $month++) {
            $months[] = \Carbon\Carbon::create($currentYear, $month, 1)->translatedFormat('M');
            $ingresos[] = ($ingresosData->get($month, 0)) / 100;
            $gastos[] = ($gastosData->get($month, 0)) / 100;
        }

        return [
            'labels' => $months,
            'ingresos' => $ingresos,
            'gastos' => $gastos,
        ];
    }
}
