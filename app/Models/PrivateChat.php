<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrivateChat extends Model
{
    protected $fillable = [
        'is_group',
        'name'
    ];

    /**
     * Participants of the private chat.
     * Pivot: private_chat_user
     */
    public function participants()
    {
        return $this->belongsToMany(User::class, 'private_chat_user')->withTimestamps();
    }

    /**
     * Messages inside this chat.
     */
    public function messages()
    {
        return $this->hasMany(Message::class, 'private_chat_id');
    }

    /**
     * Get the last message of this chat.
     */
    public function lastMessage()
    {
        return $this->hasOne(Message::class, 'private_chat_id')->latestOfMany();
    }

    /**
     * Scope to get chats for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->whereHas('participants', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }

    /**
     * Get the other participant in a 1-on-1 chat.
     */
    public function getOtherParticipant($currentUserId)
    {
        if ($this->relationLoaded('participants')) {
            return $this->participants->where('id', '!=', $currentUserId)->first();
        }
        return $this->participants()->where('user_id', '!=', $currentUserId)->first();
    }
}
