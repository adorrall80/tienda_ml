<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('q');

        $usuarios = User::with('roles')
            ->when($search, fn($q) => $q->where('name', 'like', "%$search%")
                                        ->orWhere('email', 'like', "%$search%"))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $roles = Role::orderBy('name')->pluck('name');

        return view('admin.usuarios.index', compact('usuarios', 'roles', 'search'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate(['rol' => 'required|exists:roles,name']);

        $user->syncRoles([$request->rol]);

        return back()->with('success', "Rol de {$user->name} actualizado.");
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        $user->delete();

        return back()->with('success', 'Usuario eliminado.');
    }
}
