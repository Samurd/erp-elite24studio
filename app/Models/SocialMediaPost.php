<?php

namespace App\Models;

use App\Traits\HasFiles;
use Illuminate\Database\Eloquent\Model;
use App\Models\Project;
use App\Models\Tag;
use App\Models\User;
use App\Models\File;

class SocialMediaPost extends Model
{

    use HasFiles;

    protected $fillable = [
        'mediums',
        'content_type',
        'piece_name',
        'scheduled_date',
        'project_id',
        'responsible_id',
        'comments',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    // 🔗 Relación con proyectos
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // 🔗 Relación con estados (tags)
    public function status()
    {
        return $this->belongsTo(Tag::class, 'status_id');
    }

    // 🔗 Relación con responsable
    public function responsible()
    {
        return $this->belongsTo(User::class, 'responsible_id');
    }
}
