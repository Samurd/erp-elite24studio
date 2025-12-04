<?php

namespace App\Livewire\Modules\Projects;

use App\Models\Bucket;
use App\Models\Plan;
use App\Models\Task;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class PlannerContent extends Component
{
    public $projectId;
    public $selectedPlan = null;
    public $buckets = [];
    public $projectPlans = [];
    public $newBucketName = '';

    public function getGanttTasksProperty()
    {
        if (!$this->selectedPlan) {
            return [];
        }

        $tasks = [];
        foreach ($this->buckets as $bucket) {
            foreach ($bucket->tasks as $task) {
                // Determine dates
                $start = $task->start_date
                    ? \Carbon\Carbon::parse($task->start_date)
                    : \Carbon\Carbon::parse($task->created_at);

                $end = $task->due_date
                    ? \Carbon\Carbon::parse($task->due_date)
                    : $start->copy()->addDay();

                // Ensure end date is after start date
                if ($end->lessThanOrEqualTo($start)) {
                    $end = $start->copy()->addDay();
                }

                // Determine progress based on status
                $progress = 0;
                $statusName = strtolower($task->status->name ?? '');
                if (str_contains($statusName, 'completado') || str_contains($statusName, 'completada') || str_contains($statusName, 'hecho')) {
                    $progress = 100;
                } elseif (str_contains($statusName, 'proceso') || str_contains($statusName, 'progreso')) {
                    $progress = 50;
                }

                // Determine class for styling
                $customClass = 'bar-default';
                if ($progress == 100)
                    $customClass = 'bar-completed';
                elseif ($progress == 50)
                    $customClass = 'bar-inprogress';
                elseif ($task->priority && (strtolower($task->priority->name) == 'alta' || strtolower($task->priority->name) == 'urgente'))
                    $customClass = 'bar-urgent';

                // Collect assigned users
                $assignedUsers = $task->assignedUsers->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'initials' => strtoupper(substr($user->name, 0, 2))
                    ];
                })->toArray();

                $tasks[] = [
                    'id' => (string) $task->id,
                    'name' => $task->title,
                    'start' => $start->format('Y-m-d') . 'T12:00:00',
                    'end' => $end->format('Y-m-d') . 'T12:00:00',
                    'progress' => $progress,
                    'dependencies' => '',
                    'custom_class' => $customClass,
                    'bucket_id' => $task->bucket_id,
                    // Additional fields for enhanced popup
                    'description' => $task->description ?? '',
                    'status_name' => $task->status->name ?? 'Sin estado',
                    'priority_name' => $task->priority->name ?? 'Sin prioridad',
                    'bucket_name' => $bucket->name,
                    'assigned_users' => $assignedUsers,
                    'start_formatted' => $start->format('d/m/Y'),
                    'end_formatted' => $end->format('d/m/Y'),
                    'created_at' => $task->created_at->format('d/m/Y H:i')
                ];
            }
        }

        return $tasks;
    }

    public function updateTaskDates($taskId, $startDate, $endDate)
    {
        $task = \App\Models\Task::find($taskId);
        if ($task) {
            $task->update([
                'start_date' => \Carbon\Carbon::parse($startDate)->format('Y-m-d'),
                'due_date' => \Carbon\Carbon::parse($endDate)->format('Y-m-d')
            ]);

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Fechas actualizadas correctamente'
            ]);

            // Update buckets in memory and allow Livewire to update the board view
            // The Gantt won't disappear because it's protected by wire:ignore
            $this->buckets = Bucket::where('plan_id', $this->selectedPlan)
                ->with([
                    'tasks' => function ($query) {
                        $query->with('assignedUsers')->orderBy('order');
                    }
                ])
                ->orderBy('order')
                ->get();

            $this->cachedBuckets[$this->selectedPlan] = $this->buckets;
        }
    }

    private function getStatusColor($statusName)
    {
        $status = strtolower($statusName ?? '');
        return match (true) {
            str_contains($status, 'complet') => 'rgba(34, 197, 94, 0.7)', // Green
            str_contains($status, 'progres') => 'rgba(59, 130, 246, 0.7)', // Blue
            str_contains($status, 'pendient') => 'rgba(234, 179, 8, 0.7)', // Yellow
            default => 'rgba(156, 163, 175, 0.7)', // Gray
        };
    }

    protected $listeners = [
        'plan-created' => 'refreshPlans',
        'plan-updated' => 'refreshPlans',
        'refresh-plans' => 'refreshPlans',
        'refresh-buckets' => 'refreshBuckets',
        'bucket-created' => 'refreshBuckets',
        'task-created' => 'refreshBuckets',
        'task-updated' => 'refreshBuckets',
        'task-deleted' => 'refreshBuckets',
    ];

    public function mount($projectId)
    {
        $this->projectId = $projectId;
        $this->refreshPlans();
    }

    public function updatedSelectedPlan($planId)
    {
        if (isset($this->cachedBuckets[$planId])) {
            $this->buckets = $this->cachedBuckets[$planId];
            return;
        }

        $this->refreshBuckets();
    }

    public function refreshPlans()
    {
        // Filtrar planes por project_id
        $this->projectPlans = Plan::where('project_id', $this->projectId)->get();
    }

    private $cachedBuckets = [];

    public function refreshBuckets()
    {
        if (!$this->selectedPlan) {
            $this->buckets = [];
            return;
        }

        $this->buckets = Bucket::where('plan_id', $this->selectedPlan)
            ->with([
                'tasks' => function ($query) {
                    $query->with('assignedUsers')->orderBy('order');
                }
            ])
            ->orderBy('order')
            ->get();

        $this->cachedBuckets[$this->selectedPlan] = $this->buckets;
    }

    public function resetSelection()
    {
        $this->selectedPlan = null;
        $this->buckets = [];
    }

    public function createBucket()
    {
        if (empty($this->newBucketName)) {
            return;
        }

        $maxOrder = Bucket::where('plan_id', $this->selectedPlan)->max('order') ?? 0;

        Bucket::create([
            'name' => $this->newBucketName,
            'plan_id' => $this->selectedPlan,
            'order' => $maxOrder + 1,
        ]);

        $this->newBucketName = '';
        $this->refreshBuckets();
    }

    public function deleteBucket($bucketId)
    {
        Bucket::find($bucketId)?->delete();
        $this->refreshBuckets();
    }

    public function updateBucketName($bucketId, $newName)
    {
        $bucket = Bucket::find($bucketId);
        if ($bucket && !empty($newName)) {
            $bucket->update(['name' => $newName]);
            $this->refreshBuckets();
            $this->skipRender();
        }
    }

    public function deletePlan($planId)
    {
        $plan = Plan::find($planId);
        if ($plan && $plan->project_id == $this->projectId) {
            $plan->delete(); // Cascade will delete buckets and tasks
            $this->selectedPlan = null;
            $this->buckets = [];
            $this->refreshPlans();
        }
    }

    public function deleteTask($taskId)
    {
        Task::find($taskId)?->delete();
        $this->refreshBuckets();
    }

    public function reorderBuckets($orderedIds)
    {
        $orderedIds = array_map('intval', $orderedIds);

        $currentOrder = Bucket::where('plan_id', $this->selectedPlan)
            ->orderBy('order')
            ->pluck('id')
            ->toArray();

        if ($currentOrder === $orderedIds) {
            $this->skipRender();
            return; // No change
        }

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

        // Update local state without full refresh to avoid visual flicker
        $this->buckets = Bucket::where('plan_id', $this->selectedPlan)
            ->with([
                'tasks' => function ($query) {
                    $query->with('assignedUsers')->orderBy('order');
                }
            ])
            ->orderBy('order')
            ->get();

        $this->cachedBuckets[$this->selectedPlan] = $this->buckets;

        // Skip render to prevent Livewire from re-rendering during drag
        $this->skipRender();
    }

    public function reorderTasks($oldBucketId, $newBucketId, $orderedIds)
    {
        $orderedIds = array_map('intval', $orderedIds);

        $currentTargetOrder = Task::where('bucket_id', $newBucketId)->orderBy('order')->pluck('id')->toArray();
        $currentSourceOrder = Task::where('bucket_id', $oldBucketId)->orderBy('order')->pluck('id')->toArray();

        // Skip if no real change
        if ($oldBucketId == $newBucketId && $currentTargetOrder === $orderedIds) {
            $this->skipRender();
            return;
        }

        DB::transaction(function () use ($oldBucketId, $newBucketId, $orderedIds) {
            // 🔹 Bulk update task order and bucket assignment
            if (!empty($orderedIds)) {
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
            }

            // 🔹 Reindex source bucket (remove gaps)
            if ($oldBucketId != $newBucketId) {
                $remaining = Task::where('bucket_id', $oldBucketId)->orderBy('order')->get();
                foreach ($remaining as $i => $task) {
                    $pos = $i + 1;
                    if ($task->order !== $pos) {
                        $task->order = $pos;
                        $task->save();
                    }
                }
            }

            // 🔹 Reindex target bucket
            $targetTasks = Task::where('bucket_id', $newBucketId)->orderBy('order')->get();
            foreach ($targetTasks as $i => $task) {
                $pos = $i + 1;
                if ($task->order !== $pos) {
                    $task->order = $pos;
                    $task->save();
                }
            }
        });

        // Update local state without full refresh to avoid visual flicker
        $this->buckets = Bucket::where('plan_id', $this->selectedPlan)
            ->with([
                'tasks' => function ($query) {
                    $query->with('assignedUsers')->orderBy('order');
                }
            ])
            ->orderBy('order')
            ->get();

        $this->cachedBuckets[$this->selectedPlan] = $this->buckets;

        // Always skip render to prevent visual flicker during drag
        // Livewire will update the DOM reactively based on the updated $this->buckets state
        $this->skipRender();
    }

    public function render()
    {
        return view('livewire.modules.projects.planner-content');
    }
}
