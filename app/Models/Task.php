<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'bucket_id',
        'title',
        'description',
        'status_id',
        'priority_id',
        'created_by',
        'notes',
        'start_date',
        'due_date',
    ];

    protected $casts = [
        'start_date' => 'datetime:Y-m-d H:i:s',
        'due_date' => 'datetime:Y-m-d H:i:s',
    ];



    public function bucket()
    {
        return $this->belongsTo(Bucket::class);
    }

    public function status()
    {
        return $this->belongsTo(Tag::class, 'status_id');
    }

    public function priority()
    {
        return $this->belongsTo(Tag::class, 'priority_id');
    }

    public function assignedUsers()
    {
        return $this->belongsToMany(User::class, 'task_user')->withTimestamps();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
