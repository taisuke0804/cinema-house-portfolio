<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\ScreeningService;
use App\Models\Screening;
use Inertia\Inertia;

class ScreeningController extends Controller
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
        $screeningsByDate = $this->screeningService->getGroupedScreenings();

        return inertia::render('user/screenings/Calendar', [
            'screeningsByDate' => $screeningsByDate,
        ]);
    }

    /**
     * 上映スケジュールの詳細情報を表示するページ
     */
    public function show(int $screening_id)
    {
        $screening = $this->screeningService->getScreeningDetails($screening_id);

        $authReservedSeat = $this->screeningService->getAuthReservedSeatInfo($screening_id);

        return Inertia::render('user/screenings/Show', [
            'screening' => $screening, 
            'authReservedSeat' => $authReservedSeat,
        ]);
    }
}
