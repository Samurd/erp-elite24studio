<?php

namespace App\Livewire\Modules\Teams;

use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Update extends Component
{
    public $name = '';
    public $description = '';
    public $isPublic = true;
    public $selectedMembers = [];
    public $team;

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

    public function mount(Team $team)
    {
        $this->team = $team;
        $this->name = $team->name;
        $this->description = $team->description;
        $this->isPublic = $team->isPublic;
        $this->selectedMembers = $team->members->pluck('id')->toArray();
    }

    public function save()
    {
        $this->validate();

        $this->team->update([
            'name' => $this->name,
            'description' => $this->description,
            'isPublic' => $this->isPublic,
        ]);

        // Sincronizar miembros del equipo
        $this->team->members()->sync($this->selectedMembers);

        session()->flash('message', 'Equipo actualizado exitosamente.');
        
        return redirect()->route('teams.index');
    }

    public function render()
    {
        // Excluir al usuario actual de la lista de selección si ya es miembro
        $users = User::where('id', '!=', Auth::user()->id)->orderBy('name')->get();

        return view('livewire.modules.teams.form', [
            'users' => $users,
            'isEditing' => true,
        ]);
    }
}