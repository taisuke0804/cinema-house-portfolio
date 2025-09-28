<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Admin\AdminLoginController;

Route::get('/', function () {
    return Inertia::render('Index');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('user/Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

// Route::get('/test', function () {
//     return Inertia::render('Test/Test');
// })->name('test');