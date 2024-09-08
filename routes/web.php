<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PropertyController;

Route::get('/', function () {
    return view('index');
})->name('index');

Route::get('/property/{id}', [PropertyController::class, 'show'])->name('property.show');
