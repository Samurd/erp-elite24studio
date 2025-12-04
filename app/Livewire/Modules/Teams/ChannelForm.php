<?php

namespace App\Livewire\Modules\Teams;

use App\Models\Team;
use App\Models\TeamChannel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class ChannelForm extends Component
{
    public $team;
    public $channel;
    public $name;
    public $description;
    public $is_private = false;
    public $isEditing = false;

    protected $listeners = [
        'openCreateChannelModal' => 'openCreateModal',
        'openEditChannelModal' => 'openEditModal',
    ];

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string|max:1000',
        'is_private' => 'boolean',
    ];

    public function mount(Team $team)
    {
        $this->team = $team;
    }

    public function openCreateModal()
    {
        $this->reset(['name', 'description', 'is_private', 'channel', 'isEditing']);
        $this->isEditing = false;
        $this->dispatch('openChannelModal');
    }

    public function openEditModal($channelId)
    {
        $this->channel = TeamChannel::findOrFail($channelId);
        
        // Verificar que el canal pertenezca al equipo
        if ($this->channel->team_id !== $this->team->id) {
            session()->flash('error', 'El canal no pertenece a este equipo.');
            return;
        }

        $this->name = $this->channel->name;
        $this->description = $this->channel->description;
        $this->is_private = $this->channel->is_private;
        $this->isEditing = true;
        
        $this->dispatch('openChannelModal');
    }

    public function save()
    {
        $this->validate();

        // Verificar que el usuario es miembro del equipo
        if (!$this->team->members()->where('user_id', Auth::user()->id)->exists()) {
            session()->flash('error', 'No tienes permisos para crear canales en este equipo.');
            return;
        }

        if ($this->isEditing) {
            // Actualizar canal existente
            $this->channel->update([
                'name' => $this->name,
                'description' => $this->description,
                'is_private' => $this->is_private,
            ]);

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

            // Si es canal privado, agregar al creador como miembro
            if ($this->is_private) {
                $channel->members()->attach(Auth::user()->id);
            }

            session()->flash('message', 'Canal creado exitosamente.');
        }

        $this->dispatch('closeChannelModal');
        $this->dispatch('refreshTeam');
    }

    public function closeModal()
    {
        $this->dispatch('closeChannelModal');
    }

    public function render()
    {
        return view('livewire.modules.teams.channel-form');
    }
}
