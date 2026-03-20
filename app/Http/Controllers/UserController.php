<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderByRaw("CASE role WHEN 'superuser' THEN 1 WHEN 'admin' THEN 2 WHEN 'operatore' THEN 3 END")
            ->orderBy('name')
            ->get();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $roles = $this->allowedRolesToCreate();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $roles = $this->allowedRolesToCreate();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', Rule::in($roles)],
        ]);

        User::create($validated);

        return redirect()->route('users.index')->with('success', 'Utente creato con successo.');
    }

    public function destroy(User $user)
    {
        $currentUser = auth()->user();

        if ($user->id === $currentUser->id) {
            return back()->with('error', 'Non puoi eliminare te stesso.');
        }

        if ($currentUser->role === 'admin') {
            if ($user->role !== 'operatore') {
                return back()->with('error', 'Puoi eliminare solo gli operatori.');
            }
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Utente eliminato.');
    }

    private function allowedRolesToCreate(): array
    {
        $currentUser = auth()->user();

        if ($currentUser->role === 'superuser') {
            return ['superuser', 'admin', 'operatore'];
        }

        if ($currentUser->role === 'admin') {
            return ['admin', 'operatore'];
        }

        return [];
    }
}
