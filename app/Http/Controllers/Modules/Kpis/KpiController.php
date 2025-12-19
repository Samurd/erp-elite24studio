<?php

namespace App\Http\Controllers\Modules\Kpis;

use App\Http\Controllers\Controller;
use App\Models\Kpi;
use App\Models\Role;
use Illuminate\Http\Request;
use Inertia\Inertia;

class KpiController extends Controller
{
    public function index(Request $request)
    {
        $query = Kpi::with([
            'role',
            'records' => function ($query) {
                $query->latest('record_date');
            }
        ]);

        // Search
        if ($request->input('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('indicator_name', 'like', '%' . $request->input('search') . '%')
                    ->orWhere('protocol_code', 'like', '%' . $request->input('search') . '%');
            });
        }

        // Role Filter
        if ($request->input('role_filter')) {
            $query->where('role_id', $request->input('role_filter'));
        }

        // Period Filter
        if ($request->input('period_filter')) {
            $period = $request->input('period_filter');
            if ($period === 'week') {
                $query->whereHas('records', function ($q) {
                    $q->where('record_date', '>=', now()->subDays(7));
                });
            } elseif ($period === 'month') {
                $query->whereHas('records', function ($q) {
                    $q->where('record_date', '>=', now()->subDays(30));
                });
            } elseif ($period === 'quarter') {
                $query->whereHas('records', function ($q) {
                    $q->where('record_date', '>=', now()->subDays(90));
                });
            }
        }

        $perPage = $request->input('perPage', 10);
        $kpis = $query->orderBy('created_at', 'desc')->paginate($perPage)->withQueryString();
        $roles = Role::orderBy('name')->get();

        return Inertia::render('Kpis/Index', [
            'kpis' => $kpis,
            'roles' => $roles,
            'filters' => $request->only(['search', 'role_filter', 'period_filter', 'perPage']),
        ]);
    }

    public function create()
    {
        $roles = Role::orderBy('name')->get();
        return Inertia::render('Kpis/Create', [
            'roles' => $roles,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'indicator_name' => 'required|string|max:255',
            'target_value' => 'nullable|numeric',
            'periodicity_days' => 'required|integer|min:1',
            'role_id' => 'required|exists:roles,id',
        ]);

        // Generate Protocol Code
        $latestKpi = Kpi::latest('id')->first();
        $nextId = $latestKpi ? $latestKpi->id + 1 : 1;
        $validated['protocol_code'] = 'KPI-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);

        Kpi::create($validated);

        return redirect()->route('kpis.index')->with('success', 'KPI creado exitosamente.');
    }

    public function show(Request $request, Kpi $kpi)
    {
        $query = $kpi->records()->with(['createdBy', 'files']);

        // Search in records
        if ($request->input('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('observation', 'like', '%' . $request->input('search') . '%')
                    ->orWhere('value', 'like', '%' . $request->input('search') . '%');
            });
        }

        // Date Filters
        if ($request->input('date_from')) {
            $query->whereDate('record_date', '>=', $request->input('date_from'));
        }

        if ($request->input('date_to')) {
            $query->whereDate('record_date', '<=', $request->input('date_to'));
        }

        $perPage = $request->input('perPage', 10);
        $records = $query->orderBy('record_date', 'desc')->paginate($perPage)->withQueryString();

        return Inertia::render('Kpis/Show', [
            'kpi' => $kpi->load('role'),
            'records' => $records,
            'filters' => $request->only(['search', 'date_from', 'date_to', 'perPage']),
        ]);
    }

    public function edit(Kpi $kpi)
    {
        $roles = Role::orderBy('name')->get();
        return Inertia::render('Kpis/Edit', [
            'kpi' => $kpi,
            'roles' => $roles,
        ]);
    }

    public function update(Request $request, Kpi $kpi)
    {
        $validated = $request->validate([
            'indicator_name' => 'required|string|max:255',
            'target_value' => 'nullable|numeric',
            'periodicity_days' => 'required|integer|min:1',
            'role_id' => 'required|exists:roles,id',
        ]);

        $kpi->update($validated);

        return redirect()->route('kpis.index')->with('success', 'KPI actualizado exitosamente.');
    }

    public function destroy(Kpi $kpi)
    {
        $kpi->delete();
        return redirect()->route('kpis.index')->with('success', 'KPI eliminado exitosamente.');
    }
}
