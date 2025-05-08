<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use  App\Models\Property;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function loginFunction(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            // Si el usuario es admin, redirigir a la página de propiedades
            if ($user->isAdmin()) {
                session(['is_admin' => true]);
                return redirect()->route('admin.properties')->with('success', 'Logged in as admin.');
            } else {
                return back()->with('error', 'You are not authorized to access this page.');
            }
        }
        return back()->with('error', 'Email or password is incorrect.');
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

        return redirect()->route('login');
    }

    public function pending()
    {
        // Filtrar las reservas con el estado 'pending'
        $reservations = Reservation::where('status', 'confirmed')->with('property', 'user')->get();
        $pending = Reservation::where('status', 'pending')->with('property', 'user')->get();

        return view('admin.pending', compact('reservations', 'pending'));
    }

    public function updateStatus($id)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->status = 'confirmed';
        $reservation->save();
        $this->updateReservationJson();
        return redirect()->back();
    }

    private function updateReservationJson()
    {
        $reservations = Reservation::all()->toArray();
        Storage::put('reservations.json', json_encode($reservations, JSON_PRETTY_PRINT));
        error_log("Archivo reservations.json actualizado tras creación de usuario.");
    }
}
