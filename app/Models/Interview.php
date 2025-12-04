<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Interview extends Model
{
    protected $fillable = [
        'applicant_id',
        'date',
        'time',
        'interviewer_id',
        'interview_type_id',
        'status_id',
        'result_id',
        'platform',
        'platform_url',
        'expected_results',
        'interviewer_observations',
        'rating',
    ];

    protected $casts = [
        'date' => 'date',
        'time' => 'datetime:H:i',
        'rating' => 'decimal:2',
    ];

    // Relaciones
    public function applicant()
    {
        return $this->belongsTo(Applicant::class);
    }

    public function vacancy()
    {
        return $this->belongsTo(Vacancy::class);
    }

    public function interviewer()
    {
        return $this->belongsTo(User::class, 'interviewer_id');
    }

    public function interviewType()
    {
        return $this->belongsTo(Tag::class, 'interview_type_id');
    }

    public function status()
    {
        return $this->belongsTo(Tag::class, 'status_id');
    }

    public function result()
    {
        return $this->belongsTo(Tag::class, 'result_id');
    }
}
