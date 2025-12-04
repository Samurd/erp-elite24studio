<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasFiles;

class Audit extends Model
{
    use HasFiles;

    protected $fillable = [
        'date_register',
        'date_audit',
        'objective',
        'type_id',
        'place',
        'status_id',
        'observations',
    ];

    protected $casts = [
        'date_register' => 'date',
        'date_audit' => 'date',
        'objective' => 'integer',
    ];

    public function type()
    {
        return $this->belongsTo(\App\Models\Tag::class, 'type_id');
    }

    public function status()
    {
        return $this->belongsTo(\App\Models\Tag::class, 'status_id');
    }

    public function getDefaultFolderName(): string
    {
        return 'audits';
    }
}
