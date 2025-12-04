<?php

namespace App\Livewire\Modules\Teams;

use App\Events\PrivateMessageSent;
use App\Models\Message;
use App\Models\PrivateChat;
use App\Models\User;
use App\Models\File;
use App\Models\Area;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Services\NotificationService;
use App\Actions\Cloud\Files\UploadFileAction;
use App\Actions\Cloud\Files\LinkFileAction;
use App\Actions\Cloud\Folders\GetOrCreateFolderAction;

class Chats extends Component
{
    use WithFileUploads;

    public $users = [];
    public $chats = [];
    public $selectedChat = null;
    public $selectedUser = null;
    public $messages = [];
    public $newMessage = '';

    // File attachments
    public $chatUploads = [];
    public $chatLinkIds = [];

    /**
     * Get the listeners for the component.
     */
    public function getListeners()
    {
        $listeners = [
            'message-received' => 'handleRealtimeMessage',
            'chat-attachments-updated' => 'updateChatAttachments',
            'chat-attachments-committed' => 'broadcastMessage',
        ];

        // Agregar listener específico si hay un chat seleccionado
        if ($this->selectedChat) {
            $chatId = $this->selectedChat->id;
            $listenerName1 = "echo-private:private-chat.{$chatId},PrivateMessageSent";
            $listenerName2 = "echo-private:private-chat.{$chatId},.PrivateMessageSent";

            $listeners[$listenerName1] = 'handleRealtimeMessage';
            $listeners[$listenerName2] = 'handleRealtimeMessage';
        }

        return $listeners;
    }

    public function mount($userId = null)
    {
        // Cargar todos los usuarios excepto el actual
        $this->users = User::where('id', '!=', Auth::id())
            ->orderBy('name')
            ->get()
            ->toArray();

        // Cargar chats del usuario actual
        $this->loadChats();

        // Si se especifica un userId, seleccionar ese usuario
        if ($userId) {
            $this->selectUser($userId);
        }
    }

    public function loadChats()
    {
        $this->chats = PrivateChat::forUser(Auth::id())
            ->with(['participants', 'lastMessage.user'])
            ->where('is_group', false)
            ->get()
            ->map(function ($chat) {
                $otherUser = $chat->getOtherParticipant(Auth::id());
                return [
                    'id' => $chat->id,
                    'other_user' => $otherUser ? [
                        'id' => $otherUser->id,
                        'name' => $otherUser->name,
                        'email' => $otherUser->email,
                    ] : null,
                    'last_message' => $chat->lastMessage ? [
                        'content' => $chat->lastMessage->content,
                        'created_at' => $chat->lastMessage->created_at->format('H:i'),
                    ] : null,
                ];
            })
            ->toArray();
    }

    public function selectUser($userId)
    {
        $user = User::find($userId);

        if (!$user) {
            session()->flash('error', 'Usuario no encontrado.');
            return;
        }

        $this->selectedUser = $user;

        // Obtener o crear chat con este usuario
        $this->selectedChat = Auth::user()->chatWith($userId);

        // Cargar mensajes
        $this->loadMessages();

        // Recargar lista de chats
        $this->loadChats();
    }

    public function loadMessages()
    {
        if (!$this->selectedChat) {
            return;
        }

        $this->messages = $this->selectedChat->messages()
            ->with(['user', 'files'])
            ->orderBy('created_at')
            ->get()
            ->map(function ($msg) {
                return [
                    'id' => $msg->id,
                    'content' => $msg->content,
                    'type' => $msg->type,
                    'created_at' => $msg->created_at->format('Y-m-d H:i:s'),
                    'user_id' => $msg->user_id,
                    'private_chat_id' => $msg->private_chat_id,
                    'user' => [
                        'id' => $msg->user->id,
                        'name' => $msg->user->name,
                        'email' => $msg->user->email,
                    ],
                    'files' => $msg->files->map(function ($f) {
                        return [
                            'id' => $f->id,
                            'name' => $f->name,
                            'url' => $f->url,
                            'readable_size' => $f->readable_size,
                            'mime_type' => $f->mime_type
                        ];
                    })->toArray()
                ];
            })
            ->toArray();

        $this->dispatch('messagesLoaded');
    }

    public function updateChatAttachments($data)
    {
        // Receive safe array data from ChatAttachments component
        $this->chatUploads = $data['uploads'] ?? [];
        $this->chatLinkIds = $data['links'] ?? [];
    }

