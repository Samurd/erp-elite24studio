<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{

    protected $fillable = [
        'title',
        'date',
        'start_time',
        'end_time',
        'team_id',
        'status_id',
        'notes',
        'observations',
        'goal',
        'url',
        'bookingId'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'date' => 'date',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function status()
    {
        return $this->belongsTo(Tag::class);
    }

    public function responsibles()
    {
        return $this->belongsToMany(User::class, 'meeting_responsibles');
    }
}
