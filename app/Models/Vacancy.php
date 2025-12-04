<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vacancy extends Model
{
    protected $fillable = [
        'title',
        'area',
        'contract_type_id',
        'published_at',
        'status_id',
        'user_id',
        'description',
    ];

    protected $casts = [
        'published_at' => 'date',
    ];

 
    public function contractType()
    {
        return $this->belongsTo(Tag::class, 'contract_type_id');
    }

    public function status()
    {
        return $this->belongsTo(Tag::class, 'status_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function applicants()
    {
        return $this->hasMany(Applicant::class);
    }
}
