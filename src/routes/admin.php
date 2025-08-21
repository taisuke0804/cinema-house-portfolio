<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Admin\AdminLoginController;

// ------------------------------------------------------------------------------------------------
/**
 * 管理者認証関連のルート
 * 管理者専用のログイン処理を設定
 * prefix('admin'), ->name('admin')
 */

Route::middleware('admin')->group(function () {
    Route::middleware('guest:admin')->controller(AdminLoginController::class)->group(function () {
        Route::get('login', 'showLoginForm')->name('login');
    });
});