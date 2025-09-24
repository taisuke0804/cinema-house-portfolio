<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Screening;
use Inertia\Inertia;

class ScreeningCalendarController extends Controller
{

    /**
     * カレンダー表示ページ.
     */
    public function index()
    {
        $screenings = Screening::with('movie')->get();

        return inertia('admin/screenings/Calendar', [
            'screenings' => $screenings,
        ]);
    }
}
