<?php

namespace App\Http\Controllers\Modules\Teams;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\TeamChannel;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Events\MessageSent;
use App\Services\NotificationService;

use Illuminate\Support\Facades\Log;
use App\Actions\Cloud\Files\UploadFileAction;
use App\Actions\Cloud\Folders\GetOrCreateFolderAction;
use App\Models\Area;

class TeamChannelController extends Controller
{
    // Note: Show channel is handled by TeamController@show with a param, 
    // or we can have a generic redirect/loader here.
    // Ideally, accessing a channel is just "Showing the Team with that Channel active".

    private function checkAccess(Team $team, TeamChannel $channel)
    {
        $userId = Auth::id();

        if ($channel->team_id !== $team->id) {
            abort(404);
        }

        if (!$channel->is_private) {
            // Public channel: must be team member
            if (!$team->members()->where('user_id', $userId)->exists()) {
                abort(403, 'No eres miembro del equipo.');
            }
        } else {
            // Private channel: must be channel member
            if (!$channel->members()->where('user_id', $userId)->exists()) {
                abort(403, 'No tienes acceso a este canal privado.');
            }
        }
    }

    public function store(Request $request, Team $team)
    {
        if (!$team->isOwner(Auth::user()))
            abort(403);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_private' => 'boolean',
            'parent_id' => 'nullable|exists:team_channels,id',
        ]);

        $channel = $team->channels()->create([
            'name' => $request->name,
            'slug' => \Illuminate\Support\Str::slug($request->name),
            'description' => $request->description,
            'is_private' => $request->is_private,
            'parent_id' => $request->parent_id,
        ]);

        // Auto-join creator to channel (both public and private)
        $channel->members()->attach(Auth::id());

        if ($channel->is_private) {
            if ($request->members) {
                $channel->members()->attach($request->members);
            }
        }

        return redirect()->route('teams.show', ['team' => $team->id, 'channel' => $channel->id])
            ->with('message', 'Canal creado.');
    }

    public function update(Request $request, Team $team, TeamChannel $channel)
    {
        if (!$team->isOwner(Auth::user()))
            abort(403);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_private' => 'boolean',
            'parent_id' => 'nullable|exists:team_channels,id',
        ]);

        $channel->update([
            'name' => $request->name,
            'slug' => \Illuminate\Support\Str::slug($request->name),
            'description' => $request->description,
            'is_private' => $request->is_private,
            'parent_id' => $request->parent_id,
        ]);

        // Handle private members update if needed

        return back()->with('message', 'Canal actualizado.');
    }

    public function destroy(Team $team, TeamChannel $channel)
    {
        if (!$team->isOwner(Auth::user()))
            abort(403);
        $channel->delete();
        return redirect()->route('teams.show', $team->id)->with('message', 'Canal eliminado.');
    }

    public function join(Team $team, TeamChannel $channel)
    {
        // Public channel logic
        if ($channel->is_private)
            abort(403);

        if (!$channel->members()->where('user_id', Auth::id())->exists()) {
            $channel->members()->attach(Auth::id());
        }

        return redirect()->route('teams.show', ['team' => $team->id, 'channel' => $channel->id]);
    }

    public function leave(Team $team, TeamChannel $channel)
    {
        $channel->members()->detach(Auth::id());
        return redirect()->route('teams.show', $team->id);
    }

    // --- Chat API ---

    public function messages(Team $team, TeamChannel $channel, Request $request)
    {
        $this->checkAccess($team, $channel);

        $limit = $request->input('limit', 20);
        $beforeId = $request->input('before_id');

        $query = $channel->messages()
            ->with([
                'user:id,name,email,profile_photo_path',
                'files',
                'replies.user:id,name,email,profile_photo_path',
                'replies.files',
                'reactions',
                'replies.reactions'
            ])
            ->whereNull('parent_id')
            ->latest();

        if ($beforeId) {
            $query->where('id', '<', $beforeId);
        }

        $messages = $query->take($limit)
            ->get()
            ->reverse()
            ->map(function ($msg) {
                return $this->formatMessage($msg);
            })
            ->values();

        return response()->json($messages);
    }

    public function sendMessage(Request $request, Team $team, TeamChannel $channel, NotificationService $notificationService, UploadFileAction $uploader, GetOrCreateFolderAction $folderMaker)
    {
        $this->checkAccess($team, $channel);

        // Auth check etc

        $request->validate([
            'content' => 'nullable|string', // Nullable if files exist
            'parent_id' => 'nullable|exists:messages,id',
            'files.*' => 'file|max:51200'
        ]);

        if (!$request->input('content') && !$request->hasFile('files')) {
            return response()->json(['error' => 'Mensaje vacío'], 422);
        }

        $msg = Message::create([
            'user_id' => Auth::id(),
            'channel_id' => $channel->id,
            'content' => $request->input('content') ?? '',
            'type' => 'text',
            'parent_id' => $request->input('parent_id')
        ]);

        // Handle files
        if ($request->hasFile('files')) {
            $area = Area::where('slug', 'teams')->first();
            // Fallback or handle error if area not found? assuming exists as per Private Chat logic

            if ($area) {
                // Store in Chats/Channels/{id}
                $folderPath = 'Chats/Channels/' . $channel->id;
                $folder = $folderMaker->execute($folderPath, null);

                $uploader->execute($request->file('files'), $msg, $folder->id, $area->id);
            }
        }

        $msg->load(['user', 'files']);

        // Broadcast
        try {
            $msg->load(['replies.user', 'replies.files']); // Load replies too if it was a reply? No, a new message/reply won't have deep replies yet.
            // But we need to eager load what formatMessage needs.

            broadcast(new MessageSent($team->id, $channel->id, $msg))->toOthers();

            // Get recipients based on channel type
            if ($channel->is_private) {
                // Private channel: notify all channel members except sender
                $recipients = $channel->members()
                    ->where('users.id', '!=', Auth::id())
                    ->get();
            } else {
                // Public channel: notify all team members except sender
                $recipients = $team->members()
                    ->where('users.id', '!=', Auth::id())
                    ->get();
            }

            // Send notifications to each recipient
            foreach ($recipients as $user) {

                if ($user && $user->id) {
                    try {
                        $notificationService->createImmediate(
                            $user,
                            'Nuevo mensaje en #' . $channel->name,
                            Auth::user()->name . ': ' . ($msg->content ?: 'Archivo adjunto'),
                            [
                                'action_url' => route('teams.show', ['team' => $team->id, 'channel' => $channel->id]),
                                'sender_name' => Auth::user()->name,
                                'message_content' => $msg->content,
                                'team_name' => $team->name,
                                'channel_name' => $channel->name,
                                'image_url' => asset('images/new_message.jpg'),
                            ],
                            null,
                            true,
                            'emails.new-message'
                        );
                    } catch (\Exception $e) {
                        Log::error('Error sending channel message notification to user ' . $user->id . ': ' . $e->getMessage());
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Error broadcasting channel message: ' . $e->getMessage());
        }
        return response()->json([
            'status' => 'ok',
            'message' => $this->formatMessage($msg)
        ]);
    }
    public function toggleReaction(Request $request, Team $team, TeamChannel $channel, Message $message)
    {
        $this->checkAccess($team, $channel);

        // Ensure message belongs to channel
        if ($message->channel_id !== $channel->id && $message->parent && $message->parent->channel_id !== $channel->id) {
            // Handle replies too or strict check?
            // Assuming message ID is unique global, but security wise good to check.
            // If reply, it has parent.
        }

        $request->validate(['emoji' => 'required|string']);
        $emoji = $request->input('emoji');
        $userId = Auth::id();

        $existing = \App\Models\MessageReaction::where('message_id', $message->id)
            ->where('user_id', $userId)
            ->first();

        if ($existing) {
            if ($existing->emoji === $emoji) {
                // Remove if same
                $existing->delete();
            } else {
                // Update if different
                $existing->update(['emoji' => $emoji]);
            }
        } else {
            \App\Models\MessageReaction::create([
                'message_id' => $message->id,
                'user_id' => $userId,
                'emoji' => $emoji
            ]);
        }

        // Broadcast update
        $message->load('reactions'); // Reload reactions

        // Group reactions for frontend
        $reactions = $message->reactions->groupBy('emoji')->map(function ($group) {
            return [
                'emoji' => $group->first()->emoji,
                'count' => $group->count(),
                'users' => $group->pluck('user_id') // Optional: list of user IDs if needed for hover
            ];
        })->values();

        broadcast(new \App\Events\MessageReactionUpdated($team->id, $channel->id, $message->id, $reactions))->toOthers();

        return response()->json(['status' => 'ok', 'reactions' => $reactions]);
    }

    private function formatMessage($msg)
    {
        $formatReactions = function ($msg) {
            return $msg->reactions->groupBy('emoji')->map(function ($group) {
                return [
                    'emoji' => $group->first()->emoji,
                    'count' => $group->count(),
                    'user_reacted' => $group->contains('user_id', Auth::id())
                ];
            })->values();
        };

        return [
            'id' => $msg->id,
            'content' => $msg->content,
            'user_id' => $msg->user_id,
            'created_at' => $msg->created_at->format('Y-m-d H:i:s'),
            'user' => [
                'name' => $msg->user->name,
                'profile_photo_url' => $msg->user->profile_photo_url
            ],
            'files' => $msg->files->map(fn($f) => [
                'url' => route('cloud.file.download', ['file' => $f->id]),
                'name' => $f->name,
                'readable_size' => $f->readable_size
            ]),
            'reactions' => $formatReactions($msg),
            'replies' => $msg->replies->map(fn($r) => [
                'id' => $r->id,
                'content' => $r->content,
                'user_id' => $r->user_id,
                'created_at' => $r->created_at->format('Y-m-d H:i:s'),
                'user' => [
                    'name' => $r->user->name,
                    'profile_photo_url' => $r->user->profile_photo_url
                ],
                'files' => $r->files->map(fn($f) => [
                    'url' => route('cloud.file.download', ['file' => $f->id]),
                    'name' => $f->name,
                    'readable_size' => $f->readable_size
                ]),
                'reactions' => $formatReactions($r)
            ])
        ];
    }
}
