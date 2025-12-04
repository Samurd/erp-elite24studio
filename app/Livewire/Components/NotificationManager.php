<?php

namespace App\Livewire\Components;

use App\Models\NotificationTemplate;
use App\Services\NotificationService;
use Livewire\Component;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class NotificationManager extends Component
{
    public Model $notifiable;
    public string $notifiableType;

    // Configuration
    public array $allowedTypes = []; // Empty = all types allowed
    public array $allowedFrequencies = [];

    // State
    public $templates = [];
    public bool $showModal = false;

    // Form Data
    public string $type = 'reminder'; // reminder, recurring, scheduled, now
    public ?string $title = '';
    public ?string $message = '';
    public bool $sendEmail = false;
    public string $customEmail = '';

    // Type specific fields
    public int $reminderDays = 7;
    public string $scheduledDate = '';
    public string $scheduledTime = '';
    public string $recurringInterval = 'monthly'; // daily, weekly, monthly, yearly

    protected function rules()
    {
        return [
            'title' => 'nullable|string|max:255',
            'message' => 'nullable|string|max:1000',
            'type' => 'required|in:reminder,recurring,scheduled,now',
            'sendEmail' => 'boolean',
            'customEmail' => 'nullable|email|required_if:sendEmail,true',
        ];
    }

    public function mount(Model $notifiable, array $allowedTypes = [])
    {
        $this->notifiable = $notifiable;
        $this->notifiableType = get_class($notifiable);
        $this->allowedTypes = $allowedTypes;

        // Load allowed frequencies
        if (method_exists($this->notifiable, 'getNotificationFrequencies')) {
            $this->allowedFrequencies = $this->notifiable->getNotificationFrequencies();
        } else {
            // Fallback defaults
            $this->allowedFrequencies = [
                'daily' => 'Diario',
                'weekly' => 'Semanal',
                'monthly' => 'Mensual',
                'yearly' => 'Anual',
            ];
        }

        // Set default recurring interval to first allowed frequency
        if (!empty($this->allowedFrequencies)) {
            $this->recurringInterval = array_key_first($this->allowedFrequencies);
        }

        // Set default type based on allowed types
        if (!empty($this->allowedTypes)) {
            $this->type = $this->allowedTypes[0];
        }

        $this->loadTemplates();
    }

    public function loadTemplates()
    {
        $this->templates = $this->notifiable->morphMany(NotificationTemplate::class, 'notifiable')
            ->where(function ($query) {
                // Show all recurring templates (active or inactive)
                $query->where('type', 'recurring')
                    // Show scheduled/reminder only if active
                    ->orWhere(function ($q) {
                    $q->whereIn('type', ['scheduled', 'reminder'])
                        ->where('is_active', true);
                });
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function openModal()
    {
        $this->resetForm();
        $this->showModal = true;

        // Set defaults based on model if available
        if (method_exists($this->notifiable, 'getRenewalReminderDays')) {
            $this->reminderDays = $this->notifiable->getRenewalReminderDays() ?? 7;
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset([
            'title',
            'message',
            'sendEmail',
            'customEmail',
            'reminderDays',
            'scheduledDate',
            'scheduledTime',
            'recurringInterval'
        ]);

        // Reset type to first allowed type or default
        if (!empty($this->allowedTypes)) {
            $this->type = $this->allowedTypes[0];
        } else {
            $this->type = 'reminder';
        }

        $this->reminderDays = 7;
        $this->recurringInterval = 'monthly';
    }

    public function save()
    {
        $this->validate();

        // Additional validation based on type
        if ($this->type === 'scheduled') {
            $this->validate([
                'scheduledDate' => 'required|date|after_or_equal:today',
                'scheduledTime' => 'required',
            ]);

            // If date is today, we allow past times (they will be sent immediately by the scheduler)
            // This avoids timezone frustration
            /*
            if ($this->scheduledDate === Carbon::today()->toDateString()) {
                $scheduledAt = Carbon::parse($this->scheduledDate . ' ' . $this->scheduledTime);
                if ($scheduledAt->isPast()) {
                    $this->addError('scheduledTime', 'La hora debe ser futura.');
                    return;
                }
            }
            */
        } elseif ($this->type === 'reminder') {
            $this->validate([
                'reminderDays' => 'required|integer|min:1',
            ]);
        }

        // Set default title/message if empty
        $title = $this->title;
        $message = $this->message;

        if (empty($title) && method_exists($this->notifiable, 'getDefaultNotificationTitle')) {
            $title = $this->notifiable->getDefaultNotificationTitle();
        }

        if (empty($message)) {
            if ($this->type === 'reminder' && method_exists($this->notifiable, 'getDefaultNotificationMessage')) {
                // Need event date for message
                $eventDate = $this->getEventDate();
                if ($eventDate) {
                    $message = $this->notifiable->getDefaultNotificationMessage($eventDate);
                }
            } elseif ($this->type === 'recurring' && method_exists($this->notifiable, 'getDefaultRecurringMessage')) {
                $message = $this->notifiable->getDefaultRecurringMessage();
            }

            // Fallback generic message if still empty
            if (empty($message)) {
                $message = "Notificación de " . class_basename($this->notifiable);
            }
        }

        // Fallback title if still empty
        if (empty($title)) {
            $title = "Notificación";
        }

        $service = app(NotificationService::class);
        $user = Auth::user();

        // Get default action URL if available
        $actionUrl = null;
        if (method_exists($this->notifiable, 'getDefaultNotificationUrl')) {
            $actionUrl = $this->notifiable->getDefaultNotificationUrl();
        }

        $commonData = [];
        if ($actionUrl) {
            $commonData['action_url'] = $actionUrl;
        }

        try {
            switch ($this->type) {
                case 'now':
                    $data = array_merge($commonData, ['custom_email' => $this->sendEmail ? $this->customEmail : null]);
                    $service->createImmediate(
                        user: $user,
                        title: $title,
                        message: $message,
                        data: $data,
                        notifiable: $this->notifiable
                    );
                    $this->dispatch('show-notification', [
                        'type' => 'success',
                        'message' => 'Notificación enviada exitosamente.'
                    ]);
                    break;

                case 'scheduled':
                    $scheduledAt = Carbon::parse($this->scheduledDate . ' ' . $this->scheduledTime);
                    $service->createScheduledTemplate(
                        user: $user,
                        title: $title,
                        message: $message,
                        scheduledAt: $scheduledAt,
                        data: $commonData,
                        notifiable: $this->notifiable,
                        sendEmail: $this->sendEmail,
                        customEmail: $this->customEmail ?: null
                    );
                    $this->dispatch('show-notification', [
                        'type' => 'success',
                        'message' => 'Notificación programada creada.'
                    ]);
                    break;

                case 'recurring':
                    // Calculate startsAt based on event date
                    $startsAt = null;
                    $eventDate = $this->getEventDate();

                    if ($eventDate) {
                        // For recurring, we use the exact event date
                        $startsAt = $eventDate->copy();
                    }

                    $service->createRecurringTemplate(
                        user: $user,
                        title: $title,
                        message: $message,
                        recurringPattern: ['interval' => $this->recurringInterval],
                        data: $commonData,
                        notifiable: $this->notifiable,
                        startsAt: $startsAt,
                        sendEmail: $this->sendEmail,
                        customEmail: $this->customEmail ?: null
                    );
                    $this->dispatch('show-notification', [
                        'type' => 'success',
                        'message' => 'Notificación recurrente creada.'
                    ]);
                    break;

                case 'reminder':
                    $eventDate = $this->getEventDate();

                    if (!$eventDate) {
                        $this->addError('type', 'El modelo no tiene una fecha de evento válida para recordatorios.');
                        return;
                    }

                    $service->createReminderTemplate(
                        user: $user,
                        title: $title,
                        message: $message,
                        eventDate: $eventDate,
                        daysBefore: $this->reminderDays,
                        data: $commonData,
                        notifiable: $this->notifiable,
                        sendEmail: $this->sendEmail,
                        customEmail: $this->customEmail ?: null
                    );
                    $this->dispatch('show-notification', [
                        'type' => 'success',
                        'message' => 'Recordatorio creado exitosamente.'
                    ]);
                    break;
            }

            $this->loadTemplates();
            $this->closeModal();

        } catch (\Exception $e) {
            $this->addError('general', 'Error al guardar: ' . $e->getMessage());
        }
    }

    protected function getEventDate(): ?Carbon
    {
        if (method_exists($this->notifiable, 'getEventDate')) {
            return $this->notifiable->getEventDate();
        }

        // Fallback to common date fields
        $dateFields = ['renewal_date', 'expiry_date', 'event_date', 'due_date', 'end_date'];
        foreach ($dateFields as $field) {
            if ($this->notifiable->$field) {
                return Carbon::parse($this->notifiable->$field);
            }
        }

        return null;
    }

    public function delete($id)
    {
        $service = app(NotificationService::class);
        if ($service->deleteTemplate($id)) {
            $this->loadTemplates();
            $this->dispatch('show-notification', [
                'type' => 'success',
                'message' => 'Notificación eliminada.'
            ]);
        }
    }

    public function toggle($id)
    {
        $template = NotificationTemplate::find($id);
        if ($template) {
            $service = app(NotificationService::class);
            if ($template->is_active) {
                $service->pauseTemplate($id);
            } else {
                $service->resumeTemplate($id);
            }
            $this->loadTemplates();
        }
    }

    public function render()
    {
        return view('livewire.components.notification-manager');
    }
}
