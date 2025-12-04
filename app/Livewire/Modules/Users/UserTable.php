<?php

namespace App\Livewire\Modules\Users;

use App\Models\Area;
use App\Models\Permission;
use App\Models\User;
use Livewire\Component;

class UserTable extends Component
{

    public $slug;
    public $users;

    public $confirmingUserDeletion = false;
    public $userToDelete;

    protected $listeners = ['delete' => '$refresh'];


    public function mount()
    {

        $this->users = User::select('users.id', 'users.name', 'users.email', 'users.created_at', 'users.updated_at')
            ->leftJoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->with('roles.permissions.area') // 👈 carga también los permisos de cada rol
            ->get();
    }



    public function render()
    {
        return view('livewire.modules.users.user-table');
    }


    public function confirmUserDeletion($userId)
    {
        $this->confirmingUserDeletion = true;
        $this->userToDelete = $userId;
    }

    public function deleteUser()
    {
        User::findOrFail($this->userToDelete)->delete();
        $this->confirmingUserDeletion = false;

        $this->dispatch('delete');

        session()->flash('message', 'Usuario eliminado correctamente.');
    }
}
