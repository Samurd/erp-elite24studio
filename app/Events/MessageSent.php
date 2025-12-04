<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $teamId;
    public $channelId;
    public $message;

    /**
     * Create a new event instance.
     */
    public function __construct($teamId, $channelId, Message $message)
    {
        $this->teamId = $teamId;
        $this->channelId = $channelId;
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn()
    {
        return [new PrivateChannel('teams.' . $this->teamId . '.channels.' . $this->channelId)];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs()
    {
        return 'MessageSent';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith()
    {
        return [
            'team_id' => $this->teamId,
            'channel_id' => $this->channelId,
            'message' => [
                'id' => $this->message->id,
                'content' => $this->message->content,
                'type' => $this->message->type,
                'created_at' => $this->message->created_at,
                'user_id' => $this->message->user_id,
                'channel_id' => $this->message->channel_id,
                'user' => [
                    'id' => $this->message->user->id,
                    'name' => $this->message->user->name,
                    'email' => $this->message->user->email
                ],
                'files' => $this->message->files->map(function ($f) {
                    return [
                        'id' => $f->id,
                        'name' => $f->name,
                        'url' => $f->url,
                        'readable_size' => $f->readable_size,
                        'mime_type' => $f->mime_type
                    ];
                })->toArray()
            ]
        ];
    }
}