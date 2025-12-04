<?php

namespace App\Models;

use App\Traits\HasFiles;
use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    use HasFiles;

    protected function getDefaultFolderName(): string
    {
        return 'quotes';
    }

    protected $fillable = [
        "contact_id",
        "issued_at",
        "status_id",
        "total",
        "user_id",
    ];

    protected $casts = [
        'issued_at' => 'datetime',
    ];


    public function user()
    {
        return $this->belongsTo(User::class, "user_id");
    }

    public function status()
    {
        return $this->belongsTo(Tag::class, "status_id");
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class, "contact_id");
    }
}
