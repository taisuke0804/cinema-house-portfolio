<?php

namespace App\Services\Admin;
use App\Models\Screening;
use Illuminate\Support\Facades\DB;
use App\Models\Seat;

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
}