<?php

namespace App\Models;

use App\Traits\HasFiles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class CaseRecord extends Model
{
    use SoftDeletes, HasFiles;

    protected $fillable = [
        'contact_id',
        'description',
        'status_id',
        'case_type_id',
        'assigned_to_id',
        'date',
        'channel'
    ];

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }
    public function status()
    {
        return $this->belongsTo(Tag::class, 'status_id');
    }
    public function type()
    {
        return $this->belongsTo(Tag::class, 'case_type_id');
    }
    public function assignedTo()
    {
        return $this->belongsTo(\App\Models\User::class, 'assigned_to_id');
    }

    /**
     * Nombre de carpeta por defecto para archivos de casos
     */
    protected function getDefaultFolderName(): string
    {
        return 'case-files';
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


    // public function reports() { return $this->hasMany(Report::class, 'case_id'); }
}
