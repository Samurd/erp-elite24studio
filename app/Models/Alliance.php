<?php

namespace App\Models;

use App\Traits\HasFiles;
use Illuminate\Database\Eloquent\Model;
use App\Models\Tag;

class Alliance extends Model
{

    use HasFiles;

    protected $fillable = [
        'name',
        'type_id',
        'start_date',
        'validity',
        'certified',
    ];

    protected $casts = [
        'start_date' => 'date',
        'certified' => 'boolean',
    ];

    public function type()
    {
        return $this->belongsTo(Tag::class, 'type_id');
    }
}
