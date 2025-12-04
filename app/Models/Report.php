<?php

namespace App\Models;

use App\Traits\HasFiles;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{

    use HasFiles;
    protected $fillable = [
        "title",
        "description",
        "status_id",
        "date",
        "hour",
        "user_id",
        "notes",
    ];

    protected $casts = [
        'date' => 'date',
        'hour' => 'datetime',
    ];


    public function status()
    {
        return $this->belongsTo(Tag::class, "status_id");
    }

    public function user()
    {
        return $this->belongsTo(User::class, "user_id");
    }
}
