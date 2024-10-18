<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\IndexController;
use Illuminate\Http\Request;


Route::get('/', [IndexController::class, 'index'])->name('index');

Route::get('/property/{id}', [PropertyController::class, 'show'])->name('property.show');

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::post('/admin/login', [AdminController::class, 'loginFunction'])->name('admin.login.submit');

Route::get('/admin/logout', [AdminController::class, 'logoutFunction'])->name('admin.logout');

Route::get('/admin/properties', [AdminController::class, 'properties'])->name('admin.properties');

Route::put('/admin/properties/update/{property}', [AdminController::class, 'updateProperty'])->name('admin.properties.update');


