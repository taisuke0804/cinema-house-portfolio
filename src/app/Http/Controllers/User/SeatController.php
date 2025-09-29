<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\User\ReserveSeatRequest;
use Illuminate\Support\Facades\Auth;

class SeatController extends Controller
{
    /**
     * 座席を予約する処理
     */
    public function reserve(ReserveSeatRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = Auth::id('web');

        dd($validated);
    }
}
