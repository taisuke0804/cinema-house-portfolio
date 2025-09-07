<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Requests\Admin\AdminLoginRequest;
use Illuminate\Support\Facades\Auth;

class AdminLoginController extends Controller
{
    public function showLoginForm()
    {
        return Inertia::render('admin/Login');
    }

    public function login(AdminLoginRequest $request)
    {
        $request->authenticate(); // 認証を実行

        $request->session()->regenerate(); // セッションIDの再生成

        return redirect()->intended(route('admin.dashboard'));
    }

    /**
      * 管理者ログアウト処理
      */
     public function logout(Request $request)
     {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return to_route('admin.login');
     }
}
