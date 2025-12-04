<?php

namespace App\Models;

use App\Traits\HasFiles;
use Illuminate\Database\Eloquent\Model;

class Policy extends Model
{
    use HasFiles;

    protected $fillable = [
        'name',
        'description',
        'type_id',
        'status_id',
        'issued_at',
        'reviewed_at',
        'assigned_to_id',
    ];

    protected $casts = [
        'issued_at' => 'date',
        'reviewed_at' => 'date',
    ];

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

    /**
     * Carpeta por defecto para archivos de políticas
     */
    protected function getDefaultFolderName(): string
    {
        return 'policies';
    }
}
