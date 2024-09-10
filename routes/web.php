<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PropertyController;
use Illuminate\Http\Request;


Route::get('/', function () {
    return view('index');
})->name('index');

Route::get('/property/{id}', [PropertyController::class, 'show'])->name('property.show');

// En routes/web.php
Route::post('/submit-daterange', function (Request $request) {
    $dateRange = $request->input('daterange'); // Obtiene el rango de fechas

    return "Selected Date Range: " . $dateRange; // Devuelve el rango seleccionado
});
