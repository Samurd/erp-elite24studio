<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Models\Tag;
use App\Models\User;
use App\Models\File;

class Strategy extends Model
{
     protected $fillable = [
        'name',
        'objective',
        'status_id',
        'start_date',
        'end_date',
        'target_audience',
        'platforms',
        'responsible_id',
        'notify_team',
        'add_to_calendar',
        'observations',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function status()
    {
        return $this->belongsTo(Tag::class, 'status_id');
    }

    public function responsible()
    {
        return $this->belongsTo(User::class, 'responsible_id');
    }

    public function files()
    {
        return $this->morphMany(File::class, 'fileable');
    }

    protected static function booted()
    {
        static::deleting(function ($strategy) {
            foreach ($strategy->files as $file) {
                if ($file->path && Storage::exists($file->path)) {
                    Storage::delete($file->path);
                }
                $file->delete();
            }
        });
    }
}
