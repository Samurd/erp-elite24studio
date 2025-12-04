<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Project;
use App\Models\Tag;
use App\Models\User;
use App\Models\File;

class CaseMarketing extends Model
{
    protected $fillable = [
        'subject',
        'project_id',
        'date',
        'mediums', // canales: Web, RRSS, Chatbot, Email, etc.
        'description',
        'responsible_id',
        'type_id',
        'status_id',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    // 🔗 Proyecto asociado
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // 🔗 Tipo de caso (Lead, Publicidad, Pauta, Web, Diseño, Social media , Incidente, etc.)
    public function type()
    {
        return $this->belongsTo(Tag::class, 'type_id');
    }

    // 🔗 Estado del caso (Pendiente, En revision, Aprobado, Cerrado, Activado)
    public function status()
    {
        return $this->belongsTo(Tag::class, 'status_id');
    }

    // 🔗 Responsable
    public function responsible()
    {
        return $this->belongsTo(User::class, 'responsible_id');
    }

    // 🔗 Archivos adjuntos (morph con files)
    public function files()
    {
        return $this->morphMany(File::class, 'fileable');
    }
}
