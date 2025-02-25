<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function store(Request $request)
    {

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'number' => $request->number,
            'password' => Hash::make('password'),
            'is_admin' => false,
        ]);

        $this->updateUsersJson();

        return $user;
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user = User::findOrFail($id);

        // Si se proporciona una nueva contraseña, actualízala
        if ($request->filled('password')) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        } else {
            unset($validatedData['password']);
        }

        $user->update($validatedData);

        $this->updateUsersJson();

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    private function updateUsersJson()
    {
        $users = User::all()->toArray();
        Storage::put('users.json', json_encode($users, JSON_PRETTY_PRINT));
        error_log("Archivo users.json actualizado tras creación de usuario.");
    }
}
