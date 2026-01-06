<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasFiles;

class Contract extends Model
{
    use HasFiles;
    protected $fillable = [
        'employee_id',
        'type_id',
        'category_id',
        'status_id',
        'start_date',
        'end_date',
        'amount',
        'schedule',
        'registered_by_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'amount' => 'integer',
    ];

    public function employee()
    {
        return $this->belongsTo(\App\Models\Employee::class);
    }

    public function type()
    {
        return $this->belongsTo(\App\Models\Tag::class, 'type_id');
    }

    public function category()
    {
        return $this->belongsTo(\App\Models\Tag::class, 'category_id');
    }

    public function status()
    {
        return $this->belongsTo(\App\Models\Tag::class, 'status_id');
    }

    public function registeredBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'registered_by_id');
    }




}
