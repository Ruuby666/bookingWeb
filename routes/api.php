<?php

use Illuminate\Support\Facades\Route;
use App\Models\Reservation;
use App\Models\Property;


Route::get('/api/reservations', function () {
    return Reservation::all();
});

Route::get('/api/properties', function () {
    return Property::all();
});

Route::get('/api/property-images', function () {
    // Suponiendo que guardas la imagen destacada por propiedad
    $images = Property::with('images')->get()->mapWithKeys(function ($p) {
        return [$p->id => optional($p->images->first())->filename];
    });

    return response()->json($images);
});
