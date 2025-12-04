<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Applicant extends Model
{
    protected $fillable = [
        'vacancy_id',
        'full_name',
        'email',
        'status_id',
        'notes',
    ];

    public function vacancy()
    {
        return $this->belongsTo(Vacancy::class);
    }

    public function status()
    {
        return $this->belongsTo(Tag::class, 'status_id');
    }

    public function interviews()
    {
        return $this->hasMany(Interview::class);
    }

    // Scope para búsqueda
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('full_name', 'like', '%' . $search . '%')
              ->orWhere('email', 'like', '%' . $search . '%')
              ->orWhere('identification_number', 'like', '%' . $search . '%');
        });
    }
}
