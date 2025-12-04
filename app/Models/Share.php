<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Share extends Model
{
    protected $fillable = [
        "user_id",
        "shared_with_user_id",
        "shared_with_team_id",
        "shareable_id",
        "shareable_type",
        "permission",
        "share_token",
        "expires_at",
    ];

    protected $dates = ["expires_at"];

    /** 🔍 Scope: solo enlaces activos (válidos) */
    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull("expires_at")->orWhere("expires_at", ">", now());
        });
    }

    /** 🔗 Determina si es un enlace público */
    public function isLink(): bool
    {
        return !is_null($this->share_token);
    }

    /** 🔐 Determina si el enlace sigue siendo válido */
    public function isActive(): bool
    {
        $carbonDate = Carbon::parse($this->expires_at);

        return $this->isLink()
            ? is_null($this->expires_at) || $carbonDate->isFuture()
            : true; // Los shares con usuario o team no expiran
    }

    /** Relaciones */
    public function shareable()
    {
        return $this->morphTo();
    }

    public function owner()
    {
        return $this->belongsTo(User::class, "user_id");
    }

    public function sharedWithUser()
    {
        return $this->belongsTo(User::class, "shared_with_user_id");
    }

    public function sharedWithTeam()
    {
        return $this->belongsTo(Team::class, "shared_with_team_id");
    }
}
