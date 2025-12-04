<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OffBoarding extends Model
{
    protected $fillable = [
        'employee_id',
        'project_id',
        'reason',
        'exit_date',
        'status_id',
        'responsible_id',
    ];

    protected $casts = [
        'exit_date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(\App\Models\Employee::class);
    }

    public function project()
    {
        return $this->belongsTo(\App\Models\Project::class);
    }

    public function status()
    {
        return $this->belongsTo(\App\Models\Tag::class);
    }

    public function responsible()
    {
        return $this->belongsTo(\App\Models\User::class, 'responsible_id');
    }

    public function tasks()
    {
        return $this->hasMany(\App\Models\OffBoardingTask::class);
    }

}
