<?php

namespace App\Livewire\Modules\Teams;

use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Create extends Component
{
    public $name = '';
    public $description = '';
    public $isPublic = true;
    public $selectedMembers = [];

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string|max:1000',
        'isPublic' => 'boolean',
        'selectedMembers' => 'nullable|array',
        'selectedMembers.*' => 'exists:users,id',
    ];

    protected $messages = [
        'name.required' => 'El nombre del equipo es obligatorio.',
        'name.max' => 'El nombre no puede exceder los 255 caracteres.',
        'description.max' => 'La descripción no puede exceder los 1000 caracteres.',
        'selectedMembers.*.exists' => 'Uno de los usuarios seleccionados no es válido.',
    ];

    public function save()
    {
        $this->validate();

        $team = Team::create([
            'name' => $this->name,
            'description' => $this->description,
            'isPublic' => $this->isPublic,
        ]);

        // Obtener los roles de equipo
        $ownerRole = \App\Models\TeamRole::where('slug', 'owner')->first();
        $memberRole = \App\Models\TeamRole::where('slug', 'member')->first();

        // Agregar automáticamente al usuario que crea el equipo como propietario
        $creatorId = Auth::user()->id;
        $team->members()->attach($creatorId, ['role_id' => $ownerRole->id]);

        // Agregar los miembros seleccionados con rol de miembro
        if (!empty($this->selectedMembers)) {
            $membersToAttach = [];
            foreach ($this->selectedMembers as $memberId) {
                // No agregar al creador nuevamente
                if ($memberId !== $creatorId) {
                    $membersToAttach[$memberId] = ['role_id' => $memberRole->id];
                }
            }
            if (!empty($membersToAttach)) {
                $team->members()->attach($membersToAttach);
            }
        }

        session()->flash('message', 'Equipo creado exitosamente.');

        return redirect()->route('teams.index');
    }

    public function render()
    {
        // Excluir al usuario actual de la lista de selección (ya será agregado automáticamente)
        $users = User::where('id', '!=', Auth::user()->id)->orderBy('name')->get();

        return view('livewire.modules.teams.form', [
            'users' => $users,
            'isEditing' => false,
        ]);
    }
}