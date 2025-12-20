<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Models\Employee;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\TaxRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PayrollsController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $statusFilter = $request->get('status_filter', '');
        $dateFrom = $request->get('date_from', '');
        $dateTo = $request->get('date_to', '');
        $perPage = $request->get('perPage', 10);

        $query = Payroll::query()->with(['employee', 'status']);

        if ($search) {
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('full_name', 'like', '%' . $search . '%');
            });
        }

        if ($statusFilter) {
            $query->where('status_id', $statusFilter);
        }

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $payrolls = $query->latest()->paginate($perPage);

        $statusCategory = TagCategory::where('slug', 'estado_nomina')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        return Inertia::render('Payrolls/Index', [
            'payrolls' => $payrolls,
            'statusOptions' => $statusOptions,
            'filters' => [
                'search' => $search,
                'status_filter' => $statusFilter,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'perPage' => $perPage,
            ]
        ]);
    }

    public function stats(Request $request)
    {
        $yearStats = $request->get('yearStats', date('Y'));
        $yearTaxes = $request->get('yearTaxes', date('Y'));
        $yearPayrolls = $request->get('yearPayrolls', date('Y'));
        $yearDeductions = $request->get('yearDeductions', date('Y'));

        // Status Stats
        $statusStatsRaw = Payroll::join('tags', 'payrolls.status_id', '=', 'tags.id')
            ->whereYear('payrolls.created_at', $yearStats)
            ->select('tags.name', DB::raw('count(*) as count'))
            ->groupBy('tags.name')
            ->pluck('count', 'name')
            ->toArray();

        $statusLabels = array_keys($statusStatsRaw);
        $statusData = array_values($statusStatsRaw);

        // Gender Stats
        $genderStatsRaw = Payroll::join('employees', 'payrolls.employee_id', '=', 'employees.id')
            ->join('tags', 'employees.gender_id', '=', 'tags.id')
            ->whereYear('payrolls.created_at', $yearStats)
            ->select('tags.name', DB::raw('count(*) as count'))
            ->groupBy('tags.name')
            ->pluck('count', 'name')
            ->toArray();

        $genderLabels = array_keys($genderStatsRaw);
        $genderData = array_values($genderStatsRaw);

        // Deductions per Month
        $deductionsRaw = TaxRecord::whereYear('date', $yearDeductions)
            ->select(
                DB::raw('MONTH(date) as month'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $deductionsData = [];
        for ($i = 1; $i <= 12; $i++) {
            $deductionsData[] = ($deductionsRaw[$i] ?? 0) / 100;
        }

        // Recent Payrolls
        $recentPayrolls = Payroll::with(['employee', 'status'])
            ->whereYear('created_at', $yearPayrolls)
            ->latest()
            ->take(5)
            ->get();

        // Recent Taxes
        $recentTaxes = TaxRecord::with(['type', 'status'])
            ->whereYear('date', $yearTaxes)
            ->latest('date')
            ->take(5)
            ->get();

        return Inertia::render('Payrolls/Stats', [
            'statusLabels' => $statusLabels,
            'statusData' => $statusData,
            'genderLabels' => $genderLabels,
            'genderData' => $genderData,
            'deductionsData' => $deductionsData,
            'recentPayrolls' => $recentPayrolls,
            'recentTaxes' => $recentTaxes,
            'filters' => [
                'yearStats' => $yearStats,
                'yearTaxes' => $yearTaxes,
                'yearPayrolls' => $yearPayrolls,
                'yearDeductions' => $yearDeductions,
            ]
        ]);
    }

    public function create()
    {
        $employees = Employee::active()->orderBy('full_name')->get();

        $statusCategory = TagCategory::where('slug', 'estado_nomina')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        return Inertia::render('Payrolls/Form', [
            'payroll' => null,
            'employees' => $employees,
            'statusOptions' => $statusOptions,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'nullable|exists:employees,id',
            'subtotal' => 'required|integer|min:0',
            'bonos' => 'nullable|integer|min:0',
            'deductions' => 'nullable|integer|min:0',
            'total' => 'required|integer',
            'status_id' => 'nullable|exists:tags,id',
            'observations' => 'nullable|string',
        ]);

        $payroll = Payroll::create($validated);

        // Handle file attachments
        if ($request->has('pending_file_ids') && is_array($request->pending_file_ids)) {
            \App\Actions\LinkFileAction::run($payroll, $request->pending_file_ids);
        }

        return redirect()->route('finances.payrolls.index');
    }

    public function edit(Payroll $payroll)
    {
        $payroll->load('files');

        $employees = Employee::active()->orderBy('full_name')->get();

        $statusCategory = TagCategory::where('slug', 'estado_nomina')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        return Inertia::render('Payrolls/Form', [
            'payroll' => $payroll,
            'employees' => $employees,
            'statusOptions' => $statusOptions,
        ]);
    }

    public function update(Request $request, Payroll $payroll)
    {
        $validated = $request->validate([
            'employee_id' => 'nullable|exists:employees,id',
            'subtotal' => 'required|integer|min:0',
            'bonos' => 'nullable|integer|min:0',
            'deductions' => 'nullable|integer|min:0',
            'total' => 'required|integer',
            'status_id' => 'nullable|exists:tags,id',
            'observations' => 'nullable|string',
        ]);

        $payroll->update($validated);

        // Handle file attachments
        if ($request->has('pending_file_ids') && is_array($request->pending_file_ids)) {
            \App\Actions\LinkFileAction::run($payroll, $request->pending_file_ids);
        }

        return redirect()->route('finances.payrolls.index');
    }

    public function show(Payroll $payroll)
    {
        $payroll->load(['employee', 'status', 'files']);

        return Inertia::render('Payrolls/Show', [
            'payroll' => $payroll,
        ]);
    }

    public function destroy(Payroll $payroll)
    {
        $payroll->delete();
        return redirect()->route('finances.payrolls.index');
    }
}
