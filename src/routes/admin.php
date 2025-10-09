<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Admin\AdminLoginController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\MovieController;
use App\Http\Controllers\Admin\ScreeningCalendarController;

// ------------------------------------------------------------------------------------------------
/**
 * 管理者認証関連のルート
 * ミドルウェアにてprefix、nameは設定済み
 * prefix('admin'), ->name('admin.')
 * バリデーションエラー表示にはwebミドルウェアが必要なため'web','admin'を同時にあてる
 */
Route::middleware(['web', 'admin'])->group(function () {
    // 'guest:admin' adminガードでログインしてないユーザーだけがアクセスできる
    Route::middleware('guest:admin')->controller(AdminLoginController::class)->group(function () {
        Route::get('login', 'showLoginForm')->name('login');
        Route::post('loginAttempt', 'login')->name('login.attempt');
    });

    Route::middleware(['auth:admin'])->group(function () {
        Route::post('logout', [AdminLoginController::class, 'logout'])->name('logout');
        
        Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::post('notifications/send', [AdminDashboardController::class, 'sendNotification'])->name('notifications.send');

        Route::controller(MovieController::class)->group(function () {
            Route::get('movies', 'index')->name('movies.index');
            Route::get('movies/create', 'create')->name('movies.create');
            Route::post('movies/store', 'store')->name('movies.store');
            Route::get('movies/{id}', 'show')->name('movies.show');
        });
        
        Route::controller(ScreeningCalendarController::class)->group(function () {
            Route::get('screenings/calendar', 'index')->name('screenings.calendar');
            Route::get('movies/{movie_id}/screenings/create', 'create')->name('screenings.create');
            Route::post('movies/{movie_id}/screenings', 'store')->name('screenings.store');
            Route::get('screenings/{screening_id}', 'show')->name('screenings.show');
        });
    });

});
