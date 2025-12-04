<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\NotificationTemplate;
use App\Models\User;
use App\Events\NotificationSent;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\Model;

class NotificationService
{
    /**
     * ==========================================
     * MÉTODOS PARA NOTIFICACIONES INMEDIATAS
     * ==========================================
     */

    /**
     * Crear y enviar notificación inmediata
     */
    public function createImmediate(
        User $user,
        string $title,
        string $message,
        array $data = [],
        ?Model $notifiable = null,
        bool $sendEmail = false,
        ?string $emailTemplate = null
    ): Notification {
        if ($sendEmail) {
            $data['send_email'] = true;
            if ($emailTemplate) {
                $data['email_template'] = $emailTemplate;
            }
        }
        $notification = Notification::create([
            'user_id' => $user->id,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'notifiable_type' => $notifiable ? get_class($notifiable) : null,
            'notifiable_id' => $notifiable?->id,
        ]);

        // Enviar inmediatamente
        $this->send($notification);

        return $notification;
    }

    /**
     * Enviar notificación
     */
    public function send(Notification $notification): bool
    {
        try {
            // Disparar evento de broadcasting
            broadcast(new NotificationSent($notification, $notification->user));


            // Usar email personalizado si existe, sino usar el del usuario
            $customEmail = $notification->template->data['custom_email'] ?? $notification->data['custom_email'] ?? null;
            $emailTo = $customEmail ?? $notification->user->email;

            Mail::to($emailTo)->send(new \App\Mail\NotificationEmail($notification));


            // Marcar como enviada
            $notification->markAsSent();

            return true;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error sending notification: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Enviar múltiples notificaciones
     */
    public function sendBatch(array $notifications): array
    {
        $results = [
            'sent' => 0,
            'failed' => 0,
        ];

        foreach ($notifications as $notification) {
            if ($this->send($notification)) {
                $results['sent']++;
            } else {
                $results['failed']++;
            }
        }

        return $results;
    }

    /**
     * ==========================================
     * MÉTODOS PARA TEMPLATES
     * ==========================================
     */

    /**
     * Crear template programado
     */
    public function createScheduledTemplate(
        User $user,
        string $title,
        string $message,
        Carbon $scheduledAt,
        ?array $data = null,
        $notifiable = null,
        bool $sendEmail = false,
        ?string $customEmail = null
    ): NotificationTemplate {
        $scheduledAt = is_string($scheduledAt) ? Carbon::parse($scheduledAt) : $scheduledAt;

        // Si hay email personalizado, guardarlo en data
        if ($customEmail && $sendEmail) {
            $data = $data ?? [];
            $data['custom_email'] = $customEmail;
        }

        return NotificationTemplate::create([
            'type' => 'scheduled',
            'user_id' => $user->id,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'notifiable_type' => $notifiable ? get_class($notifiable) : null,
            'notifiable_id' => $notifiable?->id,
            'scheduled_at' => $scheduledAt,
            'next_send_at' => $scheduledAt,
            'is_active' => true,
            'send_email' => $sendEmail,
        ]);
    }

    /**
     * Crear template recurrente
     * 
     * $recurringPattern ejemplos:
     * - ['interval' => 'daily']
     * - ['interval' => 'monthly', 'day' => 15]
     * - ['interval' => 'days', 'value' => 7]
     * - ['interval' => 'minutes', 'value' => 5]
     */
    public function createRecurringTemplate(
        User $user,
        string $title,
        string $message,
        array $recurringPattern,
        ?array $data = null,
        $notifiable = null,
        ?Carbon $startsAt = null,
        bool $sendEmail = false,
        ?string $customEmail = null
    ): NotificationTemplate {
        $startsAt = $startsAt ?? now();

        // Si hay email personalizado, guardarlo en data
        if ($customEmail && $sendEmail) {
            $data = $data ?? [];
            $data['custom_email'] = $customEmail;
        }

        return NotificationTemplate::create([
            'type' => 'recurring',
            'user_id' => $user->id,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'notifiable_type' => $notifiable ? get_class($notifiable) : null,
            'notifiable_id' => $notifiable?->id,
            'recurring_pattern' => $recurringPattern,
            'next_send_at' => $startsAt,
            'is_active' => true,
            'send_email' => $sendEmail,
        ]);
    }

    /**
     * Crear template de recordatorio
     */
    public function createReminderTemplate(
        User $user,
        string $title,
        string $message,
        Carbon $eventDate,
        int $daysBefore,
        ?array $data = null,
        $notifiable = null,
        bool $sendEmail = false,
        ?string $customEmail = null
    ): NotificationTemplate {
        $eventDate = is_string($eventDate) ? Carbon::parse($eventDate) : $eventDate;
        $scheduledAt = $eventDate->copy()->subDays($daysBefore);

        // Preparar data con event_date
        $data = array_merge($data ?? [], ['event_date' => $eventDate->toDateTimeString()]);

        // Si hay email personalizado, agregarlo a data
        if ($customEmail && $sendEmail) {
            $data['custom_email'] = $customEmail;
        }

        return NotificationTemplate::create([
            'type' => 'reminder',
            'user_id' => $user->id,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'notifiable_type' => $notifiable ? get_class($notifiable) : null,
            'notifiable_id' => $notifiable?->id,
            'reminder_days' => $daysBefore,
            'event_date' => $eventDate,
            'next_send_at' => $scheduledAt,
            'is_active' => true,
            'send_email' => $sendEmail,
        ]);
    }

    /**
     * Crear notificación desde template
     */
    public function createFromTemplate(NotificationTemplate $template): Notification
    {
        $notification = Notification::create([
            'template_id' => $template->id,
            'user_id' => $template->user_id,
            'title' => $template->title,
            'message' => $template->message,
            'data' => $template->data,
            'notifiable_type' => $template->notifiable_type,
            'notifiable_id' => $template->notifiable_id,
        ]);

        // Enviar la notificación
        $this->send($notification);

        // Actualizar el template
        $template->markAsSent();

        // Calcular próxima fecha de envío si es recurrente
        if ($template->type === 'recurring') {
            $nextSendAt = $template->calculateNextSendAt();
            if ($nextSendAt) {
                $template->update(['next_send_at' => $nextSendAt]);
            }
        } elseif ($template->type === 'scheduled' || $template->type === 'reminder') {
            // Los scheduled y reminder solo se envían una vez, así que eliminamos el template
            $template->delete();
        }

        return $notification;
    }

    /**
     * Pausar template
     */
    public function pauseTemplate(int $templateId): bool
    {
        $template = NotificationTemplate::find($templateId);
        if (!$template) {
            return false;
        }

        $template->deactivate();
        return true;
    }

    /**
     * Reanudar template
     */
    public function resumeTemplate(int $templateId): bool
    {
        $template = NotificationTemplate::find($templateId);
        if (!$template) {
            return false;
        }

        $template->activate();
        return true;
    }

    /**
     * Eliminar template
     */
    public function deleteTemplate(int $templateId): bool
    {
        return NotificationTemplate::destroy($templateId) > 0;
    }

    /**
     * ==========================================
     * MÉTODOS PARA GESTIÓN DE NOTIFICACIONES
     * ==========================================
     */

    /**
     * Marcar como leída
     */
    public function markAsRead(int $notificationId, int $userId): bool
    {
        $notification = Notification::where('id', $notificationId)
            ->where('user_id', $userId)
            ->first();

        if (!$notification) {
            return false;
        }

        $notification->markAsRead();
        return true;
    }

    /**
     * Marcar todas como leídas
     */
    public function markAllAsRead(int $userId): int
    {
        return Notification::where('user_id', $userId)
            ->unread()
            ->update(['read_at' => now()]);
    }

    /**
     * Eliminar notificación
     */
    public function deleteNotification(int $notificationId, int $userId): bool
    {
        return Notification::where('id', $notificationId)
            ->where('user_id', $userId)
            ->delete() > 0;
    }

    /**
     * Obtener contador de no leídas
     */
    public function getUnreadCount(int $userId): int
    {
        return Notification::where('user_id', $userId)
            ->unread()
            ->count();
    }

    /**
     * ==========================================
     * MÉTODOS PARA COMANDOS DE CONSOLA
     * ==========================================
     */

    /**
     * Obtener templates programados pendientes de envío
     */
    public function getPendingScheduledTemplates(): \Illuminate\Database\Eloquent\Collection
    {
        return NotificationTemplate::scheduled()
            ->dueForSending()
            ->get();
    }

    /**
     * Obtener templates recurrentes activos
     */
    public function getActiveRecurringTemplates(): \Illuminate\Database\Eloquent\Collection
    {
        return NotificationTemplate::recurring()
            ->dueForSending()
            ->get();
    }

    /**
     * Obtener templates de recordatorios pendientes
     */
    public function getPendingReminderTemplates(): \Illuminate\Database\Eloquent\Collection
    {
        return NotificationTemplate::reminder()
            ->dueForSending()
            ->get();
    }
}
