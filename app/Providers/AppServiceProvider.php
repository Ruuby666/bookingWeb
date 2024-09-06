<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Storage;
use App\Models\User; // Modelo de ejemplo, ajusta según tu aplicación
use App\Models\Property;
use App\Models\Reservation;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        if (!Storage::exists('data.json')) {
            $data = [
                'users' => User::all()->toArray(),
                'properties' => Property::all()->toArray(),
                'reservations' => Reservation::all()->toArray(),
            ];

            Storage::put('data.json', json_encode($data));
            error_log("Data saved in JSON.");
        } else {

            $json = Storage::get('data.json');
            $data = json_decode($json, true);

            error_log('Data retrive from JSON.');
        }

        View::share('users', $data['users']);
        View::share('properties', $data['properties']);
        View::share('reservations', $data['reservations']);
    }
}
