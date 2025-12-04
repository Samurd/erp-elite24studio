<?php

namespace App\Models;

use App\Traits\HasFiles;
use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{

    use HasFiles;


    protected $fillable = [
        'employee_id',
        'type_id',
        'start_date',
        'end_date',
        'status_id',
        'approver_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(\App\Models\Employee::class, 'employee_id');
    }

    public function type()
    {
        return $this->belongsTo(\App\Models\Tag::class, 'type_id');
    }

    public function status()
    {
        return $this->belongsTo(\App\Models\Tag::class, 'status_id');
    }

    public function approver()
    {
        return $this->belongsTo(\App\Models\User::class, 'approver_id');
    }

}
