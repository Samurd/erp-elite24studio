<?php

namespace App\Models;

use App\Traits\HasFiles;
use Illuminate\Database\Eloquent\Model;

class EventItem extends Model
{
    use HasFiles;

    protected $fillable = [
        'event_id',
        'description',
        'quantity',
        'unit_id',
        'unit_price',
        'total_price',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function unit()
    {
        return $this->belongsTo(Tag::class, 'unit_id');
    }
}
