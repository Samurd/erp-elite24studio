<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Induction extends Model
{
    protected $fillable = [
        'employee_id',
        'type_bond_id',
        'entry_date',
        'responsible_id',
        'date',
        'status_id',
        'confirmation_id',
        'resource',
        'duration',
        'observations',
    ];


    protected $casts = [
        'entry_date' => 'date',
        'date' => 'date',
    ];



    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function typeBond()
    {
        return $this->belongsTo(Tag::class, 'type_bond_id');
    }

    public function responsible()
    {
        return $this->belongsTo(User::class, 'responsible_id');
    }

    public function status()
    {
        return $this->belongsTo(Tag::class, 'status_id');
    }

    public function confirmation()
    {
        return $this->belongsTo(Tag::class, 'confirmation_id');
    }

    public function file()
    {
        return $this->morphMany(File::class, 'fileable');
    }

    protected static function booted()
    {
        static::deleting(function ($case) {
            foreach ($case->files as $file) {
                // Eliminar archivo físico si existe
                if ($file->path && Storage::exists($file->path)) {
                    Storage::delete($file->path);
                }

                // Eliminar el registro de la base de datos
                $file->delete();
            }
        });
    }
}
