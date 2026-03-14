<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\Api\ScreeningSeatService;
use App\Models\Screening;
use Illuminate\Http\JsonResponse;

class ScreeningSeatController extends Controller
{
    public function __construct(
        private ScreeningSeatService $screeningSeatService
    ) {}

    public function index(Screening $screening) : JsonResponse
    {
        $seatStatus = $this->screeningSeatService->getSeatStatus($screening);

        return response()->json($seatStatus);
    }
}
