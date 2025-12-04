<?php

namespace App\Livewire\Modules\Teams;

use App\Events\MessageSent;
use App\Models\Message;
use App\Models\Team;
use App\Models\TeamChannel;
use App\Models\TeamRole;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Actions\Cloud\Files\UploadFileAction;
use App\Actions\Cloud\Files\LinkFileAction;
use App\Actions\Cloud\Folders\GetOrCreateFolderAction;
use App\Models\Area;
use App\Models\File;

class Show extends Component
{
    use WithFileUploads;

    public $team;
    public $channel;
    public $channels = [];
    public $members = [];
    public $messages = [];
    public $isMember = false;
    public $currentUserRole = null; // Propiedad para el rol del usuario actual
    public $newMessage = '';

    // Adjuntos de chat
    public $chatUploads = [];
    public $chatLinkIds = [];

    // Propiedades para tab de miembros
    public $selectedUserId = '';
    public $availableUsers = [];
    public $teamRoles = [];      // Lista de roles disponibles

    // Propiedades para tab de configuración
    public $teamName = '';
    public $teamDescription = '';
    public $teamIsPublic = false;

    // ... (rest of the code)

    public $isPrivateTeamNonMember = false; // Nueva propiedad para controlar acceso a equipos privados

    /**
     * Get the listeners for the component.
     */
    public function getListeners()
    {
        $listeners = [
            'message-received' => 'handleRealtimeMessage',
            'chat-attachments-updated' => 'updateChatAttachments',
            'chat-attachments-committed' => 'broadcastMessage', // Nuevo listener
        ];

        // Agregar listener específico si hay un canal seleccionado
        if ($this->channel) {
            $teamId = is_object($this->team) ? $this->team->id : $this->team;
            $channelId = is_object($this->channel) ? $this->channel->id : $this->channel['id'] ?? null;

            if ($teamId && $channelId) {
                $listenerName1 = "echo-private:teams.{$teamId}.channels.{$channelId},MessageSent";
                $listenerName2 = "echo-private:teams.{$teamId}.channels.{$channelId},.MessageSent";

                $listeners[$listenerName1] = 'handleRealtimeMessage';
                $listeners[$listenerName2] = 'handleRealtimeMessage';
            }
        }

        return $listeners;
    }

    public function mount(Team $team, $channel = null)
    {
        $this->team = $team;

        // Verificar si el usuario actual pertenece al equipo y obtener su rol
        $member = $team->members()
            ->where('user_id', Auth::id())
            ->first();

        if ($member) {
            $this->isMember = true;
            $roleId = $member->pivot->role_id;
            $role = TeamRole::find($roleId);
            $this->currentUserRole = $role ? $role->toArray() : null;
        } else {
            $this->isMember = false;
            $this->currentUserRole = null;

            // Si el equipo es privado y el usuario no es miembro, bloquear acceso
            if (!$team->isPublic) {
                $this->isPrivateTeamNonMember = true;
                return; // No cargar nada más
            }
        }

        // Cargar canal si viene en la ruta
        if ($channel) {
            $this->channel = is_string($channel)
                ? TeamChannel::findOrFail($channel)
                : $channel;

            // Validar que el canal pertenezca al equipo
            if ($this->channel->team_id !== $team->id) {
                abort(404, 'El canal no pertenece a este equipo.');
            }

            // Validar acceso a canales privados
            if (
                $this->channel->is_private &&
                !$this->channel->members()->where('user_id', Auth::id())->exists()
            ) {
                // Si es privado y no es miembro, mostramos mensaje en la vista pero no abortamos
                // para permitir mostrar la UI de "Acceso Restringido"
            }
        }

        // Cargar miembros del equipo con sus roles
        $this->members = $team->members()
            ->get()
            ->map(function ($member) {
                $arr = $member->toArray();
                $arr['role_id'] = $member->pivot->role_id;

                // Obtener nombre del rol
                $role = TeamRole::find($member->pivot->role_id);
                $arr['role_name'] = $role ? $role->name : 'Sin rol';
                $arr['role_slug'] = $role ? $role->slug : null;

                return $arr;
            })
            ->toArray();

        // Cargar canales con conteos de mensajes
        $this->channels = $team->channels()
            ->withCount('messages')
            ->get()
            ->map(function ($channel) {
                $arr = $channel->toArray();

                // Verificar si el usuario actual es miembro del canal
                $isChannelMember = false;
                if ($this->isMember) {
                    // Para canales públicos, todos los miembros del equipo tienen acceso
                    if (!$channel->is_private) {
                        $isChannelMember = true;
                        $arr['members'] = $this->members;
                        $arr['members_count'] = count($this->members);
                    } else {
                        // Para canales privados, verificar si está en la tabla pivot
                        $isChannelMember = $channel->members()->where('user_id', Auth::id())->exists();
                        $members = $channel->members()->get()->toArray();
                        $arr['members'] = $members;
                        $arr['members_count'] = count($members);
                    }
                } else {
                    // No es miembro del equipo
                    if (!$channel->is_private) {
                        // Canal público pero no es miembro del equipo
                        $arr['members'] = [];
                        $arr['members_count'] = 0;
                    } else {
                        // Canal privado y no es miembro del equipo
                        $members = $channel->members()->get()->toArray();
                        $arr['members'] = $members;
                        $arr['members_count'] = count($members);
                    }
                }

                $arr['is_channel_member'] = $isChannelMember;

                return $arr;
            })
            ->toArray();

        // Cargar mensajes si hay canal abierto
        if ($this->channel) {
            $this->loadMessages();
        }

        // Inicializar propiedades de configuración
        $this->teamName = $team->name;
        $this->teamDescription = $team->description ?? '';
        $this->teamIsPublic = $team->isPublic ?? false;

        // Cargar roles disponibles
        $this->teamRoles = TeamRole::all()->toArray();

        // Cargar usuarios disponibles (usuarios que no están en el equipo)
        $this->loadAvailableUsers();
    }

