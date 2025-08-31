<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Requests\Admin\AdminLoginRequest;

class AdminLoginController extends Controller
{
    public function showLoginForm()
    {
        return Inertia::render('admin/Login');
    }

    public function login(AdminLoginRequest $request)
    {
        dd($request);
    }
}
