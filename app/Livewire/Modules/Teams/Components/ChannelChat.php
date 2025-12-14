<?php

namespace App\Livewire\Modules\Teams\Components;

use App\Events\MessageSent;
use App\Models\Message;
use App\Models\Team;
use App\Models\TeamChannel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;

class ChannelChat extends Component
{
    use WithFileUploads;

    public Team $team;
    public TeamChannel $channel;
    public $messages = [];
    public $newMessage = '';
    public $messageLimit = 20;

    // Attachments
    public $chatUploads = [];
    public $chatLinkIds = []; // IDs and metadata for linked files

    public function getListeners()
    {
        $teamId = $this->team->id;
        $channelId = $this->channel->id;

        return [
            "echo-private:teams.{$teamId}.channels.{$channelId},MessageSent" => 'handleRealtimeMessage',
            "echo-private:teams.{$teamId}.channels.{$channelId},.MessageSent" => 'handleRealtimeMessage',
            'chat-attachments-updated' => 'updateChatAttachments',
            'chat-attachments-committed' => 'finalizeMessage', // Handle post-upload finalization
        ];
    }

    public function mount(Team $team, TeamChannel $channel)
    {
        $this->team = $team;
        $this->channel = $channel;
        $this->loadMessages();
    }

    public function loadMessages()
    {
        // Permission check logic (simplified from Show.php, assuming access validation done upstream or handles safely)
        if (!$this->canAccessChannel()) {
            $this->messages = [];
            return;
        }

        $this->messages = $this->channel->messages()
            ->with(['user', 'files'])
            ->latest()
            ->take($this->messageLimit)
            ->get()
            ->reverse()
            ->map(function ($msg) {
                return $this->formatMessage($msg);
            })
            ->values()
            ->toArray();

        $this->dispatch('messagesLoaded');
    }

    public function loadMore()
    {
        $this->messageLimit += 20;
        $this->loadMessages();
        $this->dispatch('oldMessagesLoaded');
    }

    public function sendMessage($content = null, $tempId = null)
    {
        $content = $content ?? $this->newMessage;

        // Basic validation
        if (empty(trim($content)) && empty($this->chatUploads) && empty($this->chatLinkIds)) {
            return;
        }

        if (!$this->canSendMessage()) {
            $this->dispatch('error', 'No tienes permiso para enviar mensajes.');
            return;
        }

        try {
            // Create Message
            $msg = Message::create([
                'user_id' => Auth::id(),
                'channel_id' => $this->channel->id,
                'content' => $content,
                'type' => 'text',
            ]);

            // Handle Attachments via event to child component
            if (!empty($this->chatUploads) || !empty($this->chatLinkIds)) {
                $this->dispatch('commit-chat-attachments', messageId: $msg->id);
            } else {
                $this->finalizeMessage($msg->id);
            }

            // Guardar datos para la UI antes de limpiar
            $currentUploads = $this->chatUploads;
            $currentLinks = $this->chatLinkIds;

            // Limpiar adjuntos locales (UI)
            $this->chatUploads = [];
            $this->chatLinkIds = [];

            // Reset input
            $this->newMessage = '';

            // Agregar al array local para que persista tras el re-render
            $this->messages[] = [
                'id' => $msg->id,
                'content' => $msg->content,
                'user_id' => Auth::id(),
                'created_at' => $msg->created_at->format('Y-m-d H:i:s'),
                'user' => [
                    'name' => Auth::user()->name,
                ],
                // Mapear archivos si existen (similar a Chats.php)
                'files' => collect($currentUploads)->merge($currentLinks)->map(function ($f) {
                    return [
                        'url' => $f['url'] ?? '#',
                        'name' => $f['name'],
                        'readable_size' => isset($f['size']) ? $this->formatBytes($f['size']) : ($f['readable_size'] ?? '')
                    ];
                })->toArray()
            ];

            // Optimistic Update confirmation
            // We dispatch back to frontend that this tempId is now real ID msg->id
            $this->dispatch('messageAdded', ['tempId' => $tempId, 'content' => $content]);

        } catch (\Exception $e) {
            Log::error('Error sending channel message: ' . $e->getMessage());
        }
    }

    private function formatBytes($size, $precision = 2)
    {
        if ($size > 0) {
            $base = log($size) / log(1024);
            $suffixes = array('B', 'KB', 'MB', 'GB', 'TB');
            return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
        }
        return '0 B';
    }

    public function finalizeMessage($messageId)
    {
        $msg = Message::find($messageId);
        if (!$msg)
            return;

        // Broadcast
        broadcast(new MessageSent(
            $this->team->id,
            $this->channel->id,
            $msg
        ))->toOthers();

        // Notify function logic... (we can keep minimal or move to Service/Job)
        // For now, let's assume broadcasting is key.
    }

    public function handleRealtimeMessage($event)
    {
        // Add to messages list if not self (though optimistic UI handles self)
        // Assuming event payload structure matches
        $data = $event['message'] ?? $event;

        // Avoid duplicates if we received our own echo (should be handled by toOthers but safe check)
        if (($data['user_id'] ?? 0) === Auth::id())
            return;

        // We need to format it or reload. Reloading is safer for file attachments etc but slower.
        // Let's create an array entry.
        // ... formatting logic ...
        $this->dispatch('messageAdded', ['content' => $data['content']]); // Just to trigger scroll
        $this->messages[] = $this->formatMessageFromEvent($data);
    }

    // Helpers
    private function canAccessChannel()
    {
        if ($this->channel->is_private) {
            return $this->channel->members()->where('user_id', Auth::id())->exists();
        }
        return true; // Assuming public channels accessible by team members (validated in Show.php mount or route middleware)
    }

    private function canSendMessage()
    {
        return $this->canAccessChannel(); // Simplify permissions for now
    }

    private function formatMessage($msg)
    {
        return [
            'id' => $msg->id,
            'content' => $msg->content,
            'user_id' => $msg->user_id,
            'created_at' => $msg->created_at->format('Y-m-d H:i:s'),
            'user' => [
                'name' => $msg->user->name,
            ],
            'files' => $msg->files->map(fn($f) => ['url' => $f->url, 'name' => $f->name, 'readable_size' => $f->readable_size])->toArray()
        ];
    }

    private function formatMessageFromEvent($data)
    {
        // ... simplistic mapping ...
        return $data; // Ideally map fields correctly
    }

    public function updateChatAttachments($data)
    {
        $this->chatUploads = $data['uploads'] ?? [];
        $this->chatLinkIds = $data['links'] ?? [];

        $count = count($this->chatUploads) + count($this->chatLinkIds);
        $this->dispatch('attachments-updated', count: $count);
    }

    public function render()
    {
        return view('livewire.modules.teams.components.channel-chat');
    }
}