    /**
     * Unirse a un equipo público
     */
    public function joinTeam()
    {
        if ($this->isMember) {
            return;
        }

        if (!$this->team->isPublic) {
            session()->flash('error', 'No puedes unirte a un equipo privado sin invitación.');
            return;
        }

        // Obtener el rol de "member"
        $memberRole = TeamRole::where('slug', 'member')->first();

        if (!$memberRole) {
            session()->flash('error', 'Error de configuración: Rol de miembro no encontrado.');
            return;
        }

        // Agregar al usuario como miembro
        $this->team->members()->attach(Auth::id(), [
            'role_id' => $memberRole->id
        ]);

        session()->flash('message', 'Te has unido al equipo exitosamente.');

        // Recargar la página para actualizar estado
        return redirect()->route('teams.show', $this->team->id);
    }

    /**
     * Salir del equipo
     */
    public function leaveTeam()
    {
        if (!$this->isMember) {
            session()->flash('error', 'No eres miembro de este equipo.');
            return;
        }

        // Verificar si el usuario es el último owner
        if (($this->currentUserRole['slug'] ?? '') === 'owner') {
            $ownerRoleId = TeamRole::where('slug', 'owner')->first()->id;
            $ownersCount = $this->team->members()
                ->wherePivot('role_id', $ownerRoleId)
                ->count();

            if ($ownersCount <= 1) {
                session()->flash('error', 'No puedes salir del equipo si eres el único owner. Primero asigna otro owner o elimina el equipo.');
                return;
            }
        }

        // Eliminar al usuario del equipo
        $this->team->members()->detach(Auth::id());

        session()->flash('message', 'Has salido del equipo exitosamente.');
        return redirect()->route('teams.index');
    }


    /**
     * Cargar usuarios que no están en el equipo
     */
    public function loadAvailableUsers()
    {
        $memberIds = collect($this->members)->pluck('id')->toArray();
        $this->availableUsers = \App\Models\User::whereNotIn('id', $memberIds)
            ->orderBy('name')
            ->get();
    }

    // -----------------------------
    // TEAM MEMBERS MANAGEMENT
    // -----------------------------

