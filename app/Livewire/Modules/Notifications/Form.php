<?php

namespace App\Livewire\Modules\Notifications;

use Livewire\Component;
use App\Models\NotificationTemplate;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Support\Collection;

class Form extends Component
{
    public ?NotificationTemplate $template = null;

    // Form Fields
    public $title;
    public $message;
    public $type = 'reminder'; // reminder, scheduled, recurring
    public $user_id;
    public $send_email = true; // Always true
    public $is_active = true;

    // Type Specific Fields
    public $scheduled_at;
    public $reminder_days = 3;
    public $event_date; // For standalone reminders
    public $recurring_interval = 'monthly';
    public $recurring_day;
    public $selected_frequency = 'monthly';

    public $frequencies = [
        'daily' => ['label' => 'Diaria', 'interval' => 'daily', 'value' => 1],
        'weekly' => ['label' => 'Semanal', 'interval' => 'weekly', 'value' => 1],
        'biweekly' => ['label' => 'Quincenal (15 días)', 'interval' => 'days', 'value' => 15],
        'monthly' => ['label' => 'Mensual', 'interval' => 'monthly', 'value' => 1],
        'bimonthly' => ['label' => 'Bimestral', 'interval' => 'months', 'value' => 2],
        'quarterly' => ['label' => 'Trimestral', 'interval' => 'months', 'value' => 3],
        'biannual' => ['label' => 'Semestral', 'interval' => 'months', 'value' => 6],
        'yearly' => ['label' => 'Anual', 'interval' => 'yearly', 'value' => 1],
    ];

    protected function rules()
    {
        $rules = [
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:reminder,scheduled,recurring',
            'user_id' => 'required|exists:users,id',
        ];

        if ($this->type === 'scheduled') {
            $rules['scheduled_at'] = 'required|date';
        }

        if ($this->type === 'recurring') {
            $rules['selected_frequency'] = 'required';
        }

        if ($this->type === 'reminder') {
            $rules['reminder_days'] = 'required|integer|min:1';
            $rules['event_date'] = 'required|date';
        }

        return $rules;
    }

    public function mount($template = null)
    {
        if ($template) {
            $this->template = $template;
            $this->title = $template->title;
            $this->message = $template->message;
            $this->type = $template->type;
            $this->user_id = $template->user_id;
            $this->send_email = true; // Always true
            $this->is_active = $template->is_active;

            $this->scheduled_at = $template->scheduled_at?->format('Y-m-d\TH:i');
            $this->reminder_days = $template->reminder_days;
            $this->event_date = $template->event_date?->format('Y-m-d\TH:i');

            if ($template->recurring_pattern) {
                $this->recurring_day = $template->recurring_pattern['day'] ?? null;
                $this->determineSelectedFrequency($template->recurring_pattern);
            }
        } else {
            // Defaults
            $this->user_id = auth()->id();
        }
    }

    private function determineSelectedFrequency($pattern)
    {
        $interval = $pattern['interval'] ?? 'monthly';
        $value = $pattern['value'] ?? 1;

        // Check for exact matches in frequencies
        foreach ($this->frequencies as $key => $config) {
            if ($config['interval'] === $interval && $config['value'] == $value) {
                $this->selected_frequency = $key;
                return;
            }
        }

        // Fallback for legacy or custom patterns
        if ($interval === 'daily')
            $this->selected_frequency = 'daily';
        elseif ($interval === 'weekly')
            $this->selected_frequency = 'weekly';
        elseif ($interval === 'monthly')
            $this->selected_frequency = 'monthly';
        elseif ($interval === 'yearly')
            $this->selected_frequency = 'yearly';
        else
            $this->selected_frequency = 'monthly'; // Default fallback
    }

    public function getUsersProperty()
    {
        return User::orderBy('name')->get();
    }

    public function save()
    {
        $this->validate();

        $service = app(NotificationService::class);
        $user = User::find($this->user_id);

        // Prepare recurring pattern
        $recurringPattern = null;
        if ($this->type === 'recurring') {
            $config = $this->frequencies[$this->selected_frequency] ?? $this->frequencies['monthly'];
            $recurringPattern = [
                'interval' => $config['interval'],
                'value' => $config['value'],
                'day' => $this->recurring_day
            ];
        }

        // Create or Update
        if ($this->template) {
            $this->template->update([
                'title' => $this->title,
                'message' => $this->message,
                'type' => $this->type,
                'user_id' => $this->user_id,
                'notifiable_type' => null,
                'notifiable_id' => null,
                'send_email' => true,
                'is_active' => $this->is_active,
                // Update type specific fields
                'scheduled_at' => $this->type === 'scheduled' ? $this->scheduled_at : null,
                'reminder_days' => $this->type === 'reminder' ? $this->reminder_days : null,
                'event_date' => $this->type === 'reminder' ? $this->event_date : null,
                'recurring_pattern' => $recurringPattern,
            ]);

            // Recalculate next send
            if ($this->template->is_active) {
                $this->template->next_send_at = $this->template->calculateNextSendAt();
                $this->template->save();
            }

            session()->flash('success', 'Notificación actualizada exitosamente.');
        } else {
            // Create using Service
            if ($this->type === 'scheduled') {
                $service->createScheduledTemplate(
                    user: $user,
                    notifiable: null,
                    scheduledAt: \Carbon\Carbon::parse($this->scheduled_at),
                    title: $this->title,
                    message: $this->message,
                    sendEmail: true
                );
            } elseif ($this->type === 'recurring') {
                $service->createRecurringTemplate(
                    user: $user,
                    notifiable: null,
                    recurringPattern: $recurringPattern,
                    title: $this->title,
                    message: $this->message,
                    sendEmail: true
                );
            } elseif ($this->type === 'reminder') {
                $service->createReminderTemplate(
                    user: $user,
                    notifiable: null,
                    eventDate: \Carbon\Carbon::parse($this->event_date),
                    daysBefore: $this->reminder_days,
                    title: $this->title,
                    message: $this->message,
                    sendEmail: true
                );
            }

            session()->flash('success', 'Notificación creada exitosamente.');
        }

        return redirect()->route('notifications.index');
    }

    public function render()
    {
        return view('livewire.modules.notifications.form');
    }
}
