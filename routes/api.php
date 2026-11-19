<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\InvitationController;
use App\Http\Controllers\Api\ShortUrlController;

Route::prefix('v1')->group(function () {

    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/invitations/accept', [InvitationController::class,'acceptInvitation'])->name('invitations.accept');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);

        //invitation  routes
        Route::post('/invitations', [InvitationController::class,'sendInvitation'])->name('invitations.send');

        //short url routes
        Route::middleware('role:Admin,Member')->group(function () {
            Route::post('/short-urls', [ShortUrlController::class, 'store'])->name('short-urls.store');
        });

        Route::middleware('role:SuperAdmin,Admin,Member')->group(function () {
            Route::get('/short-urls', [ShortUrlController::class, 'index'])->name('short-urls.index');
        });

    });

});

// This is deafult route for user
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
