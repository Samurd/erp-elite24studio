<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Invoice;
use App\Models\Payroll;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\Employee;
use Carbon\Carbon;
use Inertia\Inertia;

class FinancesController extends Controller
{
    public function index()
    {
        $totalExpenses = (int) Expense::sum("amount");
        $totalGrossIncome = (int) Income::sum("amount");
        $netIncome = $totalGrossIncome - $totalExpenses;

        return Inertia::render('Finances/Index', [
            'totalExpenses' => $totalExpenses,
            'totalGrossIncome' => $totalGrossIncome,
            'netIncome' => $netIncome,
            'recentTransactions' => $this->loadRecentTransactions(),
            'clientInvoicesData' => $this->getClientInvoicesData(),
            'providerInvoicesData' => $this->getProviderInvoicesData(),
            'billingAccountsData' => $this->getBillingAccountsData(),
            'payrollPaymentsData' => $this->getPayrollPaymentsData(),
            'payrollGenderData' => $this->getPayrollGenderData(),
        ]);
    }

    private function loadRecentTransactions()
    {
        $transactions = collect();

        // Get recent invoices
        $invoices = Invoice::with(['contact', 'status'])
            ->orderBy('invoice_date', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($invoice) {
                return [
                    'type' => 'invoice',
                    'description' => $invoice->contact ? $invoice->contact->name : 'Sin contacto',
                    'subtitle' => $invoice->code,
                    'date' => $invoice->invoice_date,
                    'amount' => $invoice->total,
                    'is_income' => true,
                    'status' => $invoice->status ? $invoice->status->name : 'Sin estado',
                ];
            });

        // Get recent expenses
        $expenses = Expense::with(['category'])
            ->orderBy('date', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($expense) {
                return [
                    'type' => 'expense',
                    'description' => $expense->name,
                    'subtitle' => $expense->category ? $expense->category->name : 'Sin categoría',
                    'date' => $expense->date,
                    'amount' => $expense->amount,
                    'is_income' => false,
                    'status' => 'Completada',
                ];
            });

        // Get recent incomes
        $incomes = Income::with(['category'])
            ->orderBy('date', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($income) {
                return [
                    'type' => 'income',
                    'description' => $income->name,
                    'subtitle' => $income->category ? $income->category->name : 'Sin categoría',
                    'date' => $income->date,
                    'amount' => $income->amount,
                    'is_income' => true,
                    'status' => 'Completada',
                ];
            });

        // Merge and sort by date
        $transactions = $transactions
            ->merge($invoices)
            ->merge($expenses)
            ->merge($incomes)
            ->sortByDesc('date')
            ->take(10)
            ->values();

        return $transactions->toArray();
    }

    private function getClientInvoicesData()
    {
        $relationTypeCategory = TagCategory::where('slug', 'tipo_relacion')->first();
        $clienteTag = $relationTypeCategory
            ? Tag::where('category_id', $relationTypeCategory->id)->where('name', 'Cliente')->first()
            : null;

        $statusCategory = TagCategory::where('slug', 'estado_factura')->first();
        $allStatuses = $statusCategory
            ? Tag::where('category_id', $statusCategory->id)->get()
            : collect();

        if (!$clienteTag || !$statusCategory) {
            return ['labels' => [], 'data' => [], 'colors' => []];
        }

        $clientInvoices = Invoice::whereHas('contact', function ($q) use ($clienteTag) {
            $q->where('relation_type_id', $clienteTag->id);
        })
            ->where('code', 'like', 'INV-%')
            ->where('code', 'not like', 'INV-PRV-%')
            ->get();

        $totalClient = $clientInvoices->count();
        $labels = [];
        $data = [];
        $colors = [];

        foreach ($allStatuses as $status) {
            $count = $clientInvoices->where('status_id', $status->id)->count();
            if ($count > 0) {
                $labels[] = $status->name;
                $data[] = $totalClient > 0 ? round(($count / $totalClient) * 100, 1) : 0;
                $colors[] = $status->color ?? '#D1D5DB';
            }
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'colors' => $colors,
        ];
    }

