<?php

namespace App\Livewire\Modules\Rrhh\Interviews\Calendar;

use App\Models\Interview;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Index extends Component
{
    public $events = [];

    public function mount()
    {
        $this->loadEvents();
    }

    public function loadEvents()
    {
        $events = [];

        // Get ALL interviews in the system
        $interviews = Interview::with(['interviewer', 'applicant', 'interviewType', 'status', 'result'])
            ->get();

        foreach ($interviews as $interview) {
            $dateTime = $interview->date->format('Y-m-d');
            if ($interview->time) {
                $dateTime .= ' ' . $interview->time->format('H:i:s');
            }

            // Determine color based on status
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

            $interviewerName = $interview->interviewer ? ' - ' . $interview->interviewer->name : ' (Sin asignar)';

            $events[] = [
                'title' => '🎤 ' . ($interview->applicant->name ?? 'Entrevista') . $interviewerName,
                'start' => $dateTime,
                'color' => $color,
                'extendedProps' => [
                    'applicant' => $interview->applicant->name ?? 'N/A',
                    'interviewer' => $interview->interviewer->name ?? 'Sin asignar',
                    'interviewType' => $interview->interviewType->name ?? 'N/A',
                    'status' => $interview->status->name ?? 'N/A',
                    'result' => $interview->result->name ?? 'Pendiente',
                    'platform' => $interview->platform ?? '',
                    'platform_url' => $interview->platform_url ?? '',
                    'rating' => $interview->rating ?? 0,
                    'observations' => $interview->interviewer_observations ?? '',
                    'expected_results' => $interview->expected_results ?? ''
                ]
            ];
        }

        $this->events = $events;
    }

    public function render()
    {
        return view('livewire.modules.rrhh.interviews.calendar.index');
    }
}
