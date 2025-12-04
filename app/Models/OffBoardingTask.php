<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OffBoardingTask extends Model
{
    protected $fillable = [
        'off_boarding_id',
        'content',
        'completed',
        'team_id',
        'completed_by',
        'completed_at',
    ];

    protected $casts = [
        'completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function offBoarding()
    {
        return $this->belongsTo(OffBoarding::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function completedBy()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

}
