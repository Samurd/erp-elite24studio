<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class LicenseStatusUpdate extends Model
{
    protected $fillable = [
        'license_id',
        'date',
        'responsible_id',
        'status_id',
        'description',
        'internal_notes',
        'created_by'
    ];

    public function license()
    {
        return $this->belongsTo(License::class);
    }

    public function responsible()
    {
        return $this->belongsTo(User::class, 'responsible_id');
    }

    public function status()
    {
        return $this->belongsTo(Tag::class, 'status_id');
    }

    public function files()
    {
        return $this->morphMany(File::class, 'fileable');
    }

    protected static function booted()
    {
        static::deleting(function ($update) {
            foreach ($update->files as $file) {
                if ($file->path && Storage::exists($file->path)) {
                    Storage::delete($file->path);
                }
                $file->delete();
            }
        });
    }
}
