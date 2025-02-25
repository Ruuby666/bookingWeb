<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Property;
use App\Models\Reservation;
use App\Services\ReservationService;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        
    }

     public function boot(): void
    {

        if (!Storage::exists('users.json')) {
            $users = User::all()->toArray();
            Storage::put('users.json', json_encode($users));
            error_log("Users data saved in JSON.");
        } else {
            $users = json_decode(Storage::get('users.json'), true);
            error_log('Users data retrieved from JSON.');
        }

        if (!Storage::exists('properties.json')) {
            $properties = Property::all()->toArray();
            Storage::put('properties.json', json_encode($properties));
            error_log("Properties data saved in JSON.");
        } else {
            $properties = json_decode(Storage::get('properties.json'), true);
            error_log('Properties data retrieved from JSON.');
        }

        // Check if reservations.json exists; if not, create it
        if (!Storage::exists('reservations.json')) {
            $reservations = Reservation::all()->toArray();
            Storage::put('reservations.json', json_encode($reservations));
            error_log("Reservations data saved in JSON.");
        } else {
            $reservations = json_decode(Storage::get('reservations.json'), true);
            error_log('Reservations data retrieved from JSON.');
        }

        // Share the data with views
        View::share('users', $users);
        View::share('properties', $properties);
        View::share('reservations', $reservations);
    }
}