    /**
     * Agregar un miembro al equipo
     */
    public function addMember()
    {
        $this->validate([
            'selectedUserId' => 'required|exists:users,id'
        ]);

        // Verificar que el usuario no sea ya miembro
        $isMemberAlready = $this->team->members()
            ->where('user_id', $this->selectedUserId)
            ->exists();

        if ($isMemberAlready) {
            session()->flash('error', 'Este usuario ya es miembro del equipo.');
            return;
        }

        // Obtener el rol de "member"
        $memberRole = TeamRole::where('slug', 'member')->first();

        if (!$memberRole) {
            session()->flash('error', 'No se pudo encontrar el rol de miembro.');
            return;
        }

        // Agregar miembro con rol de "member"
        $this->team->members()->attach($this->selectedUserId, [
            'role_id' => $memberRole->id
        ]);

        // Recargar miembros
        $this->members = $this->team->members()
            ->get()
            ->map(function ($member) {
                $arr = $member->toArray();
                $arr['role_id'] = $member->pivot->role_id;
                $role = TeamRole::find($member->pivot->role_id);
                $arr['role_name'] = $role ? $role->name : 'Sin rol';
                $arr['role_slug'] = $role ? $role->slug : null;
                return $arr;
            })
            ->toArray();

        // Recargar usuarios disponibles
        $this->loadAvailableUsers();

        // Resetear selección
        $this->selectedUserId = '';

        session()->flash('message', 'Miembro agregado exitosamente.');
    }

    /**
     * Eliminar un miembro del equipo
     */
    public function removeMember($userId)
    {
        // Verificar permisos usando policy
        if (!$this->team->isOwner(Auth::user())) {
            session()->flash('error', 'Solo los propietarios pueden eliminar miembros.');
            return;
        }

        // Verificar si el miembro a eliminar es un owner
        $memberToRemove = $this->team->members()->where('user_id', $userId)->first();
        if ($memberToRemove) {
            $role = TeamRole::find($memberToRemove->pivot->role_id);
            if ($role && $role->slug === 'owner') {
                // Contar cuántos owners hay
                $ownerRoleId = TeamRole::where('slug', 'owner')->first()->id;
                $ownersCount = $this->team->members()
                    ->wherePivot('role_id', $ownerRoleId)
                    ->count();

                if ($ownersCount <= 1) {
                    session()->flash('error', 'No puedes eliminar al único propietario del equipo.');
                    return;
                }
            }
        }

        // Eliminar miembro
        $this->team->members()->detach($userId);

        // Recargar miembros con roles
        $this->members = $this->team->members()
            ->get()
            ->map(function ($member) {
                $arr = $member->toArray();
                $arr['role_id'] = $member->pivot->role_id;
                $role = TeamRole::find($member->pivot->role_id);
                $arr['role_name'] = $role ? $role->name : 'Sin rol';
                $arr['role_slug'] = $role ? $role->slug : null;
                return $arr;
            })
            ->toArray();

        // Recargar usuarios disponibles
        $this->loadAvailableUsers();

        session()->flash('message', 'Miembro eliminado del equipo.');
    }

    /**
     * Cambiar el rol de un miembro
     */
    public function changeRole($userId, $newRoleId)
    {
        // Verificar permisos usando policy
        if (!$this->team->isOwner(Auth::user())) {
            session()->flash('error', 'Solo los propietarios pueden cambiar roles.');
            return;
        }

        $this->validate([
            'newRoleId' => 'required|exists:team_roles,id'
        ], [
            'newRoleId' => $newRoleId
        ]);

        // Obtener el miembro
        $member = $this->team->members()->where('user_id', $userId)->first();
        if (!$member) {
            session()->flash('error', 'El usuario no es miembro de este equipo.');
            return;
        }

        $currentRoleId = $member->pivot->role_id;
        $currentRole = TeamRole::find($currentRoleId);
        $newRole = TeamRole::find($newRoleId);

        // Si el rol actual es owner, verificar que no sea el último
        if ($currentRole && $currentRole->slug === 'owner') {
            $ownerRoleId = TeamRole::where('slug', 'owner')->first()->id;
            $ownersCount = $this->team->members()
                ->wherePivot('role_id', $ownerRoleId)
                ->count();

            if ($ownersCount <= 1) {
                session()->flash('error', 'No puedes cambiar el rol del único propietario del equipo.');
                return;
            }
        }

        // Actualizar el rol en la tabla pivot
        $this->team->members()->updateExistingPivot($userId, [
            'role_id' => $newRoleId
        ]);

        // Recargar miembros con roles
        $this->members = $this->team->members()
            ->get()
            ->map(function ($member) {
                $arr = $member->toArray();
                $arr['role_id'] = $member->pivot->role_id;
                $role = TeamRole::find($member->pivot->role_id);
                $arr['role_name'] = $role ? $role->name : 'Sin rol';
                $arr['role_slug'] = $role ? $role->slug : null;
                return $arr;
            })
            ->toArray();

        session()->flash('message', "Rol cambiado a {$newRole->name} exitosamente.");
    }

