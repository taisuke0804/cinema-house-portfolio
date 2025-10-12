<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Inertia\Inertia;

class AdminUserController extends Controller
{
    /**
     * ユーザー一覧表示
     */
    public function index()
    {
        $users = User::select('id', 'name', 'email', 'created_at')
            ->orderByDesc('id')
            ->paginate(10);
        
            return Inertia::render('admin/users/Index', [
            'users' => $users,
        ]);
    }
}
