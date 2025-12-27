<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamChannel extends Model
{
    protected $fillable = [
        'team_id',
        'name',
        'slug',
        'description',
        'is_private',
        'parent_id',
    ];

    /**
     * Relation: Channel belongs to a Team.
     */
    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Relation: Parent channel.
     */
    public function parent()
    {
        return $this->belongsTo(TeamChannel::class, 'parent_id');
    }

    /**
     * Relation: Child channels (subchannels).
     */
    public function children()
    {
        return $this->hasMany(TeamChannel::class, 'parent_id');
    }

    /**
     * Relation: Private channels have explicit members.
     * Public channels NO usan esta relación.
     *
     * Pivot: channel_user
     */
    public function members()
    {
        return $this->belongsToMany(User::class, 'channel_user', 'channel_id', 'user_id')->withTimestamps();
    }

    /**
     * Relation: Channel has many messages.
     */
    public function messages()
    {
        return $this->hasMany(Message::class, 'channel_id');
    }
}
