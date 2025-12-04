<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Change extends Model
{
    protected $fillable = [
        'worksite_id',
        'change_date',
        'change_type_id',
        'requested_by',
        'description',
        'budget_impact_id',
        'status_id',
        'approved_by',
        'internal_notes',
    ];

    protected $casts = [
        'change_date' => 'date',
    ];

    // Obra / sitio de construcción
    public function worksite()
    {
        return $this->belongsTo(Worksite::class, 'worksite_id');
    }

    // Tipo de cambio (TAG)
    public function type()
    {
        return $this->belongsTo(Tag::class, 'change_type_id');
    }

    // Impacto en presupuesto (TAG)
    public function budgetImpact()
    {
        return $this->belongsTo(Tag::class, 'budget_impact_id');
    }

    // Estado (TAG)
    public function status()
    {
        return $this->belongsTo(Tag::class, 'status_id');
    }

    // Usuario aprobador
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Archivos adjuntos — relación polimórfica
    public function files()
    {
        return $this->morphMany(File::class, 'fileable');
    }
}
