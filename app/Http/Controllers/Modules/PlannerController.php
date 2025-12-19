<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Models\Bucket;
use App\Models\Plan;
use App\Models\Task;
use App\Models\Tag;
use App\Models\TagCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Services\NotificationService;

class PlannerController extends Controller
{
    public function index(Request $request)
    {
        $groupPlans = Plan::whereNotNull('team_id')->get();
        $personalPlans = Plan::whereNull('team_id')
            ->whereNull('project_id')
            ->get();

        $teams = \App\Services\CommonDataCacheService::getAllTeams();

        return Inertia::render('Modules/Planner/Index', [
            'groupPlans' => $groupPlans,
            'personalPlans' => $personalPlans,
            'teams' => $teams,
            'selectedPlan' => null,
            'buckets' => [],
        ]);
    }

    public function show($id)
    {
        $plan = Plan::findOrFail($id);

        $groupPlans = Plan::whereNotNull('team_id')->get();
        $personalPlans = Plan::whereNull('team_id')
            ->whereNull('project_id')
            ->get();

        $plan->load(['team.members']); // Load team members for assignment

        $state_type = TagCategory::where('slug', 'estado_tarea')->first();
        $priority_type = TagCategory::where('slug', 'prioridad_tarea')->first();

        $priorities = $priority_type ? (Tag::where('category_id', $priority_type->id)->get() ?? []) : [];
        $states = $state_type ? (Tag::where('category_id', $state_type->id)->get() ?? []) : [];

        $buckets = Bucket::where('plan_id', $plan->id)
            ->with([
                'tasks' => function ($query) {
                    $query->with(['assignedUsers', 'status', 'priority'])->orderBy('order');
                }
            ])
            ->orderBy('order')
            ->get();

        $teams = \App\Services\CommonDataCacheService::getAllTeams();

        $teams = \App\Services\CommonDataCacheService::getAllTeams();

        return Inertia::render('Modules/Planner/Index', [
            'groupPlans' => $groupPlans,
            'personalPlans' => $personalPlans,
            'teams' => $teams,
            'selectedPlan' => $plan,
            'buckets' => $buckets,
            'priorities' => $priorities,
            'states' => $states,
        ]);
    }

