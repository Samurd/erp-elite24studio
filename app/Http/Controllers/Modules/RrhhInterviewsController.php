<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Models\Applicant;
use App\Models\Interview;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Services\PermissionCacheService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RrhhInterviewsController extends Controller
{
    public function index(Request $request)
    {
        $query = Interview::with([
            'applicant',
            'interviewer',
            'interviewType',
            'status',
            'result'
        ]);

        if ($request->search) {
            $query->whereHas('applicant', function ($q) use ($request) {
                $q->where('full_name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->status_filter) {
            $query->where('status_id', $request->status_filter);
        }

        if ($request->interview_type_filter) {
            $query->where('interview_type_id', $request->interview_type_filter);
        }

        if ($request->result_filter) {
            $query->where('result_id', $request->result_filter);
        }

        if ($request->interviewer_filter) {
            $query->where('interviewer_id', $request->interviewer_filter);
        }

        if ($request->date_from) {
            $query->where('date', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->where('date', '<=', $request->date_to);
        }

        $interviews = $query->orderBy('date', 'desc')
            ->orderBy('time', 'desc')
            ->paginate($request->perPage ?? 10)
            ->withQueryString();

        return Inertia::render('Rrhh/Interviews/Index', [
            'interviews' => $interviews,
            'statusOptions' => $this->getTags('estado_entrevista'),
            'interviewTypeOptions' => $this->getTags('tipo_entrevista'),
            'resultOptions' => $this->getTags('resultado_entrevista'),
            'interviewerOptions' => PermissionCacheService::getUsersByArea('rrhh'),
            'filters' => $request->all(),
        ]);
    }

    public function calendar(Request $request)
    {
        $events = [];
        $interviews = Interview::with(['interviewer', 'applicant', 'interviewType', 'status', 'result'])->get();

        foreach ($interviews as $interview) {
            // Determine color based on status (replicating Livewire logic)
            $color = '#3b82f6'; // Default blue
            if ($interview->status) {
                switch ($interview->status->name) {
                    case 'Programada':
                    case 'Scheduled':
                        $color = '#3b82f6'; // Blue
                        break;
                    case 'Completada':
                    case 'Completed':
                        $color = '#10b981'; // Green
                        break;
                    case 'Cancelada':
                    case 'Cancelled':
                        $color = '#ef4444'; // Red
                        break;
                    case 'En Proceso':
                    case 'In Progress':
                        $color = '#f59e0b'; // Amber
                        break;
                    default:
                        $color = '#6b7280'; // Gray
                }
            }

            $start = $interview->date->format('Y-m-d');
            if ($interview->time) {
                $start .= 'T' . $interview->time->format('H:i:s');
            }

            $events[] = [
                'id' => $interview->id,
                'title' => '🎤 ' . ($interview->applicant->full_name ?? 'Entrevista'),
                'start' => $start,
                'color' => $color,
                'extendedProps' => [
                    'applicant' => $interview->applicant->full_name ?? 'N/A',
                    'interviewer' => $interview->interviewer->name ?? 'Sin asignar',
                    'status' => $interview->status->name ?? 'N/A',
                ]
            ];
        }

        return Inertia::render('Rrhh/Interviews/Calendar', [
            'events' => $events,
        ]);
    }

    public function create()
    {
        return Inertia::render('Rrhh/Interviews/Form', [
            'interview' => null,
            'statusOptions' => $this->getTags('estado_entrevista'),
            'interviewTypeOptions' => $this->getTags('tipo_entrevista'),
            'resultOptions' => $this->getTags('resultado_entrevista'),
            'interviewerOptions' => PermissionCacheService::getUsersByArea('rrhh'),
            'applicants' => Applicant::orderBy('full_name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'applicant_id' => 'required|exists:applicants,id',
            'date' => 'required|date',
            'time' => 'nullable|date_format:H:i',
            'interviewer_id' => 'nullable|exists:users,id',
            'interview_type_id' => 'nullable|exists:tags,id',
            'status_id' => 'nullable|exists:tags,id',
            'result_id' => 'nullable|exists:tags,id',
            'platform' => 'nullable|string|max:255',
            'platform_url' => 'nullable|url|max:500',
            'expected_results' => 'nullable|string',
            'interviewer_observations' => 'nullable|string',
            'rating' => 'nullable|numeric|min:0|max:10',
        ]);

        if (empty($validated['interviewer_id'])) {
            $validated['interviewer_id'] = auth()->id();
        }

        Interview::create($validated);

        return redirect()->route('rrhh.interviews.index')->with('success', 'Entrevista creada exitosamente.');
    }

    public function edit(Interview $interview)
    {
        return Inertia::render('Rrhh/Interviews/Form', [
            'interview' => $interview,
            'statusOptions' => $this->getTags('estado_entrevista'),
            'interviewTypeOptions' => $this->getTags('tipo_entrevista'),
            'resultOptions' => $this->getTags('resultado_entrevista'),
            'interviewerOptions' => PermissionCacheService::getUsersByArea('rrhh'),
            'applicants' => Applicant::orderBy('full_name')->get(),
        ]);
    }

    public function update(Request $request, Interview $interview)
    {
        $validated = $request->validate([
            'applicant_id' => 'required|exists:applicants,id',
            'date' => 'required|date',
            'time' => 'nullable|date_format:H:i',
            'interviewer_id' => 'nullable|exists:users,id',
            'interview_type_id' => 'nullable|exists:tags,id',
            'status_id' => 'nullable|exists:tags,id',
            'result_id' => 'nullable|exists:tags,id',
            'platform' => 'nullable|string|max:255',
            'platform_url' => 'nullable|url|max:500',
            'expected_results' => 'nullable|string',
            'interviewer_observations' => 'nullable|string',
            'rating' => 'nullable|numeric|min:0|max:10',
        ]);

        $interview->update($validated);

        return redirect()->route('rrhh.interviews.index')->with('success', 'Entrevista actualizada exitosamente.');
    }

    public function destroy(Interview $interview)
    {
        $interview->delete();
        return redirect()->route('rrhh.interviews.index')->with('success', 'Entrevista eliminada exitosamente.');
    }

    private function getTags($slug)
    {
        $category = TagCategory::where('slug', $slug)->first();
        return $category ? Tag::where('category_id', $category->id)->get() : collect();
    }
}
