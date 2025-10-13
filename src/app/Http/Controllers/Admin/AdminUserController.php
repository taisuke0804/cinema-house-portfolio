<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class AdminUserController extends Controller
{
    /**
     * ユーザー一覧表示
     */
    public function index(): Response
    {
        $users = User::select('id', 'name', 'email', 'created_at')
            ->orderByDesc('id')
            ->paginate(10);
        
        return Inertia::render('admin/users/Index', [
            'users' => $users,
        ]);
    }

    /**
     * ユーザー新規登録フォームを表示
     */
    public function create(): Response
    {
        return Inertia::render('admin/users/Create');
    }
}
