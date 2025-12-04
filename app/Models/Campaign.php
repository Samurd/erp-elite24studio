<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Tag;
use App\Models\File;
use App\Models\Donation;

class Campaign extends Model
{

    protected $fillable = [
        'name',
        'date_event',
        'address',
        'responsible_id',
        'status_id',
        'alliances',
        'goal',
        'estimated_budget',
        'description',
    ];

    
    protected $casts = [
        'date_event' => 'date',
    ];

    public function donations()
    {
        return $this->hasMany(Donation::class);
    }

    public function volunteers()
    {
        return $this->hasMany(Volunteer::class);
    }

    public function responsible()
    {
        return $this->belongsTo(User::class, 'responsible_id');
    }

    public function status()
    {
        return $this->belongsTo(Tag::class, 'status_id');
    }


    public function files()
    {
        return $this->morphMany(File::class, 'fileable');
    }

    
}
