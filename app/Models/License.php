<?php

namespace App\Models;

use App\Traits\HasFiles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class License extends Model
{

    use HasFiles;
    protected $fillable = [
        'project_id',
        'license_type_id',
        'status_id',
        'entity',
        'company',
        'eradicated_number',
        'eradicatd_date',
        'estimated_approval_date',
        'expiration_date',
        'requires_extension',
        'observations',
    ];

    protected $casts = [
        'eradicatd_date' => 'date',
        'estimated_approval_date' => 'date',
        'expiration_date' => 'date',
        'requires_extension' => 'boolean',
    ];

    /* ----------------- Relationships ----------------- */

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function type()
    {
        return $this->belongsTo(Tag::class, 'license_type_id');
    }

    public function status()
    {
        return $this->belongsTo(Tag::class, 'status_id');
    }

    public function statusUpdates()
    {
        return $this->hasMany(LicenseStatusUpdate::class);
    }

    /* ----------------- File Management ----------------- */

    use \App\Traits\HasFiles;

    public function getDefaultFolderName(): string
    {
        return 'licenses';
    }
}
