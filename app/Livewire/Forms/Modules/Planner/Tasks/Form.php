<?php

namespace App\Livewire\Forms\Modules\Planner\Tasks;

use App\Models\Task;
use App\Services\NotificationService;
use Livewire\Attributes\Validate;
use Livewire\Form as LivewireForm;

class Form extends LivewireForm
{
    public ?Task $task = null;

    #[Validate("required|string")]
    public $title;

    #[Validate("required|exists:tags,id")]
    public $status_id;

    #[Validate("required|exists:tags,id")]
    public $priority_id;

    // #[Validate('required|exists:users,id')]
    // public $created_by;

    #[Validate("nullable|string")]
    public $notes;

    #[Validate("nullable|date")]
    public $start_date;

    #[Validate("nullable|date")]
    public $due_date;

    #[Validate("nullable|array")]
    public $assignedUsers = [];

    public function setTask(Task $task)
    {
        $this->task = $task;
        $this->title = $task->title;
        $this->status_id = $task->status_id;
        $this->priority_id = $task->priority_id;
        $this->notes = $task->notes;
        // $this->start_date = $task->start_date;
        // $this->due_date = $task->due_date;
        $this->start_date = $task->start_date ? $task->start_date->format('Y-m-d') : null;
        $this->due_date = $task->due_date ? $task->due_date->format('Y-m-d') : null;
        $this->assignedUsers = $task->assignedUsers->pluck('id')->toArray();
    }

    public function update()
    {
        $notificationService = app(NotificationService::class);
        $this->validate();

        $this->task->update([
            "title" => $this->title,
            "status_id" => $this->status_id,
            "priority_id" => $this->priority_id,
            "notes" => $this->notes,
            "start_date" => $this->start_date,
            "due_date" => $this->due_date,
        ]);

        $this->task->assignedUsers()->sync($this->assignedUsers);

        $users = \App\Models\User::whereIn('id', $this->assignedUsers)->get();
        foreach ($users as $user) {
            $notificationService->createImmediate(
                $user,
                "Tarea Actualizada",
                "Se ha actualizado la tarea: " . $this->task->title . " en plan: " . $this->task->bucket->plan->name,
                [
                    'task_title' => $this->task->title,
                    'due_date' => $this->task->due_date?->format('d/m/Y'),
                    'priority' => $this->task->priority?->name,
                    'image_url' => public_path('images/new_task.jpg'), // Ejemplo: $this->task->image_url
                ],
                $this->task,
                true,
                'emails.task-assigned'
            );
        }

        $this->resetForm();
    }

    public function store($bucket_id)
    {
        $notificationService = app(NotificationService::class);
        $this->validate();

        // $lastOrder = Task::where('bucket_id', $bucket_id)
        //     ->max('order');

        // Si no hay buckets aún, empezamos desde 1
        $nextOrder = Task::where('bucket_id', $bucket_id)->count() + 1;

        $task = Task::create([
            "bucket_id" => $bucket_id,
            "title" => $this->title,
            "status_id" => $this->status_id,
            "priority_id" => $this->priority_id,
            "notes" => $this->notes,
            "start_date" => $this->start_date,
            "due_date" => $this->due_date,
            "order" => $nextOrder,
        ]);

        $task->assignedUsers()->sync($this->assignedUsers);

        $users = \App\Models\User::whereIn('id', $this->assignedUsers)->get();
        foreach ($users as $user) {
            $notificationService->createImmediate(
                $user,
                "Nueva tarea asignada",
                "Tarea: " . $task->title . " en plan: " . $task->bucket->plan->name,
                [
                    'task_title' => $task->title,
                    'due_date' => $task->due_date?->format('d/m/Y'),
                    'priority' => $task->priority?->name,
                    'image_url' => public_path('images/new_task.jpg'), // Ejemplo: $task->image_url
                ],
                $task,
                true,
                'emails.task-assigned'
            );
        }

        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset();
        $this->clearValidation();
    }
}
