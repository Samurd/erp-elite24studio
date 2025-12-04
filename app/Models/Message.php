<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use \App\Traits\HasFiles;

    protected $fillable = [
        'user_id',
        'channel_id',
        'private_chat_id',
        'content',
        'type'
    ];

    /**
     * Sender of the message.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * If message belongs to a channel.
     */
    public function channel()
    {
        return $this->belongsTo(TeamChannel::class, 'channel_id');
    }

    /**
     * If message belongs to a private chat.
     */
    public function privateChat()
    {
        return $this->belongsTo(PrivateChat::class, 'private_chat_id');
    }

    /**
     * User mentions (@user).
     */
    public function mentions()
    {
        return $this->hasMany(MessageMention::class);
    }
}
