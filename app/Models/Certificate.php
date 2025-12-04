<?php

namespace App\Models;

use App\Traits\HasFiles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Certificate extends Model
{
    use HasFiles;

    protected $fillable = [
        'name',
        'description',
        'type_id',
        'status_id',
        'issued_at',
        'expires_at',
        'assigned_to_id',
    ];

    protected $casts = [
        'issued_at' => 'date',
        'expires_at' => 'date',
    ];

    protected function getDefaultFolderName(): string
    {
        return 'certificates';
    }

    // Relaciones con Tags
    public function type()
    {
        return $this->belongsTo(Tag::class, 'type_id');
    }

    public function status()
    {
        return $this->belongsTo(Tag::class, 'status_id');
    }

    // Relación con usuario asignado
    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to_id');
    }

    protected static function booted()
    {
        static::deleting(function ($cert) {
            foreach ($cert->files as $file) {
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
