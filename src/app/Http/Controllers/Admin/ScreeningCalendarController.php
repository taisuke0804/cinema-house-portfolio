<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Screening;
use App\Models\Movie;
use Inertia\Inertia;
use Inertia\Response;

class ScreeningCalendarController extends Controller
{

    /**
     * カレンダー表示ページ.
     */
    public function index()
    {
        $screenings = Screening::with('movie')->get();

        return inertia::render('admin/screenings/Calendar', [
            'screenings' => $screenings,
        ]);
    }

    /**
     * 上映スケジュールの新規登録画面を表示
     */
    public function create(int $movie_id): Response
    {
        $movie = Movie::findOrFail($movie_id);

        return Inertia::render('admin/screenings/Create', [
            'movie' => [
                'id' => $movie->id,
                'title' => $movie->title,
            ],
        ]);
    }
}
