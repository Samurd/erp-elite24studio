<?php

namespace App\Models;

use App\Traits\HasFiles;
use Illuminate\Database\Eloquent\Model;
use App\Models\Campaign;
use App\Models\Tag;

class Volunteer extends Model
{

    use HasFiles;

    protected $fillable = [
        'campaign_id',
        'name',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'country',
        'role',
        'status_id',
        'certified',
    ];


    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function status()
    {
        return $this->belongsTo(Tag::class, 'status_id');
    }

}
