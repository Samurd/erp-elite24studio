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
    public $isMember = false;
    public $currentUserRole = null; // Propiedad para el rol del usuario actual

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
        return [
            'refreshTeam' => '$refresh',
        ];
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
        // Cargar miembros del equipo con sus roles (asegurando únicos por ID de usuario)
        $this->members = $team->members()
            ->get()
            ->unique('id')
            ->map(function ($member) {
                $arr = $member->toArray();
                $arr['role_id'] = $member->pivot->role_id;

                // Obtener nombre del rol
                $role = TeamRole::find($member->pivot->role_id);
                $arr['role_name'] = $role ? $role->name : 'Sin rol';
                $arr['role_slug'] = $role ? $role->slug : null;

                return $arr;
            })
            ->values() // Re-indexar array después del unique
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
    // CHAT LOGIC REFACTORED TO:
    // Teams\Components\ChannelChat.php
    // -----------------------------

    public function openEditChannelModal($channelId)
    {
        $this->dispatch('openEditChannelModal', $channelId);
    }

    public function render()
    {
        return view('livewire.modules.teams.show');
    }

}


