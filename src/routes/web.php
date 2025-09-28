<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\User\ScreeningController;

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
        });
    });
});

// Route::get('/test', function () {
//     return Inertia::render('Test/Test');
// })->name('test');