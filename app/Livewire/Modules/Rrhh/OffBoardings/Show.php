<?php

namespace App\Livewire\Modules\Rrhh\OffBoardings;

use Livewire\Component;
use App\Models\OffBoarding;
use App\Models\OffBoardingTask;
use App\Models\Team;

class Show extends Component
{
    public OffBoarding $offboarding;
    public $newTaskContent = '';
    public $newTaskTeamId = null;

    public function mount(OffBoarding $offboarding)
    {
        $this->offboarding = $offboarding;
    }

    public function addTask()
    {
        $this->validate([
            'newTaskContent' => 'required|string|max:500',
            'newTaskTeamId' => 'nullable|exists:teams,id',
        ]);

        OffBoardingTask::create([
            'off_boarding_id' => $this->offboarding->id,
            'content' => $this->newTaskContent,
            'team_id' => $this->newTaskTeamId,
            'completed' => false,
        ]);

        $this->newTaskContent = '';
        $this->newTaskTeamId = null;

        session()->flash('success', 'Tarea agregada exitosamente.');

        // Refresh the offboarding to load new tasks
        $this->offboarding->refresh();
    }

    public function toggleTask($taskId)
    {
        $task = OffBoardingTask::find($taskId);

        if ($task && $task->off_boarding_id === $this->offboarding->id) {
            $task->completed = !$task->completed;

            if ($task->completed) {
                $task->completed_by = auth()->id();
                $task->completed_at = now();
            } else {
                $task->completed_by = null;
                $task->completed_at = null;
            }

            $task->save();

            // Refresh the offboarding
            $this->offboarding->refresh();
        }
    }

    public function deleteTask($taskId)
    {
        $task = OffBoardingTask::find($taskId);

        if ($task && $task->off_boarding_id === $this->offboarding->id) {
            $task->delete();
            session()->flash('success', 'Tarea eliminada exitosamente.');

            // Refresh the offboarding
            $this->offboarding->refresh();
        }
    }

    public function render()
    {
        // Reload offboarding with all relationships
        $this->offboarding->load(['employee', 'project', 'status', 'responsible', 'tasks.team', 'tasks.completedBy']);

        // Get teams for task assignment
        $teams = Team::orderBy('name')->get();

        return view('livewire.modules.rrhh.off-boardings.show', [
            'teams' => $teams,
        ]);
    }
}
