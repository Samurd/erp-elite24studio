<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Tag;
use App\Models\TagCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ExpensesController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $page = $request->get('page', 1);

        // Get expenses with pagination and search
        $expenses = Expense::query()
            ->with(['category', 'result', 'createdBy'])
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            })
            ->orderByDesc("created_at")
            ->paginate(10);

        return Inertia::render('Expenses/Index', [
            'expenses' => $expenses,
            'search' => $search,
            'totals' => $this->getTotals(),
            'chartData' => $this->getChartData($request),
        ]);
    }

    public function create()
    {
        $categoryType = TagCategory::where("slug", "categoria_gasto")->first();
        $resultType = TagCategory::where("slug", "resultado_ingreso")->first();

        $categories = Tag::where("category_id", $categoryType?->id)->get() ?? [];
        $results = Tag::where("category_id", $resultType?->id)->get() ?? [];
        $users = \App\Services\PermissionCacheService::getUsersByArea('finanzas');

        return Inertia::render('Expenses/Form', [
            'expense' => null,
            'categories' => $categories,
            'results' => $results,
            'users' => $users,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'category_id' => 'required',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'amount' => 'required|numeric',
            'created_by_id' => 'required',
            'result_id' => 'required',
        ]);

        Expense::create($validated);

        return redirect()->route('finances.expenses.index');
    }

    public function edit(Expense $expense)
    {
        $categoryType = TagCategory::where("slug", "categoria_gasto")->first();
        $resultType = TagCategory::where("slug", "resultado_ingreso")->first();

        $categories = Tag::where("category_id", $categoryType?->id)->get() ?? [];
        $results = Tag::where("category_id", $resultType?->id)->get() ?? [];
        $users = \App\Services\PermissionCacheService::getUsersByArea('finanzas');

        return Inertia::render('Expenses/Form', [
            'expense' => $expense,
            'categories' => $categories,
            'results' => $results,
            'users' => $users,
        ]);
    }

    public function update(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'category_id' => 'required',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'amount' => 'required|numeric',
            'created_by_id' => 'required',
            'result_id' => 'required',
        ]);

        $expense->update($validated);

        return redirect()->route('finances.expenses.index');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();
        return redirect()->route('finances.expenses.index');
    }

    private function getTotals()
    {
        $categoryType = TagCategory::where("slug", "categoria_gasto")->first();

        if (!$categoryType) {
            return [
                'totalDirect' => 0,
                'totalIndirect' => 0,
                'totalTaxes' => 0,
                'totalFinance' => 0,
            ];
        }

        $tagDirect = Tag::where("category_id", $categoryType->id)
            ->where("name", "Costos Directos")
            ->first();
        $tagIndirect = Tag::where("category_id", $categoryType->id)
            ->where("name", "Gastos Indirectos")
            ->first();
        $tagTaxes = Tag::where("category_id", $categoryType->id)
            ->where("name", "Impuestos")
            ->first();
        $tagFinance = Tag::where("category_id", $categoryType->id)
            ->where("name", "Gastos Financieros")
            ->first();

        return [
            'totalDirect' => $tagDirect ? Expense::where("category_id", $tagDirect->id)->sum("amount") : 0,
            'totalIndirect' => $tagIndirect ? Expense::where("category_id", $tagIndirect->id)->sum("amount") : 0,
            'totalTaxes' => $tagTaxes ? Expense::where("category_id", $tagTaxes->id)->sum("amount") : 0,
            'totalFinance' => $tagFinance ? Expense::where("category_id", $tagFinance->id)->sum("amount") : 0,
        ];
    }

    private function getChartData(Request $request)
    {
        $yearChart1 = $request->get('yearChart1', now()->year);
        $monthChart2 = $request->get('monthChart2', now()->month);
        $yearTable = $request->get('yearTable', now()->year);

        // Chart 1: Expenses by month
        $expenses = Expense::whereYear("date", $yearChart1)->get();
        $expenseByMonth = collect(range(1, 12))
            ->mapWithKeys(function ($month) use ($expenses) {
                $total = $expenses
                    ->filter(function ($expense) use ($month) {
                        return Carbon::parse($expense->date)->month == $month;
                    })
                    ->sum("amount") / 100;
                return [$month => $total];
            })
            ->toArray();

        // Chart 2: Expenses by category for selected month
        $expensesMonth = Expense::with("category")
            ->whereMonth("date", $monthChart2)
            ->whereYear("date", $yearChart1)
            ->get();

        $expenseByCategory = $expensesMonth
            ->groupBy(function ($expense) {
                return $expense->category->name ?? "Sin categoría";
            })
            ->map(function ($group) {
                return $group->sum("amount") / 100;
            })
            ->sortDesc()
            ->toArray();

        // Top 5 expenses
        $topProjects = Expense::with(["category", "createdBy"])
            ->whereYear("date", $yearTable)
            ->orderByDesc("amount")
            ->limit(5)
            ->get();

        return [
            'yearChart1' => $yearChart1,
            'monthChart2' => $monthChart2,
            'yearTable' => $yearTable,
            'expenseByMonth' => $expenseByMonth,
            'expenseByCategory' => $expenseByCategory,
            'topProjects' => $topProjects,
        ];
    }
}
