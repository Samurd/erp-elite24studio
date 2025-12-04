<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PunchItem extends Model
{
    protected $fillable = [
        'worksite_id',
        'status_id',
        'observations',
        'responsible_id',
    ];

    public function worksite()
    {
        return $this->belongsTo(Worksite::class, 'worksite_id');
    }

    public function status()
    {
        return $this->belongsTo(Tag::class, 'status_id');
    }

    public function responsible()
    {
        return $this->belongsTo(User::class, 'responsible_id');
    }

    public function files()
    {
        return $this->morphMany(File::class, 'fileable');
    }
}
