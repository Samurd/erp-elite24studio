<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OauthConnection extends Model
{
    protected $fillable = [
        'user_id',
        'provider',
        'provider_id',
        'email',
        'name',
        'avatar',
        'access_token',
        'refresh_token',
        'token_expires_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
