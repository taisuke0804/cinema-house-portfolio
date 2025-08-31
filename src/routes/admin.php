<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Admin\AdminLoginController;

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
});
