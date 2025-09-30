<?php

namespace App\Services\Admin;
use App\Models\Screening;
use Illuminate\Support\Facades\DB;
use App\Models\Seat;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ScreeningService
{
    /**
     * 上映スケジュールの新規登録
     */
    public function storeScreening(array $validated): void
    {
        // 日付 + 時刻を datetime に組み立て
        $validated['start_time'] = $validated['screening_date'] . ' ' . $validated['start_time'] . ':00';
        $validated['end_time']   = $validated['screening_date'] . ' ' . $validated['end_time'] . ':00';

        // DBに不要なフィールドを削除
        unset($validated['screening_date']);

        DB::transaction(function () use ($validated) {
            $screening = Screening::create($validated);

            $rows = range('A', 'B');
            $numbers = range(1, 10);

            $seats = [];
            foreach ($rows as $row) {
                foreach ($numbers as $number) {
                    $seats[] = [
                        'screening_id' => $screening->id,
                        'row' => $row,
                        'number' => $number,
                        'is_reserved' => false,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            Seat::insert($seats);
        });

    }

    /**
     * 上映スケジュールの詳細情報を取得
     */
    public function getScreeningDetails(int $screening_id): array
    {
        $screening = Screening::with([
            'movie:id,title,genre',
            'seats.user:id,name',
        ])
        ->select('id', 'movie_id', 'start_time', 'end_time')
        ->findOrFail($screening_id);

        // 行ごとにグループ化
        $groupedSeats = $screening->seats
            ->groupBy('row')
            // valuesメソッドはキーをリセット後、連続した整数にした新しいコレクションを返します。
            ->map(fn($rowSeats) => $rowSeats->sortBy('number')->values());
        
        // 日付・時間フォーマット
        $screening_date = Carbon::parse($screening->start_time)->format('Y年m月d日');
        $start_time = Carbon::parse($screening->start_time)->format('H:i');
        $end_time = Carbon::parse($screening->end_time)->format('H:i');
        // dd($screening->start_time);
        // Vueに渡すデータを加工して返却
        return [
            'screening_id' => $screening->id,
            'date' => $screening->start_time,
            'screening_date' => $screening_date,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'movie' => [
                'title' => $screening->movie->title,
                'genre' => $screening->movie->genre,
                'genre_label' => $screening->movie->genre->getLabel(),
            ],
            'seats' => $groupedSeats,
        ];

    }

    /**
     * ログインユーザーの予約済み座席情報を取得
     * 詳細画面全体で使用するためのメソッド
     */
    public function getAuthReservedSeatInfo(int $screening_id): ?array
    {
        $authReservedSeat = Seat::where('screening_id', $screening_id)
            ->where('user_id', Auth::guard('web')->id())
            ->where('is_reserved', true)
            ->first();

        if (!$authReservedSeat) {
            return null;
        }

        // Vue 側で必要な項目だけ返す
        return [
            'row' => $authReservedSeat->row,
            'number' => $authReservedSeat->number,
        ];
    }
}