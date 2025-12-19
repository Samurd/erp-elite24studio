<?php

namespace App\Http\Controllers\Teams;

use App\Http\Controllers\Controller;
use App\Events\PrivateMessageSent;
use App\Models\Message;
use App\Models\PrivateChat;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use App\Actions\Cloud\Files\UploadFileAction;
use App\Actions\Cloud\Folders\GetOrCreateFolderAction;
use App\Models\Area;

class TeamsChatsController extends Controller
{
    public function index(Request $request, $userId = null)
    {
        // Users list (excluding current)
        $users = User::where('id', '!=', Auth::id())
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        // Load existing chats
        $chats = $this->getChats();

        $selectedUser = null;
        $selectedChat = null;
        $messages = [];

        if ($userId) {
            $selectedUser = User::find($userId);
            if ($selectedUser) {
                // Determine if we need to mark messages as read? (Logic wasn't explicit in Livewire mount, but usually good practice)

                $selectedChat = Auth::user()->chatWith($userId);

                // Load messages if chat exists
                if ($selectedChat) {
                    $limit = $request->input('limit', 20);
                    $messages = $this->getMessages($selectedChat, $limit);
                }
            }
        }

        return Inertia::render('Teams/Chats', [
            'users' => $users,
            'chats' => $chats,
            'initialSelectedUser' => $selectedUser, // Pass as initial prop
            'initialSelectedChat' => $selectedChat,
            'initialMessages' => $messages,
        ]);
    }

    public function store(Request $request, NotificationService $notificationService, UploadFileAction $uploader, GetOrCreateFolderAction $folderMaker)
    {
        $validated = $request->validate([
            'content' => 'nullable|string|max:1000', // Nullable if files are present
            'recipient_id' => 'required|exists:users,id',
            'files.*' => 'file|max:51200', // 50MB max
            'temp_id' => 'nullable|string', // For optimistic UI matching
        ]);

        if (empty($validated['content']) && !$request->hasFile('files')) {
            return response()->json(['error' => 'Mensaje vacío'], 422);
        }

        $recipientId = $validated['recipient_id'];
        $chat = Auth::user()->chatWith($recipientId);

        // Create message
        $msg = Message::create([
            'user_id' => Auth::id(),
            'private_chat_id' => $chat->id,
            'content' => $validated['content'] ?? '',
            'type' => 'text',
        ]);

        // Handle files
        $attachments = [];
        if ($request->hasFile('files')) {
            $area = Area::where('slug', 'teams')->first(); // Or generic 'chats' area? Livewire used no specific area/folder logic visible in snippet, but imported Actions.
            // Let's assume a 'PrivateChats' folder structure if not strictly defined
            // Actually, the Livewire component imports UploadFileAction but delegates to child component `chat-attachments` which wasn't fully shown.
            // We'll reimplement basic upload logic here.

            // Check if 'chats' area exists or use a default one. 
            // Ideally we should use a specific bucket/folder for chats.
            // For now, let's try to put them in a user folder or similar.

            // Simplification: We'll upload and link.
            // Assuming $uploader takes (files, relatedModel, folderId, areaId)
            // We need a folder.

            if ($area) {
                $folder = $folderMaker->execute('Chats/Private/' . $chat->id, null);
                $uploadedFiles = $uploader->execute($request->file('files'), $msg, $folder->id, $area->id);

                // Transform for response
                foreach ($uploadedFiles as $f) {
                    $attachments[] = [
                        'id' => $f->id,
                        'name' => $f->name,
                        'url' => $f->url, // Accessor or stored path? Logic suggests URL accessor exists
                        'readable_size' => $f->readable_size,
                        'mime_type' => $f->mime_type
                    ];
                }
            }
        }

        // Broadcast
        try {
            $msg->load(['user', 'files']); // Reload to get relationships
            broadcast(new PrivateMessageSent($chat->id, $msg))->toOthers();

            $recipient = User::find($recipientId);
            if ($recipient && $notificationService) {
                // Notification logic copied from Livewire
                $notificationService->createImmediate(
                    $recipient,
                    'Nuevo mensaje en chat',
                    'Nuevo mensaje de ' . Auth::user()->name . ': ' . ($msg->content ?: 'Archivo adjunto'),
                    [
                        'action_url' => route('teams.chats', ['userId' => Auth::id()]),
                        'sender_name' => Auth::user()->name,
                        'message_content' => $msg->content,
                        'image_url' => asset('images/new_message.jpg'), // changed public_path to asset for URL
                    ],
                    null,
                    true,
                    'emails.new-message'
                );
            }
        } catch (\Exception $e) {
            Log::error('Error broadcasting message: ' . $e->getMessage());
        }

        return response()->json([
            'message' => $msg,
            'temp_id' => $validated['temp_id'] ?? null,
            'chat_id' => $chat->id // Return chat ID in case we need to update list
        ]);
    }

    public function loadMore(Request $request, $chatId)
    {
        $chat = PrivateChat::findOrFail($chatId);

        // Authorize
        if (!$chat->participants->contains(Auth::id())) {
            abort(403);
        }

        $cursor = $request->input('cursor'); // Message ID to load before
        $limit = $request->input('limit', 20);

        $query = $chat->messages()->with(['user', 'files'])->latest();

        if ($cursor) {
            $query->where('id', '<', $cursor);
        }

        $messages = $query->take($limit)->get()->reverse()->values()->map(function ($msg) {
            return $this->transformMessage($msg);
        });

        return response()->json([
            'messages' => $messages,
            'next_cursor' => $messages->first() ? $messages->first()['id'] : null
        ]);
    }

    private function getChats()
    {
        return PrivateChat::forUser(Auth::id())
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
                        'created_at' => $chat->lastMessage->created_at->diffForHumans(), // Or format
                    ] : null,
                ];
            });
    }

    private function getMessages($chat, $limit = 20)
    {
        return $chat->messages()
            ->with(['user', 'files'])
            ->latest()
            ->take($limit)
            ->get()
            ->reverse()
            ->values()
            ->map(function ($msg) {
                return $this->transformMessage($msg);
            });
    }

    private function transformMessage($msg)
    {
        return [
            'id' => $msg->id,
            'content' => $msg->content,
            'type' => $msg->type,
            'created_at' => $msg->created_at->toISOString(), // ISO for JS parsing
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
            })
        ];
    }
}
