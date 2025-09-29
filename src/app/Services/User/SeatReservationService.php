<?php

namespace App\Services\User;

use App\Models\Seat;
use Illuminate\Support\Facades\DB;

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
}