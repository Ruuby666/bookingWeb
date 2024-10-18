<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use  App\Models\Property;

class AdminController extends Controller
{
    public function loginFunction(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);

        if (
            $credentials['email'] === env('ADMIN_EMAIL') &&
            $credentials['password'] === env('ADMIN_PASSWORD')
        ) {
            session(['is_admin' => true]);

            return redirect()->route('admin.properties')->with('success', 'Logged in as admin.');
        }

        return back()->withErrors([
            'email' => 'Invalid admin credentials.'
        ]);
    }

    public function logoutFunction()
    {
        // Elimina la sesión del admin
        session()->forget('is_admin');

        return redirect()->route('index')->with('success', 'Logged out successfully.');
    }

    public function properties()
    {
        if (session('is_admin')) {
            $properties = Property::all();
            return view('admin.admin', compact('properties'));
        }

        return redirect()->route('admin.login');
    }
}
