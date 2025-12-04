<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class KpiRecord extends Model
{

    protected $fillable = [
        'kpi_id',
        'record_date',
        'value',
        'observation',
        'created_by_id',
    ];

    public function kpi()
    {
        return $this->belongsTo(Kpi::class);
    }

    public function files()
    {
        return $this->morphMany(File::class, 'fileable');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    protected static function booted()
    {
        static::deleting(function ($kpi_record) {
            foreach ($kpi_record->files as $file) {
                if ($file->path && Storage::exists($file->path)) {
                    Storage::delete($file->path);
                }
                $file->delete();
            }
        });
    }
}