    private function getProviderInvoicesData()
    {
        $relationTypeCategory = TagCategory::where('slug', 'tipo_relacion')->first();
        $proveedorTag = $relationTypeCategory
            ? Tag::where('category_id', $relationTypeCategory->id)->where('name', 'Proveedor')->first()
            : null;

        $statusCategory = TagCategory::where('slug', 'estado_factura')->first();
        $allStatuses = $statusCategory
            ? Tag::where('category_id', $statusCategory->id)->get()
            : collect();

        if (!$proveedorTag || !$statusCategory) {
            return ['labels' => [], 'data' => [], 'colors' => []];
        }

        $providerInvoices = Invoice::whereHas('contact', function ($q) use ($proveedorTag) {
            $q->where('relation_type_id', $proveedorTag->id);
        })
            ->where('code', 'like', 'INV-PRV-%')
            ->get();

        $totalProvider = $providerInvoices->count();
        $labels = [];
        $data = [];
        $colors = [];

        foreach ($allStatuses as $status) {
            $count = $providerInvoices->where('status_id', $status->id)->count();
            if ($count > 0) {
                $labels[] = $status->name;
                $data[] = $totalProvider > 0 ? round(($count / $totalProvider) * 100, 1) : 0;
                $colors[] = $status->color ?? '#D1D5DB';
            }
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'colors' => $colors,
        ];
    }

    private function getBillingAccountsData()
    {
        $relationTypeCategory = TagCategory::where('slug', 'tipo_relacion')->first();
        $clienteTag = $relationTypeCategory
            ? Tag::where('category_id', $relationTypeCategory->id)->where('name', 'Cliente')->first()
            : null;

        $statusCategory = TagCategory::where('slug', 'estado_factura')->first();
        $allStatuses = $statusCategory
            ? Tag::where('category_id', $statusCategory->id)->get()
            : collect();

        if (!$clienteTag || !$statusCategory) {
            return ['labels' => [], 'data' => [], 'colors' => []];
        }

        $billingAccounts = Invoice::whereHas('contact', function ($q) use ($clienteTag) {
            $q->where('relation_type_id', $clienteTag->id);
        })
            ->where('code', 'like', 'CC-%')
            ->get();

        $totalBilling = $billingAccounts->count();
        $labels = [];
        $data = [];
        $colors = [];

        foreach ($allStatuses as $status) {
            $count = $billingAccounts->where('status_id', $status->id)->count();
            if ($count > 0) {
                $labels[] = $status->name;
                $data[] = $totalBilling > 0 ? round(($count / $totalBilling) * 100, 1) : 0;
                $colors[] = $status->color ?? '#D1D5DB';
            }
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'colors' => $colors,
        ];
    }

    private function getPayrollPaymentsData()
    {
        $statusCategory = TagCategory::where('slug', 'estado_nomina')->first();
        $allStatuses = $statusCategory
            ? Tag::where('category_id', $statusCategory->id)->get()
            : collect();

        $payrolls = Payroll::all();
        $totalPayrolls = $payrolls->count();

        $labels = [];
        $data = [];
        $colors = [];

        foreach ($allStatuses as $status) {
            $count = $payrolls->where('status_id', $status->id)->count();
            if ($count > 0) {
                $labels[] = $status->name;
                $data[] = $totalPayrolls > 0 ? round(($count / $totalPayrolls) * 100, 1) : 0;
                $colors[] = $status->color ?? '#D1D5DB';
            }
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'colors' => $colors,
        ];
    }

    private function getPayrollGenderData()
    {
        $genderCategory = TagCategory::where('slug', 'genero')->first();
        $allGenders = $genderCategory
            ? Tag::where('category_id', $genderCategory->id)->get()
            : collect();

        $employees = Employee::with('gender')->get();
        $totalEmployees = $employees->count();

        $labels = [];
        $data = [];
        $colors = [];

        foreach ($allGenders as $gender) {
            $count = $employees->where('gender_id', $gender->id)->count();
            if ($count > 0) {
                $labels[] = $gender->name;
                $data[] = $totalEmployees > 0 ? round(($count / $totalEmployees) * 100, 1) : 0;
                $colors[] = $gender->color ?? '#D1D5DB';
            }
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'colors' => $colors,
        ];
    }
}
