<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Approver extends Model
{
    protected $fillable = [
        'approval_id',
        'user_id',
        'order',
        'status_id',
        'comment',
        'responded_at',
    ];

    protected $casts = [
        'responded_at' => 'datetime',
    ];


    public function approval()
    {
        return $this->belongsTo(Approval::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function status()
    {
        return $this->belongsTo(Tag::class, 'status_id');
    }
}
