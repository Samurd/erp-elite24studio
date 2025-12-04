<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Notification extends Model
{
    protected $fillable = [
        'title',
        'message',
        'data',
        'notifiable_type',
        'notifiable_id',
        'user_id',
        'template_id',
        'read_at',
        'sent_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
        'sent_at' => 'datetime',
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
     * Template que generó esta notificación (si aplica)
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(NotificationTemplate::class, 'template_id');
    }

    /**
     * Scopes
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    public function scopeFromTemplate($query)
    {
        return $query->whereNotNull('template_id');
    }

    public function scopeManual($query)
    {
        return $query->whereNull('template_id');
    }

    /**
     * Métodos de utilidad
     */
    public function markAsRead(): self
    {
        $this->update(['read_at' => now()]);
        return $this;
    }

    public function markAsUnread(): self
    {
        $this->update(['read_at' => null]);
        return $this;
    }

    public function markAsSent(): self
    {
        $this->update(['sent_at' => now()]);
        return $this;
    }

    /**
     * Accessor para verificar si está leída
     */
    public function getIsReadAttribute(): bool
    {
        return $this->read_at !== null;
    }

    /**
     * Accessor para verificar si está no leída
     */
    public function getIsUnreadAttribute(): bool
    {
        return $this->read_at === null;
    }
}
