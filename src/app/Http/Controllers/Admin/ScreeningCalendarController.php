<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Screening;
use App\Models\Movie;
use Inertia\Inertia;
use Inertia\Response;
use App\Http\Requests\Admin\StoreScreeningRequest;
use App\Services\Admin\ScreeningService;

class ScreeningCalendarController extends Controller
{

    private $screeningService;

    public function __construct(ScreeningService $screeningService)
    {
        $this->screeningService = $screeningService;
    }

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

    /**
     * 上映スケジュールの新規登録処理
     */
    public function store(StoreScreeningRequest $request)
    {
        $validated = $request->only('movie_id', 'screening_date', 'start_time', 'end_time');

        $this->screeningService->storeScreening($validated);
        
        return redirect()->route('admin.screenings.calendar')->with('success', '上映スケジュール新規登録が完了しました');
    }
}
