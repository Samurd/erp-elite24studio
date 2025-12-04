<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PrivateMessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $chatId;
    public $message;

    /**
     * Create a new event instance.
     */
    public function __construct($chatId, Message $message)
    {
        $this->chatId = $chatId;
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn()
    {
        return [new PrivateChannel('private-chat.' . $this->chatId)];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs()
    {
        return 'PrivateMessageSent';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith()
    {
        return [
            'chat_id' => $this->chatId,
            'message' => [
                'id' => $this->message->id,
                'content' => $this->message->content,
                'type' => $this->message->type,
                'created_at' => $this->message->created_at,
                'user_id' => $this->message->user_id,
                'private_chat_id' => $this->message->private_chat_id,
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
