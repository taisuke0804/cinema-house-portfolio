<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\User\ScreeningController;
use App\Http\Controllers\User\SeatController;
use App\Http\Controllers\User\MovieController;

Route::get('/', function () {
    return Inertia::render('Index');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('user/Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

/**
 * ユーザー認証関連のルート
 */
Route::prefix('user')->name('user.')->middleware(['web'])->group(function () {
    Route::middleware(['auth:web'])->group(function () {
        Route::controller(ScreeningController::class)->group(function () {
            Route::get('screenings', 'index')->name('screenings');
            Route::get('screenings/{screening_id}', 'show')->name('screenings.show');
        });

        Route::controller(SeatController::class)->group(function () {
            Route::post('seats/reserve', 'reserve')->name('seats.reserve');
            Route::get('seats/reserve/complete', 'complete')->name('reservation.complete');
            Route::get('reservations', 'index')->name('reservations.index');
            Route::get('reservations/{seat_id}/pdf', 'exportPdf')->name('reservations.pdf');
            Route::delete('reservations/{seat_id}', 'cancel')->name('reservations.cancel');
        });

        Route::controller(MovieController::class)->group(function () {
            Route::get('movies', 'index')->name('movies.index');
            Route::get('movies/{id}', 'show')->name('movies.show');
            Route::post('/movies/{id}/like', 'toggle')->name('movies.like');
        });
    });
});

// Route::get('/test', function () {
//     return Inertia::render('Test/Test');
// })->name('test');