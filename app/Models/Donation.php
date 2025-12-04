<?php

namespace App\Models;

use App\Traits\HasFiles;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{

    use HasFiles;

    protected $fillable = [
        'name',
        'campaign_id',
        'amount',
        'payment_method',
        'date',
        'certified',
    ];


    protected $casts = [
        'date' => 'date',
        'certified' => 'boolean',
    ];


    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

}
