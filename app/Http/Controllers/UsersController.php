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

        $areaUsuarios = Area::with('permissions')
            ->where('slug', $this->slug)
            ->first();

        $permissionCreate = $areaUsuarios?->permissions
            ->where('action', 'create')
            ->first()?->name;

        return view('modules.users', [
            'slug' => $this->slug,
            'permissionCreate' => $permissionCreate
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'full_name' => 'required',
            'email' => 'required|email',
            'role' => 'required',
            'password' => 'required',
        ]);

        $plainPassword = $data['password'];
        $data['password'] = bcrypt($plainPassword);


        $role = Role::where('id', $data['role'])->first();


        $user = User::create([
            'name' => $data['full_name'],
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        $user->assignRole($role);

        return redirect()->route('users.index')->with('success', 'Usuario creado correctamente');
    }
}