    // -----------------------------
    // TEAM CONFIGURATION
    // -----------------------------

    /**
     * Actualizar configuración del equipo
     */
    public function updateTeam()
    {
        $this->validate([
            'teamName' => 'required|string|max:255',
            'teamDescription' => 'nullable|string|max:1000',
            'teamIsPublic' => 'boolean'
        ]);

        // Verificar que el usuario sea miembro y owner
        if (!$this->isMember || ($this->currentUserRole['slug'] ?? '') !== 'owner') {
            session()->flash('error', 'Solo los owners pueden actualizar la configuración del equipo.');
            return;
        }

        $this->team->update([
            'name' => $this->teamName,
            'description' => $this->teamDescription,
            'isPublic' => $this->teamIsPublic
        ]);

        session()->flash('message', 'Equipo actualizado exitosamente.');
    }

    /**
     * Eliminar el equipo
     */
    public function deleteTeam()
    {
        // Verificar que el usuario sea miembro y owner
        if (!$this->isMember || ($this->currentUserRole['slug'] ?? '') !== 'owner') {
            session()->flash('error', 'Solo los owners pueden eliminar este equipo.');
            return;
        }

        $teamName = $this->team->name;
        $this->team->delete();

        session()->flash('message', "El equipo '$teamName' ha sido eliminado.");

        return redirect()->route('teams.index');
    }

    // -----------------------------
    // JOIN / LEAVE CHANNEL
    // -----------------------------

    public function joinChannel($channelId)
    {
        $channel = TeamChannel::find($channelId);

        if (!$channel) {
            session()->flash('error', 'Canal no encontrado.');
            return;
        }

        // Verificar que el usuario sea miembro del equipo
        if (!$this->isMember) {
            session()->flash('error', 'Debes ser miembro del equipo para unirte a canales.');
            return;
        }

        if ($channel->is_private) {
            session()->flash('error', 'No puedes unirte a un canal privado. Debes ser invitado por un administrador.');
            return;
        }

        // Para canales públicos, verificar si ya está en la tabla pivot
        if ($channel->members()->where('user_id', Auth::id())->exists()) {
            session()->flash('error', 'Ya eres miembro de este canal.');
            return;
        }

        $channel->members()->attach(Auth::id());

        session()->flash('message', 'Te uniste al canal exitosamente.');

        // Recargar canales para actualizar el estado
        $this->mount($this->team, $this->channel);
        $this->dispatch('refreshTeam');
    }

    public function leaveChannel($channelId)
    {
        $channel = TeamChannel::find($channelId);

        if (!$channel) {
            session()->flash('error', 'Canal no encontrado.');
            return;
        }

        // Para canales públicos, verificar si está en la tabla pivot
        // Para canales privados, también verificar en la tabla pivot
        if (!$channel->members()->where('user_id', Auth::id())->exists()) {
            session()->flash('error', 'No eres miembro de este canal.');
            return;
        }

        // Verificar si el usuario es el único miembro del canal privado
        if ($channel->is_private) {
            $membersCount = $channel->members()->count();
            if ($membersCount <= 1) {
                session()->flash('error', 'No puedes salir de este canal privado porque eres el único miembro. Primero invita a otros miembros o elimina el canal.');
                return;
            }
        }

        $channel->members()->detach(Auth::id());

        session()->flash('message', 'Has salido del canal.');

        // Recargar para actualizar el estado
        $this->mount($this->team, $this->channel);
        $this->dispatch('refreshTeam');
    }

    /**
     * Eliminar un canal
     */
    public function deleteChannel($channelId)
    {
        $channel = TeamChannel::find($channelId);

        if (!$channel) {
            session()->flash('error', 'Canal no encontrado.');
            return;
        }

        // Verificar que el usuario sea miembro y owner del equipo
        if (!$this->isMember || ($this->currentUserRole['slug'] ?? '') !== 'owner') {
            session()->flash('error', 'Solo los owners del equipo pueden eliminar canales.');
            return;
        }

        // Verificar que el canal pertenezca al equipo
        if ($channel->team_id !== $this->team->id) {
            session()->flash('error', 'Este canal no pertenece a este equipo.');
            return;
        }

        $channelName = $channel->name;
        $channel->delete();

        session()->flash('message', "El canal '$channelName' ha sido eliminado.");

        // Redirigir a la lista de canales
        return redirect()->route('teams.channels.index', $this->team->id);
    }

