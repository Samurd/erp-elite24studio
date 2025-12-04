<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;
use App\Models\Team;
use App\Models\TeamChannel;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// Canal para mensajes de equipos y canales específicos
Broadcast::channel('teams.{teamId}.channels.{channelId}', function ($user, $teamId, $channelId) {
    // Log para depuración
    Log::info('Channel authorization attempt', [
        'user_id' => $user->id,
        'team_id' => $teamId,
        'channel_id' => $channelId
    ]);

    // Verificar si el usuario está autenticado
    if (!$user) {
        Log::warning('Unauthenticated user trying to access channel');
        return false;
    }

    // Verificar si el usuario es miembro del equipo
    $team = Team::find($teamId);
    if (!$team) {
        Log::warning('Team not found', ['team_id' => $teamId]);
        return false;
    }

    $isTeamMember = $team->members()->where('user_id', $user->id)->exists();
    if (!$isTeamMember) {
        Log::warning('User is not a team member', ['user_id' => $user->id, 'team_id' => $teamId]);
        return false;
    }

    // Verificar acceso al canal específico
    $channel = TeamChannel::find($channelId);
    if (!$channel || $channel->team_id != $teamId) {
        Log::warning('Channel not found or does not belong to team', ['channel_id' => $channelId, 'team_id' => $teamId]);
        return false;
    }

    // Si el canal es público, todos los miembros del equipo tienen acceso
    if (!$channel->is_private) {
        Log::info('Access granted to public channel', ['channel_id' => $channelId]);
        return true;
    }

    // Si el canal es privado, verificar si el usuario es miembro explícito
    $isChannelMember = $channel->members()->where('user_id', $user->id)->exists();
    if (!$isChannelMember) {
        Log::warning('User is not a channel member', ['user_id' => $user->id, 'channel_id' => $channelId]);
        return false;
    }

    Log::info('Access granted to private channel', ['channel_id' => $channelId]);
    return true;
});

// Canal para chats privados entre usuarios
Broadcast::channel('private-chat.{chatId}', function ($user, $chatId) {
    // Log para depuración
    Log::info('Private chat authorization attempt', [
        'user_id' => $user->id,
        'chat_id' => $chatId
    ]);

    // Verificar si el usuario está autenticado
    if (!$user) {
        Log::warning('Unauthenticated user trying to access private chat');
        return false;
    }

    // Verificar si el chat existe
    $chat = \App\Models\PrivateChat::find($chatId);
    if (!$chat) {
        Log::warning('Private chat not found', ['chat_id' => $chatId]);
        return false;
    }

    // Verificar si el usuario es participante del chat
    $isParticipant = $chat->participants()->where('user_id', $user->id)->exists();
    if (!$isParticipant) {
        Log::warning('User is not a participant of the chat', ['user_id' => $user->id, 'chat_id' => $chatId]);
        return false;
    }

    Log::info('Access granted to private chat', ['chat_id' => $chatId]);
    return true;
});

// Canal para notificaciones privadas de usuarios
Broadcast::channel('notifications.{userId}', function ($user, $userId) {
    // Solo el usuario propietario puede escuchar sus notificaciones
    return (int) $user->id === (int) $userId;
});