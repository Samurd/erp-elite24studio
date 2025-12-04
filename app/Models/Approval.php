<?php

namespace App\Models;

use App\Traits\HasFiles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Approval extends Model
{
    use HasFiles;

    protected $fillable = [
        'name',
        'description',
        'buy',
        'created_by_id',
        'status_id',
        'priority_id',
        'approved_at',
        'rejected_at',
        'cancelled_at',
        'all_approvers',
    ];

    protected $casts = [
        // 'buy' => 'boolean',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function approvers()
    {
        return $this->hasMany(Approver::class);
    }

    public function status()
    {
        return $this->belongsTo(Tag::class, 'status_id');
    }

    public function priority()
    {
        return $this->belongsTo(Tag::class, 'priority_id');
    }


    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }



    protected static function booted()
    {
        static::deleting(function ($approval) {
            foreach ($approval->files as $file) {
                // Eliminar archivo físico si existe
                if ($file->path && Storage::exists($file->path)) {
                    Storage::delete($file->path);
                }

                // Eliminar el registro de la base de datos
                $file->delete();
            }
        });
    }
}
