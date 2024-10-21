<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\IndexController;
use App\Mail\ContactMail;
use Illuminate\Support\Facades\Mail;
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

Route::post('/send-email', [MailController::class, 'sendEmail'])->name('send.email');

Route::get('/send-test-email', function() {
    $data = [
        'name' => 'Test User',
        'number' => '123456789',
        'email' => 'test@example.com',
        'message' => 'This is a test email',
        'daterange' => '2024-11-01 to 2024-11-10',
    ];
    $sub = 'test email';

    try {
        Mail::to('rubensepulvedareal@gmail.com')->send(new ContactMail($data, $sub));
        return 'Email sent successfully!';
    } catch (\Exception $e) {
        return 'Error sending email: ' . $e->getMessage();
    }
});

