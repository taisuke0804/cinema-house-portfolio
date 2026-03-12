<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Screening;

class ScreeningSeatController extends Controller
{
    public function index(Screening $screening){
        return response()->json([
            'screening_id' => $screening->id,
            'total_seats' => 0,
            'reserved_seats' => 0,
            'available_seats' => 0,
            'occupancy_rate' => 0,
            'seats' => [],
        ]);
    }
}
