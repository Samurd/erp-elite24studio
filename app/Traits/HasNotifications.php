<?php

namespace App\Traits;

use App\Models\NotificationTemplate;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Support\Str;

trait HasNotifications
{
    /**
     * Relación polimórfica con notification templates
     */
    public function notificationTemplates()
    {
        return $this->morphMany(NotificationTemplate::class, 'notifiable');
    }

    /**
     * Crear recordatorio simple (una sola vez)
     * 
     * @param int $daysBefore Días antes del evento
     * @param string|null $title Título personalizado
     * @param string|null $message Mensaje personalizado
     * @param Carbon|null $eventDate Fecha del evento (por defecto usa el campo de fecha del modelo)
     * @return NotificationTemplate|null
     */
    public function createReminderNotification(
        int $daysBefore,
        ?string $title = null,
        ?string $message = null,
        ?Carbon $eventDate = null,
        bool $sendEmail = false
    ): ?NotificationTemplate {
        if (!$this->user_id) {
            return null;
        }

        // Intentar obtener fecha del evento desde el modelo
        $eventDate = $eventDate ?? $this->getEventDate();

        if (!$eventDate) {
            return null;
        }

        $notificationService = app(NotificationService::class);

        // Generar título y mensaje por defecto
        $title = $title ?? $this->getDefaultNotificationTitle();
        $message = $message ?? $this->getDefaultNotificationMessage($eventDate);

        return $notificationService->createReminderTemplate(
            user: $this->user,
            title: $title,
            message: $message,
            eventDate: $eventDate,
            daysBefore: $daysBefore,
            data: $this->getNotificationData($eventDate),
            notifiable: $this,
            sendEmail: $sendEmail
        );
    }

    /**
     * Crear notificación recurrente
     * 
     * @param int|null $daysBefore Días antes (usa getRenewalReminderDays si es null)
     * @param string|null $title Título personalizado
     * @param string|null $message Mensaje personalizado
     * @return NotificationTemplate|null
     */
    public function createRecurringNotification(
        ?int $daysBefore = null,
        ?string $title = null,
        ?string $message = null,
        bool $sendEmail = false
    ): ?NotificationTemplate {
        if (!$this->user_id) {
            return null;
        }

        $eventDate = $this->getEventDate();
        if (!$eventDate) {
            return null;
        }

        // Para recurring, usamos la fecha exacta del evento
        $recurringPattern = $this->getRecurringPattern();
        if (!$recurringPattern) {
            return null;
        }

        // Calcular primera fecha de envío (Fecha exacta del evento)
        $firstSendDate = $eventDate->copy();

        $notificationService = app(NotificationService::class);

        $title = $title ?? $this->getDefaultNotificationTitle();
        $message = $message ?? $this->getDefaultRecurringMessage();

        return $notificationService->createRecurringTemplate(
            user: $this->user,
            title: $title,
            message: $message,
            recurringPattern: $recurringPattern,
            data: $this->getNotificationData($eventDate, 0), // 0 días antes
            notifiable: $this,
            startsAt: $firstSendDate,
            sendEmail: $sendEmail
        );
    }

    /**
     * Crear notificación automática
     * Detecta automáticamente si debe ser recurrente o simple
     */
    public function createAutomaticNotification(): ?NotificationTemplate
    {
        $daysBefore = $this->getRenewalReminderDays();
        $eventDate = $this->getEventDate();

        if (!$daysBefore || !$eventDate || !$this->user_id) {
            return null;
        }

        // Verificar si ya existe una notificación activa
        $existing = $this->notificationTemplates()
            ->whereIn('type', ['reminder', 'recurring'])
            ->where('is_active', true)
            ->first();

        if ($existing) {
            $this->updateNotifications();
            return $existing;
        }

        // Si tiene patrón recurrente, usar recurring
        if (method_exists($this, 'getRecurringPattern') && $this->getRecurringPattern()) {
            return $this->createRecurringNotification();
        }

        // Si no, usar reminder simple
        return $this->createReminderNotification($daysBefore);
    }

    /**
     * Actualizar notificaciones existentes
     */
    public function updateNotifications(?Carbon $newEventDate = null): void
    {
        $newEventDate = $newEventDate ?? $this->getEventDate();
        if (!$newEventDate) {
            return;
        }

        $daysBefore = $this->getRenewalReminderDays();

        $this->notificationTemplates()
            ->whereIn('type', ['reminder', 'recurring'])
            ->where('is_active', true)
            ->each(function ($template) use ($newEventDate, $daysBefore) {
                // Si es recurring, es la fecha exacta. Si es reminder, es días antes.
                if ($template->type === 'recurring') {
                    $nextSendAt = $newEventDate->copy();
                    $daysBeforeValue = 0;
                } else {
                    $nextSendAt = $newEventDate->copy()->subDays($daysBefore);
                    $daysBeforeValue = $daysBefore;
                }

                $template->update([
                    'event_date' => $newEventDate,
                    'next_send_at' => $nextSendAt,
                    'reminder_days' => $daysBeforeValue,
                    'data' => array_merge($template->data ?? [], [
                        'event_date' => $newEventDate->toDateString(),
                    ]),
                ]);
            });
    }

