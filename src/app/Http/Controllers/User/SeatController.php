<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\User\ReserveSeatRequest;
use Illuminate\Support\Facades\Auth;
use App\Services\User\SeatReservationService;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;

class SeatController extends Controller
{
    private $seatReservationService;

    public function __construct(SeatReservationService $seatReservationService)
    {
        $this->seatReservationService = $seatReservationService;
    }

    /**
     * 座席を予約する処理
     */
    public function reserve(ReserveSeatRequest $request)
    {
        $reservationData = $request->validated();
        $reservationData['user_id'] = Auth::guard('web')->id();

        $this->seatReservationService->reserveSeat($reservationData);

        return redirect()->route('user.reservation.complete');
    }

    /**
     * 座席予約完了画面
     */
    public function complete()
    {
        return Inertia::render('user/ReservationComplete');
    }

    /**
     * 予約した座席の一覧を表示
     */
    public function index()
    {
        $reservations = $this->seatReservationService->getReserveList();

        return Inertia::render('user/reservations/Index', [
            'reservations' => $reservations,
        ]);
    }

    /**
     * 予約した座席情報のPDFを出力
     */
    public function exportPdf(int $seat_id)
    {
        $pdf = Pdf::loadView('pdf.reservation');

    	return $pdf->stream();
    }
}
