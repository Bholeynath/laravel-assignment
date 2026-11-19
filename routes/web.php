
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ShortUrlController;

// Login page
Route::get('/', function () {
    return view('auth.login');
})->name('login');

// Dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

// Accept invitation page
Route::get('/accept-invitation', function () {
    return view('auth.accept-invitation');
})->name('invitation.accept');

// Short URL redirect
Route::get('/{shortCode}', [ShortUrlController::class, 'redirect'])
    ->where('shortCode', '[A-Za-z0-9]{8}')
    ->name('short-urls.redirect');
