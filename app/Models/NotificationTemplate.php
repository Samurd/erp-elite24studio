<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class NotificationTemplate extends Model
{
    protected $fillable = [
        'type',
        'title',
        'message',
        'data',
        'notifiable_type',
        'notifiable_id',
        'user_id',
        'scheduled_at',
        'recurring_pattern',
        'reminder_days',
        'event_date',
        'last_sent_at',
        'next_send_at',
        'is_active',
        'send_email',
        'expires_at',
    ];

    protected $casts = [
        'data' => 'array',
        'recurring_pattern' => 'array',
        'scheduled_at' => 'datetime',
        'event_date' => 'datetime',
        'last_sent_at' => 'datetime',
        'next_send_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
        'send_email' => 'boolean',
    ];

    /**
     * Relación polimórfica con cualquier modelo
     */
    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Usuario destinatario
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Notificaciones generadas desde este template
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'template_id');
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function scopeScheduled($query)
    {
        return $query->where('type', 'scheduled');
    }

    public function scopeRecurring($query)
    {
        return $query->where('type', 'recurring');
    }

    public function scopeReminder($query)
    {
        return $query->where('type', 'reminder');
    }

    public function scopeDueForSending($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('next_send_at')
                    ->orWhere('next_send_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    /**
     * Métodos de utilidad
     */
    public function activate(): self
    {
        $this->update(['is_active' => true]);
        return $this;
    }

    public function deactivate(): self
    {
        $this->update(['is_active' => false]);
        return $this;
    }

    public function markAsSent(): self
    {
        $this->update(['last_sent_at' => now()]);
        return $this;
    }

    /**
     * Verifica si el template debe generar una notificación
     */
    public function shouldSend(): bool
    {
        // No está activo
        if (!$this->is_active) {
            return false;
        }

        // Está expirado
        if ($this->expires_at && $this->expires_at->isPast()) {
            $this->deactivate();
            return false;
        }

        // Verifica si es tiempo de enviar
        if ($this->next_send_at) {
            return $this->next_send_at->isPast() || $this->next_send_at->isToday();
        }

        return true;
    }

    /**
     * Calcula la próxima fecha de envío según el tipo
     */
    public function calculateNextSendAt(): ?Carbon
    {
        if ($this->type === 'scheduled') {
            // Scheduled solo se envía una vez
            return null;
        }

        if ($this->type === 'recurring') {
            return $this->calculateNextRecurringDate();
        }

        if ($this->type === 'reminder') {
            return $this->calculateNextReminderDate();
        }

        return null;
    }

    /**
     * Calcula próxima fecha para recurrentes
     */
    private function calculateNextRecurringDate(): Carbon
    {
        $base = $this->last_sent_at ?? now();
        $pattern = $this->recurring_pattern ?? ['interval' => 'daily'];
        $interval = $pattern['interval'] ?? 'daily';

        return match ($interval) {
            'daily' => $base->copy()->addDay(),
            'weekly' => $base->copy()->addWeek(),
            'monthly' => $this->calculateMonthlyOccurrence($base, $pattern),
            'months' => $base->copy()->addMonths($pattern['value'] ?? 1),
            'yearly' => $base->copy()->addYear(),
            'days' => $base->copy()->addDays($pattern['value'] ?? 1),
            'minutes' => $base->copy()->addMinutes($pattern['value'] ?? 1),
            default => $base->copy()->addDay(),
        };
    }

    /**
     * Calcula próxima fecha para recordatorios
     */
    private function calculateNextReminderDate(): ?Carbon
    {
        // Los recordatorios normalmente son de una sola vez
        // Pero podrían repetirse si el evento se repite
        return null;
    }

    /**
     * Calcula ocurrencia mensual
     */
    private function calculateMonthlyOccurrence(Carbon $base, array $pattern): Carbon
    {
        $next = $base->copy()->addMonth();

        if (isset($pattern['day'])) {
            $day = min($pattern['day'], $next->daysInMonth);
            $next->day($day);
        }

        return $next;
    }
}
