<?php

namespace App\Services\User;

use App\Models\Screening;
use App\Models\Seat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;

class SeatReservationService
{
    /**
     * 座席を予約する処理
     */
    public function reserveSeat(array $reservationData): void
    {
        DB::transaction(function () use ($reservationData) {
            $seat = Seat::where('id', $reservationData['seat_id'])
                ->where('screening_id', $reservationData['screening_id'])
                ->lockForUpdate() // 排他制御
                ->firstOrFail(); // マッチする行が見つからなかった場合、自動的に404を返す
            

            if ($seat->is_reserved) {
                // 例外ハンドラによりHTTP例外を投げる
                if ($seat->user_id === $reservationData['user_id']) {
                    abort(422, 'すでにこの座席を予約済みです。');
                } else {
                    abort(422, '他のユーザーがすでに予約しています。');
                }
            }
            
            $seat->update([
                'user_id' => $reservationData['user_id'],
                'is_reserved' => true,
            ]);
        });
    }

    /**
     * ログインしたユーザーの予約した座席の一覧を取得
     */
    public function getReserveList(): Collection
    {
        $userId = Auth::guard('web')->id();
        $today = Carbon::today();

        $authReserveList = Seat::with([
            'screening:id,start_time,end_time,movie_id',
            'screening.movie:id,title'
        ])
        ->whereHas('screening', function ($query) use ($today) {
            $query->where('start_time', '>=', $today);
        })
        ->select('seats.id', 'seats.screening_id', 'seats.row', 'seats.number', 'seats.is_reserved')
        ->where('seats.user_id', $userId)
        ->where('seats.is_reserved', true)
        ->get();

        $authReserveList->transform(function (Seat $seat) {
            $seat->screening->date = $seat->screening->start_time->format('Y年m月d日');
            $seat->screening->start_format = $seat->screening->start_time->format('H:i');
            $seat->screening->end_format = $seat->screening->end_time->format('H:i');
            return $seat;
        });

        // 日付順にSORTする
        $authReserveList = $authReserveList->sortBy('screening.start_time')->values();

        return $authReserveList;

        // DBの処理重視なら以下のような書き方もあり。
        // Seat::with(['screening.movie'])
        // ->join('screenings', 'seats.screening_id', '=', 'screenings.id')
        // ->where('seats.user_id', $userId)
        // ->where('seats.is_reserved', true)
        // ->where('screenings.start_time', '>=', $today)
        // ->orderBy('screenings.start_time', 'asc')
        // ->get(['seats.*']);

    }

    /**
     * ログインしたユーザーの予約した座席情報を取得（個別）
     * PDF出力用
     */
    public function getReservationData(int $seat_id): ?Seat
    {
        $reservationData = Seat::with([
            'screening:id,start_time,end_time,movie_id',
            'screening.movie:id,title'
        ])
        ->select('seats.id', 'seats.screening_id', 'seats.row', 'seats.number')
        ->where('seats.id', $seat_id)
        ->where('seats.is_reserved', true)
        ->first();
        
        return $reservationData;
    }
}