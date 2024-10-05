<?php

use App\Http\Controllers\Customer\AuthenticationController;
use App\Http\Controllers\Customer\ReservationController;
use App\Http\Controllers\Customer\TableController;
use App\Http\Middleware\AllowAnonymousOnly;
use App\Http\Middleware\AllowAuthenticatedOnly;
use Illuminate\Support\Facades\Route;

Route::controller(ReservationController::class)->group(function () {
    Route::prefix('reservations')->group(function () {
        Route::get('/', 'listReservations');
        Route::post('/', 'makeReservation');
        Route::delete('/{reservation}', 'cancelReservations');
    });
});

Route::controller(TableController::class)->group(function () {
    Route::prefix('tables')->group(function () {
        Route::get('/date/{date}/length/{length}', 'listTablesForDateTimeAndDuration');
    });
});

Route::controller(AuthenticationController::class)->group(function () {
    Route::prefix('auth')->group(function () {
        Route::get('/me', 'me')
            ->middleware('auth:sanctum')
            ->middleware(AllowAuthenticatedOnly::class);
        Route::post('/register', 'register')->middleware(AllowAnonymousOnly::class);
        Route::post('/login', 'login')->middleware(AllowAnonymousOnly::class);
        Route::post('/logout', 'logout')
            ->middleware(AllowAuthenticatedOnly::class)
            ->middleware('auth:sanctum');
    });
});
