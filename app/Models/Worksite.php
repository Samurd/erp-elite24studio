<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Worksite extends Model
{
    protected $fillable = [
        'project_id',
        'name',
        'type_id',
        'status_id',
        'responsible_id',
        'address',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function type()
    {
        return $this->belongsTo(Tag::class, 'type_id');
    }

    public function status()
    {
        return $this->belongsTo(Tag::class, 'status_id');
    }

    public function responsible()
    {
        return $this->belongsTo(User::class, 'responsible_id');
    }

    public function punchItems()
    {
        return $this->hasMany(PunchItem::class, 'worksite_id');
    }

    public function changes()
    {
        return $this->hasMany(Change::class, 'worksite_id');
    }

    public function visits()
    {
        return $this->hasMany(Visit::class, 'worksite_id');
    }

    
}
