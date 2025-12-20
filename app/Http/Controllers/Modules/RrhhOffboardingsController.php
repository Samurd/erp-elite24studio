<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\OffBoarding;
use App\Models\OffBoardingTask;
use App\Models\Project;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RrhhOffboardingsController extends Controller
{
    public function index(Request $request)
    {
        $query = OffBoarding::with(['employee', 'project', 'status', 'responsible']);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->whereHas('employee', function ($eq) use ($request) {
                    $eq->where('full_name', 'like', '%' . $request->search . '%');
                })
                    ->orWhereHas('project', function ($pq) use ($request) {
                        $pq->where('name', 'like', '%' . $request->search . '%');
                    })
                    ->orWhere('reason', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->status_filter) {
            $query->where('status_id', $request->status_filter);
        }

        if ($request->date_from) {
            $query->whereDate('exit_date', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('exit_date', '<=', $request->date_to);
        }

        $offboardings = $query->orderBy('exit_date', 'desc')
            ->paginate($request->perPage ?? 10)
            ->withQueryString();

        return Inertia::render('Rrhh/OffBoardings/Index', [
            'offboardings' => $offboardings,
            'statusOptions' => $this->getTags('estado_offboarding'),
            'filters' => $request->all(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Rrhh/OffBoardings/Form', [
            'offboarding' => null,
            'employees' => Employee::orderBy('full_name')->get(),
            'projects' => Project::orderBy('name')->get(),
            'users' => User::orderBy('name')->get(),
            'statusOptions' => $this->getTags('estado_offboarding'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'project_id' => 'nullable|exists:projects,id',
            'reason' => 'nullable|string',
            'exit_date' => 'required|date',
            'status_id' => 'nullable|exists:tags,id',
            'responsible_id' => 'nullable|exists:users,id',
        ]);

        OffBoarding::create($validated);

        return redirect()->route('rrhh.offboardings.index')->with('success', 'OffBoarding creado exitosamente.');
    }

    public function show(OffBoarding $offboarding)
    {
        $offboarding->load(['employee', 'project', 'status', 'responsible', 'tasks.team', 'tasks.completedBy']);

        return Inertia::render('Rrhh/OffBoardings/Show', [
            'offboarding' => $offboarding,
            'teams' => Team::orderBy('name')->get(),
        ]);
    }

    public function edit(OffBoarding $offboarding)
    {
        return Inertia::render('Rrhh/OffBoardings/Form', [
            'offboarding' => $offboarding,
            'employees' => Employee::orderBy('full_name')->get(),
            'projects' => Project::orderBy('name')->get(),
            'users' => User::orderBy('name')->get(),
            'statusOptions' => $this->getTags('estado_offboarding'),
        ]);
    }

    public function update(Request $request, OffBoarding $offboarding)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'project_id' => 'nullable|exists:projects,id',
            'reason' => 'nullable|string',
            'exit_date' => 'required|date',
            'status_id' => 'nullable|exists:tags,id',
            'responsible_id' => 'nullable|exists:users,id',
        ]);

        $offboarding->update($validated);

        return redirect()->route('rrhh.offboardings.index')->with('success', 'OffBoarding actualizado exitosamente.');
    }

    public function destroy(OffBoarding $offboarding)
    {
        $offboarding->delete();
        return redirect()->route('rrhh.offboardings.index')->with('success', 'OffBoarding eliminado exitosamente.');
    }

    // Task Management Methods

    public function addTask(Request $request, OffBoarding $offboarding)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:500',
            'team_id' => 'nullable|exists:teams,id',
        ]);

        $offboarding->tasks()->create([
            'content' => $validated['content'],
            'team_id' => $validated['team_id'],
            'completed' => false,
        ]);

        return redirect()->back()->with('success', 'Tarea agregada exitosamente.');
    }

    public function toggleTask(OffBoardingTask $task)
    {
        $task->completed = !$task->completed;

        if ($task->completed) {
            $task->completed_by = \Illuminate\Support\Facades\Auth::id();
            $task->completed_at = now();
        } else {
            $task->completed_by = null;
            $task->completed_at = null;
        }

        $task->save();

        return redirect()->back()->with('success', 'Estado de tarea actualizado.');
    }

    public function deleteTask(OffBoardingTask $task)
    {
        $task->delete();
        return redirect()->back()->with('success', 'Tarea eliminada exitosamente.');
    }

    private function getTags($slug)
    {
        $category = TagCategory::where('slug', $slug)->first();
        return $category ? Tag::where('category_id', $category->id)->get() : collect();
    }
}
