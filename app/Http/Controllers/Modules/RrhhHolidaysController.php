<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Holiday;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Actions\Cloud\Files\LinkFileAction;

class RrhhHolidaysController extends Controller
{
    public function index(Request $request)
    {
        $query = Holiday::with(['employee', 'type', 'status', 'approver']);

        if ($request->search) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('full_name', 'like', '%' . $request->search . '%')
                    ->orWhere('identification_number', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->type_filter) {
            $query->where('type_id', $request->type_filter);
        }

        if ($request->status_filter) {
            $query->where('status_id', $request->status_filter);
        }

        $year = $request->input('year', date('Y'));
        if ($year) {
            $query->whereYear('start_date', $year);
        }

        $holidays = $query->orderBy('start_date', 'desc')
            ->paginate($request->perPage ?? 10)
            ->withQueryString();

        return Inertia::render('Rrhh/Holidays/Index', [
            'holidays' => $holidays,
            'typeOptions' => $this->getTags('tipo_vacacion'),
            'statusOptions' => $this->getTags('estado_vacacion'),
            'yearOptions' => range(date('Y') - 5, date('Y') + 2),
            'filters' => array_merge($request->all(), ['year' => $year]),
        ]);
    }

    public function create()
    {
        return Inertia::render('Rrhh/Holidays/Form', [
            'holiday' => null,
            'employees' => Employee::orderBy('full_name')->get(),
            'users' => User::orderBy('name')->get(),
            'typeOptions' => $this->getTags('tipo_vacacion'),
            'statusOptions' => $this->getTags('estado_vacacion'),
        ]);
    }

    public function store(Request $request, LinkFileAction $linkFileAction)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'type_id' => 'nullable|exists:tags,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status_id' => 'nullable|exists:tags,id',
            'approver_id' => 'nullable|exists:users,id',
            'pending_file_ids' => 'nullable|array',
        ]);

        $holiday = Holiday::create([
            'employee_id' => $validated['employee_id'],
            'type_id' => $validated['type_id'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'status_id' => $validated['status_id'],
            'approver_id' => $validated['approver_id'],
        ]);

        // Handle files
        if (!empty($validated['pending_file_ids'])) {
            $linkFileAction->execute($validated['pending_file_ids'], $holiday);
        }

        return redirect()->route('rrhh.holidays.index')->with('success', 'Solicitud creada exitosamente.');
    }

    public function show(Holiday $holiday)
    {
        $year = request('year', date('Y'));
        $perPage = request('perPage', 10);

        // Historical data for this employee
        $historicalQuery = Holiday::with(['employee', 'type', 'status', 'approver'])
            ->where('employee_id', $holiday->employee_id) // Filter by same employee as viewed
            ->whereYear('start_date', $year)
            ->orderBy('start_date', 'desc');

        $historicalData = $historicalQuery->paginate($perPage)->withQueryString();

        // Stats
        $yearlyStats = Holiday::where('employee_id', $holiday->employee_id)
            ->whereYear('start_date', $year)->get();

        $totalDays = $yearlyStats->sum(function ($h) {
            $start = Carbon::parse($h->start_date);
            $end = Carbon::parse($h->end_date);
            return $start->diffInDays($end) + 1;
        });

        $lastDate = $yearlyStats->max('end_date');
        $requestsInYear = $yearlyStats->count();

        $holiday->load(['employee', 'type', 'status', 'approver', 'files']);

        return Inertia::render('Rrhh/Holidays/Show', [
            'holiday' => $holiday,
            'historicalData' => $historicalData,
            'stats' => [
                'totalDays' => $totalDays,
                'lastDate' => $lastDate,
                'requestsInYear' => $requestsInYear,
            ],
            'filters' => ['year' => $year],
            'yearOptions' => range(date('Y') - 5, date('Y') + 2),
        ]);
    }

    public function edit(Holiday $holiday)
    {
        $holiday->load('files'); // Ensure files are loaded for ModelAttachments

        return Inertia::render('Rrhh/Holidays/Form', [
            'holiday' => $holiday,
            'employees' => Employee::orderBy('full_name')->get(),
            'users' => User::orderBy('name')->get(),
            'typeOptions' => $this->getTags('tipo_vacacion'),
            'statusOptions' => $this->getTags('estado_vacacion'),
        ]);
    }

    public function update(Request $request, Holiday $holiday)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'type_id' => 'nullable|exists:tags,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status_id' => 'nullable|exists:tags,id',
            'approver_id' => 'nullable|exists:users,id',
        ]);

        $holiday->update($validated);

        return redirect()->route('rrhh.holidays.index')->with('success', 'Solicitud actualizada exitosamente.');
    }

    public function destroy(Holiday $holiday)
    {
        $holiday->delete();
        return redirect()->route('rrhh.holidays.index')->with('success', 'Solicitud eliminada exitosamente.');
    }

    private function getTags($slug)
    {
        $category = TagCategory::where('slug', $slug)->first();
        return $category ? Tag::where('category_id', $category->id)->get() : collect();
    }
}
