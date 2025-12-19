<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Models\NotificationTemplate;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $query = NotificationTemplate::with(['user', 'notifiable']);

        // Search by Title or Message
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('message', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by Status
        if ($request->status_filter === 'active') {
            $query->active();
        } elseif ($request->status_filter === 'inactive') {
            $query->inactive();
        }

        // Filter by Type
        if ($request->type_filter) {
            $query->where('type', $request->type_filter);
        }

        $notifications = $query->orderBy('created_at', 'desc')
            ->paginate($request->input('perPage', 10))
            ->withQueryString();

        return Inertia::render('Notifications/Index', [
            'notifications' => $notifications,
            'filters' => $request->only(['search', 'status_filter', 'type_filter', 'perPage']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Notifications/Create', [
            'users' => User::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request, NotificationService $service)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:reminder,scheduled,recurring',
            'user_id' => 'required|exists:users,id',
            'scheduled_at' => 'required_if:type,scheduled|nullable|date',
            'reminder_days' => 'required_if:type,reminder|nullable|integer|min:1',
            'event_date' => 'required_if:type,reminder|nullable|date',
            'selected_frequency' => 'required_if:type,recurring',
        ]);

        $user = User::find($request->user_id);

        $frequencies = [
            'daily' => ['label' => 'Diaria', 'interval' => 'daily', 'value' => 1],
            'weekly' => ['label' => 'Semanal', 'interval' => 'weekly', 'value' => 1],
            'biweekly' => ['label' => 'Quincenal (15 días)', 'interval' => 'days', 'value' => 15],
            'monthly' => ['label' => 'Mensual', 'interval' => 'monthly', 'value' => 1],
            'bimonthly' => ['label' => 'Bimestral', 'interval' => 'months', 'value' => 2],
            'quarterly' => ['label' => 'Trimestral', 'interval' => 'months', 'value' => 3],
            'biannual' => ['label' => 'Semestral', 'interval' => 'months', 'value' => 6],
            'yearly' => ['label' => 'Anual', 'interval' => 'yearly', 'value' => 1],
        ];

        // Prepare recurring pattern
        $recurringPattern = null;
        if ($request->type === 'recurring') {
            $config = $frequencies[$request->selected_frequency] ?? $frequencies['monthly'];
            $recurringPattern = [
                'interval' => $config['interval'],
                'value' => $config['value'],
                'day' => $request->input('recurring_day')
            ];
        }

        if ($request->type === 'scheduled') {
            $service->createScheduledTemplate(
                user: $user,
                notifiable: null,
                scheduledAt: \Carbon\Carbon::parse($request->scheduled_at),
                title: $request->title,
                message: $request->message,
                sendEmail: true
            );
        } elseif ($request->type === 'recurring') {
            $service->createRecurringTemplate(
                user: $user,
                notifiable: null,
                recurringPattern: $recurringPattern,
                title: $request->title,
                message: $request->message,
                sendEmail: true
            );
        } elseif ($request->type === 'reminder') {
            $service->createReminderTemplate(
                user: $user,
                notifiable: null,
                eventDate: \Carbon\Carbon::parse($request->event_date),
                daysBefore: $request->reminder_days,
                title: $request->title,
                message: $request->message,
                sendEmail: true
            );
        }

        return redirect()->route('notifications.index')->with('success', 'Notificación creada exitosamente.');
    }

    public function edit($id)
    {
        $notification = NotificationTemplate::findOrFail($id);

        // Prepare additional data for frontend if needed, e.g., frequency processing
        // But most of it can be done in the Vue component based on recurring_pattern

        return Inertia::render('Notifications/Edit', [
            'notification' => $notification,
            'users' => User::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $template = NotificationTemplate::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:reminder,scheduled,recurring',
            'user_id' => 'required|exists:users,id',
            'scheduled_at' => 'required_if:type,scheduled|nullable|date',
            'reminder_days' => 'required_if:type,reminder|nullable|integer|min:1',
            'event_date' => 'required_if:type,reminder|nullable|date',
            'selected_frequency' => 'required_if:type,recurring',
        ]);

        $frequencies = [
            'daily' => ['label' => 'Diaria', 'interval' => 'daily', 'value' => 1],
            'weekly' => ['label' => 'Semanal', 'interval' => 'weekly', 'value' => 1],
            'biweekly' => ['label' => 'Quincenal (15 días)', 'interval' => 'days', 'value' => 15],
            'monthly' => ['label' => 'Mensual', 'interval' => 'monthly', 'value' => 1],
            'bimonthly' => ['label' => 'Bimestral', 'interval' => 'months', 'value' => 2],
            'quarterly' => ['label' => 'Trimestral', 'interval' => 'months', 'value' => 3],
            'biannual' => ['label' => 'Semestral', 'interval' => 'months', 'value' => 6],
            'yearly' => ['label' => 'Anual', 'interval' => 'yearly', 'value' => 1],
        ];

        // Prepare recurring pattern
        $recurringPattern = null;
        if ($request->type === 'recurring') {
            $config = $frequencies[$request->selected_frequency] ?? $frequencies['monthly'];
            $recurringPattern = [
                'interval' => $config['interval'],
                'value' => $config['value'],
                'day' => $request->input('recurring_day')
            ];
        }

        $template->update([
            'title' => $request->title,
            'message' => $request->message,
            'type' => $request->type,
            'user_id' => $request->user_id,
            'notifiable_type' => null,
            'notifiable_id' => null,
            'send_email' => true, // default as per requirements
            'is_active' => $request->boolean('is_active', true),
            'scheduled_at' => $request->type === 'scheduled' ? $request->scheduled_at : null,
            'reminder_days' => $request->type === 'reminder' ? $request->reminder_days : null,
            'event_date' => $request->type === 'reminder' ? $request->event_date : null,
            'recurring_pattern' => $recurringPattern,
        ]);

        // Recalculate next send
        if ($template->is_active) {
            $template->next_send_at = $template->calculateNextSendAt();
            $template->save();
        }

        return redirect()->route('notifications.index')->with('success', 'Notificación actualizada exitosamente.');
    }

    public function destroy($id)
    {
        $template = NotificationTemplate::findOrFail($id);
        $template->delete();

        return redirect()->back()->with('success', 'Notificación eliminada exitosamente.');
    }

    public function toggleStatus($id)
    {
        $template = NotificationTemplate::findOrFail($id);
        $template->is_active = !$template->is_active;
        $template->save();

        $status = $template->is_active ? 'activada' : 'pausada';
        return redirect()->back()->with('success', "Notificación {$status} exitosamente.");
    }
}
