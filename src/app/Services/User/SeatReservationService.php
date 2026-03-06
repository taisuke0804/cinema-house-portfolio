<?php

namespace App\Services\User;

use App\Models\Screening;
use App\Models\Seat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class SeatReservationService
{
    /**
     * 座席を予約する処理
     */
    public function reserveSeat(array $reservationData): void
    {
        DB::transaction(function () use ($reservationData) {
            $seatIds = $reservationData['seat_ids'];
            $screeningId = $reservationData['screening_id'];
            $userId = $reservationData['user_id'];

            $seats = Seat::query()
                ->whereIn('id', $seatIds)
                ->where('screening_id', $screeningId)
                ->lockForUpdate()
                ->get();

            if ($seats->count() !== count($seatIds)) {
                abort(422, '選択した座席情報が正しくありません。');
            }

            // 自分自身の予約があるかどうか
            $alreadyReservedBySelf = $seats->first(fn ($s) => $s->is_reserved && $s->user_id === $userId);
            if ($alreadyReservedBySelf) {
                abort(422, 'すでにこの座席を予約済みです。');
            }

            // 他のユーザーが予約済みかどうか
            $alreadyReservedByOther = $seats->first(fn ($s) => $s->is_reserved && $s->user_id !== $userId);
            if ($alreadyReservedByOther) {
                abort(422, '他のユーザーがすでに予約しています。');
            }

            // 全て空席ならまとめて座席予約処理
            Seat::query()
                ->whereIn('id', $seatIds)
                ->where('screening_id', $screeningId)
                ->where('is_reserved', false)
                ->update([
                    'user_id' => $userId,
                    'is_reserved' => true,
                ]);
        });
    }

    /**
     * ログインしたユーザーの予約した座席の一覧を取得（上映スケジュール単位）
     */
    public function getReserveList(): Collection
    {
        $userId = Auth::guard('web')->id();
        $today = Carbon::today();

        $seats = Seat::with([
                'screening:id,start_time,end_time,movie_id',
                'screening.movie:id,title',
            ])
            ->whereHas('screening', function ($query) use ($today) {
                $query->where('start_time', '>=', $today);
            })
            ->select('seats.id', 'seats.screening_id', 'seats.row', 'seats.number', 'seats.user_id', 'seats.is_reserved')
            ->where('seats.user_id', $userId)
            ->where('seats.is_reserved', true)
            ->orderBy('seats.screening_id')
            ->orderBy('seats.row')
            ->orderBy('seats.number')
            ->get();

        // screening_id ごとにまとめる
        $grouped = $seats
            ->groupBy('screening_id')
            ->map(function (Collection $group) {
                $first = $group->first();
                $screening = $first->screening;

                $seatLabels = $group
                    ->map(fn (Seat $seat) => $seat->row . $seat->number)
                    ->values();

                return collect([
                    'screening_id' => $screening->id,
                    'movie_title' => $screening->movie->title,
                    'date' => $screening->start_time->format('Y年m月d日'),
                    'start_time' => $screening->start_time->format('H:i'),
                    'end_time' => $screening->end_time->format('H:i'),
                    'seat_labels' => $seatLabels,
                ]);
            })
            // 上映開始日時順に並べ替える
            ->sortBy(fn ($item) => $item['date'] . ' ' . $item['start_time'])
            ->values();

        return $grouped;

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

    /**
     * 座席予約をキャンセルする処理
     */
    public function cancelSeat(int $seat_id): void
    {
        $seat = Seat::where('id', $seat_id)
            ->where('user_id', Auth::id())
            ->where('is_reserved', true)
            ->firstOrFail();

        $seat->update([
            'is_reserved' => false,
            'user_id' => null,
        ]);
    }
}
