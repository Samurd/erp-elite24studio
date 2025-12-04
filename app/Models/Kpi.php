<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kpi extends Model
{

    protected $fillable = [
        'protocol_code',
        'indicator_name',
        'target_value',
        'periodicity_days',
        'role_id',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function records()
    {
        return $this->hasMany(KpiRecord::class);
    }

    // 🔹 Calcula automáticamente la próxima fecha a realizar
    public function getNextDueDateAttribute()
    {
        $lastRecord = $this->records()->latest('record_date')->first();

        if (!$lastRecord) {
            return null; // No hay registros aún
        }

        // Asegurarse de que record_date sea una instancia de Carbon
        $recordDate = $lastRecord->record_date;
        if (is_string($recordDate)) {
            $recordDate = \Carbon\Carbon::parse($recordDate);
        }

        return $recordDate->addDays($this->periodicity_days);
    }
}
