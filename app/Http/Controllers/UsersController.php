<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public $slug = "usuarios";

    public function index()
    {
        $users = User::with([
            'roles.permissions.area'
        ])
            ->paginate(10)
            ->withQueryString();


        // Transform data if necessary, or pass as is. 
        // View expects permissions to show actions.

        $permissionCreate = \App\Services\AreaPermissionService::canArea('create', $this->slug);
        $canUpdate = \App\Services\AreaPermissionService::canArea('update', $this->slug);
        $canDelete = \App\Services\AreaPermissionService::canArea('delete', $this->slug);

        return \Inertia\Inertia::render('Users/Index', [
            'users' => $users,
            'slug' => $this->slug,
            'permissions' => [
                'create' => $permissionCreate,
                'update' => $canUpdate,
                'delete' => $canDelete,
            ]
        ]);
    }

    public function create()
    {
        $roles = Role::all();
        // Permissions are now loaded via AJAX when the modal opens to improve performance

        return \Inertia\Inertia::render('Users/Form', [
            'roles' => $roles,
            'isEdit' => false,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'role' => 'required',
            'password' => 'required|min:6',
        ]);

        $plainPassword = $data['password'];
        $data['password'] = bcrypt($plainPassword);


        $role = Role::where('id', $data['role'])->first();


        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        if ($role) {
            $user->assignRole($role);
        }

        return redirect()->route('users.index')->with('success', 'Usuario creado correctamente');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        // Permissions are now loaded via AJAX
        $user->load('roles');

        return \Inertia\Inertia::render('Users/Form', [
            'user' => $user,
            'roles' => $roles,
            'isEdit' => true,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required',
            'password' => 'nullable|min:6',
        ]);

        $user->name = $data['name'];
        $user->email = $data['email'];

        if (!empty($data['password'])) {
            $user->password = bcrypt($data['password']);
        }

        $user->save();

        $role = Role::where('id', $data['role'])->first();
        if ($role) {
            $user->syncRoles($role);
        }

        return redirect()->route('users.index')->with('success', 'Usuario actualizado correctamente');
    }

    // Role & Permission Management (AJAX)

    public function storeRole(Request $request)
    {
        $request->validate([
            'roleName' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    if (Role::where('name', \Illuminate\Support\Str::slug($value, '_'))->exists()) {
                        $fail('El nombre del rol ya está en uso.');
                    }
                },
            ],
            'selectedPermissions' => 'array|min:1',
        ]);

        $slug = \Illuminate\Support\Str::slug($request->roleName, '_');

        $role = Role::create([
            'name' => $slug,
            'display_name' => $request->roleName,
            'guard_name' => 'web',
        ]);

        $role->permissions()->sync($request->selectedPermissions);

        return response()->json(['success' => true, 'role' => $role, 'message' => 'Rol creado con éxito']);
    }

    public function updateRole(Request $request, Role $role)
    {
        $request->validate([
            'roleName' => 'required|string|max:255',
            'selectedPermissions' => 'array|min:1',
        ]);

        $role->update([
            'display_name' => $request->roleName,
            'name' => \Illuminate\Support\Str::slug($request->roleName, '_'),
        ]);

        $role->permissions()->sync($request->selectedPermissions);

        return response()->json(['success' => true, 'role' => $role, 'message' => 'Rol actualizado con éxito']);
    }

    public function getRole($id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        return response()->json($role);
    }

    public function getAllPermissions()
    {
        $permissions = \App\Models\Permission::with('area')->get();
        return response()->json($permissions);
    }

    public function storePermission(Request $request)
    {
        $request->validate([
            'newPermissionName' => 'required|string|max:255',
            'selectedArea' => 'required|integer|exists:areas,id',
        ]);

        $slug = \Illuminate\Support\Str::slug($request->newPermissionName, '_');

        if (
            \App\Models\Permission::where('name', $slug)
                ->where('area_id', $request->selectedArea)
                ->exists()
        ) {
            return response()->json(['errors' => ['newPermissionName' => ['Ese permiso ya existe en esta área.']]], 422);
        }

        $permission = \App\Models\Permission::create([
            'name' => $slug,
            'guard_name' => 'web',
            'area_id' => $request->selectedArea,
        ]);

        // Return updated permissions list
        $permissions = \App\Models\Permission::with('area')->get();

        return response()->json(['success' => true, 'permission' => $permission, 'permissions' => $permissions]);
    }

    public function destroy($id)
    {
        if (\App\Services\AreaPermissionService::canArea('delete', $this->slug)) {
            $user = User::findOrFail($id);
            $user->delete();
            return redirect()->route('users.index')->with('success', 'Usuario eliminado correctamente.');
        }

        return redirect()->route('users.index')->with('error', 'No tienes permiso para eliminar usuarios.');
    }
}
