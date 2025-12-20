<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Models\Income;
use App\Models\Tag;
use App\Models\TagCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GrossIncomeController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $yearChart1 = $request->get('yearChart1', now()->year);
        $monthChart2 = $request->get('monthChart2', now()->month);
        $yearTable = $request->get('yearTable', now()->year);

        // Get incomes with pagination and search
        $incomes = Income::query()
            ->with(['category', 'result', 'createdBy', 'type'])
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            })
            ->orderByDesc("created_at")
            ->paginate(10);

        return Inertia::render('Gross/Index', [
            'incomes' => $incomes,
            'search' => $search,
            'chartData' => $this->getChartData($yearChart1, $monthChart2, $yearTable),
            'filters' => [
                'yearChart1' => $yearChart1,
                'monthChart2' => $monthChart2,
                'yearTable' => $yearTable,
            ]
        ]);
    }

    public function create()
    {
        $typeType = TagCategory::where("slug", "tipo_ingreso")->first();
        $categoryType = TagCategory::where("slug", "categoria_ingreso")->first();
        $resultType = TagCategory::where("slug", "resultado_ingreso")->first();

        $incomeTypes = Tag::where("category_id", $typeType?->id)->get() ?? [];
        $categories = Tag::where("category_id", $categoryType?->id)->get() ?? [];
        $results = Tag::where("category_id", $resultType?->id)->get() ?? [];
        $users = \App\Services\PermissionCacheService::getUsersByArea('finanzas');

        return Inertia::render('Gross/Form', [
            'income' => null,
            'incomeTypes' => $incomeTypes,
            'categories' => $categories,
            'results' => $results,
            'users' => $users,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'type_id' => 'required',
            'category_id' => 'nullable',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'amount' => 'required|integer|min:0',
            'created_by_id' => 'required',
            'result_id' => 'required',
        ]);

        Income::create($validated);

        return redirect()->route('finances.gross.index');
    }

    public function edit(Income $income)
    {
        $typeType = TagCategory::where("slug", "tipo_ingreso")->first();
        $categoryType = TagCategory::where("slug", "categoria_ingreso")->first();
        $resultType = TagCategory::where("slug", "resultado_ingreso")->first();

        $incomeTypes = Tag::where("category_id", $typeType?->id)->get() ?? [];
        $categories = Tag::where("category_id", $categoryType?->id)->get() ?? [];
        $results = Tag::where("category_id", $resultType?->id)->get() ?? [];
        $users = \App\Services\PermissionCacheService::getUsersByArea('finanzas');

        return Inertia::render('Gross/Form', [
            'income' => $income,
            'incomeTypes' => $incomeTypes,
            'categories' => $categories,
            'results' => $results,
            'users' => $users,
        ]);
    }

    public function update(Request $request, Income $income)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'type_id' => 'required',
            'category_id' => 'nullable',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'amount' => 'required|integer|min:0',
            'created_by_id' => 'required',
            'result_id' => 'required',
        ]);

        $income->update($validated);

        return redirect()->route('finances.gross.index');
    }

    public function destroy(Income $income)
    {
        $income->delete();
        return redirect()->route('finances.gross.index');
    }

    private function getChartData($yearChart1, $monthChart2, $yearTable)
    {
        // Chart 1: Gross by month
        $incomes = Income::whereYear("date", $yearChart1)->get();
        $grossByMonth = collect(range(1, 12))
            ->mapWithKeys(function ($month) use ($incomes) {
                $total = $incomes
                    ->filter(function ($income) use ($month) {
                        return Carbon::parse($income->date)->month == $month;
                    })
                    ->sum("amount") / 100;
                return [$month => $total];
            })
            ->toArray();

        // Chart 2: Gross by category for selected month
        $incomesMonth = Income::with("category")
            ->whereMonth("date", $monthChart2)
            ->whereYear("date", $yearChart1)
            ->whereHas("category")
            ->get();

        $grossByCategory = $incomesMonth
            ->groupBy(function ($income) {
                return $income->category->name;
            })
            ->map(function ($group) {
                return $group->sum("amount") / 100;
            })
            ->sortDesc()
            ->toArray();

        // Top 5 incomes
        $topProjects = Income::with(["category", "createdBy"])
            ->whereYear("date", $yearTable)
            ->orderByDesc("amount")
            ->limit(5)
            ->get();

        return [
            'grossByMonth' => array_values($grossByMonth),
            'grossByCategoryLabels' => array_keys($grossByCategory),
            'grossByCategoryData' => array_values($grossByCategory),
            'topProjects' => $topProjects,
        ];
    }
}
