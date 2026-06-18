<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\ReservationPriceController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\IsAdmin;
use App\Models\Property;
use App\Models\Reservation;
use Illuminate\Support\Facades\Route;

Route::get('/', [IndexController::class, 'index'])->name('index');

Route::get('/property/{id}', [PropertyController::class, 'show'])->name('property.show');

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::post('/admin/login', [AdminController::class, 'loginFunction'])->name('admin.login.submit')->middleware('throttle:5,1');

Route::post('/send-email', [MailController::class, 'sendEmail'])->name('send.email')->middleware('throttle:3,1');

Route::get('/property/{id}/reservations', [ReservationController::class, 'data'])->name('property.reservations.data');

Route::get('/api/properties', function () {
    return response()->json(Property::all());
});

Route::get('/api/reservations', function () {
    return response()->json(
        Reservation::where('status', 'confirmed')
            ->get(['property_id', 'check_in', 'check_out']),
    );
});

// --- Admin routes ---
Route::middleware([IsAdmin::class])->group(function (): void {
    Route::post('/admin/logout', [AdminController::class, 'logoutFunction'])->name('admin.logout');
    Route::get('/admin/properties', [AdminController::class, 'properties'])->name('admin.properties');
    Route::get('/admin/reservations/pending', [AdminController::class, 'pending'])->name('admin.reservations.pending');
    Route::post('/admin/reservations/pending/update/{id}', [AdminController::class, 'updateStatus'])->name('admin.reservations.pending.update');
    Route::delete('/properties/{id}', [PropertyController::class, 'destroy'])->name('properties.destroy');
    Route::get('/properties/create', [PropertyController::class, 'create'])->name('properties.create');
    Route::post('/properties/store', [PropertyController::class, 'store'])->name('properties.store');
    Route::get('/properties/{property}/edit', [PropertyController::class, 'edit'])->name('properties.edit');
    Route::put('/properties/{property}', [PropertyController::class, 'update'])->name('properties.update');
    Route::get('/suggestion/{reservation}', [AdminController::class, 'suggestionEmail'])->name('suggestion.create');
    Route::post('/reservations/{id}/send-suggestion', [MailController::class, 'sendSuggestion'])->name('reservations.sendSuggestion');
    Route::get('/admin/calendar', [AdminController::class, 'calendar'])->name('admin.calendar');
    Route::get('/admin/calendar/reservations', [AdminController::class, 'getConfirmedReservations'])->name('admin.calendar.reservations');
    Route::post('/admin/calendar/reservation/update-time', [AdminController::class, 'updateTime'])->name('admin.calendar.reservations.update-time');
    Route::get('/admin/calendar/export-excel', [AdminController::class, 'exportExcel'])->name('admin.calendar.export-excel');
    Route::get('/admin/calendar/export-factura-excel', [AdminController::class, 'exportfacturaExcel'])->name('admin.calendar.export-factura-excel');
    Route::get('/admin/reservation-prices', [ReservationPriceController::class, 'index'])->name('admin.reservation_prices');
    Route::delete('/reservation-prices/{id}', [ReservationPriceController::class, 'destroy'])->name('reservation-prices.destroy');
    Route::post('/reservation-prices/create', [ReservationPriceController::class, 'create'])->name('reservation-prices.create');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
});

// --- Super Admin routes ---
Route::middleware(['super_admin'])->prefix('super-admin')->name('super_admin.')->group(function (): void {
    Route::get('/admins', [SuperAdminController::class, 'index'])->name('index');
    Route::get('/admins/create', [SuperAdminController::class, 'create'])->name('create');
    Route::post('/admins', [SuperAdminController::class, 'store'])->name('store');
    Route::get('/admins/{admin}/edit', [SuperAdminController::class, 'edit'])->name('edit');
    Route::put('/admins/{admin}', [SuperAdminController::class, 'update'])->name('update');
    Route::post('/admins/{admin}/toggle', [SuperAdminController::class, 'toggleAdmin'])->name('toggle');
    Route::delete('/admins/{admin}', [SuperAdminController::class, 'destroy'])->name('destroy');
});

Route::get('/api/property-price-range', [ReservationPriceController::class, 'getPriceRange']);

Route::get('/privacy', function () {
    return view('privacy');
})->name('privacy');

Route::get('/api/images', function () {
    $properties = Property::all();

    $images = $properties->mapWithKeys(function ($property) {
        return [$property->id => $property->images_div];
    });

    return response()->json($images);
});
