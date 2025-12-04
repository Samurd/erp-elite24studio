<?php

namespace App\Livewire\Modules\Projects;

use App\Models\Project;
use Livewire\Component;

class Show extends Component
{
    public $project;

    public function mount(Project $project)
    {
        $this->project = $project->load([
            'contact',
            'status',
            'projectType',
            'currentStage',
            'responsible',
            'team',
            'stages',
            'plans.owner',
            'plans.team',
            'plans.buckets',
            'plans.tasks',
            'files'
        ]);
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

    public function render()
    {
        return view('livewire.modules.projects.show', [
            'project' => $this->project,
        ]);
    }
}