    /**
     * Pausar todas las notificaciones
     */
    public function pauseNotifications(): int
    {
        return $this->notificationTemplates()
            ->where('is_active', true)
            ->update(['is_active' => false]);
    }

    /**
     * Reanudar todas las notificaciones
     */
    public function resumeNotifications(): int
    {
        return $this->notificationTemplates()
            ->where('is_active', false)
            ->update(['is_active' => true]);
    }

    /**
     * Eliminar todas las notificaciones
     */
    public function deleteNotifications(): int
    {
        return $this->notificationTemplates()->delete();
    }

    /**
     * Obtener notificaciones activas
     */
    public function getActiveNotificationsAttribute()
    {
        return $this->notificationTemplates()
            ->where('is_active', true)
            ->orderBy('next_send_at')
            ->get();
    }

    /**
     * ========================================
     * MÉTODOS QUE DEBEN SER IMPLEMENTADOS
     * ========================================
     */

    /**
     * Obtener días antes del evento para enviar notificación
     * DEBE ser implementado en el modelo para personalizar
     * 
     * @return int|null
     */
    public function getRenewalReminderDays(): ?int
    {
        // Default: 7 días antes
        // Sobrescribir este método en el modelo para personalizar
        return 7;
    }

    /**
     * ========================================
     * MÉTODOS OPCIONALES PARA PERSONALIZAR
     * ========================================
     */

    /**
     * Obtener fecha del evento
     * Por defecto busca: renewal_date, expiry_date, event_date, due_date
     * Puede ser sobrescrito en el modelo
     */
    public function getEventDate(): ?Carbon
    {
        $dateFields = ['renewal_date', 'expiry_date', 'event_date', 'due_date', 'end_date'];

        foreach ($dateFields as $field) {
            if (isset($this->$field)) {
                return $this->$field instanceof Carbon ? $this->$field : Carbon::parse($this->$field);
            }
        }

        return null;
    }

    /**
     * Obtener título por defecto de la notificación
     * Puede ser sobrescrito en el modelo
     */
    public function getDefaultNotificationTitle(): string
    {
        $modelName = class_basename($this);
        $name = $this->name ?? $this->title ?? "#{$this->id}";
        return "Recordatorio: {$modelName} {$name}";
    }

    /**
     * Obtener mensaje por defecto para reminder
     * Puede ser sobrescrito en el modelo
     */
    public function getDefaultNotificationMessage(Carbon $eventDate): string
    {
        $modelName = class_basename($this);
        $name = $this->name ?? $this->title ?? "#{$this->id}";
        return "{$modelName} '{$name}' vence el {$eventDate->format('d/m/Y')}. Recuerda revisarlo a tiempo.";
    }

    /**
     * Obtener mensaje por defecto para recurring
     * Puede ser sobrescrito en el modelo
     */
    public function getDefaultRecurringMessage(): string
    {
        $modelName = class_basename($this);
        $name = $this->name ?? $this->title ?? "#{$this->id}";
        return "{$modelName} '{$name}' se renovará pronto. Recuerda verificarlo.";
    }

    /**
     * Obtener URL de acción por defecto para la notificación
     * Puede ser sobrescrito en el modelo (ej: route('subs.show', $this))
     */
    public function getDefaultNotificationUrl(): ?string
    {
        return null;
    }

    /**
     * Obtener data para la notificación
     * Puede ser sobrescrito en el modelo para agregar más datos
     */
    protected function getNotificationData(?Carbon $eventDate = null, ?int $daysBefore = null): array
    {
        $modelName = Str::snake(class_basename($this));
        $routeName = Str::plural($modelName);

        $data = [
            "{$modelName}_id" => $this->id,
            "{$modelName}_name" => $this->name ?? $this->title ?? null,
        ];

        if ($eventDate) {
            $data['event_date'] = $eventDate->toDateString();
        }

        if ($daysBefore) {
            $data['days_before'] = $daysBefore;
        }

        // Intentar generar URL de acción
        try {
            $data['action_url'] = route("{$routeName}.edit", $this->id);
        } catch (\Exception $e) {
            // Si la ruta no existe, omitir
        }

        return $data;
    }

    /**
     * Obtener patrón de recurrencia
     * OPCIONAL - Solo implementar si el modelo tiene eventos recurrentes
     * 
     * @return array|null
     */
    protected function getRecurringPattern(): ?array
    {
        return null;
    }
}
