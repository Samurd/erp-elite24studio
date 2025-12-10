<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Project;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ProjectPdfController extends Controller
{
    public function exportGantt(Project $project, Plan $plan)
    {
        // 1. Validate relationships
        if ($plan->project_id !== $project->id) {
            abort(404);
        }

        // 2. Fetch Data (Optimize for read)
        $buckets = $plan->buckets()
            ->with([
                'tasks' => function ($query) {
                    $query->with(['assignedUsers', 'status', 'priority'])
                        ->orderBy('order');
                }
            ])
            ->orderBy('order')
            ->get();

        // 3. Flatten tasks for the linear Gantt view
        $tasks = [];
        $minDate = Carbon::now();
        $maxDate = Carbon::now()->addWeeks(1);

        foreach ($buckets as $bucket) {
            foreach ($bucket->tasks as $task) {
                $start = $task->start_date ? Carbon::parse($task->start_date) : Carbon::parse($task->created_at);
                $end = $task->due_date ? Carbon::parse($task->due_date) : $start->copy()->addDay();

                if ($end->lessThanOrEqualTo($start)) {
                    $end = $start->copy()->addDay();
                }

                if ($start->lessThan($minDate))
                    $minDate = $start->copy();
                if ($end->greaterThan($maxDate))
                    $maxDate = $end->copy();

                // Calculate progress
                $progress = 0;
                $statusName = strtolower($task->status->name ?? '');
                if (str_contains($statusName, 'completado') || str_contains($statusName, 'completada') || str_contains($statusName, 'hecho')) {
                    $progress = 100;
                } elseif (str_contains($statusName, 'proceso') || str_contains($statusName, 'progreso')) {
                    $progress = 50;
                }

                $tasks[] = (object) [
                    'id' => $task->id,
                    'title' => $task->title,
                    'owner' => $task->assignedUsers->first()->name ?? 'Unassigned',
                    'start_date' => $start,
                    'due_date' => $end,
                    'duration' => $start->diffInDays($end),
                    'progress' => $progress,
                    'status_color' => $this->getStatusColor($statusName),
                    'parent_name' => $bucket->name // Grouping by bucket visually if needed
                ];
            }
        }

        // Buffer dates for the timeline view
        $minDate = $minDate->startOfWeek();
        $maxDate = $maxDate->endOfWeek()->addWeek(); // Add a buffer week

        // 4. Generate PDF
        $pdf = Pdf::loadView('exports.projects.gantt', [
            'project' => $project,
            'plan' => $plan,
            'tasks' => $tasks,
            'startDate' => $minDate,
            'endDate' => $maxDate,
            'generatedAt' => now()->format('d/m/Y'),
            'manager' => $project->responsible ?? auth()->user(),
        ])
            ->setPaper('a3', 'landscape'); // A3 Landscape for wide gantt charts

        return $pdf->download("Gantt-{$project->name}-{$plan->name}.pdf");
    }

    private function getStatusColor($statusName)
    {
        $status = strtolower($statusName ?? '');
        return match (true) {
            str_contains($status, 'complet') => '#22c55e', // Green
            str_contains($status, 'progres') => '#3b82f6', // Blue
            str_contains($status, 'pendient') => '#eab308', // Yellow
            default => '#9ca3af', // Gray
        };
    }
}
