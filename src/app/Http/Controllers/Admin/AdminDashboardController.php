<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Http\Requests\Admin\SendNotificationRequest;
use App\Models\User;
use App\Jobs\SendBulkNotificationMail;

class AdminDashboardController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('admin/Dashboard');
    }

    /**
     * ユーザーへ管理者からメールを一斉送信
     */
    public function sendNotification(SendNotificationRequest $request)
    {
        $validated = $request->validated();
        $userIds = User::pluck('id')->toArray();

        SendBulkNotificationMail::dispatch($userIds, $validated['subject'], $validated['bodyText']);

        return back()->with('success', '通知メールの送信をキューに登録しました。');
    }
}
