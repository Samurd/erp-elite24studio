<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApuCampaign extends Model
{

    protected $fillable = [
        'campaign_id',
        'description',
        'quantity',
        'unit_id',
        'unit_price',
        'total_price',
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class, 'campaign_id');
    }


    public function unit()
    {
        return $this->belongsTo(Tag::class, 'unit_id');
    }
}
