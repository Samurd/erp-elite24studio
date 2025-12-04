<?php

namespace App\Models;

use App\Traits\HasFiles;
use App\Traits\HasNotifications;
use Illuminate\Database\Eloquent\Model;

class Sub extends Model
{
    use HasFiles, HasNotifications;

    protected $fillable = [
        'name',
        'frequency_id',
        'type',
        'amount',
        'start_date',
        'renewal_date',
        'status_id',
        'user_id',
        'notes',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::created(function (Sub $sub) {
            if ($sub->renewal_date && $sub->user_id) {
                $sub->createAutomaticNotification();
            }
        });

        static::updated(function (Sub $sub) {
            if ($sub->isDirty(['renewal_date', 'frequency_id'])) {
                $sub->updateNotifications();

                if ($sub->notificationTemplates()->whereIn('type', ['reminder', 'recurring'])->count() === 0) {
                    $sub->createAutomaticNotification();
                }
            }
        });

        static::deleted(function (Sub $sub) {
            $sub->deleteNotifications();
        });
    }

    protected $casts = [
        'start_date' => 'date',
        'renewal_date' => 'date',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function status()
    {
        return $this->belongsTo(Tag::class);
    }

    public function frequency()
    {
        return $this->belongsTo(Tag::class);
    }

    /**
     * Carpeta por defecto para archivos de suscripciones
     */
    protected function getDefaultFolderName(): string
    {
        return 'subs';
    }

    /**
     * ========================================
     * MÉTODOS PARA NOTIFICACIONES
     * ========================================
     */

    /**
     * Días antes de la renovación para enviar notificación
     * Requerido por HasNotifications trait
     */

    // Título por defecto para las notificaciones
    public function getDefaultNotificationTitle(): string
    {
        return "Notificación de la suscripción: {$this->name}";
    }

    // Mensaje por defecto para recordatorios (reminder)
    public function getDefaultNotificationMessage(): string
    {
        return "La suscripción: {$this->name}, {$this->frequency->name} vence el {$this->renewal_date->format('d/m/Y')}.";
    }

    public function getDefaultRecurringMessage(): string
    {
        return "La suscripción: {$this->name} con frecuencia {$this->frequency->name} vence el {$this->renewal_date->format('d/m/Y')}.";
    }

    public function getDefaultNotificationUrl(): ?string
    {
        return route('subs.edit', ['sub' => $this]);
    }

    public function getRenewalReminderDays(): ?int
    {
        if (!$this->frequency) {
            return 7; // Por defecto 7 días
        }

        return match (strtolower($this->frequency->name)) {
            'mensual' => 3,
            'trimestral' => 7,
            'semestral' => 15,
            'anual' => 30,
            default => 7,
        };
    }

    /**
     * Patrón de recurrencia para notificaciones
     * Opcional - solo para modelos con eventos recurrentes
     */
    protected function getRecurringPattern(): ?array
    {
        if (!$this->frequency) {
            return null;
        }

        return match (strtolower($this->frequency->name)) {
            'mensual' => ['interval' => 'monthly'],
            'trimestral' => ['interval' => 'months', 'value' => 3],
            'semestral' => ['interval' => 'months', 'value' => 6],
            'anual' => ['interval' => 'yearly'],
            default => null,
        };
    }
    /**
     * Obtener frecuencias permitidas para notificaciones recurrentes
     */
    public function getNotificationFrequencies(): array
    {
        // Ensure frequency is loaded if it exists
        if (!$this->relationLoaded('frequency') && $this->frequency_id) {
            $this->load('frequency');
        }

        if (!$this->frequency) {
            return [
                'daily' => 'Diario',
                'weekly' => 'Semanal',
                'monthly' => 'Mensual',
                'yearly' => 'Anual',
            ];
        }

        return match (strtolower($this->frequency->name)) {
            'mensual' => ['monthly' => 'Mensual'],
            'trimestral' => ['months' => 'Trimestral (Cada 3 meses)'],
            'semestral' => ['months' => 'Semestral (Cada 6 meses)'],
            'anual' => ['yearly' => 'Anual'],
            'semanal' => ['weekly' => 'Semanal'],
            'diario' => ['daily' => 'Diario'],
            default => [
                'daily' => 'Diario',
                'weekly' => 'Semanal',
                'monthly' => 'Mensual',
                'yearly' => 'Anual',
            ],
        };
    }
}
