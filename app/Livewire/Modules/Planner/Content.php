<?php

namespace App\Livewire\Modules\Planner;

use App\Models\Bucket;
use App\Models\Plan;
use App\Models\Task;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class Content extends Component
{
    public $selectedPlan = null;
    public $buckets = [];
    public $groupPlans = [];
    public $personalPlans = [];
    public $newBucketName = '';

    protected $listeners = [
        'plan-created' => 'refreshPlans',
        'plan-updated' => 'refreshPlans',
        'bucket-created' => 'refreshBuckets',
        'task-created' => 'refreshBuckets',
        'task-updated' => 'refreshBuckets',
        'task-deleted' => 'refreshBuckets',
        'refresh-plans' => 'refreshPlans',
    ];

    public function mount()
    {
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
        $this->groupPlans = Plan::whereNotNull('team_id')->get();
        $this->personalPlans = Plan::whereNull('team_id')
            ->whereNull('project_id')
            ->get();
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
        if ($plan) {
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
        return view('livewire.modules.planner.content');
    }
}
