<?php

namespace App\Livewire\Modules\Calendar;

use App\Models\Task;
use App\Models\Event;
use App\Models\Meeting;
use App\Models\Project;
use App\Models\Campaign;
use App\Models\Sub;
use App\Models\CaseRecord;
use App\Models\Invoice;
use App\Models\Certificate;
use App\Models\Induction;
use App\Models\Policy;
use App\Models\SocialMediaPost;
use App\Models\PunchItem;
use App\Models\CaseMarketing;
use App\Models\CalendarEvent;
use App\Models\NotificationTemplate;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Index extends Component
{
    public $events = [];

    // Form properties
    public $eventId = null;
    public $title = '';
    public $description = '';
    public $start;
    public $end;
    public $isAllDay = false;
    public $color = '#3b82f6';

    public function mount()
    {
        $this->loadEvents();
    }

    public function loadEvents()
    {
        $userId = Auth::id();
        $events = [];

        // Tasks - where user is assigned
        $tasks = Task::whereHas('assignedUsers', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->with(['assignedUsers', 'status'])->get();

        foreach ($tasks as $task) {
            if ($task->start_date) {
                $events[] = [
                    'title' => '📋 ' . $task->title,
                    'start' => $task->start_date,
                    'end' => $task->due_date,
                    'color' => '#3b82f6',
                    'extendedProps' => [
                        'type' => 'Task',
                        'description' => $task->description ?? '',
                        'status' => $task->status->name ?? 'N/A'
                    ]
                ];
            }
        }

        // Events - where user is responsible
        $eventsData = Event::where('responsible_id', $userId)
            ->with(['responsible', 'type', 'status'])
            ->get();

        foreach ($eventsData as $event) {
            $events[] = [
                'title' => '🎉 ' . $event->name,
                'start' => $event->event_date,
                'allDay' => true,
                'color' => '#10b981',
                'extendedProps' => [
                    'type' => 'Event',
                    'location' => $event->location ?? '',
                    'status' => $event->status->name ?? 'N/A'
                ]
            ];
        }

        // Meetings - where user is responsible
        $meetings = Meeting::whereHas('responsibles', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->with(['responsibles', 'status'])->get();

        foreach ($meetings as $meeting) {
            $events[] = [
                'title' => '💼 ' . $meeting->title,
                'start' => $meeting->date . ' ' . $meeting->start_time,
                'end' => $meeting->date . ' ' . $meeting->end_time,
                'color' => '#8b5cf6',
                'extendedProps' => [
                    'type' => 'Meeting',
                    'goal' => $meeting->goal ?? '',
                    'url' => $meeting->url ?? '',
                    'status' => $meeting->status->name ?? 'N/A'
                ]
            ];
        }

        // Projects - where user is responsible
        $projects = Project::where('responsible_id', $userId)
            ->with(['responsible', 'status'])
            ->get();

        foreach ($projects as $project) {
            $events[] = [
                'title' => '🚀 ' . $project->name,
                'start' => $project->created_at,
                'allDay' => true,
                'color' => '#f59e0b',
                'extendedProps' => [
                    'type' => 'Project',
                    'description' => $project->description ?? '',
                    'status' => $project->status->name ?? 'N/A'
                ]
            ];
        }

        // Campaigns - where user is responsible
        $campaigns = Campaign::where('responsible_id', $userId)
            ->with(['responsible', 'status'])
            ->get();

        foreach ($campaigns as $campaign) {
            $events[] = [
                'title' => '📢 ' . $campaign->name,
                'start' => $campaign->date_event,
                'allDay' => true,
                'color' => '#ec4899',
                'extendedProps' => [
                    'type' => 'Campaign',
                    'goal' => $campaign->goal ?? '',
                    'address' => $campaign->address ?? '',
                    'status' => $campaign->status->name ?? 'N/A'
                ]
            ];
        }

        // Subscriptions - where user is assigned
        $subs = Sub::where('user_id', $userId)
            ->with(['user', 'status'])
            ->get();

        foreach ($subs as $sub) {
            // Start date
            if ($sub->start_date) {
                $events[] = [
                    'title' => '💳 ' . $sub->name . ' (Inicio)',
                    'start' => $sub->start_date,
                    'allDay' => true,
                    'color' => '#06b6d4',
                    'extendedProps' => [
                        'type' => 'Subscription',
                        'amount' => $sub->amount ?? 0,
                        'status' => $sub->status->name ?? 'N/A'
                    ]
                ];
            }
            // Renewal date
            if ($sub->renewal_date) {
                $events[] = [
                    'title' => '🔄 ' . $sub->name . ' (Renovación)',
                    'start' => $sub->renewal_date,
                    'allDay' => true,
                    'color' => '#0ea5e9',
                    'extendedProps' => [
                        'type' => 'Subscription Renewal',
                        'amount' => $sub->amount ?? 0,
                        'status' => $sub->status->name ?? 'N/A'
                    ]
                ];
            }
        }

        // Case Records - where user is assigned
        $cases = CaseRecord::where('assigned_to_id', $userId)
            ->with(['assignedTo', 'status', 'type'])
            ->get();

        foreach ($cases as $case) {
            if ($case->date) {
                $events[] = [
                    'title' => '📁 Caso: ' . ($case->contact->name ?? 'N/A'),
                    'start' => $case->date,
                    'allDay' => true,
                    'color' => '#ef4444',
                    'extendedProps' => [
                        'type' => 'Case',
                        'description' => $case->description ?? '',
                        'caseType' => $case->type->name ?? 'N/A',
                        'status' => $case->status->name ?? 'N/A'
                    ]
                ];
            }
        }

        // Invoices - created by user
        $invoices = Invoice::where('created_by_id', $userId)
            ->with(['createdBy', 'status', 'contact'])
            ->get();

        foreach ($invoices as $invoice) {
            $events[] = [
                'title' => '🧾 ' . $invoice->code,
                'start' => $invoice->invoice_date,
                'allDay' => true,
                'color' => '#14b8a6',
                'extendedProps' => [
                    'type' => 'Invoice',
                    'total' => $invoice->total ?? 0,
                    'contact' => $invoice->contact->name ?? 'N/A',
                    'status' => $invoice->status->name ?? 'N/A'
                ]
            ];
        }

        // Certificates - assigned to user
        $certificates = Certificate::where('assigned_to_id', $userId)
            ->with(['assignedTo', 'status', 'type'])
            ->get();

        foreach ($certificates as $certificate) {
            if ($certificate->issued_at) {
                $events[] = [
                    'title' => '📜 ' . $certificate->name . ' (Emitido)',
                    'start' => $certificate->issued_at,
                    'allDay' => true,
                    'color' => '#a855f7',
                    'extendedProps' => [
                        'type' => 'Certificate',
                        'description' => $certificate->description ?? '',
                        'certificateType' => $certificate->type->name ?? 'N/A',
                        'status' => $certificate->status->name ?? 'N/A'
                    ]
                ];
            }
            if ($certificate->expires_at) {
                $events[] = [
                    'title' => '⚠️ ' . $certificate->name . ' (Vence)',
                    'start' => $certificate->expires_at,
                    'allDay' => true,
                    'color' => '#f97316',
                    'extendedProps' => [
                        'type' => 'Certificate Expiry',
                        'description' => $certificate->description ?? '',
                        'certificateType' => $certificate->type->name ?? 'N/A',
                        'status' => $certificate->status->name ?? 'N/A'
                    ]
                ];
            }
        }

        // Inductions - where user is responsible
        $inductions = Induction::where('responsible_id', $userId)
            ->with(['responsible', 'status', 'employee'])
            ->get();

        foreach ($inductions as $induction) {
            if ($induction->date) {
                $events[] = [
                    'title' => '👥 Inducción: ' . ($induction->employee->name ?? 'N/A'),
                    'start' => $induction->date,
                    'allDay' => true,
                    'color' => '#6366f1',
                    'extendedProps' => [
                        'type' => 'Induction',
                        'employee' => $induction->employee->name ?? 'N/A',
                        'duration' => $induction->duration ?? '',
                        'observations' => $induction->observations ?? '',
                        'status' => $induction->status->name ?? 'N/A'
                    ]
                ];
            }
        }

        // Policies - assigned to user
        $policies = Policy::where('assigned_to_id', $userId)
            ->with(['assignedTo', 'status', 'type'])
            ->get();

        foreach ($policies as $policy) {
            if ($policy->issued_at) {
                $events[] = [
                    'title' => '📋 ' . $policy->name . ' (Emitida)',
                    'start' => $policy->issued_at,
                    'allDay' => true,
                    'color' => '#84cc16',
                    'extendedProps' => [
                        'type' => 'Policy',
                        'description' => $policy->description ?? '',
                        'policyType' => $policy->type->name ?? 'N/A',
                        'status' => $policy->status->name ?? 'N/A'
                    ]
                ];
            }
            if ($policy->reviewed_at) {
                $events[] = [
                    'title' => '🔍 ' . $policy->name . ' (Revisión)',
                    'start' => $policy->reviewed_at,
                    'allDay' => true,
                    'color' => '#a3e635',
                    'extendedProps' => [
                        'type' => 'Policy Review',
                        'description' => $policy->description ?? '',
                        'policyType' => $policy->type->name ?? 'N/A',
                        'status' => $policy->status->name ?? 'N/A'
                    ]
                ];
            }
        }

        // Social Media Posts - where user is responsible
        $socialPosts = SocialMediaPost::where('responsible_id', $userId)
            ->with(['responsible', 'project'])
            ->get();

        foreach ($socialPosts as $post) {
            if ($post->scheduled_date) {
                $events[] = [
                    'title' => '📱 ' . $post->piece_name,
                    'start' => $post->scheduled_date,
                    'allDay' => true,
                    'color' => '#22d3ee',
                    'extendedProps' => [
                        'type' => 'Social Media Post',
                        'mediums' => $post->mediums ?? '',
                        'contentType' => $post->content_type ?? '',
                        'project' => $post->project->name ?? 'N/A',
                        'comments' => $post->comments ?? ''
                    ]
                ];
            }
        }

        // Punch Items - where user is responsible
        $punchItems = PunchItem::where('responsible_id', $userId)
            ->with(['responsible', 'status', 'worksite'])
            ->get();

        foreach ($punchItems as $item) {
            $events[] = [
                'title' => '🔧 Punch List: ' . ($item->worksite->name ?? 'N/A'),
                'start' => $item->created_at,
                'allDay' => true,
                'color' => '#fbbf24',
                'extendedProps' => [
                    'type' => 'Punch Item',
                    'worksite' => $item->worksite->name ?? 'N/A',
                    'observations' => $item->observations ?? '',
                    'status' => $item->status->name ?? 'N/A'
                ]
            ];
        }

        // Marketing Cases - where user is responsible
        $marketingCases = CaseMarketing::where('responsible_id', $userId)
            ->with(['responsible', 'status', 'type', 'project'])
            ->get();

        foreach ($marketingCases as $case) {
            if ($case->date) {
                $events[] = [
                    'title' => '📊 ' . $case->subject,
                    'start' => $case->date,
                    'allDay' => true,
                    'color' => '#fb7185',
                    'extendedProps' => [
                        'type' => 'Marketing Case',
                        'description' => $case->description ?? '',
                        'mediums' => $case->mediums ?? '',
                        'project' => $case->project->name ?? 'N/A',
                        'caseType' => $case->type->name ?? 'N/A',
                        'status' => $case->status->name ?? 'N/A'
                    ]
                ];
            }
        }

        // Personal Calendar Events - created by user
        $calendarEvents = CalendarEvent::where('user_id', $userId)->get();

        foreach ($calendarEvents as $event) {
            $events[] = [
                'id' => 'personal-' . $event->id, // Unique ID for frontend handling
                'title' => $event->title,
                'start' => $event->start_date->toIso8601String(),
                'end' => $event->end_date ? $event->end_date->toIso8601String() : null,
                'allDay' => $event->is_all_day,
                'color' => $event->color,
                'extendedProps' => [
                    'type' => 'Personal',
                    'eventId' => $event->id,
                    'description' => $event->description ?? '',
                    'isPersonal' => true // Flag to allow editing
                ]
            ];
        }

        $this->events = $events;
    }

    public function saveEvent(NotificationService $notificationService)
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'start' => 'required|date',
            'end' => 'nullable|date|after_or_equal:start',
        ]);

        $event = null;

        if ($this->eventId) {
            $event = CalendarEvent::where('id', $this->eventId)->where('user_id', Auth::id())->first();
            if ($event) {
                $event->update([
                    'title' => $this->title,
                    'description' => $this->description,
                    'start_date' => $this->start,
                    'end_date' => $this->end,
                    'is_all_day' => $this->isAllDay,
                    'color' => $this->color,
                ]);
            }
        } else {
            $event = CalendarEvent::create([
                'user_id' => Auth::id(),
                'title' => $this->title,
                'description' => $this->description,
                'start_date' => $this->start,
                'end_date' => $this->end,
                'is_all_day' => $this->isAllDay,
                'color' => $this->color,
            ]);
        }

        if ($event) {
            $this->scheduleNotification($event, $notificationService);
        }

        $this->resetForm();
        $this->loadEvents();
        $this->dispatch('event-saved');
        $this->dispatch('refresh-calendar', events: $this->events);
    }

    public function deleteEvent($id)
    {
        $event = CalendarEvent::where('id', $id)->where('user_id', Auth::id())->first();
        if ($event) {
            // Delete notification template
            NotificationTemplate::where('notifiable_type', CalendarEvent::class)
                ->where('notifiable_id', $event->id)
                ->delete();

            $event->delete();
            $this->loadEvents();
            $this->dispatch('event-deleted');
            $this->dispatch('refresh-calendar', events: $this->events);
        }
    }

    public function updateEventDrop($id, $start, $end, NotificationService $notificationService)
    {
        $event = CalendarEvent::where('id', $id)->where('user_id', Auth::id())->first();
        if ($event) {
            $event->update([
                'start_date' => $start,
                'end_date' => $end,
            ]);
            $this->scheduleNotification($event, $notificationService);
            // Optimistic UI: We don't reload events here to avoid lag. 
            // The frontend updates the view immediately.
        }
    }

    protected function scheduleNotification($event, $notificationService)
    {
        // Remove existing
        NotificationTemplate::where('notifiable_type', CalendarEvent::class)
            ->where('notifiable_id', $event->id)
            ->delete();

        // Create new scheduled notification for the start time
        $notificationService->createScheduledTemplate(
            user: Auth::user(),
            title: 'Recordatorio de Evento: ' . $event->title,
            message: $event->description ?? 'Tienes un evento programado para ahora.',
            scheduledAt: Carbon::parse($event->start_date),
            notifiable: $event,
            sendEmail: true
        );
    }

    public function resetForm()
    {
        $this->eventId = null;
        $this->title = '';
        $this->description = '';
        $this->start = null;
        $this->end = null;
        $this->isAllDay = false;
        $this->color = '#3b82f6';
    }

    public function render()
    {
        return view('livewire.modules.calendar.index');
    }
}
