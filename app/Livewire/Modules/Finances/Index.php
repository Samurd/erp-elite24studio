<?php

namespace App\Livewire\Modules\Finances;

use App\Models\Expense;
use App\Models\Income;
use App\Models\Invoice;
use App\Models\Payroll;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\Employee;
use Livewire\Component;

class Index extends Component
{
    public $totalExpenses;
    public $totalGrossIncome;
    public $netIncome;
    public $recentTransactions = [];
    public $clientInvoicesData = ['labels' => [], 'data' => [], 'colors' => []];
    public $providerInvoicesData = ['labels' => [], 'data' => [], 'colors' => []];
    public $billingAccountsData = ['labels' => [], 'data' => [], 'colors' => []];
    public $payrollPaymentsData = ['labels' => [], 'data' => [], 'colors' => []];
    public $payrollGenderData = ['labels' => [], 'data' => [], 'colors' => []];

    public function mount()
    {
        $this->totalExpenses = Expense::sum("amount");
        $this->totalGrossIncome = Income::sum("amount");
        $this->netIncome = $this->totalGrossIncome - $this->totalExpenses;

        $this->loadRecentTransactions();
        $this->loadInvoiceChartData();
        $this->loadPayrollChartData();
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

        $this->recentTransactions = $transactions->toArray();
    }

    private function loadInvoiceChartData()
    {
        // Get relation type tags
        $relationTypeCategory = TagCategory::where('slug', 'tipo_relacion')->first();
        $clienteTag = $relationTypeCategory
            ? Tag::where('category_id', $relationTypeCategory->id)->where('name', 'Cliente')->first()
            : null;
        $proveedorTag = $relationTypeCategory
            ? Tag::where('category_id', $relationTypeCategory->id)->where('name', 'Proveedor')->first()
            : null;

        // Get all invoice statuses dynamically
        $statusCategory = TagCategory::where('slug', 'estado_factura')->first();
        $allStatuses = $statusCategory
            ? Tag::where('category_id', $statusCategory->id)->get()
            : collect();

        // Client invoices (INV- but not INV-PRV-)
        if ($clienteTag && $statusCategory) {
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
                if ($count > 0) { // Solo incluir estados que tengan facturas
                    $labels[] = $status->name;
                    $data[] = $totalClient > 0 ? round(($count / $totalClient) * 100, 1) : 0;
                    $colors[] = $status->color ?? '#D1D5DB'; // Color del tag o gris por defecto
                }
            }

            $this->clientInvoicesData = [
                'labels' => $labels,
                'data' => $data,
                'colors' => $colors,
            ];
        }

        // Provider invoices (INV-PRV-)
        if ($proveedorTag && $statusCategory) {
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

            $this->providerInvoicesData = [
                'labels' => $labels,
                'data' => $data,
                'colors' => $colors,
            ];
        }

        // Billing accounts (CC-)
        if ($clienteTag && $statusCategory) {
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

            $this->billingAccountsData = [
                'labels' => $labels,
                'data' => $data,
                'colors' => $colors,
            ];
        }
    }

    private function loadPayrollChartData()
    {
        // Get payroll status category
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

        $this->payrollPaymentsData = [
            'labels' => $labels,
            'data' => $data,
            'colors' => $colors,
        ];

        // Gender distribution - Dynamic based on gender tags
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

        $this->payrollGenderData = [
            'labels' => $labels,
            'data' => $data,
            'colors' => $colors,
        ];
    }

    public function render()
    {
        return view("livewire.modules.finances.index");
    }
}
