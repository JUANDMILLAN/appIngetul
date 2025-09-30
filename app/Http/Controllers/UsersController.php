<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string)$request->get('q',''));

        $users = User::query()
            ->when($q !== '', function($qq) use ($q){
                $qq->where(function($w) use ($q){
                    $w->where('name','like',"%{$q}%")
                      ->orWhere('email','like',"%{$q}%");
                });
            })
            ->with('roles')
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('users.index', compact('users','q'));
    }

    public function create()
    {
        $roles       = Role::orderBy('name')->get();
        $permissions = Permission::orderBy('name')->get();
        return view('users.create', compact('roles','permissions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required','string','max:150'],
            'email'    => ['required','email','max:150','unique:users,email'],
            'password' => ['required','string','min:6','confirmed'],
            'roles'    => ['array'],
            'permissions' => ['array'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email'=> $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // Asignación inicial
        $user->syncRoles($data['roles'] ?? []);
        $user->syncPermissions($data['permissions'] ?? []);

        return redirect()->route('users.index')->with('ok','Usuario creado.');
    }

    public function edit(User $user)
    {
        $roles       = Role::orderBy('name')->get();
        $permissions = Permission::orderBy('name')->get();
        $user->load('roles','permissions');

        return view('users.edit', compact('user','roles','permissions'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'  => ['required','string','max:150'],
            'email' => ['required','email','max:150', Rule::unique('users','email')->ignore($user->id)],
            'password' => ['nullable','string','min:6','confirmed'],
        ]);

        $payload = [
            'name' => $data['name'],
            'email'=> $data['email'],
        ];
        if (!empty($data['password'])) {
            $payload['password'] = Hash::make($data['password']);
        }
        $user->update($payload);

        return redirect()->route('users.edit', $user)->with('ok','Usuario actualizado.');
    }

    public function destroy(User $user)
    {
        // Evita que un usuario se borre a sí mismo si quieres
        if (auth()->id() === $user->id) {
            return back()->with('error', 'No puedes eliminarte a ti mismo.');
        }
        $user->delete();

        return redirect()->route('users.index')->with('ok','Usuario eliminado.');
    }

    // Gestionar roles del usuario
    public function syncRoles(Request $request, User $user)
    {
        $data = $request->validate([
            'roles' => ['array'],
        ]);
        $user->syncRoles($data['roles'] ?? []);
        return back()->with('ok','Roles actualizados.');
    }

    // Gestionar permisos directos del usuario
    public function syncPermissions(Request $request, User $user)
    {
        $data = $request->validate([
            'permissions' => ['array'],
        ]);
        $user->syncPermissions($data['permissions'] ?? []);
        return back()->with('ok','Permisos actualizados.');
    }
}
