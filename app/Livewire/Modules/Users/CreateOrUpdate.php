<?php

namespace App\Livewire\Modules\Users;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class CreateOrUpdate extends Component
{
    public $name;
    public $email;
    public $role;
    public $password;

    // Props para edición
    public $userId = null;
    public $isEdit = false;



    // Create Role modal
    public $openCreateRoleModal = false;
    public $roleName;
    public array $selectedPermissions = [];
    public $newPermissionName;
    public $permissions = [];
    public $selectedArea;


    // Props to edit role

    public $isEditRole = false;
    public $roleId = null;


    function getRules()
    {
        if ($this->isEdit) {
            return [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $this->userId,
                'role' => 'required|integer',
                'password' => 'nullable|min:6',
            ];
        }

        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|integer', // ahora espera un ID
            'password' => 'required|min:6',
        ];
    }

    public function mount($user = null)
    {
        $this->permissions = Permission::with('area')->get();

        if ($user) {
            $user = User::findOrFail($user);
            $this->isEdit   = true;
            $this->userId   = $user->id;
            $this->name     = $user->name;
            $this->email    = $user->email;
            $this->role     = optional($user->roles->first())->id;
            $this->password = null; // opcional en edición
        }
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName, $this->getRules());
    }

    public function save()
    {
        $validateData = $this->validate($this->getRules());

        if ($this->isEdit) {
            // Actualizar usuario existente
            $user = User::findOrFail($this->userId);
            $user->name = $validateData['name'];
            $user->email = $validateData['email'];

            if (!empty($validateData['password'])) {
                $user->password = Hash::make($validateData['password']);
            }

            $user->save();

            // Sincronizar rol
            $role = Role::find($validateData['role']);
            if ($role) {
                $user->syncRoles([$role->name]); // syncRoles recibe nombres
            }

            session()->flash('success', 'Usuario actualizado correctamente');
        } else {
            // Crear el usuario
            $user = User::create([
                'name' => $validateData['name'],
                'email' => $validateData['email'],
                'password' => Hash::make($validateData['password']),
            ]);

            // Asignar el rol usando Spatie Permission
            $role = Role::find($validateData['role']);
            if ($role) {
                $user->assignRole($role->name); // assignRole recibe el nombre, no el id
            }

            session()->flash('success', 'Usuario creado correctamente');
        }

        return $this->redirectRoute('users.index', navigate: true);
    }



    public function createRole()
    {
        $this->validate([
            'roleName' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    $slug = Str::slug($value, '-');
                    if (Role::where('name', $slug)->exists()) {
                        $fail('El nombre del rol ya está en uso.');
                    }
                },
            ],
            'selectedPermissions' => 'array|min:1',
        ]);

        $slug = Str::slug($this->roleName, '_');

        $role = Role::create([
            'name' => $slug,
            'display_name' => $this->roleName,
            'guard_name' => 'web',
        ]);

        $role->permissions()->sync($this->selectedPermissions);



        // ✅ Actualizar lista y seleccionar el nuevo rol automáticamente
        $this->role = $role->id;


        // Resetear modal
        $this->reset(['openCreateRoleModal', 'roleName', 'selectedPermissions']);

        session()->flash('success', 'Rol creado con éxito');
    }

    public function applyDependencies($permissionId, $isChecked)
    {
        $permission = Permission::find($permissionId);
        if (!$permission) return;
    
        // aseguramos que selectedPermissions siempre es array
        if (!is_array($this->selectedPermissions)) {
            $this->selectedPermissions = [];
        }
    
        // --- CASO 1: Se está marcando un permiso ---
        if ($isChecked) {
            // Si marca create/update/delete → forzar view
            if (in_array($permission->action, ['create', 'update', 'delete'])) {
                $viewPermission = Permission::where('area_id', $permission->area_id)
                    ->where('action', 'view')
                    ->first();
    
                if ($viewPermission && !in_array($viewPermission->id, $this->selectedPermissions)) {
                    $this->selectedPermissions[] = $viewPermission->id;
                }
            }
        }
    
        // --- CASO 2: Se está desmarcando un permiso ---
        else {
            // Si desmarca "view" → quitar create/update/delete
            if ($permission->action === 'view') {
                $otherPermissions = Permission::where('area_id', $permission->area_id)
                    ->whereIn('action', ['create', 'update', 'delete'])
                    ->pluck('id')
                    ->toArray();
    
                $this->selectedPermissions = array_values(array_diff($this->selectedPermissions, $otherPermissions));
            }
        }
    }
    

    public function createPermission()
    {
        $this->validate([
            'newPermissionName' => 'required|string|max:255',
            'selectedArea' => 'required|integer|exists:areas,id',
        ]);

        $slug = Str::slug($this->newPermissionName, '_');

        // Validación compuesta manual
        if (Permission::where('name', $slug)
            ->where('area_id', $this->selectedArea)
            ->exists()
        ) {
            $this->addError('newPermissionName', 'Ese permiso ya existe en esta área.');
            return;
        }

        $permiso = Permission::create([
            'name' => $slug,
            'guard_name' => 'web',
            'area_id' => $this->selectedArea,
        ]);

        $this->permissions = \App\Models\Permission::with('area')->get(); // refrescar
        $this->selectedPermissions[] = $permiso->id;
        $this->newPermissionName = '';
    }

    // Abrir modal en modo edición
    public function editRole()
    {
        if (!$this->role) {
            return;
        }
        $role = Role::with('permissions')->findOrFail($this->role);

        $this->roleId = $role->id;
        $this->roleName = $role->display_name ?? $role->name;
        $this->selectedPermissions = $role->permissions->pluck('id')->toArray();

        $this->isEditRole = true;
        $this->openCreateRoleModal = true;
    }

    // Guardar cambios
    public function updateRole()
    {
        $this->validate([
            'roleName' => 'required|string|max:255',
            'selectedPermissions' => 'array|min:1',
        ]);

        $role = Role::findOrFail($this->roleId);

        $role->update([
            'display_name' => $this->roleName,
            'name' => Str::slug($this->roleName, '_'),
        ]);

        $role->permissions()->sync($this->selectedPermissions);

        // Reset
        $this->reset(['openCreateRoleModal', 'roleName', 'selectedPermissions', 'roleId', 'isEditRole']);

        session()->flash('success', 'Rol actualizado con éxito');
    }

    public function render()
    {
        $roles = Role::all();

        return view('livewire.modules.users.create-or-update', compact('roles'));
    }
}
