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
        $screenings = Screening::with('movie')->get();

        return inertia::render('user/screenings/Calendar', [
            'screenings' => $screenings,
        ]);
    }
}
