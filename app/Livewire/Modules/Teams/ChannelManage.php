<?php

namespace App\Livewire\Modules\Teams;

use App\Models\Team;
use App\Models\TeamChannel;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class ChannelManage extends Component
{
    public $team;
    public $channel;
    public $name;
    public $description;
    public $is_private = 0; // 0 for public, 1 for private
    public $selectedMembers = [];
    public $isEditing = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string|max:1000',
        'is_private' => 'boolean',
        'selectedMembers' => 'nullable|array',
        'selectedMembers.*' => 'exists:users,id',
    ];

    protected $messages = [
        'selectedMembers.*.exists' => 'Uno de los usuarios seleccionados no es válido.',
    ];

    public function mount(Team $team, $channel = null)
    {
        $this->team = $team;

        // Verificar que el usuario es miembro del equipo
        $member = $this->team->members()->where('user_id', Auth::user()->id)->first();
        if (!$member) {
            abort(403, 'No tienes permisos para gestionar canales en este equipo.');
        }

        // Verificar que el usuario sea owner
        $role = \App\Models\TeamRole::find($member->pivot->role_id);
        if (!$role || $role->slug !== 'owner') {
            abort(403, 'Solo los propietarios pueden gestionar canales.');
        }

        if ($channel) {
            // Modo edición
            $this->channel = TeamChannel::findOrFail($channel);

            // Verificar que el canal pertenezca al equipo
            if ($this->channel->team_id !== $this->team->id) {
                abort(404, 'El canal no pertenece a este equipo.');
            }

            $this->name = $this->channel->name;
            $this->description = $this->channel->description;
            $this->is_private = $this->channel->is_private ? 1 : 0;
            $this->isEditing = true;

            // Cargar miembros existentes si es privado
            if ($this->channel->is_private) {
                $this->selectedMembers = $this->channel->members()->pluck('users.id')->toArray();
            }
        }
    }

    public function save()
    {
        // Verificar permisos nuevamente por seguridad
        $member = $this->team->members()->where('user_id', Auth::user()->id)->first();
        $role = $member ? \App\Models\TeamRole::find($member->pivot->role_id) : null;

        if (!$role || $role->slug !== 'owner') {
            abort(403, 'No tienes permisos para realizar esta acción.');
        }

        $this->validate();

        if ($this->isEditing) {
            // Actualizar canal existente
            $this->channel->update([
                'name' => $this->name,
                'description' => $this->description,
                'is_private' => $this->is_private,
            ]);

            // Actualizar miembros si es privado
            if ($this->is_private) {
                // Asegurar que el usuario actual esté incluido
                $membersToSync = array_unique(array_merge($this->selectedMembers, [Auth::id()]));
                $this->channel->members()->sync($membersToSync);
            } else {
                // Si cambió de privado a público, eliminar todos los miembros
                $this->channel->members()->detach();
            }

            session()->flash('message', 'Canal actualizado exitosamente.');
        } else {
            // Crear nuevo canal
            $channel = TeamChannel::create([
                'team_id' => $this->team->id,
                'name' => $this->name,
                'slug' => Str::slug($this->name),
                'description' => $this->description,
                'is_private' => $this->is_private,
            ]);

            // Si es canal privado, agregar miembros seleccionados y al creador
            if ($this->is_private) {
                $membersToAttach = array_unique(array_merge($this->selectedMembers, [Auth::id()]));
                $channel->members()->attach($membersToAttach);
            }

            session()->flash('message', 'Canal creado exitosamente.');
        }

        return redirect()->route('teams.show', $this->team->id);
    }

    public function cancel()
    {
        return redirect()->route('teams.show', $this->team->id);
    }

    public function render()
    {
        // Obtener miembros del equipo excluyendo al usuario actual
        $teamMembers = $this->team->members()
            ->where('users.id', '!=', Auth::id())
            ->orderBy('name')
            ->get();

        return view('livewire.modules.teams.channel-manage', [
            'teamMembers' => $teamMembers,
        ]);
    }
}
