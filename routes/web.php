<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminPropertyController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\PublicApiController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\ReservationPriceController;
use App\Http\Controllers\StatusReservationController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\IsAdmin;
use Illuminate\Support\Facades\Route;

Route::get('/', [IndexController::class, 'index'])->name('index');

Route::get('/property/{id}', [PropertyController::class, 'show'])->name('property.show');

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::post('/admin/login', [AdminAuthController::class, 'loginFunction'])->name('admin.login.submit')->middleware('throttle:5,1');

Route::post('/send-email', [MailController::class, 'sendEmail'])->name('send.email')->middleware('throttle:3,1');

Route::get('/property/{id}/reservations', [ReservationController::class, 'data'])->name('property.reservations.data');

Route::get('/api/properties', [PublicApiController::class, 'properties']);
Route::get('/api/reservations', [PublicApiController::class, 'reservations']);
Route::get('/api/images', [PublicApiController::class, 'images']);

// --- Admin routes ---
Route::middleware([IsAdmin::class])->group(function (): void {
    Route::post('/admin/logout', [AdminAuthController::class, 'logoutFunction'])->name('admin.logout');
    Route::get('/admin/properties', [AdminPropertyController::class, 'properties'])->name('admin.properties');
    Route::get('/admin/reservations/pending', [StatusReservationController::class, 'pending'])->name('admin.reservations.pending');
    Route::post('/admin/reservations/pending/update/{id}', [StatusReservationController::class, 'updateStatus'])->name('admin.reservations.pending.update');
    Route::delete('/properties/{id}', [PropertyController::class, 'destroy'])->name('properties.destroy');
    Route::get('/properties/create', [PropertyController::class, 'create'])->name('properties.create');
    Route::post('/properties/store', [PropertyController::class, 'store'])->name('properties.store');
    Route::get('/properties/{property}/edit', [PropertyController::class, 'edit'])->name('properties.edit');
    Route::put('/properties/{property}', [PropertyController::class, 'update'])->name('properties.update');
    Route::get('/suggestion/{reservation}', [StatusReservationController::class, 'suggestionEmail'])->name('suggestion.create');
    Route::post('/reservations/{id}/send-suggestion', [MailController::class, 'sendSuggestion'])->name('reservations.sendSuggestion');
    Route::get('/admin/calendar', [CalendarController::class, 'calendar'])->name('admin.calendar');
    Route::get('/admin/calendar/reservations', [CalendarController::class, 'getConfirmedReservations'])->name('admin.calendar.reservations');
    Route::post('/admin/calendar/reservation/update-time', [CalendarController::class, 'updateTime'])->name('admin.calendar.reservations.update-time');
    Route::get('/admin/calendar/export-excel', [ExportController::class, 'exportExcel'])->name('admin.calendar.export-excel');
    Route::get('/admin/calendar/export-factura-excel', [ExportController::class, 'exportInvoiceExcel'])->name('admin.calendar.export-factura-excel');
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
