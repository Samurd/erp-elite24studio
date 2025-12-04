<?php

namespace App\Models;

use App\Traits\HasFiles;
use Illuminate\Database\Eloquent\Model;

class Adpiece extends Model
{

    use HasFiles;

    protected $fillable = [
        'type_id',
        'format_id',
        'status_id',
        'project_id',
        'team_id',
        'strategy_id',
        'name',
        'media',
        'instructions',
    ];

    // Relaciones con otras tablas
    public function type()
    {
        return $this->belongsTo(Tag::class, 'type_id');
    }

    public function format()
    {
        return $this->belongsTo(Tag::class, 'format_id');
    }

    public function status()
    {
        return $this->belongsTo(Tag::class, 'status_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function responsible()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    public function strategy()
    {
        return $this->belongsTo(Strategy::class);
    }
}
