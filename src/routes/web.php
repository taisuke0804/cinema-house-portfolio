<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Admin\AdminLoginController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

// ------------------------------------------------------------------------------------------------
/**
 * 管理者認証関連のルート
 * 管理者専用のログイン処理を設定
 */

Route::prefix('admin')->name('admin')->group(function () {
    Route::controller(AdminLoginController::class)->group(function () {
        Route::get('login', 'showLoginForm')->name('login');
    });
});

// Route::get('/test', function () {
//     return Inertia::render('Test/Test');
// })->name('test');