    public function sendMessage(NotificationService $notificationService)
    {
        try {
            $this->validate([
                'newMessage' => 'required|string|max:1000',
            ]);

            if (!$this->selectedChat) {
                session()->flash('error', 'No hay chat seleccionado.');
                return;
            }

            // Crear mensaje
            $msg = Message::create([
                'user_id' => Auth::id(),
                'private_chat_id' => $this->selectedChat->id,
                'content' => $this->newMessage,
                'type' => 'text',
            ]);

            // Procesar adjuntos (Delegar al componente hijo)
            if (!empty($this->chatUploads) || !empty($this->chatLinkIds)) {
                $this->dispatch('commit-chat-attachments', messageId: $msg->id);
            } else {
                // Si no hay adjuntos, emitir broadcast inmediatamente
                $this->broadcastMessage($msg->id, $notificationService);
            }

            // Guardar datos para la UI antes de limpiar
            $currentUploads = $this->chatUploads;
            $currentLinks = $this->chatLinkIds;

            // Limpiar adjuntos locales (UI)
            $this->chatUploads = [];
            $this->chatLinkIds = [];

            // Reset input
            $this->newMessage = '';

            // Agregar al array local con estado de envío
            $messageData = [
                'id' => $msg->id,
                'content' => $msg->content,
                'type' => $msg->type,
                'created_at' => $msg->created_at->format('Y-m-d H:i:s'),
                'user_id' => Auth::id(),
                'private_chat_id' => $this->selectedChat->id,
                'user' => [
                    'id' => Auth::id(),
                    'name' => Auth::user()->name,
                    'email' => Auth::user()->email,
                ],
                'is_sender' => true,
                'status' => 'sent',
                'files' => collect($currentUploads)->merge($currentLinks)->map(function ($f) {
                    return [
                        'id' => $f['id'] ?? null,
                        'name' => $f['name'],
                        'url' => $f['url'] ?? '#',
                        'readable_size' => isset($f['size']) ? $this->formatBytes($f['size']) : ($f['readable_size'] ?? ''),
                        'mime_type' => $f['mime_type']
                    ];
                })->toArray()
            ];

            $this->messages[] = $messageData;
            $this->dispatch('messageAdded');

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error in sendMessage: ' . $e->getMessage());
            session()->flash('error', 'Ocurrió un error al enviar el mensaje.');
        }
    }

    public function broadcastMessage($messageId, NotificationService $notificationService = null)
    {
        $msg = Message::find($messageId);
        if (!$msg)
            return;

        // Broadcast evento
        try {
            broadcast(new PrivateMessageSent(
                $this->selectedChat->id,
                $msg
            ));

            Log::info('PrivateMessageSent event broadcasted', [
                'chat_id' => $this->selectedChat->id,
                'message_id' => $msg->id
            ]);

            $recipient = $this->selectedChat->getOtherParticipant(Auth::id());

            if ($recipient && $notificationService) {
                $notificationService->createImmediate(
                    $recipient,
                    'Nuevo mensaje en chat',
                    'Nuevo mensaje de ' . Auth::user()->name . ': ' . $msg->content,
                    [
                        'action_url' => route('teams.chats', ['userId' => Auth::id()]),
                        'sender_name' => Auth::user()->name,
                        'message_content' => $msg->content,
                        'image_url' => public_path('images/new_message.jpg'),
                    ],
                    null,
                    true,
                    'emails.new-message'
                );
            }
        } catch (\Exception $e) {
            Log::error('Error broadcasting PrivateMessageSent event: ' . $e->getMessage());
        }

        // Recargar lista de chats para actualizar último mensaje
        $this->loadChats();
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

    public function handleRealtimeMessage(...$params)
    {
        $event = $params[0] ?? $params;

        Log::info('handleRealtimeMessage called in Chats', [
            'params' => $params,
            'event' => $event,
            'current_chat_id' => $this->selectedChat ? $this->selectedChat->id : null,
        ]);

        if (
            $this->selectedChat &&
            isset($event['chat_id']) &&
            $event['chat_id'] == $this->selectedChat->id
        ) {
            if (isset($event['message'])) {
                $messageId = $event['message']['id'] ?? null;
                $userId = $event['message']['user_id'] ?? null;

                // Solo agregar si es de otro usuario y no existe
                if ($userId != Auth::id() && $messageId) {
                    $messageExists = collect($this->messages)->firstWhere('id', $messageId);
                    if (!$messageExists) {
                        $this->messages[] = $event['message'];
                        $this->dispatch('messageAdded');

                        // Recargar lista de chats
                        $this->loadChats();
                    }
                }
            }
        }
    }

    public function render()
    {
        return view('livewire.modules.teams.chats');
    }
}
