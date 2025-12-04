<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    protected $fillable = [
        'worksite_id',
        'visit_date',
        'performed_by',
        'general_observations',
        'status_id',
        'internal_notes',
    ];

    protected $casts = [
        'visit_date' => 'date'
    ];

    // Relación con la obra
    public function worksite()
    {
        return $this->belongsTo(Worksite::class, 'worksite_id');
    }

    // Usuario que realizó la visita (alias para compatibilidad)
    public function visitor()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    // Usuario que realizó la visita
    public function performer()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    // Estado como TAG
    public function status()
    {
        return $this->belongsTo(Tag::class, 'status_id');
    }

    // Archivos adjuntos
    public function files()
    {
        return $this->morphMany(File::class, 'fileable');
    }
}
