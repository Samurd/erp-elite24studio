<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Tag;
use App\Models\User;

class Event extends Model
{
    protected $fillable = [
        'name',
        'type_id',
        'event_date',
        'location',
        'status_id',
        'responsible_id',
        'observations',
    ];
    
    protected $casts = [
        'event_date' => 'date',
    ];

    public function items()
    {
        return $this->hasMany(EventItem::class);
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
}
