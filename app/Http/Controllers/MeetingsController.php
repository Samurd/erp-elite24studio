<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\Team;
use App\Models\User;
use App\Services\MeetingNotificationService;
use App\Services\CommonDataCacheService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class MeetingsController extends Controller
{
    public function index(Request $request)
    {
        $query = Meeting::with([
            'team',
            'status',
            'responsibles'
        ]);

        // Search by title or notes
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('notes', 'like', '%' . $search . '%')
                    ->orWhere('observations', 'like', '%' . $search . '%');
            });
        }

        // Filter by status
        if ($status = $request->input('status_filter')) {
            $query->where('status_id', $status);
        }

        // Filter by team
        if ($team = $request->input('team_filter')) {
            $query->where('team_id', $team);
        }

        // Filter by goal
        if ($request->has('goal_filter') && $request->input('goal_filter') !== null) {
            $query->where('goal', $request->input('goal_filter'));
        }

        // Filter by date range
        if ($dateFrom = $request->input('date_from')) {
            $query->where('date', '>=', $dateFrom);
        }

        if ($dateTo = $request->input('date_to')) {
            $query->where('date', '<=', $dateTo);
        }

        $meetings = $query->orderBy('date', 'desc')
            ->orderBy('start_time', 'desc')
            ->paginate($request->input('perPage', 10))
            ->withQueryString();

        // Get options for filters
        $statusCategory = TagCategory::where('slug', 'estado_reunion')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();
        $teamOptions = Team::all();

        return Inertia::render('Meetings/Index', [
            'meetings' => $meetings,
            'statusOptions' => $statusOptions,
            'teamOptions' => $teamOptions,
            'filters' => $request->only(['search', 'status_filter', 'team_filter', 'goal_filter', 'date_from', 'date_to', 'perPage']),
        ]);
    }

    public function create()
    {
        // Get status options
        $statusCategory = TagCategory::where('slug', 'estado_reunion')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        // Get team options (cached)
        $teamOptions = CommonDataCacheService::getAllTeams();

        // Get user options for responsibles (cached)
        $userOptions = CommonDataCacheService::getAllUsers();

        return Inertia::render('Meetings/Form', [
            'statusOptions' => $statusOptions,
            'teamOptions' => $teamOptions,
            'userOptions' => $userOptions,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'team_id' => 'nullable|exists:teams,id',
            'status_id' => 'nullable|exists:tags,id',
            'notes' => 'nullable|string',
            'observations' => 'nullable|string',
            'url' => 'nullable|url',
            'goal' => 'boolean',
            'responsibles' => 'nullable|array',
            'responsibles.*' => 'exists:users,id',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $meeting = Meeting::create([
                'title' => $validated['title'],
                'date' => $validated['date'],
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'team_id' => $validated['team_id'] ?? null,
                'status_id' => $validated['status_id'] ?? null,
                'notes' => $validated['notes'],
                'observations' => $validated['observations'],
                'url' => $validated['url'],
                'goal' => $validated['goal'] ?? false,
            ]);

            if (!empty($validated['responsibles'])) {
                $meeting->responsibles()->attach($validated['responsibles']);
            }

            // Send notifications
            app(MeetingNotificationService::class)->notifyNewMeeting($meeting);
        });

        return redirect()->route('meetings.index')->with('success', 'Reunión creada exitosamente.');
    }

    public function show(Meeting $meeting)
    {
        $meeting->load([
            'team',
            'status',
            'responsibles'
        ]);

        return Inertia::render('Meetings/Show', [
            'meeting' => $meeting
        ]);
    }

    public function edit(Meeting $meeting)
    {
        $meeting->load('responsibles');

        // Get status options
        $statusCategory = TagCategory::where('slug', 'estado_reunion')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        // Get team options (cached)
        $teamOptions = CommonDataCacheService::getAllTeams();

        // Get user options for responsibles (cached)
        $userOptions = CommonDataCacheService::getAllUsers();

        return Inertia::render('Meetings/Form', [
            'meeting' => $meeting,
            'statusOptions' => $statusOptions,
            'teamOptions' => $teamOptions,
            'userOptions' => $userOptions,
        ]);
    }

    public function update(Request $request, Meeting $meeting)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'team_id' => 'nullable|exists:teams,id',
            'status_id' => 'nullable|exists:tags,id',
            'notes' => 'nullable|string',
            'observations' => 'nullable|string',
            'url' => 'nullable|url',
            'goal' => 'boolean',
            'responsibles' => 'nullable|array',
            'responsibles.*' => 'exists:users,id',
        ]);

        DB::transaction(function () use ($validated, $meeting) {
            $meeting->update([
                'title' => $validated['title'],
                'date' => $validated['date'],
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'team_id' => $validated['team_id'] ?? null,
                'status_id' => $validated['status_id'] ?? null,
                'notes' => $validated['notes'],
                'observations' => $validated['observations'],
                'url' => $validated['url'],
                'goal' => $validated['goal'] ?? false,
            ]);

            if (isset($validated['responsibles'])) {
                $meeting->responsibles()->sync($validated['responsibles']);
            }
        });

        return redirect()->route('meetings.show', $meeting->id)->with('success', 'Reunión actualizada exitosamente.');
    }

    public function destroy(Meeting $meeting)
    {
        $meeting->delete();
        return redirect()->route('meetings.index')->with('success', 'Reunión eliminada exitosamente.');
    }
}
