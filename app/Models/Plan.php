<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'project_id',
        'team_id',
        'owner_id',
        'name',
        'description',
    ];

    /** 🔹 Un plan pertenece a un proyecto, si es null, seria del modulo planner task */
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    /** 
     * 🔹 Un plan puede pertenecer (o no) a un team
     * Si es NULL → plan personal del usuario
     */
    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * 🔹 Usuario que creó el plan
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * 🔹 Buckets dentro del plan
     */
    public function buckets()
    {
        return $this->hasMany(Bucket::class);
    }

    /**
     * 🔹 Tareas dentro del plan (a través de los buckets)
     */
    public function tasks()
    {
        return $this->hasManyThrough(Task::class, Bucket::class);
    }
}
