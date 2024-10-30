<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use  App\Models\Property;
use App\Models\Reservation;

class AdminController extends Controller
{
    public function loginFunction(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);

        if ($credentials['email'] === env('ADMIN_EMAIL') && $credentials['password'] === env('ADMIN_PASSWORD')) {
            session(['is_admin' => true]);

            return redirect()->route('admin.properties')->with('success', 'Logged in as admin.');
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
        $reservations = Reservation::where('status', 'confirmed')->with('property','user')->get();
        $pending = Reservation::where('status', 'pending')->with('property','user')->get();

        return view('admin.pending', compact('reservations','pending'));
    }

    public function updateStatus($id)
    {
        $reservation = Reservation::findOrFail($id);

        // Cambiar el estado a "completed" o cualquier otro estado que desees
        $reservation->status = 'completed'; // O puedes definir otro estado
        $reservation->save();

        return redirect()->route('reservations.pending')->with('success', 'Reservation status updated successfully.');
    }
}
