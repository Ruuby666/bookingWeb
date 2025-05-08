<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\IsAdmin;



Route::get('/', [IndexController::class, 'index'])->name('index');

Route::get('/property/{id}', [PropertyController::class, 'show'])->name('property.show');

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::post('/admin/login', [AdminController::class, 'loginFunction'])->name('admin.login.submit')->middleware('throttle:5,1');

Route::post('/send-email', [MailController::class, 'sendEmail'])->name('send.email');

Route::middleware([IsAdmin::class])->group(function () {
    Route::get('/admin/logout', [AdminController::class, 'logoutFunction'])->name('admin.logout');

    Route::get('/admin/properties', [AdminController::class, 'properties'])->name('admin.properties');

    Route::get('/admin/reservations/pending', [AdminController::class, 'pending'])->name('admin.reservations.pending');

    Route::post('/admin/reservations/pending/update/{id}', [AdminController::class, 'updateStatus'])->name('admin.reservations.pending.update');

    Route::put('/admin/properties/update/{property}', [AdminController::class, 'updateProperty'])->name('admin.properties.update');

    Route::delete('/properties/{id}', [PropertyController::class, 'destroy'])->name('properties.destroy');

    Route::get('/properties/create', [PropertyController::class, 'create'])->name('properties.create');

    Route::post('/properties/store', [PropertyController::class, 'store'])->name('properties.store');

    Route::get('/properties/{property}/edit', [PropertyController::class, 'edit'])->name('properties.edit'); // Formulario de edición

    Route::put('/properties/{property}', [PropertyController::class, 'update'])->name('properties.update'); // Actualizar propiedad

});

// Resource routes
Route::resource('users', UserController::class);
Route::resource('properties', PropertyController::class);
Route::resource('reservations', ReservationController::class);