    // -----------------------------
    // LOAD MESSAGES
    // -----------------------------

    public function loadMessages()
    {
        if (!$this->channel)
            return;

        // Verificar si el usuario tiene acceso al canal
        $hasAccess = false;

        if ($this->isMember) {
            if (!$this->channel->is_private) {
                // Canal público: todos los miembros del equipo tienen acceso
                $hasAccess = true;
            } else {
                // Canal privado: verificar si está en la tabla pivot
                $hasAccess = $this->channel->members()->where('user_id', Auth::id())->exists();
            }
        }

        if (!$hasAccess) {
            $this->messages = []; // No cargar mensajes si no tiene acceso
            return;
        }

        $this->messages = $this->channel->messages()
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
                    'channel_id' => $msg->channel_id,
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

    // -----------------------------
    // SEND MESSAGE
    // -----------------------------

    public function updateChatAttachments($data)
    {
        // Ahora recibimos arrays seguros para la UI, no objetos
        $this->chatUploads = $data['uploads'] ?? [];
        $this->chatLinkIds = $data['links'] ?? []; // IDs y metadata de links
    }

    public function sendMessage(\App\Services\NotificationService $notificationService)
    {
        try {
            $this->validate([
                'newMessage' => 'required|string|max:1000',
            ]);

            if (!$this->channel || !$this->isMember) {
                session()->flash('error', 'No tienes permisos para enviar mensajes en este canal.');
                return;
            }

            // Validar acceso al canal
            $hasAccess = false;
            if (!$this->channel->is_private) {
                // Canal público: todos los miembros del equipo tienen acceso
                $hasAccess = true;
            } else {
                // Canal privado: verificar si está en la tabla pivot
                $hasAccess = $this->channel->members()->where('user_id', Auth::id())->exists();
            }

            if (!$hasAccess) {
                if ($this->channel->is_private) {
                    session()->flash('error', 'Este es un canal privado. Debes ser invitado por un administrador para enviar mensajes.');
                } else {
                    session()->flash('error', 'Debes unirte a este canal público para enviar mensajes.');
                }
                return;
            }

            // Crear mensaje
            $msg = Message::create([
                'user_id' => Auth::id(),
                'channel_id' => $this->channel->id,
                'content' => $this->newMessage,
                'type' => 'text',
            ]);

            // Procesar adjuntos (Delegar al componente hijo)
            if (!empty($this->chatUploads) || !empty($this->chatLinkIds)) {
                $this->dispatch('commit-chat-attachments', messageId: $msg->id);
            } else {
                // Si no hay adjuntos, emitir broadcast inmediatamente
                $this->broadcastMessage($msg->id);
            }

            // Guardar datos para la UI antes de limpiar
            $currentUploads = $this->chatUploads;
            $currentLinks = $this->chatLinkIds;

            // Limpiar adjuntos locales (UI)
            $this->chatUploads = [];
            $this->chatLinkIds = [];

            // Reset input
            $this->newMessage = '';

            // Agregar al array con estado de envío
            $messageData = [
                'id' => $msg->id,
                'content' => $msg->content,
                'type' => $msg->type,
                'created_at' => $msg->created_at->format('Y-m-d H:i:s'),
                'user_id' => Auth::id(),
                'channel_id' => $this->channel->id,
                'user' => [
                    'id' => Auth::id(),
                    'name' => Auth::user()->name,
                    'email' => Auth::user()->email,
                ],
                'is_sender' => true, // Marcar como enviado por el usuario actual
                'status' => 'sent', // Estado inicial
                'files' => collect($currentUploads)->merge($currentLinks)->map(function ($f) {
                    // Mapear datos de UI (arrays) a formato de mensaje
                    return [
                        'id' => $f['id'] ?? null, // Uploads nuevos no tienen ID aún
                        'name' => $f['name'],
                        'url' => $f['url'] ?? '#', // Uploads nuevos no tienen URL pública aún
                        'readable_size' => isset($f['size']) ? $this->formatBytes($f['size']) : ($f['readable_size'] ?? ''),
                        'mime_type' => $f['mime_type']
                    ];
                })->toArray()
            ];

            $this->messages[] = $messageData;
            $this->dispatch('messageAdded');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Dejar que Livewire maneje la excepción de validación
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error in sendMessage: ' . $e->getMessage());
            session()->flash('error', 'Ocurrió un error al enviar el mensaje.');
        }
    }

    public function broadcastMessage($messageId)
    {
        $msg = Message::find($messageId);
        if (!$msg)
            return;

        // Emitir evento realtime (Reverb)
        try {
            broadcast(new MessageSent(
                $this->team->id,
                $this->channel->id,
                $msg
            ));

            // Para depuración
            Log::info('MessageSent event broadcasted', [
                'team_id' => $this->team->id,
                'channel_id' => $this->channel->id,
                'message_id' => $msg->id
            ]);

            // Notificar a los miembros
            $notificationService = app(\App\Services\NotificationService::class);
            $membersToNotify = collect();

            if ($this->channel->is_private) {
                // Canales privados: solo miembros del canal
                $membersToNotify = $this->channel->members()
                    ->where('user_id', '!=', Auth::id())
                    ->get();
            } else {
                // Canales públicos: todos los miembros del equipo
                $membersToNotify = $this->team->members()
                    ->where('user_id', '!=', Auth::id())
                    ->get();
            }

            foreach ($membersToNotify as $member) {
                $notificationService->createImmediate(
                    $member,
                    'Nuevo mensaje en canal ' . $this->channel->name,
                    'Nuevo mensaje de ' . Auth::user()->name . ': ' . $msg->content,
                    [
                        'action_url' => route('teams.channels.show', ['team' => $this->team->id, 'channel' => $this->channel->id]),
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
            Log::error('Error broadcasting MessageSent event: ' . $e->getMessage());
            // No interrumpir el flujo si falla el broadcast
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

    // -----------------------------
    // HANDLE REALTIME MESSAGE
    // -----------------------------

    public function handleRealtimeMessage(...$params)
    {
        // El evento puede venir como primer parámetro o como array
        $event = $params[0] ?? $params;

        // Para depuración
        Log::info('handleRealtimeMessage called', [
            'params' => $params,
            'event' => $event,
            'current_channel_id' => $this->channel ? $this->channel->id : null,
            'event_type' => gettype($event)
        ]);

        // Asegurar que el mensaje sea del canal actual
        if (
            $this->channel &&
            isset($event['channel_id']) &&
            $event['channel_id'] == $this->channel->id
        ) {
            // Verificar si el mensaje trae datos válidos
            if (isset($event['message'])) {
                $messageId = $event['message']['id'] ?? null;
                $userId = $event['message']['user_id'] ?? null;

                // Buscar si el mensaje ya existe en la lista local
                $existingIndex = collect($this->messages)->search(function ($msg) use ($messageId) {
                    return isset($msg['id']) && $msg['id'] == $messageId;
                });

                if ($existingIndex !== false) {
                    // El mensaje YA EXISTE
                    if ($userId == Auth::id()) {
                        // Si es nuestro, actualizamos estado a delivered
                        $this->messages[$existingIndex]['status'] = 'delivered';
                        // Si el evento trae archivos (porque ya se procesaron), actualizamos los archivos también
                        if (!empty($event['message']['files'])) {
                            $this->messages[$existingIndex]['files'] = $event['message']['files'];
                        }
                    }
                    $this->dispatch('messageAdded');
                } else {
                    // Es nuestro mensaje pero NO estaba en la lista local?
                    // Esto puede pasar si se recargó la página o algo raro. Lo agregamos.
                    // OJO: Si acabamos de enviar, debería estar.
                    // Si llega aquí es porque el ID del evento no coincide con el ID temporal (si usáramos IDs temporales)
                    // Pero aquí usamos IDs reales de BD.
                    // Asumimos que si no está, lo agregamos.
                    $this->messages[] = $event['message'];
                    $this->dispatch('messageAdded');
                }
            } else {
                // Fallback: recargar todos los mensajes
                Log::info('No message found in event, reloading messages');
                $this->loadMessages();
            }

            // Disparar evento para actualizar el scroll
            $this->dispatch('messageAdded');
            Log::info('messageAdded dispatched');
        } else {
            Log::warning('Message not for current channel', [
                'event_channel_id' => $event['channel_id'] ?? 'not_set',
                'current_channel_id' => $this->channel ? $this->channel->id : null
            ]);
        }
    }

    public function openEditChannelModal($channelId)
    {
        $this->dispatch('openEditChannelModal', $channelId);
    }

    public function render()
    {
        return view('livewire.modules.teams.show');
    }

}