    public function storeBucket(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'plan_id' => 'required|exists:plans,id',
        ]);

        $maxOrder = Bucket::where('plan_id', $validated['plan_id'])->max('order') ?? 0;

        Bucket::create([
            'name' => $validated['name'],
            'plan_id' => $validated['plan_id'],
            'order' => $maxOrder + 1,
        ]);

        return redirect()->back();
    }

    public function updateBucket(Request $request, $id)
    {
        $bucket = Bucket::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $bucket->update(['name' => $validated['name']]);

        return redirect()->back();
    }

    public function destroyBucket($id)
    {
        Bucket::findOrFail($id)->delete();
        return redirect()->back();
    }

    public function storePlan(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'team_id' => 'nullable|exists:teams,id',
            'project_id' => 'nullable|exists:projects,id',
        ]);

        Plan::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'team_id' => $validated['team_id'] ?? null,
            'project_id' => $validated['project_id'] ?? null,
            'owner_id' => auth()->id(),
        ]);

        return redirect()->back();
    }

    public function updatePlan(Request $request, $id)
    {
        $plan = Plan::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $plan->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
        ]);

        return redirect()->back();
    }

    public function reorderBuckets(Request $request)
    {
        $request->validate([
            'orderedIds' => 'required|array',
            'orderedIds.*' => 'integer|exists:buckets,id',
        ]);

        $orderedIds = $request->orderedIds;

        DB::transaction(function () use ($orderedIds) {
            $caseSql = 'CASE id ';
            foreach ($orderedIds as $index => $id) {
                $pos = $index + 1;
                $caseSql .= "WHEN {$id} THEN {$pos} ";
            }
            $caseSql .= 'END';

            $idsCsv = implode(',', $orderedIds);
            DB::update("UPDATE buckets SET `order` = {$caseSql} WHERE id IN ({$idsCsv})");
        });

        return redirect()->back();
    }

    public function reorderTasks(Request $request)
    {
        $request->validate([
            'oldBucketId' => 'required|integer|exists:buckets,id',
            'newBucketId' => 'required|integer|exists:buckets,id',
            'orderedIds' => 'required|array', // Tasks in the NEW bucket
            'orderedIds.*' => 'integer|exists:tasks,id',
        ]);

        $newBucketId = $request->newBucketId;
        $orderedIds = $request->orderedIds;

        DB::transaction(function () use ($newBucketId, $orderedIds) {
            // Update orders and bucket_id
            $caseSql = 'CASE id ';
            foreach ($orderedIds as $index => $taskId) {
                $position = $index + 1;
                $caseSql .= "WHEN {$taskId} THEN {$position} ";
            }
            $caseSql .= 'END';

            $idsCsv = implode(',', $orderedIds);
            DB::update(
                "UPDATE tasks SET bucket_id = ?, `order` = {$caseSql} WHERE id IN ({$idsCsv})",
                [$newBucketId]
            );
        });

        return redirect()->back();
    }

    public function destroyPlan($id)
    {
        $plan = Plan::findOrFail($id);
        $plan->delete();

        return to_route('planner.index');
    }

    public function destroyTask($id)
    {
        Task::findOrFail($id)->delete();
        return redirect()->back();
    }

    public function storeTask(Request $request)
    {
        $validated = $request->validate([
            'bucket_id' => 'required|exists:buckets,id',
            'title' => 'required|string|max:255',
            'status_id' => 'required|exists:tags,id',
            'priority_id' => 'required|exists:tags,id',
            'notes' => 'nullable|string',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'assignedUsers' => 'nullable|array',
            'assignedUsers.*' => 'exists:users,id',
        ]);

        $nextOrder = Task::where('bucket_id', $validated['bucket_id'])->count() + 1;

        $task = Task::create([
            'bucket_id' => $validated['bucket_id'],
            'title' => $validated['title'],
            'status_id' => $validated['status_id'],
            'priority_id' => $validated['priority_id'],
            'notes' => $validated['notes'],
            'start_date' => $validated['start_date'],
            'due_date' => $validated['due_date'],
            'order' => $nextOrder,
            'created_by' => auth()->id(),
        ]);

        if (!empty($validated['assignedUsers'])) {
            $task->assignedUsers()->sync($validated['assignedUsers']);
            $this->notifyAssignees($task, $validated['assignedUsers']);
        }

        return redirect()->back();
    }

    public function updateTask(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'status_id' => 'required|exists:tags,id',
            'priority_id' => 'required|exists:tags,id',
            'notes' => 'nullable|string',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'assignedUsers' => 'nullable|array',
            'assignedUsers.*' => 'exists:users,id',
        ]);

        $task->update([
            'title' => $validated['title'],
            'status_id' => $validated['status_id'],
            'priority_id' => $validated['priority_id'],
            'notes' => $validated['notes'],
            'start_date' => $validated['start_date'],
            'due_date' => $validated['due_date'],
        ]);

        if (isset($validated['assignedUsers'])) {
            $task->assignedUsers()->sync($validated['assignedUsers']);
            // Optionally notify on update too, but simplified for now to just assignment
            $this->notifyAssignees($task, $validated['assignedUsers'], true);
        }

        return redirect()->back();
    }

    private function notifyAssignees(Task $task, array $userIds, $isUpdate = false)
    {
        $notificationService = app(NotificationService::class);
        $users = \App\Models\User::whereIn('id', $userIds)->get();

        foreach ($users as $user) {
            $notificationService->createImmediate(
                $user,
                $isUpdate ? "Tarea Actualizada" : "Nueva tarea asignada",
                ($isUpdate ? "Se ha actualizado la tarea: " : "Tarea: ") . $task->title . " en plan: " . $task->bucket->plan->name,
                [
                    'task_title' => $task->title,
                    'due_date' => $task->due_date?->format('d/m/Y'),
                    'priority' => $task->priority?->name,
                ],
                $task,
                true,
                'emails.task-assigned'
            );
        }
    }
}
