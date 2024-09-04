<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
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
        // Cargar los datos desde la base de datos
        $users = User::all();
        $properties = Property::all();
        $reservations = Reservation::all();

        // Compartir los datos con todas las vistas
        View::share('users', $users);
        View::share('properties', $properties);
        View::share('reservations', $reservations);
    }
}
