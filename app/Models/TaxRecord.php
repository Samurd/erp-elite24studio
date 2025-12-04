<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasFiles;

class TaxRecord extends Model
{
    use HasFiles;

    protected $fillable = [
        'type_id',
        'status_id',
        'entity',
        'base',
        'porcentage',
        'amount',
        'date',
        'observations',
    ];

    protected $casts = [
        'base' => 'integer',
        'porcentage' => 'integer',
        'amount' => 'integer',
        'date' => 'date',
    ];

    public function type()
    {
        return $this->belongsTo(Tag::class, 'type_id');
    }

    public function status()
    {
        return $this->belongsTo(Tag::class, 'status_id');
    }

    public function getDefaultFolderName(): string
    {
        return 'taxes';
    }
}
