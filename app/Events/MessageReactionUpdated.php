<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageReactionUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $teamId;
    public $channelId;
    public $messageId;
    public $reactions;

    /**
     * Create a new event instance.
     */
    public function __construct($teamId, $channelId, $messageId, $reactions)
    {
        $this->teamId = $teamId;
        $this->channelId = $channelId;
        $this->messageId = $messageId;
        $this->reactions = $reactions;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("teams.{$this->teamId}.channels.{$this->channelId}"),
        ];
    }

    public function broadcastAs()
    {
        return 'MessageReactionUpdated';
    }
}
