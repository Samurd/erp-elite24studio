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
            ->with(['user', 'files'])
            ->latest();

        if ($beforeId) {
            $query->where('id', '<', $beforeId);
        }

        $messages = $query->take($limit)
            ->get()
            ->reverse()
            ->map(function ($msg) {
                return [
                    'id' => $msg->id,
                    'content' => $msg->content,
                    'user_id' => $msg->user_id,
                    'created_at' => $msg->created_at->format('Y-m-d H:i:s'),
                    'user' => ['name' => $msg->user->name],
                    'files' => $msg->files->map(fn($f) => [
                        'url' => $f->url,
                        'name' => $f->name,
                        'readable_size' => $f->readable_size
                    ])
                ];
            })
            ->values();

        return response()->json($messages);
    }

    public function sendMessage(Request $request, Team $team, TeamChannel $channel, NotificationService $notificationService)
    {
        $this->checkAccess($team, $channel);

        // Auth check etc

        $request->validate(['content' => 'required_without:files']); // simplified

        $msg = Message::create([
            'user_id' => Auth::id(),
            'channel_id' => $channel->id,
            'content' => $request->input('content') ?? '',
            'type' => 'text'
        ]);

        // If attachments... (Handle handled by dedicated Attachment actions usually, or passed here)
        // For migration purpose, assume files might be linked here or via separate action.
        // The implementation plan mentions "commit-chat-attachments". 
        // We'll rely on a standard implementation or the specific logic from `ChannelChat.php`.
        // In `ChannelChat.php` logic:
        // `commit-chat-attachments` is emitted. 
        // Ideally we should use the existing logic or `ModelAttachmentsCreator`.

        // Let's assume frontend sends file IDs to attach if any.

        $msg->load(['user', 'files']);

        // Broadcast
        try {
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
            'message' => [
                'id' => $msg->id,
                'content' => $msg->content,
                'user_id' => $msg->user_id,
                'created_at' => $msg->created_at->format('Y-m-d H:i:s'),
                'user' => ['name' => $msg->user->name],
                'files' => $msg->files->map(fn($f) => [
                    'url' => $f->url,
                    'name' => $f->name,
                    'readable_size' => $f->readable_size
                ])
            ]
        ]);
    }
}
