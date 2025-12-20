<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Tag;
use App\Models\TagCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RrhhAttendancesController extends Controller
{
    public function index(Request $request)
    {
        $view = $request->input('view', 'daily');

        if ($view === 'consolidated') {
            return $this->consolidatedView($request);
        }

        return $this->dailyView($request);
    }

    private function dailyView(Request $request)
    {
        $query = Attendance::with(['employee', 'status', 'modality']);

        if ($request->search) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('full_name', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->employee_filter) {
            $query->where('employee_id', $request->employee_filter);
        }

        if ($request->status_filter) {
            $query->where('status_id', $request->status_filter);
        }

        if ($request->modality_filter) {
            $query->where('modality_id', $request->modality_filter);
        }

        if ($request->date_from) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $attendances = $query->orderBy('date', 'desc')
            ->orderBy('check_in', 'desc')
            ->paginate($request->perPage ?? 10)
            ->withQueryString();

        return Inertia::render('Rrhh/Attendances/Index', [
            'view' => 'daily',
            'attendances' => $attendances,
            'employees' => Employee::orderBy('full_name')->get(),
            'statusOptions' => $this->getTags('estado_asistencia'),
            'modalityOptions' => $this->getTags('modalidad_trabajo'),
            'filters' => $request->all(),
        ]);
    }

    private function consolidatedView(Request $request)
    {
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));

        $data = $this->getConsolidatedData($month, $year);

        return Inertia::render('Rrhh/Attendances/Index', [
            'view' => 'consolidated',
            'consolidated' => $data,
            'statusTags' => $this->getTags('estado_asistencia'),
            'filters' => $request->all(),
            'currentMonth' => $month,
            'currentYear' => $year,
        ]);
    }

    private function getConsolidatedData($month, $year)
    {
        $startDate = Carbon::create($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();

        $consolidated = [];
        $currentDate = $startDate->copy();

        $totalEmployees = Employee::count();
        $statusTags = $this->getTags('estado_asistencia');

        while ($currentDate <= $endDate) {
            $dayAttendances = Attendance::whereDate('date', $currentDate->format('Y-m-d'))->get();

            $statusCounts = [];
            foreach ($statusTags as $tag) {
                $statusCounts[$tag->id] = $dayAttendances->where('status_id', $tag->id)->count();
            }

            $specialDay = null;
            if ($currentDate->isWeekend()) {
                $specialDay = $currentDate->isSaturday() ? 'Sábado' : 'Domingo';
            }

            $consolidated[] = [
                'date' => $currentDate->format('Y-m-d'), // Send as string for serializability
                'day_number' => $currentDate->day,
                'day_name' => $currentDate->isoFormat('dddd'),
                'total_employees' => $totalEmployees,
                'status_counts' => $statusCounts,
                'special_day' => $specialDay,
            ];

            $currentDate->addDay();
        }

        return $consolidated;
    }

    public function create()
    {
        return Inertia::render('Rrhh/Attendances/Form', [
            'attendance' => null,
            'employees' => Employee::orderBy('full_name')->get(),
            'statusOptions' => $this->getTags('estado_asistencia'),
            'modalityOptions' => $this->getTags('modalidad_trabajo'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'check_in' => 'required|date_format:H:i',
            'check_out' => 'required|date_format:H:i',
            'status_id' => 'nullable|exists:tags,id',
            'modality_id' => 'nullable|exists:tags,id',
            'observations' => 'nullable|string',
        ]);

        Attendance::create($validated);

        return redirect()->route('rrhh.attendances.index')->with('success', 'Asistencia registrada exitosamente.');
    }

    public function edit(Attendance $attendance)
    {
        return Inertia::render('Rrhh/Attendances/Form', [
            'attendance' => $attendance,
            'employees' => Employee::orderBy('full_name')->get(),
            'statusOptions' => $this->getTags('estado_asistencia'),
            'modalityOptions' => $this->getTags('modalidad_trabajo'),
        ]);
    }

    public function update(Request $request, Attendance $attendance)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'check_in' => 'required|date_format:H:i',
            'check_out' => 'required|date_format:H:i',
            'status_id' => 'nullable|exists:tags,id',
            'modality_id' => 'nullable|exists:tags,id',
            'observations' => 'nullable|string',
        ]);

        $attendance->update($validated);

        return redirect()->route('rrhh.attendances.index')->with('success', 'Asistencia actualizada exitosamente.');
    }

    public function destroy(Attendance $attendance)
    {
        $attendance->delete();
        return redirect()->route('rrhh.attendances.index')->with('success', 'Asistencia eliminada exitosamente.');
    }

    private function getTags($slug)
    {
        $category = TagCategory::where('slug', $slug)->first();
        return $category ? Tag::where('category_id', $category->id)->get() : collect();
    }
}
